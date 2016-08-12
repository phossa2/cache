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

use Psr\Cache\CacheItemPoolInterface;
use Phossa2\Event\EventCapableAbstract;
use Phossa2\Cache\Traits\DriverAwareTrait;
use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Cache\Interfaces\DriverAwareInterface;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * CacheItem
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventCapableAbstract
 * @see     CacheItemExtendedInterface
 * @see     DriverAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CacheItem extends EventCapableAbstract implements CacheItemExtendedInterface, DriverAwareInterface
{
    use DriverAwareTrait;

    /**
     * cache pool
     *
     * @var    CacheItemPoolInterface
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
     * Marker for cache hit
     *
     * @var    bool
     * @access protected
     */
    protected $hit;

    /**
     * marker for got value already
     *
     * @var    bool
     * @access protected
     */
    protected $got = false;

    /**
     * Item value
     *
     * @var    mixed
     * @access protected
     */
    protected $value;

    /**
     * default expiration time in seconds, max is 0x7fffffff
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
     * event names
     */
    const EVENT_BEFORE_HAS = 'cacheitem.before.has';
    const EVENT_AFTER_HAS = 'cacheitem.after.has';
    const EVENT_BEFORE_GET = 'cacheitem.before.get';
    const EVENT_AFTER_GET = 'cacheitem.after.get';
    const EVENT_BEFORE_DELETE = 'cacheitem.before.delete';
    const EVENT_AFTER_DELETE = 'cacheitem.after.delete';
    const EVENT_BEFORE_SAVE = 'cacheitem.before.save';
    const EVENT_AFTER_SAVE = 'cacheitem.after.save';
    const EVENT_BEFORE_DEFER = 'cacheitem.before.defer';
    const EVENT_AFTER_DEFER = 'cacheitem.after.defer';

    /**
     * Constructor
     *
     * @param  string $key
     * @param  DriverInterface $driver
     * @param  array $properties optional properties
     * @access public
     */
    public function __construct(
        /*# string */ $key,
        DriverInterface $driver,
        array $properties = []
    ) {
        $this->key = $key;
        $this->setDriver($driver);
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
        }

        $this->got = true;

        if (!$this->isHit()) {
            return null;
        }

        return $this->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function isHit()
    {
        if (is_bool($this->hit)) {
            return $this->hit;
        }

        if ($this->trigger(self::EVENT_BEFORE_HAS) &&
            $this->getDriver()->has($this->key) &&
            $this->trigger(self::EVENT_AFTER_HAS)
        ) {
            return $this->setHit(true);
        }

        return $this->setHit(false);
    }

    /**
     * {@inheritDoc}
     */
    public function set($value)
    {
        $this->got = true;
        $this->value = $value;
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
    public function delete()/*# : bool */
    {
        if (!$this->trigger(self::EVENT_BEFORE_DELETE)) {
            return false;
        }

        if (!$this->getDriver()->delete($this->key)) {
            return false;
        }

        $this->setHit(false);

        if (!$this->trigger(self::EVENT_AFTER_DELETE)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function save()/*# : bool */
    {
        if (!$this->trigger(self::EVENT_BEFORE_SAVE)) {
            return false;
        }

        if (!$this->getDriver()->save($this)) {
            return false;
        }

        if (!$this->trigger(self::EVENT_AFTER_SAVE)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred()/*# : bool */
    {
        if (!$this->trigger(self::EVENT_BEFORE_DEFER)) {
            return false;
        }

        if (!$this->getDriver()->saveDeferred($this)) {
            return false;
        }

        if (!$this->trigger(self::EVENT_AFTER_DEFER)) {
            return false;
        }

        return true;
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
        $param = ['key' => $this->key];
        if (!$this->trigger(self::EVENT_BEFORE_GET, $param)) {
            $this->setHit(false);
            return null;
        }

        // get the value from the pool
        $val = $this->getDriver()->get($this->key);

        // after get
        $param['value'] = $val;
        if (!$this->trigger(self::EVENT_AFTER_GET, $param)) {
            $this->setHit(false);
            return null;
        }

        $this->set($val);

        return $this->value;
    }
}
