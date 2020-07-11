<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * User.
 */
class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $screen_name;

    /**
     * @var string|null
     */
    private $profile_image_url;

    /**
     * @var string|null
     */
    private $profile_image_url_https;

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

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set screen_name.
     */
    public function setScreenName(string $screenName): self
    {
        $this->screen_name = $screenName;

        return $this;
    }

    /**
     * Get screen_name.
     */
    public function getScreenName(): string
    {
        return $this->screen_name;
    }

    /**
     * Set profile_image_url.
     */
    public function setProfileImageUrl(?string $profileImageUrl): self
    {
        $this->profile_image_url = $profileImageUrl;

        return $this;
    }

    /**
     * Get profile_image_url.
     */
    public function getProfileImageUrl(): ?string
    {
        return $this->profile_image_url;
    }

    /**
     * Set profile_image_url_https.
     */
    public function setProfileImageUrlHttps(?string $profileImageUrlHttps): self
    {
        $this->profile_image_url_https = $profileImageUrlHttps;

        return $this;
    }

    /**
     * Get profile_image_url_https.
     */
    public function getProfileImageUrlHttps(): ?string
    {
        return $this->profile_image_url_https;
    }

    /**
     * Get profile image, with HTTPS if available.
     */
    public function getProfileImageUrlHttpOrHttps(): ?string
    {
        if (!is_null($this->getProfileImageUrlHttps())) {
            return $this->getProfileImageUrlHttps();
        }
        // else

        return $this->getProfileImageUrl();
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
     * Add a tweet.
     */
    public function addTweet(Tweet $tweet): self
    {
        $this->tweets->add($tweet);

        return $this;
    }

    /**
     * Call setter functions.
     */
    public function setValues(\stdClass $userTmp): self
    {
        $this
            ->setName($userTmp->name)
            ->setScreenName($userTmp->screen_name)
            ->setProfileImageUrlHttps($userTmp->profile_image_url_https);

        return $this;
    }
}
