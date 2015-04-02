<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Media
 */
class Media
{
    /**
     * @var bigint
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
     * @var ArrayCollection
     */
    private $tweets;
    
    public function __construct()
    {
        $this->tweets = new ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param bigint $id
     * @return Media 
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
     * Set media_url_https
     *
     * @param string $mediaUrlHttps
     * @return Media
     */
    public function setMediaUrlHttps($mediaUrlHttps)
    {
        $this->media_url_https = $mediaUrlHttps;

        return $this;
    }
    
    /**
     * Get media_url_https
     *
     * @return string 
     */
    public function getMediaUrlHttps()
    {
        return $this->media_url_https;
    }
    
    /**
     * Set url
     *
     * @param string $url
     * @return Media
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
    
    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Set display_url
     *
     * @param string $displayUrl
     * @return Media
     */
    public function setDisplayUrl($displayUrl)
    {
        $this->display_url = $displayUrl;

        return $this;
    }
    
    /**
     * Get display_url
     *
     * @return string 
     */
    public function getDisplayUrl()
    {
        return $this->display_url;
    }
    
    /**
     * Set expanded_url
     *
     * @param string $expandedUrl
     * @return Media
     */
    public function setExpandedUrl($expandedUrl)
    {
        $this->expanded_url = $expandedUrl;

        return $this;
    }
    
    /**
     * Get expanded_url
     *
     * @return string 
     */
    public function getExpandedUrl()
    {
        return $this->expanded_url;
    }
    
    /**
     * Add a tweet
     *
     * @return Media
     */
    public function addTweet(Tweet $tweet)
    {
        $this->tweets[] = $tweet;
        
        return $this;
    }
}
