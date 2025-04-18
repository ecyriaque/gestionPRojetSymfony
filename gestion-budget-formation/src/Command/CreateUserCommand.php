<?php

// src/Command/CreateUserCommand.php
// src/Command/CreateUserCommand.php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    private $entityManager;
    private $passwordHasher;
    
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    
    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Crée un nouvel utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l\'utilisateur')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom de l\'utilisateur') // Ajout du nom
            ->addArgument('role', InputArgument::REQUIRED, 'Rôle de l\'utilisateur'); // Ajout du rôle
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name');  // Récupération du nom
        $role = $input->getArgument('role');  // Récupération du rôle
        
        // Standardiser le format du rôle (première lettre en majuscule, reste en minuscule)
        $standardizedRole = ucfirst(strtolower($role));
        
        // Créer l'entité Utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($email);
        $utilisateur->setNom($name);  // Définir le nom de l'utilisateur
        
        // Encoder le mot de passe avec UserPasswordHasherInterface
        $encodedPassword = $this->passwordHasher->hashPassword($utilisateur, $password);
        $utilisateur->setMotdepasse($encodedPassword);
        
        // Définir le rôle de l'utilisateur
        $utilisateur->setRole($standardizedRole);
        
        // Sauvegarder l'utilisateur en base de données
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();
        
        $output->writeln('Utilisateur créé avec succès !');
        return Command::SUCCESS;
    }
}