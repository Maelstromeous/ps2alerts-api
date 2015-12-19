<?php

namespace Ps2alerts\Api\Loader;

use Ps2Alerts\Api\Contract\RedisAwareInterface;
use Ps2Alerts\Api\Contract\RedisAwareTrait;

abstract class AbstractLoader implements RedisAwareInterface
{
    use RedisAwareTrait;

    /**
     * Redis key namespace
     *
     * @var string
     */
    protected $cacheNamespace;

    /**
     * Flag whether or not the result is allowed to be cached
     *
     * @var boolean
     */
    protected $cacheable = true;

    /**
     * Sets a flag whether or not the content should be cached
     *
     * @param boolean $toggle
     */
    public function setCacheable($toggle)
    {
        // If statement is here because can't figure out how to typehint a boolean...
        if ($toggle === true || $toggle === false) {
            $this->cacheable = $toggle;
        }
    }

    /**
     * Returns cacheable property
     *
     * @return boolean
     */
    public function getCacheable()
    {
        return $this->cacheable;
    }

    /**
     * Sets the Cache namespace key for Redis
     *
     * @param string $string
     */
    public function setCacheNamespace($string)
    {
        $this->cacheNamespace = $string;
    }

    /**
     * Gets the Cache namespace key for Redis
     *
     * @param string $string
     */
    public function getCacheNamespace()
    {
        return $this->cacheNamespace;
    }

    /**
     * Checks for a key within Redis and returns it's existance
     *
     * @param  string  $key
     * @return boolean
     */
    public function checkRedis($key)
    {
        return $this->getRedisDriver()->exists($key);
    }

    /**
     * Retrieves a key from within Redis
     *
     * @param  string $key
     * @return string JSON decoded value
     */
    public function getFromRedis($key)
    {
        return json_decode($this->getRedisDriver()->get($key));
    }

    /**
     * Sets JSON encoded value within Redis with an expiry in seconds
     *
     * @param string  $key
     * @param mixed   $value   Data to encode into JSON and store
     * @param integer $expires Cache expiry time in seconds
     */
    public function setExpireKey($key, $value, $expires = 3600)
    {
        return $this->getRedisDriver()->setEx($key, $expires, $value);
    }

    /**
     * Caches the data gathered and then returns again to the source script
     *
     * @param  mixed  $data Data to store
     * @param  string $key  Redis key to be used to store the data
     *
     * @return mixed  $data
     */
    public function cacheAndReturn($data, $key)
    {
        // Encode here so that the numbers can be converted to ints
        $data = json_encode($data, JSON_NUMERIC_CHECK);

        // Only cache if we're allowed to cache it
        if ($this->getCacheable() === true) {
            $this->setExpireKey($key, $data);
        }

        // Decode again so it can be used again in the app.
        // This may seem like a performance overhead, but it ensures consistency.
        // The function is only going to be fired when cache is missing.
        return json_decode($data);
    }
}
