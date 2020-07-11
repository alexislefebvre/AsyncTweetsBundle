<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Media.
 */
class Media
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $media_url_https;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $display_url;

    /**
     * @var string
     */
    private $expanded_url;

    /**
     * @var Collection<int, Tweet>
     */
    private $tweets;

    public function __construct(?int $id = null)
    {
        if (!is_null($id)) {
            $this->setId($id);
        }

        $this->tweets = new ArrayCollection();
    }

    /**
     * Set id.
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
     * Set media_url_https.
     */
    public function setMediaUrlHttps(string $mediaUrlHttps): self
    {
        $this->media_url_https = $mediaUrlHttps;

        return $this;
    }

    /**
     * Get media_url_https.
     */
    public function getMediaUrlHttps(): string
    {
        return $this->media_url_https;
    }

    /**
     * Set url.
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set display_url.
     */
    public function setDisplayUrl(string $displayUrl): self
    {
        $this->display_url = $displayUrl;

        return $this;
    }

    /**
     * Get display_url.
     */
    public function getDisplayUrl(): string
    {
        return $this->display_url;
    }

    /**
     * Set expanded_url.
     */
    public function setExpandedUrl(string $expandedUrl): self
    {
        $this->expanded_url = $expandedUrl;

        return $this;
    }

    /**
     * Get expanded_url.
     */
    public function getExpandedUrl(): ?string
    {
        return $this->expanded_url;
    }

    /**
     * Add a tweet.
     */
    public function addTweet(Tweet $tweet): self
    {
        $this->tweets->add($tweet);

        return $this;
    }

    /**
     * Remove a tweet.
     */
    public function removeTweet(Tweet $tweet): self
    {
        $this->tweets->removeElement($tweet);

        return $this;
    }

    /**
     * Get tweets.
     *
     * @return Collection<int, Tweet>
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    /**
     * Call setter functions.
     */
    public function setValues(\stdClass $mediaTmp): self
    {
        $this
            ->setMediaUrlHttps($mediaTmp->media_url_https)
            ->setUrl($mediaTmp->url)
            ->setDisplayUrl($mediaTmp->display_url)
            ->setExpandedUrl($mediaTmp->expanded_url);

        return $this;
    }
}
