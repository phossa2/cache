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

namespace Phossa2\Cache\Driver;

use Psr\Cache\CacheItemInterface;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Shared\Error\ErrorAwareTrait;
use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Shared\Error\ErrorAwareInterface;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * DriverAbstract
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     DriverInterface
 * @see     ErrorAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
abstract class DriverAbstract extends ObjectAbstract implements DriverInterface, ErrorAwareInterface
{
    use ErrorAwareTrait;

    /**
     * number of items to defer, 0 means no defer
     *
     * @var    int
     * @access protected
     */
    protected $defer_count = 0;

    /**
     * defer pool
     *
     * @var    CacheItemExtendedInterface[]
     * @access protected
     */
    protected $defer = [];

    /**
     * @param  array $properties
     * @access public
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $key)/*# : array */
    {
        $meta = $this->isDeferred($key);
        if (!empty($meta)) {
            return $meta;
        } else {
            return $this->driverHas($key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)
    {
        $meta = $this->isDeferred($key);
        if (!empty($meta)) {
            return $this->getFromDeferred($key);
        } else {
            return $this->driverGet($key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        if ($this->defer_count) {
            if ($this->flushDeferred()) {
                $this->defer[$item->getKey()] = $item;
                return true;
            }
            return false;
        } else {
            return $this->save($item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(CacheItemInterface $item)/*# : bool */
    {
        $meta = $this->isDeferred($item->getKey());
        if (!empty($meta)) {
            unset($this->defer[$item->getKey()]);
        }
        return $this->driverDelete($item);
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        foreach ($this->defer as $item) {
            if (!$this->save($item)) {
                return false;
            }
        }
        $this->defer = [];
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        $this->defer = [];
        return $this->driverClear();
    }

    /**
     * {@inheritDoc}
     */
    public function purage()/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function ping()/*# : bool */
    {
        return true;
    }

    /**
     * Flush deferred items
     *
     * @access protected
     */
    protected function flushDeferred()
    {
        if (count($this->defer) > $this->defer_count) {
            return $this->commit();
        }
        return true;
    }

    /**
     * In deferred array ?
     *
     * @param  string $key
     * @return array
     * @access protected
     */
    protected function isDeferred(/*# string */ $key)/*# : array */
    {
        $res = [];
        if (isset($this->defer[$key])) {
            $res['expire'] = $this->defer[$key]->getExpiration()->getTimestamp();
        }
        return $res;
    }

    /**
     * Get value from deferred array
     * @param  string $key
     * @return string
     * @access protected
     */
    protected function getFromDeferred(/*# string */ $key)/*# : string */
    {
        return (string) $this->defer[$key];
    }

    /**
     * Driver specific has()
     *
     * @param  string $key
     * @return array
     * @access protected
     */
    abstract protected function driverHas(/*# string */ $key)/*# : array */;

    /**
     * Driver specific get()
     *
     * @param  string $key
     * @return string|null
     * @access protected
     */
    abstract protected function driverGet(/*# string */ $key);

    /**
     * Driver specific delete()
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access protected
     */
    abstract protected function driverDelete(CacheItemInterface $item)/*# : bool */;

    /**
     * Driver specific clear()
     *
     * @return bool
     * @access protected
     */
    abstract protected function driverClear()/*# : bool */;
}
