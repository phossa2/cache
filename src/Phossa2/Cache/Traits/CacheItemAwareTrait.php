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

namespace Phossa2\Cache\Traits;

use Phossa2\Cache\CacheItem;
use Psr\Cache\CacheItemInterface;
use Phossa2\Cache\Message\Message;
use Phossa2\Cache\Exception\InvalidArgumentException;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * CacheItemAwareTrait
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait CacheItemAwareTrait
{
    /**
     * item factory method, signatures as follows
     *
     *     function(
     *          string $key,
     *          CachePool $driver,
     *          array $properties = []
     *     ): CacheItemInterface
     *
     * @var    callable
     * @access protected
     */
    protected $item_factory;

    /**
     * Local cache of generated CacheItemInterface
     *
     * @var    CacheItemExtendedInterface[]
     * @access protected
     */
    protected $item_cache;

    /**
     * Use local cache for generated item
     *
     * @var    bool
     * @access protected
     */
    protected $use_item_cache = false;

    /**
     * Set cache item factory callable
     *
     * @param  callable $itemFactory
     * @return $this
     * @access public
     */
    public function setItemFactory(callable $itemFactory = null)
    {
        $this->item_factory = $itemFactory;
        return $this;
    }

    /**
     * Get a cache item
     *
     * @param  string $key
     * @return CacheItemExtendedInterface
     * @throws InvalidArgumentException if $key is invalid
     * @access protected
     */
    protected function getCacheItem(/*# string */ $key)/*# : CacheItemExtendedInterface */
    {
        // validate key first
        $this->validateKey($key);

        // try local cache
        if ($this->use_item_cache) {
            if (!isset($this->item_cache[$key[0]][$key])) {
                $this->item_cache[$key[0]][$key] = $this->createCacheItem($key);
            }
            return $this->item_cache[$key[0]][$key];
        } else {
            return $this->createCacheItem($key);
        }
    }

    /**
     * Create a cache item on the fly
     *
     * @param  string $key
     * @return CacheItemExtendedInterface
     * @access protected
     */
    protected function createCacheItem(/*# string */ $key)/*# : CacheItemExtendedInterface */
    {
        if (is_callable($this->item_factory)) {
            $func = $this->item_factory;
            $item = $func($key, $this);
        } else {
            $item = new CacheItem($key, $this);
        }

        return $item;
    }

    /**
     * Validate key string
     *
     * @param  string &$key key to check
     * @return void
     * @throws InvalidArgumentException
     * @access protected
     */
    protected function validateKey(/*# string */ &$key)
    {
        // validate key
        if (is_string($key)) {
            $key = trim($key);
            return;
        }

        // throw exception
        throw new InvalidArgumentException(
            Message::get(Message::CACHE_INVALID_KEY, $key),
            Message::CACHE_INVALID_KEY
        );
    }
}
