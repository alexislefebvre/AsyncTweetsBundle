<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Tweet.
 */
class Tweet
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $retweet_count = 0;

    /**
     * @var int
     */
    private $favorite_count = 0;

    /**
     * @var User
     */
    private $user;

    /**
     * In timeline: false for retweeted Tweets.
     *
     * @var bool
     */
    private $in_timeline = false;

    /**
     * @var Tweet
     */
    private $retweeted_status = null;

    /**
     * @var Collection<int, Tweet>
     */
    private $retweeting_statuses;

    /**
     * @var Collection<int, Media>
     */
    private $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->retweeting_statuses = new ArrayCollection();
    }

    /**
     * Set id.
     *
     * @return Tweet
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set created_at.
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at.
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * Set text.
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function getTextLinkified(): ?string
    {
        /* @see http://stackoverflow.com/questions/507436/how-do-i-linkify-urls-in-a-string-with-php/507459#507459 */
        return preg_replace(
            '~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~',
            '<a href="\\0">\\0</a>',
            $this->getText()
        );
    }

    /**
     * Set retweet_count.
     */
    public function setRetweetCount(int $retweetCount): self
    {
        $this->retweet_count = $retweetCount;

        return $this;
    }

    /**
     * Get retweet_count.
     */
    public function getRetweetCount(): int
    {
        return $this->retweet_count;
    }

    /**
     * Set favorite_count.
     */
    public function setFavoriteCount(int $favoriteCount): self
    {
        $this->favorite_count = $favoriteCount;

        return $this;
    }

    /**
     * Get favorite_count.
     */
    public function getFavoriteCount(): int
    {
        return $this->favorite_count;
    }

    /**
     * Set user.
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        $this->user->addTweet($this);

        return $this;
    }

    /**
     * Get User.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set in timeline.
     */
    public function setInTimeline(bool $inTimeline): self
    {
        $this->in_timeline = $inTimeline;

        return $this;
    }

    /**
     * Get in timeline.
     */
    public function isInTimeline(): bool
    {
        return $this->in_timeline;
    }

    /**
     * Set retweeted
     * "This attribute contains a representation of the original Tweet
     *  that was retweeted.".
     */
    public function setRetweetedStatus(self $retweetedStatus): self
    {
        $this->retweeted_status = $retweetedStatus;

        return $this;
    }

    /**
     * Get retweeted status.
     */
    public function getRetweetedStatus(): ?self
    {
        return $this->retweeted_status;
    }

    /**
     * Get medias.
     *
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        $this->medias->add($media);
        $media->addTweet($this);

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        $this->medias->removeElement($media);
        $media->removeTweet($this);

        return $this;
    }

    /**
     * Get retweeting status.
     *
     * @return Collection<int, Tweet>
     */
    public function getRetweetingStatuses(): Collection
    {
        return $this->retweeting_statuses;
    }

    /**
     * Call setter functions.
     */
    public function setValues(\stdClass $tweetTmp): self
    {
        $this
            ->setCreatedAt(new \DateTime($tweetTmp->created_at))
            ->setText($tweetTmp->text)
            ->setRetweetCount($tweetTmp->retweet_count)
            ->setFavoriteCount($tweetTmp->favorite_count);

        return $this;
    }

    /**
     * Check that tweet can be deleted.
     */
    public function mustBeKept(int $tweetId): bool
    {
        if (count($this->getRetweetingStatuses()) == 0) {
            // This tweet has not been retweeted
            return false;
        }

        // Check that this tweet has not been retweeted after $tweetId
        foreach ($this->getRetweetingStatuses() as $retweeting_status) {
            // This tweet is retweeted in the timeline, keep it
            if ($retweeting_status->getId() >= $tweetId) {
                return true;
            }
        }

        return false;
    }
}
