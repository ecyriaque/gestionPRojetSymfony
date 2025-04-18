<?php
// src/Repository/UtilisateurRepository.php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email LIKE :val')  // Recherche par e-mail
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('u.email', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')  // Recherche unique par e-mail
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
