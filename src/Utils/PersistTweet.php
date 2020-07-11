<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Utils;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\Table;

class PersistTweet
{
    private $em;
    private $displayTable;
    private $table;

    public function __construct(EntityManagerInterface $em, bool $displayTable, ?Table $table)
    {
        $this->em = $em;
        $this->displayTable = $displayTable;
        $this->table = $table;
    }

    /**
     * @param \stdClass $userTmp
     *
     * @return User
     */
    protected function persistUser(\stdClass $userTmp)
    {
        $user = $this->em
            ->getRepository('AsyncTweetsBundle:User')
            ->findOneBy(['id' => $userTmp->id]);

        if (!$user) {
            // Only set the id when adding the User
            $user = new User($userTmp->id);
        }

        // Update other fields
        $user->setValues($userTmp);

        $this->em->persist($user);

        return $user;
    }

    /**
     * @param array<\stdClass> $medias
     * @param Tweet $tweet
     */
    public function iterateMedias($medias, Tweet $tweet): void
    {
        foreach ($medias as $mediaTmp) {
            if ($mediaTmp->type == 'photo') {
                $this->persistMedia($tweet, $mediaTmp);
            }
        }
    }

    protected function addMedias(\stdClass $tweetTmp, Tweet $tweet): void
    {
        if ((isset($tweetTmp->entities))
            && (isset($tweetTmp->entities->media))) {
            $this->iterateMedias($tweetTmp->entities->media, $tweet);
        }
    }

    protected function createTweet(\stdClass $tweetTmp, User $user, bool $inTimeline): Tweet
    {
        $tweet = new Tweet();

        $tweet
            ->setId($tweetTmp->id)
            ->setValues($tweetTmp)
            ->setUser($user)
            ->setInTimeline($inTimeline);

        $this->addMedias($tweetTmp, $tweet);

        return $tweet;
    }

    protected function persistTweet(
        \stdClass $tweetTmp,
        User $user,
        bool $inTimeline
    ): Tweet {
        $tweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => $tweetTmp->id]);

        if (!$tweet) {
            $tweet = $this->createTweet($tweetTmp, $user, $inTimeline);
        }

        if (isset($tweetTmp->retweeted_status)) {
            $retweet = $this->persistRetweetedTweet($tweetTmp);
            $tweet->setRetweetedStatus($retweet);
        }

        $this->em->persist($tweet);
        $this->em->flush();

        return $tweet;
    }

    protected function persistRetweetedTweet(\stdClass $tweetTmp): Tweet
    {
        $retweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => $tweetTmp->retweeted_status->id]);

        if (!$retweet) {
            $retweet = $this->addTweet(
                $tweetTmp->retweeted_status
            );
        }

        return $retweet;
    }

    protected function persistMedia(Tweet $tweet, \stdClass $mediaTmp): void
    {
        $media = $this->em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findOneBy(['id' => $mediaTmp->id]);

        if (!$media) {
            // Only set the id and values when adding the Media
            $media = new Media($mediaTmp->id);
            $media->setValues($mediaTmp);
            $this->em->persist($media);
            $this->em->flush();
        }

        $tweet->addMedia($media);
    }

    public function addTweet(\stdClass $tweetTmp, bool $inTimeline = false): Tweet
    {
        $user = $this->persistUser($tweetTmp->user);

        $tweet = $this->persistTweet($tweetTmp, $user, $inTimeline);

        // Ignore retweeted tweets
        if ($this->displayTable && $inTimeline) {
            $this->table->addRow([
                $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
                mb_substr($tweet->getText(), 0, 40),
                $user->getName(),
            ]);
        }

        return $tweet;
    }
}
