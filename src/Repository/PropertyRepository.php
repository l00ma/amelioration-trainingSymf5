<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }



    /**
     * @return Query
     */

    public function findAllVisibleQuery(PropertySearch $search): Query
    {
        $query = $this->findVisibleQuery();

        if ($search->getCodePostal()) {
            $value = $search->getCodePostal();
            for ($i = 0; $i <= 3; $i++) {
                $query = $query
                    ->andWhere('p.postal_code LIKE :codepostal')
                    ->setParameter(':codepostal', $value . '%');
                if (empty($query->getQuery()->getResult())) {
                    $value = substr($value, 0, -1);
                } else {
                    #dd($query->getQuery()->getResult());
                    break;
                }
            }
        }
        if ($search->getMaxPrice()) {
            $query = $query
                ->andWhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }
        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }
        if ($search->getMaxSurface()) {
            $query = $query
                ->andWhere('p.surface <= :maxsurface')
                ->setParameter('maxsurface', $search->getMaxSurface());
        }
        if ($search->getNbrRooms()) {
            $query = $query
                ->andWhere('p.rooms = :nbrrooms')
                ->setParameter('nbrrooms', $search->getNbrRooms());
        }
        if ($search->getOptions()->count() > 0) {
            $k = 0;
            foreach ($search->getOptions() as $spec) {
                $k++;
                $query = $query
                    ->andWhere(":spec$k MEMBER OF p.specs")
                    ->setParameter("spec$k", $spec);
            }
        }
        if ($search->getNbid()) {
            $query = $query
                ->andWhere('p.id = :nbid')
                ->setParameter('nbid', $search->getNbId());
        }
        if ($search->getIsSold()) {
            $query = $this->findInvisibleQuery()
                ->andWhere('p.sold = :issold')
                ->setParameter('issold', $search->getIsSold());
        }
        return $query->getQuery();
    }

    /**
     * @return Property[]
     */

    public function findLatest(): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    // public function add(Property $entity, bool $flush = false): void
    // {
    //     $this->getEntityManager()->persist($entity);

    //     if ($flush) {
    //         $this->getEntityManager()->flush();
    //     }
    // }

    // public function remove(Property $entity, bool $flush = false): void
    // {
    //     $this->getEntityManager()->remove($entity);

    //     if ($flush) {
    //         $this->getEntityManager()->flush();
    //     }
    // }

    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false');
    }

    private function findInvisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.sold = true');
    }

    //    /**
    //     * @return Property[] Returns an array of Property objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Property
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
