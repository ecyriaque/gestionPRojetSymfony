# Plan de déploiement et architecture serveur

**Application :** Gestion de Projets Symfony  
**Stack :** PHP 8.3 · Symfony 6.4 · MySQL 8 · Apache · Docker Compose · Cloudflare Zero Trust Tunnel  
**Auteur :** Emilio Cyriaque  

---

## 1. Architecture globale

```
                         INTERNET
                             │
                     ┌───────▼────────┐
                     │  Cloudflare    │  TLS, DDoS protection, WAF
                     │  Edge Network  │  app.mondomaine.fr
                     └───────┬────────┘
                             │  Tunnel chiffré (sortant depuis le serveur)
                             │  Aucun port entrant ouvert sur le serveur
                             │
              ┌──────────────▼──────────────────────┐
              │           Serveur Linux              │
              │                                      │
              │  ┌──────────────────────────────┐   │
              │  │     Docker Compose           │   │
              │  │                              │   │
              │  │  ┌────────────┐              │   │
              │  │  │ cloudflared│◄─────────────┼───┼── Tunnel sortant
              │  │  │  (tunnel)  │              │   │
              │  │  └─────┬──────┘              │   │
              │  │        │ http://webserver:80  │   │
              │  │        │ (réseau Docker)      │   │
              │  │  ┌─────▼──────────────────┐  │   │
              │  │  │  webserver             │  │   │
              │  │  │  PHP 8.3 + Apache      │  │   │
              │  │  │  Symfony 6.4           │  │   │
              │  │  └─────┬──────────────────┘  │   │
              │  │        │                      │   │
              │  │  ┌─────▼──────────────────┐  │   │
              │  │  │  database (MySQL 8)    │  │   │
              │  │  │  Port 3306 interne     │  │   │
              │  │  └────────────────────────┘  │   │
              │  │                              │   │
              │  │  ┌────────────────────────┐  │   │
              │  │  │  ELK Stack             │  │   │
              │  │  │  Elastic/Logstash/Kib. │  │   │
              │  │  └────────────────────────┘  │   │
              │  └──────────────────────────────┘   │
              └──────────────────────────────────────┘
```

**Avantages de Cloudflare Tunnel :**
- Aucun port 80/443 ouvert publiquement sur le serveur
- TLS géré par Cloudflare (certificat automatique)
- Protection DDoS et WAF inclus
- L'IP réelle du visiteur est transmise via `X-Forwarded-For`

---

## 2. Comment fonctionne le flux d'une requête API

```
App tierce (IP: 1.2.3.4)
        │
        │  HTTPS vers api.mondomaine.fr
        ▼
Cloudflare Edge
        │  Ajoute header : X-Forwarded-For: 1.2.3.4
        │  Via tunnel chiffré (cloudflared)
        ▼
Container cloudflared
        │  Forward vers http://webserver:80
        ▼
Container webserver (Symfony)
        │
        ├── trusted_proxies: 172.16.0.0/12,10.0.0.0/8,192.168.0.0/16
        │   → Symfony fait confiance au réseau Docker
        │   → getClientIp() retourne 1.2.3.4 (depuis X-Forwarded-For)
        │
        ├── ApiIpRestrictionSubscriber
        │   → Vérifie 1.2.3.4 dans API_ALLOWED_IPS
        │   → Bloque si non autorisé (403)
        │
        └── ApiKeyAuthenticator
            → Vérifie header X-API-Key
            → Bloque si clé invalide (401)
```

---

## 3. Environnements

| Environnement | `APP_ENV` | URL | Tunnel Cloudflare |
|---|---|---|---|
| **Dev** | `dev` | `http://localhost:80` | Non (accès direct via port) |
| **Staging** | `prod` | `https://staging.mondomaine.fr` | Oui (tunnel staging) |
| **Production** | `prod` | `https://app.mondomaine.fr` | Oui (tunnel prod) |

---

## 4. Prérequis serveur (staging & prod)

- OS : Debian 12 / Ubuntu 22.04 LTS
- Docker Engine ≥ 24.0 + Docker Compose ≥ 2.20
- 2 vCPU / 4 Go RAM minimum · 20 Go SSD
- **Aucun port 80/443 à ouvrir** — tout passe par le tunnel Cloudflare
- Accès SSH avec clé uniquement (pas de mot de passe)
- Compte Cloudflare avec Zero Trust activé

---

## 5. Configuration Cloudflare (à faire une seule fois par environnement)

### 5.1 Créer le tunnel

1. Se connecter sur [Cloudflare Dashboard](https://dash.cloudflare.com)
2. Aller dans **Zero Trust > Networks > Tunnels**
3. Cliquer **Create a tunnel** → **Cloudflared**
4. Nommer le tunnel (ex: `gestion-projets-prod`)
5. Copier le **Token** affiché → ce sera `CLOUDFLARE_TUNNEL_TOKEN` dans `.env`

### 5.2 Configurer la route du tunnel

Dans la configuration du tunnel, ajouter une **Public Hostname** :

| Champ | Valeur |
|---|---|
| Subdomain | `app` (ou `staging`) |
| Domain | `mondomaine.fr` |
| Type | `HTTP` |
| URL | `webserver:80` |

> Le tunnel accède au webserver via le réseau Docker interne — pas besoin d'IP.

### 5.3 Ports à ouvrir sur le serveur

Uniquement le port SSH (22) pour l'administration. Le tunnel Cloudflare est **sortant uniquement**.

```
Firewall serveur :
  - 22/tcp  → SSH (restreindre aux IPs admin si possible)
  - TOUT LE RESTE → bloqué en entrée
```

---

## 6. Variables d'environnement par environnement

Le fichier `.env` à la racine (jamais commité) alimente Docker Compose, qui injecte les variables dans le container webserver.

### Dev (`.env` local)

```dotenv
# Docker
COMPOSE_PROJECT_NAME=lamp
PHPVERSION=php83
DATABASE=mysql8
HOST_MACHINE_UNSECURE_APACHE_PORT=80
HOST_MACHINE_SECURE_APACHE_PORT=443
HOST_MACHINE_MYSQL_PORT=3306
HOST_MACHINE_PMA_PORT=8080
MYSQL_ROOT_PASSWORD=root_password
MYSQL_USER=app_user
MYSQL_PASSWORD=app_password
MYSQL_DATABASE=symfony_project
PHP_INI=./config/php/php.ini
UPLOAD_LIMIT=512M
MEMORY_LIMIT=512M

# Symfony
APP_ENV=dev
APP_SECRET=une_chaine_aleatoire_32_caracteres
DATABASE_URL=mysql://app_user:app_password@database:3306/symfony_project?serverVersion=8.0&charset=utf8mb4
MAILER_DSN=smtp://host.docker.internal:1025

# Cloudflare — pas de tunnel en dev
CLOUDFLARE_TUNNEL_TOKEN=

# Sécurité — vide = aucune restriction en dev
TRUSTED_PROXIES=
API_ALLOWED_IPS=
SWAGGER_ALLOWED_CIDRS=
SWAGGER_USERNAME=swagger_admin
SWAGGER_PASSWORD=dev_password
```

### Staging / Production (`.env` sur le serveur)

```dotenv
# Docker
COMPOSE_PROJECT_NAME=lamp
PHPVERSION=php83
DATABASE=mysql8
# Ports non exposés en prod — cloudflared gère l'accès
HOST_MACHINE_UNSECURE_APACHE_PORT=127.0.0.1:8080
HOST_MACHINE_SECURE_APACHE_PORT=127.0.0.1:8443
HOST_MACHINE_MYSQL_PORT=127.0.0.1:3306
HOST_MACHINE_PMA_PORT=127.0.0.1:8081
MYSQL_ROOT_PASSWORD=<mot_de_passe_fort>
MYSQL_USER=app_user
MYSQL_PASSWORD=<mot_de_passe_fort>
MYSQL_DATABASE=symfony_project
PHP_INI=./config/php/php.ini
UPLOAD_LIMIT=512M
MEMORY_LIMIT=512M

# Symfony
APP_ENV=prod
APP_SECRET=<généré avec : openssl rand -hex 32>
DATABASE_URL=mysql://app_user:<mot_de_passe>@database:3306/symfony_project?serverVersion=8.0&charset=utf8mb4
MAILER_DSN=smtp://user:pass@smtp.example.com:587

# Cloudflare Tunnel — token depuis Cloudflare Dashboard
CLOUDFLARE_TUNNEL_TOKEN=<token_depuis_cloudflare_dashboard>

# trusted_proxies — laisser vide = plages Docker par défaut (172.16.0.0/12,10.0.0.0/8,192.168.0.0/16)
TRUSTED_PROXIES=

# IPs autorisées à appeler l'API (serveurs de l'app tierce)
API_ALLOWED_IPS=<IP_staging_ou_prod_app_tierce>

# CIDRs autorisés à accéder au Swagger (réseau interne entreprise)
SWAGGER_ALLOWED_CIDRS=10.0.0.0/8,192.168.1.0/24

# HTTP Basic Auth pour le Swagger
SWAGGER_USERNAME=swagger_admin
SWAGGER_PASSWORD=<mot_de_passe_fort>
```

---

## 7. Procédure de déploiement

### 7.1 Premier déploiement

```bash
# 1. Cloner le dépôt
git clone https://github.com/votre-org/gestionPRojetSymfony.git /var/www/app
cd /var/www/app

# 2. Créer le .env avec les vraies valeurs (voir section 6)
nano .env

# 3. Construire et démarrer les containers
docker compose up -d --build

# 4. Installer les dépendances PHP
docker exec -it lamp-php83 bash -c \
  "cd /var/www/html && composer install --no-dev --optimize-autoloader"

# 5. Vider le cache Symfony
docker exec -it lamp-php83 bash -c \
  "cd /var/www/html && APP_ENV=prod php bin/console cache:clear"

# 6. Exécuter les migrations
docker exec -it lamp-php83 bash -c \
  "cd /var/www/html && php bin/console doctrine:migrations:migrate --no-interaction"

# 7. Créer la clé API pour l'application tierce
docker exec -it lamp-php83 bash -c \
  "cd /var/www/html && php bin/console app:create-api-key 'App Tierce Prod' --ip=<IP_app_tierce>"
# La clé s'affiche une seule fois — la transmettre par canal sécurisé

# 8. Vérifier que cloudflared est connecté
docker logs lamp-cloudflared
# Doit afficher : "Registered tunnel connection"
```

### 7.2 Mise à jour manuelle (sans CI/CD)

```bash
git pull origin main
docker compose up -d --build webserver
docker exec -it lamp-php83 bash -c \
  "cd /var/www/html && composer install --no-dev --optimize-autoloader && \
   php bin/console doctrine:migrations:migrate --no-interaction && \
   APP_ENV=prod php bin/console cache:clear"
```

---

## 7bis. CI/CD avec GitHub Actions + Self-Hosted Runner

Les déploiements sont automatisés via GitHub Actions. Un **self-hosted runner** installé sur chaque serveur exécute les commandes directement — pas de secrets à transmettre à GitHub, pas d'accès SSH depuis l'extérieur.

### Architecture CI/CD

```
Développeur
    │
    │  git push origin main
    ▼
GitHub
    │  Trigger workflow deploy-staging.yml
    ▼
Runner staging (sur le serveur staging)
    │
    ├── git pull origin main
    ├── docker compose up -d --build
    ├── composer install
    ├── php bin/console doctrine:migrations:migrate
    └── php bin/console cache:clear


Développeur
    │
    │  git tag v1.2.0 && git push --tags
    ▼
GitHub
    │  Trigger workflow deploy-production.yml
    │  + Attente d'approbation manuelle (GitHub Environments)
    ▼
Runner production (sur le serveur prod)
    │
    └── (mêmes étapes)
```

### Stratégie de déclenchement

| Événement Git | Cible | Approbation |
|---|---|---|
| `push` sur `main` | Staging | Automatique |
| `git tag v*` | Production | Manuelle (GitHub Environments) |

### Installation du self-hosted runner sur le serveur

```bash
# 1. Sur GitHub : Settings > Actions > Runners > New self-hosted runner
#    Choisir Linux x64 — copier les commandes affichées

# 2. Sur le serveur, créer un utilisateur dédié
sudo useradd -m -s /bin/bash github-runner
sudo usermod -aG docker github-runner   # accès Docker sans sudo
sudo su - github-runner

# 3. Télécharger et configurer le runner (commandes depuis GitHub)
mkdir actions-runner && cd actions-runner
curl -o actions-runner-linux-x64.tar.gz -L https://github.com/actions/runner/releases/download/v2.x.x/actions-runner-linux-x64-2.x.x.tar.gz
tar xzf ./actions-runner-linux-x64.tar.gz
./config.sh --url https://github.com/VOTRE_ORG/gestionPRojetSymfony --token TOKEN_GITHUB

# 4. Ajouter des labels pour distinguer staging et prod
#    Lors de la config, entrer le label : staging  (ou production)

# 5. Installer comme service systemd
sudo ./svc.sh install github-runner
sudo ./svc.sh start

# 6. Vérifier
sudo ./svc.sh status
```

### Répertoire de travail du runner

Le runner doit être lancé depuis le répertoire du projet (ou y avoir accès) :

```bash
# Le runner travaille dans /home/github-runner/actions-runner/_work/
# Mais les commandes docker exec ciblent les containers par nom (lamp-php83)
# donc le runner n'a besoin que d'accès au docker daemon et au répertoire du projet

# S'assurer que le projet est cloné au bon endroit
sudo chown -R github-runner:github-runner /var/www/app

# Le workflow fait git pull depuis le répertoire du projet
# à configurer dans le workflow avec : working-directory: /var/www/app
```

### Protection de l'environnement Production sur GitHub

1. Sur GitHub : **Settings > Environments > New environment** → nommer `production`
2. Activer **Required reviewers** → ajouter les personnes autorisées à approuver
3. Le workflow `deploy-production.yml` attend l'approbation avant de s'exécuter

---

## 8. Gestion des clés API

```bash
# Créer une clé (affichée une seule fois)
docker exec -it lamp-php83 bash -c \
  "cd /var/www/html && php bin/console app:create-api-key 'Nom Application' --ip=1.2.3.4"

# La clé est hashée en SHA-256 en base — impossible de la récupérer ensuite
# Transmettre via un gestionnaire de secrets (Bitwarden, 1Password, Vault...)
```

**En-tête à utiliser côté application tierce :**
```
X-API-Key: <clé_générée>
```

---

## 9. Checklist avant mise en production

```
[ ] .env créé sur le serveur (jamais commité dans Git)
[ ] APP_SECRET généré avec : openssl rand -hex 32
[ ] CLOUDFLARE_TUNNEL_TOKEN renseigné
[ ] Tunnel Cloudflare configuré et route pointant vers webserver:80
[ ] Firewall serveur : uniquement SSH ouvert en entrée
[ ] API_ALLOWED_IPS renseigné avec les IPs de l'app tierce
[ ] SWAGGER_ALLOWED_CIDRS renseigné avec les CIDRs de l'entreprise
[ ] SWAGGER_USERNAME / SWAGGER_PASSWORD renseignés
[ ] Mots de passe MySQL différents du dev
[ ] docker compose up -d réussi sans erreur
[ ] docker logs lamp-cloudflared → "Registered tunnel connection"
[ ] Migrations exécutées sans erreur
[ ] Cache Symfony vidé en mode prod
[ ] Clé API générée et transmise à l'équipe tierce par canal sécurisé
[ ] Test : appel API depuis une IP autorisée avec la clé → 200 OK
[ ] Test : appel API depuis une IP non autorisée → 403 Forbidden
[ ] Test : accès Swagger depuis le réseau entreprise → HTTP auth demandée
[ ] Test : accès Swagger depuis une IP externe → 403 Forbidden
```

---

## 10. Monitoring

Les logs sont centralisés dans la stack ELK (Elasticsearch + Logstash + Kibana).  
Kibana est accessible uniquement en interne via un second tunnel Cloudflare dédié, ou via SSH tunnel.

```bash
# Logs Apache/PHP en temps réel
docker logs -f lamp-php83

# Logs Symfony
docker exec -it lamp-php83 tail -f /var/www/html/var/log/prod.log

# Statut du tunnel Cloudflare
docker logs -f lamp-cloudflared
```
