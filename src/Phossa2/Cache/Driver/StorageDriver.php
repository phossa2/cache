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

use Phossa2\Storage\Storage;
use Psr\Cache\CacheItemInterface;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * StorageDriver
 *
 * Driver using Phossa2\Storage library
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     DriverAbstract
 * @version 2.0.0
 * @since   2.0.0 added
 */
class StorageDriver extends DriverAbstract
{
    /**
     * Storage backend
     *
     * @var    Storage
     * @access protected
     */
    protected $storage;

    /**
     * Cache directory in storage
     *
     * @var    string
     * @access protected
     */
    protected $cache_dir;

    /**
     * Inject storage object
     *
     * @param  Storage $storage
     * @param  string $cacheDir
     * @param  array $properties
     * @access public
     */
    public function __construct(
        Storage $storage,
        /*# string */ $cacheDir = '/cache',
        array $properties = []
    ) {
        $this->storage = $storage;
        $this->cache_dir = rtrim($cacheDir, '/');
        parent::__construct($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)/*# : bool */
    {
        if ($item instanceof CacheItemExtendedInterface) {
            $key = $item->getKey();
            $exp = $item->getExpiration()->getTimestamp();

            $res = $this->storage->put(
                $this->getPath($key),
                (string) $item,
                ['mtime' => $exp]
            );
            return $res ?: $this->resetError();
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function driverHas(/*# string */ $key)/*# : array */
    {
        $path = $this->getPath($key);

        // not found
        if (!$this->storage->has($path)) {
            return [];
        }

        // translate 'mtime' to 'expire'
        $meta = $this->storage->meta($path);
        if (isset($meta['mtime'])) {
            $meta['expire'] = (int) $meta['mtime'];
        }

        return $meta;
    }

    /**
     * {@inheritDoc}
     */
    protected function driverGet(/*# string */ $key)
    {
        $res = $this->storage->get($this->getPath($key));
        $this->resetError();
        return is_string($res) ? $res : null;
    }

    /**
     * {@inheritDoc}
     */
    protected function driverDelete(CacheItemInterface $item)/*# : bool */
    {
        $key = $item->getKey();
        $res = $this->storage->del($this->getPath($key));
        return $res ?: $this->resetError();
    }

    /**
     * {@inheritDoc}
     */
    protected function driverClear()/*# : bool */
    {
        $res = $this->storage->del($this->cache_dir);
        return $res ?: $this->resetError();
    }

    /**
     * Generate full path in storage base on the given key
     *
     * @param  string $key
     * @access protected
     */
    protected function getPath(/*# string */ $key)/*# : string */
    {
        return $this->cache_dir . '/' . $key;
    }

    /**
     * Reset to storage error
     *
     * @return false
     * @access protected
     */
    protected function resetError()/*# : bool */
    {
        $this->copyError($this->storage);
        return false;
    }
}
