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

namespace Phossa2\Cache\Extension;

use Phossa2\Cache\CacheItem;
use Phossa2\Cache\CachePool;
use Phossa2\Event\Interfaces\EventInterface;
use Phossa2\Event\EventableExtensionAbstract;

/**
 * Serialize
 *
 * Serialize the value before save, and unserlize after get
 *
 * ```php
 * $cachePool->addExtension(new Serialize());
 * ```
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionAbstract
 * @see     CacheItem
 * @see     CachePool
 * @version 2.0.1
 * @since   2.0.0 added
 * @since   2.0.1 moved to EventableExtensionAbstract
 */
class Serialize extends EventableExtensionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['doSerialize', 'doUnserialize'];
    }

    /**
     * {@inheritDoc}
     */
    protected function extensionHandles()/*# : array */
    {
        return [
            ['event' => CachePool::EVENT_SAVE_BEFORE, 'handler' => ['doSerialize', 50]],
            ['event' => CachePool::EVENT_DEFER_BEFORE, 'handler' => ['doSerialize', 50]],
            ['event' => CachePool::EVENT_GET_AFTER, 'handler' => ['doUnserialize', 50]],
        ];
    }

    /**
     * Serialize data
     *
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function doSerialize(EventInterface $event)/*# : bool */
    {
        /* @var CacheItem $item */
        $item = $event->getParam('item');
        $item->setStrVal(serialize($item->get()));
        return true;
    }

    /**
     * Unserialize data
     *
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function doUnserialize(EventInterface $event)/*# : bool */
    {
        /* @var CacheItem $item */
        $item = $event->getParam('item');
        $val = @unserialize($item->getStrVal());
        if (false !== $val) {
            $item->set($val);
        }
        return true;
    }
}
