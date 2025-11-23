<?php

namespace App\Repository;

use App\Entity\Ouvrage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ouvrage>
 */
class OuvrageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ouvrage::class);
    }

    public function search(array $criteria)
{
    $qb = $this->createQueryBuilder('o')
        ->leftJoin('o.auteurs', 'a')
        ->leftJoin('o.categories', 'c')
        ->leftJoin('o.tags', 't')
        ->leftJoin('o.exemplaires', 'e')
        ->addSelect('a', 'c', 't', 'e');

    if (!empty($criteria['titre'])) {
        $qb->andWhere('o.titre LIKE :titre')
           ->setParameter('titre', '%' . $criteria['titre'] . '%');
    }

    if (!empty($criteria['auteur'])) {
        $qb->andWhere(':auteur MEMBER OF o.auteurs')
           ->setParameter('auteur', $criteria['auteur']);
    }

    if (!empty($criteria['categorie'])) {
        $qb->andWhere(':categorie MEMBER OF o.categories')
           ->setParameter('categorie', $criteria['categorie']);
    }

    if (!empty($criteria['tag'])) {
        $qb->andWhere(':tag MEMBER OF o.tags')
           ->setParameter('tag', $criteria['tag']);
    }

    if (!empty($criteria['disponible'])) {
        $qb->andWhere('e.disponibilite = true');
    }

    return $qb->getQuery()->getResult();
}


//    /**
//     * @return Ouvrage[] Returns an array of Ouvrage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ouvrage
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
