<?php

namespace Badger\Bundle\UserBundle\Doctrine\Repository;

use Badger\Component\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @author  Adrien Pétremann <hello@grena.fr>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($qb->expr()->count('u'))
            ->from('UserBundle:User', 'u');

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortedUserByUnlockedBadges($order = 'DESC', $limit = 10)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u AS user, COUNT(bc.id) AS nbUnlockedBadges')
            ->from('UserBundle:User', 'u')
            ->leftJoin('GameBundle:BadgeCompletion', 'bc')
            ->where('bc.user = u')
            ->setMaxResults($limit)
            ->orderBy('nbUnlockedBadges', $order)
            ->groupBy('u')
        ;

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUsernames()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u.username')
            ->from('UserBundle:User', 'u')
        ;

        $result = $qb->getQuery()->getResult();

        return array_column($result, 'username');
    }

    /**
     * {@inheritdoc}
     */
    public function getNewUsersForMonth($month, $year)
    {
        $lastDay = date('t', mktime(0, 0, 0, $month, 1, $year));

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from('UserBundle:User', 'u')
            ->where('u.date_registered >= :firstDayOfMonth')
            ->andWhere('u.date_registered <= :lastDayOfMonth')
            ->orderBy('u.date_registered', 'DESC')
            ->setParameter('firstDayOfMonth', date(sprintf('%s-%s-01', $year, $month)))
            ->setParameter('lastDayOfMonth', date(sprintf('%s-%s-%s', $year, $month, $lastDay)))
        ;

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getUsersPerUnlockedBadgesForMonth($month, $year)
    {

    }
}
