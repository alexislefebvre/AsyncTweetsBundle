<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * TweetRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @extends \Doctrine\ORM\EntityRepository<Tweet>
 */
class TweetRepository extends EntityRepository
{
    const TWEETS_PER_PAGE = 5;

    /**
     * @return int|mixed|string
     */
    public function getWithUsers(int $page = 1)
    {
        $firstResult = (($page - 1) * self::TWEETS_PER_PAGE);

        $qb = $this->createQueryBuilder('t');

        $query = $qb
            ->select('t, user, rt, rt_user')
            ->innerJoin('t.user', 'user')
            ->leftJoin('t.retweeted_status', 'rt')
            ->leftJoin('rt.user', 'rt_user')

            // Ignore tweets that were only retweeted
            ->where($qb->expr()->eq('t.in_timeline', 'true'))

            ->orderBy('t.id', 'DESC')

            ->setFirstResult($firstResult)
            ->setMaxResults(self::TWEETS_PER_PAGE);

        return $query->getQuery()->getResult();
    }

    private function getWithUsersAndMediasQuery(QueryBuilder $qb): QueryBuilder
    {
        $query = $qb
            ->select('t, user, medias, rt, rt_user')
            ->innerJoin('t.user', 'user')
            ->leftJoin('t.medias', 'medias')
            ->leftJoin('t.retweeted_status', 'rt')
            ->leftJoin('rt.user', 'rt_user')

            // Ignore tweets that were only retweeted
            ->where($qb->expr()->eq('t.in_timeline', 'true'))

            ->orderBy('t.id', 'ASC')

            ->setFirstResult(0)
            ->setMaxResults(self::TWEETS_PER_PAGE);

        return $query;
    }

    /**
     * @return array<Tweet>
     */
    public function getWithUsersAndMedias(?int $firstTweetId = null): array
    {
        $qb = $this->createQueryBuilder('t');

        $query = $this->getWithUsersAndMediasQuery($qb);

        if (!is_null($firstTweetId)) {
            $query = $query->andWhere(
                $qb->expr()->gte('t.id', $firstTweetId)
            );
        }

        return $query->getQuery()->getResult();
    }

    private function getTweetId(string $condition, string $order, int $tweetId): ?int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id')

            ->where('t.id '.$condition.' :tweetId')
            ->setParameter(':tweetId', $tweetId)

            ->andWhere('t.in_timeline = true')

            ->orderBy('t.id', $order)

            ->setFirstResult(self::TWEETS_PER_PAGE - 1)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return is_array($result) ? $result['id'] : null;
    }

    public function getPreviousTweetId(int $tweetId): ?int
    {
        return $this->getTweetId('<', 'DESC', $tweetId);
    }

    public function getNextTweetId(int $tweetId): ?int
    {
        return $this->getTweetId('>', 'ASC', $tweetId);
    }

    public function countPendingTweets(?int $lastTweetId = null): int
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('t');

        $query = $qb
            ->add('select', $qb->expr()->count('t.id'))
            // Ignore tweets that were only retweeted
            ->where(
                $qb->expr()->eq('t.in_timeline', 'true')
            );

        if (!is_null($lastTweetId)) {
            $query = $query->andWhere(
                $qb->expr()->gte('t.id', $lastTweetId)
            );
        }

        // return result of "COUNT()" query
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getLastTweet(): ?Tweet
    {
        $qb = $this->createQueryBuilder('t')
            ->addOrderBy('t.id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array<Tweet>
     */
    private function getTweetsLessThanId(int $tweetId): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t, m')
            ->leftJoin('t.medias', 'm')
            ->where('t.id < :tweetId')
            ->setParameter(':tweetId', $tweetId)

            // Get retweeted tweets (it would break foreign keys)
            //  http://stackoverflow.com/questions/15087933/how-to-do-left-join-in-doctrine/15088250#15088250
            ->leftJoin(
                'AsyncTweetsBundle:Tweet',
                't2',
                'WITH',
                't.id = t2.retweeted_status'
            )

            ->orderBy('t.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Remove Media not associated to any Tweet.
     */
    private function removeOrphanMedias(Media $media): void
    {
        if (count($media->getTweets()) == 0) {
            $this->_em->remove($media);
        }
    }

    /**
     * Remove the tweet and return 1 is the deleted tweet is not a
     *  retweet.
     */
    protected function removeTweet(Tweet $tweet): int
    {
        $count = 0;

        foreach ($tweet->getMedias() as $media) {
            $tweet->removeMedia($media);
            $this->removeOrphanMedias($media);
        }

        // Don't count tweets that were only retweeted
        if ($tweet->isInTimeline()) {
            $count = 1;
        }

        $this->_em->remove($tweet);

        return $count;
    }

    /**
     * Delete tweets and return the number of deleted tweets (excluding
     *  retweeted-only tweets).
     */
    public function deleteAndHideTweetsLessThanId(int $tweetId): int
    {
        $count = 0;

        $tweets = $this->getTweetsLessThanId($tweetId);

        foreach ($tweets as $tweet) {
            if ($tweet->mustBeKept($tweetId)) {
                // The Tweet is still in the timeline, it can only be hidden
                $tweet->setInTimeline(false);
                $this->_em->persist($tweet);
            } else {
                // The Tweet has not been retweeted, it can be removed
                $count += $this->removeTweet($tweet);
            }
        }

        $this->_em->flush();

        return $count;
    }
}
