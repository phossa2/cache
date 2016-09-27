<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa2\Cache
 * @copyright Copyright (c) 2016 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa2\Cache;

use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * CacheItem
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     CacheItemExtendedInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CacheItem extends ObjectAbstract implements CacheItemExtendedInterface
{
    /**
     * cache pool
     *
     * @var    CachePool
     * @access protected
     */
    protected $pool;

    /**
     * item key
     *
     * @var    string
     * @access protected
     */
    protected $key;

    /**
     * flag for cache hit
     *
     * @var    bool
     * @access protected
     */
    protected $hit;

    /**
     * flag for got value already
     *
     * @var    bool
     * @access protected
     */
    protected $got = false;

    /**
     * Item exact value
     *
     * @var    mixed
     * @access protected
     */
    protected $value;

    /**
     * Processed item value
     *
     * @var    string
     * @access protected
     */
    protected $strval;

    /**
     * default expiration timestamp in seconds
     *
     * - 0 for no expiration
     *
     * - max value is 0x7fffffff
     *
     * @var    int
     * @access protected
     */
    protected $expire = 0;

    /**
     * default TTL in seconds
     *
     * @var    int
     * @access protected
     */
    protected $ttl = 28800;

    /**
     * Constructor
     *
     * @param  string $key
     * @param  CachePool $pool
     * @param  array $properties optional properties
     * @access public
     */
    public function __construct(
        /*# string */ $key,
        CachePool $pool,
        array $properties = []
    ) {
        $this->key = $key;
        $this->pool = $pool;
        $this->setProperties($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        if ($this->got) {
            return $this->value;
        } else {
            $this->got = true;
            if (!$this->isHit()) {
                return null;
            }
            return $this->getValue();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isHit()
    {
        if (is_bool($this->hit)) {
            return $this->hit;
        } else {
            return $this->hasHit();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($value)
    {
        if ($this->trigger(CachePool::EVENT_SET_BEFORE)) {
            $this->got = true;
            $this->value = $value;
            $this->strval = null;
            $this->trigger(CachePool::EVENT_SET_AFTER);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof \DateTime) {
            $this->expire = $expiration->getTimestamp();
        } elseif (is_int($expiration)) {
            $this->expire = $expiration;
        } else {
            $this->expire = time() + $this->ttl;
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function expiresAfter($time)
    {
        if ($time instanceof \DateInterval) {
            $time = $time->format("%s");
        } elseif (!is_numeric($time)) {
            $time = $this->ttl;
        } else {
            $time = (int) $time;
        }
        $this->expire = time() + $time;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiration()/*# : \DateTime */
    {
        // set default expiration if not yet
        if ($this->expire === 0) {
            $this->expiresAt(null);
        }

        return new \DateTime('@' . $this->expire);
    }

    /**
     * {@inheritDoc}
     */
    public function setStrVal($strval)
    {
        $this->strval = $strval;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getStrVal()
    {
        return $this->strval;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()/*# : string */
    {
        if (is_string($this->strval)) {
            return $this->strval;
        } elseif (is_string($this->value)) {
            return $this->value;
        } else {
            return serialize($this->value);
        }
    }

    /**
     * Set hit status
     *
     * @param  bool $hit
     * @return bool current hit status
     * @access protected
     */
    protected function setHit(/*# bool */ $hit)/*# : bool */
    {
        $this->hit = (bool) $hit;
        return $this->hit;
    }

    /**
     * Get value from the cache pool
     *
     * @return mixed
     * @access protected
     */
    protected function getValue()
    {
        // before get
        if (!$this->trigger(CachePool::EVENT_GET_BEFORE)) {
            $this->setHit(false);
            return null;
        }

        // get string value from the pool
        $this->strval = $this->pool->getDriver()->get($this->key);

        // after get
        if (!$this->trigger(CachePool::EVENT_GET_AFTER)) {
            $this->setHit(false);
            $this->set(null);
        }

        return $this->postGetValue();
    }

    /**
     * Convert $this->strval to $this->value if not yet
     *
     * @return mixed
     * @access protected
     */
    protected function postGetValue()
    {
        if (is_null($this->value)) {
            $val = @unserialize($this->strval);
            $this->value = false === $val ? $this->strval : $val;
        }
        return $this->value;
    }

    /**
     * Get hit status from driver
     *
     * @return bool
     * @access protected
     */
    protected function hasHit()/*# : bool */
    {
        if (!$this->trigger(CachePool::EVENT_HAS_BEFORE)) {
            return $this->setHit(false);
        }

        $meta = $this->pool->getDriver()->has($this->key);
        if (isset($meta['expire'])) {
            $this->expire = $meta['expire'];
        } else {
            return $this->setHit(false);
        }

        if (!$this->trigger(CachePool::EVENT_HAS_AFTER)) {
            return $this->setHit(false);
        }

        return $this->setHit(true);
    }

    /**
     * Trigger cache pool event
     *
     * @param  string $eventName
     * @return bool
     * @access protected
     */
    protected function trigger(/*# string */ $eventName)/*# : bool */
    {
        return $this->pool->trigger($eventName, ['item' => $this]);
    }
}
