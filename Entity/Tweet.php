<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tweet
 */
class Tweet
{
    /**
     * @var bigint
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
     * @var integer
     */
    private $retweet_count;
    
    /**
     * @var integer
     */
    private $favorite_count;
        
    /**
     * @var integer
     */
    private $user;
    
    /**
     * @var ArrayCollection
     */
    private $medias;
    
    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param bigint $id
     * @return Tweet 
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Tweet
     */
    public function setCreatedAt(\Datetime $createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }
    
    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Set text
     *
     * @param string $text
     * @return Tweet
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
    
    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
    
    public function getTextLinkified()
    {
        /** @see http://stackoverflow.com/questions/507436/how-do-i-linkify-urls-in-a-string-with-php/507459#507459 */
        return preg_replace(
            "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
            "<a href=\"\\0\">\\0</a>", 
            $this->getText()
        );
    }
    
    /**
     * Set retweet_count
     *
     * @param integer $retweetCount
     * @return Tweet
     */
    public function setRetweetCount($retweetCount)
    {
        $this->retweet_count = $retweetCount;

        return $this;
    }
    
    /**
     * Get retweet_count
     *
     * @return integer 
     */
    public function getRetweetCount()
    {
        return $this->retweet_count;
    }
    
    /**
     * Set favorite_count
     *
     * @param integer $favoriteCount
     * @return Tweet
     */
    public function setFavoriteCount($favoriteCount)
    {
        $this->favorite_count = $favoriteCount;

        return $this;
    }
    
    /**
     * Get favorite_count
     *
     * @return integer
     */
    public function getFavoriteCount()
    {
        return $this->favorite_count;
    }
    
    /**
     * Set user
     *
     * @param User $user
     * @return Tweet
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user->addTweet($this);

        return $this;
    }
    
    /**
     * Get User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Get medias
     *
     * @return ArrayCollection 
     */
    public function getMedias()
    {
        return $this->medias;
    }
    
    /**
     * Add a media
     *
     * @return Tweet
     */
    public function addMedia(Media $media)
    {
        $media->addTweet($this);
        $this->medias[] = $media;
        
        return $this;
    }
}
