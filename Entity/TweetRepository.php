<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TweetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TweetRepository extends EntityRepository
{
    private $nbTweets = 10;
    
    public function getWithUsers($page = 1)
    {
        $firstResult = (($page - 1) * $this->nbTweets);
        $qb = $this->createQueryBuilder('t')
                
            ->addSelect('user')
            ->innerJoin('t.user', 'user')
            
            ->orderBy('t.id', 'DESC')
            
            ->setFirstResult($firstResult)
            ->setMaxResults($this->nbTweets)
        ;
        
        return $qb->getQuery()->getResult();
    }
    
    public function getWithUsersAndMedias($lastTweetId = null,
        $orderByUser = false)
    {
        $qb = $this->createQueryBuilder('t')
                
            ->addSelect('user')
            ->innerJoin('t.user', 'user')
            
            ->addSelect('medias')
            ->leftJoin('t.medias', 'medias')
        ;
        
        if ($orderByUser)
        {
            $qb = $qb
                ->addOrderBy('user.id', 'ASC')
            ;
        }
        
        $qb = $qb    
            ->addOrderBy('t.id', 'ASC')
            ->setFirstResult(0)
            ->setMaxResults($this->nbTweets)
        ;
        
        if (! is_null($lastTweetId))
        {
            $qb = $qb
                ->where(
                    $qb->expr()->gte('t.id', $lastTweetId)
                )
            ;
        }
        
        return $qb->getQuery()->getResult();
    }
    
    public function countPendingTweets($lastTweetId = null)
    {
        $qb = $this->createQueryBuilder('t');
        
        $qb = $qb
            ->add('select', $qb->expr()->count('t.id'))
        ;
        
        if (! is_null($lastTweetId))
        {
            $qb = $qb
                ->where(
                    $qb->expr()->gte('t.id', $lastTweetId)
                )
            ;
        }
        
        # return COUNT() result
        return $qb->getQuery()->getSingleScalarResult();
    }
}
