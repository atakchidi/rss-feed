<?php

namespace App\Repository;

use App\Entity\ClaimedUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ClaimedUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClaimedUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClaimedUser[]    findAll()
 * @method ClaimedUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClaimedUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClaimedUser::class);
    }

    public function isMailTaken($email): bool
    {
        return (bool) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
