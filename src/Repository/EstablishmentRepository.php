<?php

namespace App\Repository;

use App\Entity\Establishment;
use App\Entity\Authority;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Establishment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Establishment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Establishment[]    findAll()
 * @method Establishment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstablishmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Establishment::class);
    }

    /*
     * Search eastablishments based on query params and sort by
     */
    public function findEstablishments(Authority $authority, string $postCode, string $sortBy): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.authority = :authority')
            ->setParameter('authority', $authority)
            ->leftJoin('e.rating', 'r');
        //Append postcode search if present
        if ($postCode) {
            $qb->andwhere('e.postCode = :postCode')
                ->setParameter('postCode', $postCode);
        }
        //Append sort by based on query params
        if ($sortBy) {
            if ($sortBy == 'rating') {
                $qb->orderBy('r.ratingKey', 'DESC');
            } elseif ($sortBy == 'name') {
                $qb->orderBy('e.name', 'ASC');
            }
        }
        $query = $qb->getQuery();

        return $query->execute();
    }
}
