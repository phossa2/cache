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

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Shared\Error\ErrorAwareTrait;
use Phossa2\Cache\Traits\DriverAwareTrait;
use Phossa2\Shared\Error\ErrorAwareInterface;
use Phossa2\Cache\Traits\CacheItemAwareTrait;
use Phossa2\Cache\Interfaces\DriverAwareInterface;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * CachePool
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     CacheItemPoolInterface
 * @see     DriverAwareInterface
 * @see     ErrorAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CachePool extends ObjectAbstract implements CacheItemPoolInterface, DriverAwareInterface, ErrorAwareInterface
{
    use DriverAwareTrait, CacheItemAwareTrait, ErrorAwareTrait;

    const EVENT_BEFORE_CLEARPOOL = 'cache.before.clearpool';
    const EVENT_AFTER_CLEARPOOL = 'cache.after.clearpool';
    const EVENT_BEFORE_DELETE = 'cache.before.delete';
    const EVENT_AFTER_DELETE = 'cache.after.delete';

    /**
     * {@inheritDoc}
     */
    public function getItem($key)
    {
        return $this->getCacheItem($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(array $keys = array())
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->getItem($key);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hasItem($key)
    {
        return $this->getCacheItem($key)->isHit();
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        if (!$this->getDriver()->clear()) {
            $this->copyError($this->getDriver());
            return false;
        } else {
            return $this->flushError();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItem($key)
    {
        if ($this->getCacheItem($key)->delete()) {
            return $this->flushError();
        } else {
            $this->copyError($this->getDriver());
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
                return false;
            }
        }
        return $this->flushError();
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)
    {
        if ($item instanceof CacheItemExtendedInterface) {
            $res = $item->save();
            $this->copyError($this->getDriver());
            return $res;

        } elseif ($this->getDriver()->save($item)) {
            return $this->flushError();

        } else {
            $this->copyError($this->getDriver());
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if ($item instanceof CacheItemExtendedInterface) {
            $res = $item->saveDeferred();
            $this->copyError($this->getDriver());
            return $res;

        } elseif ($this->getDriver()->saveDeferred($item)) {
            return $this->flushError();

        } else {
            $this->copyError($this->getDriver());
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        if (!$this->getDriver()->commit()) {
            $this->copyError($this->getDriver());
            return false;
        } else {
            return $this->flushError();
        }
    }
}
