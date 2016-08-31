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

use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Phossa2\Storage\Driver\LocalDriver;
use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Shared\Error\ErrorAwareTrait;
use Phossa2\Cache\Traits\DriverAwareTrait;
use Phossa2\Cache\Traits\UtilityAwareTrait;
use Phossa2\Cache\Traits\FallbackAwareTrait;
use Phossa2\Shared\Error\ErrorAwareInterface;
use Phossa2\Cache\Traits\CacheItemAwareTrait;
use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Cache\Interfaces\DriverAwareInterface;
use Phossa2\Cache\Interfaces\UtilityAwareInterface;
use Phossa2\Cache\Interfaces\FallbackAwareInterface;
use Phossa2\Event\EventableExtensionCapableAbstract;

/**
 * CachePool
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionCapableAbstrac
 * @see     CacheItemPoolInterface
 * @see     DriverAwareInterface
 * @see     ErrorAwareInterface
 * @version 2.0.1
 * @since   2.0.0 added
 * @since   2.0.1 updated __construct()
 *                moved to EventableExtensionCapableAbstrac
 */
class CachePool extends EventableExtensionCapableAbstract implements CacheItemPoolInterface, DriverAwareInterface, FallbackAwareInterface, UtilityAwareInterface, ErrorAwareInterface
{
    use DriverAwareTrait, CacheItemAwareTrait, UtilityAwareTrait, FallbackAwareTrait, ErrorAwareTrait;

    /**
     * event names
     * @var    string
     */
    const EVENT_HAS_BEFORE = 'cache.has.before';
    const EVENT_HAS_AFTER = 'cache.has.after';
    const EVENT_GET_BEFORE = 'cache.get.before';
    const EVENT_GET_AFTER = 'cache.get.after';
    const EVENT_SET_BEFORE = 'cache.set.before';
    const EVENT_SET_AFTER = 'cache.set.after';
    const EVENT_CLEAR_BEFORE = 'cache.clear.before';
    const EVENT_CLEAR_AFTER = 'cache.clear.after';
    const EVENT_DELETE_BEFORE = 'cache.delete.before';
    const EVENT_DELETE_AFTER = 'cache.delete.after';
    const EVENT_SAVE_BEFORE = 'cache.save.before';
    const EVENT_SAVE_AFTER = 'cache.save.after';
    const EVENT_DEFER_BEFORE = 'cache.defer.before';
    const EVENT_DEFER_AFTER = 'cache.defer.after';
    const EVENT_COMMIT_BEFORE = 'cache.commit.before';
    const EVENT_COMMIT_AFTER = 'cache.commit.after';

    /**
     * Constructor
     *
     * @param  DriverInterface $driver
     * @access public
     * @since  2.0.1 removed eventManager param
     */
    public function __construct(DriverInterface $driver = null)
    {
        // use default driver
        if (is_null($driver)) {
            $driver = new StorageDriver(
                new Storage('/', new Filesystem(new LocalDriver(sys_get_temp_dir()))),
                '/cache'
            );
        }
        $this->setDriver($driver);
    }

    /**
     * Destructor
     *
     * @access public
     */
    public function __destruct()
    {
        $this->commit();
    }

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
        return $this->eventableAction(
            'clear',
            self::EVENT_CLEAR_BEFORE,
            self::EVENT_CLEAR_AFTER
        );
    }

    /**
     * {@inheritDoc}
     */
    public function deleteItem($key)
    {
        $item = $this->getItem($key);
        return $this->eventableAction(
            'delete',
            self::EVENT_DELETE_BEFORE,
            self::EVENT_DELETE_AFTER,
            $item
        );
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
        return $this->eventableAction(
            'save',
            self::EVENT_SAVE_BEFORE,
            self::EVENT_SAVE_AFTER,
            $item
        );
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->eventableAction(
            'saveDeferred',
            self::EVENT_DEFER_BEFORE,
            self::EVENT_DEFER_AFTER,
            $item
        );
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        return $this->eventableAction(
            'commit',
            self::EVENT_COMMIT_BEFORE,
            self::EVENT_COMMIT_AFTER
        );
    }

    /**
     * Execute an action
     *
     * @param  string $action
     * @param  string $beforeEvent
     * @param  string $afterEvent
     * @param  CacheItemInterface $item
     * @return bool
     * @access protected
     */
    protected function eventableAction(
        /*# string */ $action,
        /*# string */ $beforeEvent,
        /*# string */ $afterEvent,
        CacheItemInterface $item = null
    )/*# : bool */ {
        $param = ['item' => $item];
        if (!$this->trigger($beforeEvent, $param)) {
            return false;
        }
        if (!$this->driverAction($action, $item)) {
            $this->copyError($this->getDriver());
            return false;
        }
        if (!$this->trigger($afterEvent, $param)) {
            return false;
        }
        return $this->flushError();
    }

    /**
     * Execute an action with the driver
     *
     * @param  string $action
     * @param  CacheItemInterface|null $item
     * @access protected
     */
    protected function driverAction(/*# string */ $action, $item)/*# : bool */
    {
        if (is_null($item)) {
            return $this->getDriver()->{$action}();
        } else {
            return $this->getDriver()->{$action}($item);
        }
    }
}
