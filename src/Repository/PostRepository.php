<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param string $checksum
     *
     * @return Post|null
     * @throws NonUniqueResultException
     */
    public function findOneByChecksum(string $checksum): ?Post
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.checksum = :checksum')
            ->setParameter('checksum', $checksum)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
