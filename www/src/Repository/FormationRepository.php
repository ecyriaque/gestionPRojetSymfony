<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    //    /**
    //     * @return Formation[] Returns an array of Formation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Formation
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouver toutes les formations triées par date de formation descendante
     */
    public function findAllSortedByDate(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.dateformation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les formations liées à un projet spécifique
     */
    public function findByProjet(int $projetId): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.session', 's')
            ->join('s.projet', 'p')
            ->where('p.projetid = :projetId')
            ->setParameter('projetId', $projetId)
            ->orderBy('f.dateformation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les formations par organisme
     */
    public function findByOrganisme(string $organisme): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.organisme LIKE :organisme')
            ->setParameter('organisme', '%' . $organisme . '%')
            ->orderBy('f.dateformation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les formations sur une période donnée
     */
    public function findByPeriod(\DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.dateformation BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('f.dateformation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
