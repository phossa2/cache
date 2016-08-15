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

use Phossa2\Cache\CacheItem;
use Phossa2\Cache\CachePool;
use Phossa2\Event\Interfaces\EventInterface;
use Phossa2\Cache\Extension\CacheExtensionAbstract;

/**
 * Serialize
 *
 * Serialize/unserialize the value
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     CacheExtensionAbstract
 * @see     CacheItem
 * @see     CachePool
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Serialize extends CacheExtensionAbstract
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
    protected function cacheEvents()/*# : array */
    {
        return [
            ['event' => 'cache.save.before', 'handler' => ['doSerialize', 50]],
            ['event' => 'cache.defer.before', 'handler' => ['doSerialize', 50]],
            ['event' => 'cache.get.after', 'handler' => ['doUnserialize', 50]],
        ];
    }

    /**
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function doSerialize(EventInterface $event)/*# : bool */
    {
        /* @var CachePool $pool */
        $pool = $event->getTarget();

        /* @var CacheItem */
        $item = $event->getParam('item');
    }
}
