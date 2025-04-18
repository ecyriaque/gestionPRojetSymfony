<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    public function findByClient($clientId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByName($name): ?Projet
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
