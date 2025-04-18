# Tests pour l'application de gestion de budget de formation

Ce dossier contient différents types de tests pour l'application de gestion de budget de formation.

## Structure des tests

- `Entity/` - Tests unitaires pour les entités
- `Service/` - Tests unitaires pour les services
- `Controller/` - Tests d'intégration pour les contrôleurs
- `Functional/` - Tests fonctionnels pour les scénarios complets

## Types de tests

### Tests unitaires

Les tests unitaires vérifient que les composants individuels de l'application fonctionnent correctement de manière isolée.

#### Tests des entités

- `Entity/ClientTest.php` - Teste les getters et setters de l'entité Client
- `Entity/ProjetTest.php` - Teste les getters et setters de l'entité Projet
- `Entity/UtilisateurTest.php` - Teste les getters et setters et les méthodes spécifiques de l'entité Utilisateur
- `Entity/FormationTest.php` - Teste les getters et setters de l'entité Formation
- `Entity/SessionFormationTest.php` - Teste les getters et setters de l'entité SessionFormation
- `Entity/FactureTest.php` - Teste les getters et setters et les transitions d'état de l'entité Facture
- `Entity/AppelDeFondsTest.php` - Teste les getters et setters de l'entité AppelDeFonds
- `Entity/AlerteBudgetTest.php` - Teste les getters et setters de l'entité AlerteBudget

#### Tests des services

- `Service/BudgetCalculatorTest.php` - Teste les méthodes de calcul du service BudgetCalculator

### Tests d'intégration

Les tests d'intégration vérifient que les composants interagissent correctement entre eux.

- `Controller/ClientControllerTest.php` - Teste les routes et actions du contrôleur Client

### Tests fonctionnels

Les tests fonctionnels vérifient le comportement de l'application du point de vue de l'utilisateur.

- `Functional/ApplicationTest.php` - Teste les principales fonctionnalités de l'application

## Exécution des tests

### Prérequis

Pour exécuter les tests, vous devez avoir :

1. Une base de données de test configurée
2. Un utilisateur `admin@example.com` dans cette base de données

### Commandes

Pour exécuter tous les tests :

```bash
vendor/bin/phpunit
```

Pour exécuter uniquement les tests unitaires des entités :

```bash
vendor/bin/phpunit tests/Entity
```

Pour exécuter uniquement les tests unitaires des services :

```bash
vendor/bin/phpunit tests/Service
```

Pour exécuter uniquement les tests d'intégration et fonctionnels :

```bash
vendor/bin/phpunit tests/Controller
vendor/bin/phpunit tests/Functional
```

### Dépendances

Pour les tests fonctionnels et d'intégration, assurez-vous d'avoir installé :

```bash
composer require --dev symfony/browser-kit symfony/css-selector
```

## Bonnes pratiques

- Chaque test doit être indépendant des autres
- Utilisez des assertions explicites pour faciliter le débogage
- Nommez clairement vos méthodes de test pour comprendre ce qu'elles testent
- Préparez des données de test appropriées dans la méthode `setUp()`
- Testez les cas limites et les valeurs extrêmes en plus des cas normaux
- Vérifiez les relations entre entités et la cohérence des données
