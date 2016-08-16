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
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Shared\Error\ErrorAwareTrait;
use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Shared\Error\ErrorAwareInterface;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * StorageDriver
 *
 * Driver using Phossa2\Storage library
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     DriverInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class StorageDriver extends ObjectAbstract implements DriverInterface, ErrorAwareInterface
{
    use ErrorAwareTrait;

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
     * @access public
     */
    public function __construct(Storage $storage, /*# string */ $cacheDir)
    {
        $this->storage = $storage;
        $this->cache_dir = rtrim($cacheDir, '/');
    }

    /**
     * Use mtime as expiration time
     *
     * {@inheritDoc}
     */
    public function has(/*# string */ $key)/*# : array */
    {
        $path = $this->getPath($key);

        // not found
        if (!$this->storage->has($path)) {
            return [];
        }

        // translate 'mtime' to 'expire'
        $meta = $this->storage->meta($path);
        if (isset($meta['mtime'])) {
            $meta['expire'] = $meta['mtime'];
        }

        return $meta;
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)
    {
        $res = $this->storage->get($this->getPath($key));
        $this->resetError();
        return is_string($res) ? $res : null;
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
                $this->getPath($key), (string) $item, ['mtime' => $exp]
            );
            return $res ?: $this->resetError();
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        return $this->save($item);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(CacheItemInterface $item)/*# : bool */
    {
        $key = $item->getKey();
        $res = $this->storage->del($this->getPath($key));
        return $res ?: $this->resetError();
    }

    /**
     * {@inheritDoc}
     */
    public function commit()/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        $res = $this->storage->del($this->cache_dir);
        return $res ?: $this->resetError();
    }

    /**
     * {@inheritDoc}
     */
    public function ping()/*# : bool */
    {
        return true;
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
