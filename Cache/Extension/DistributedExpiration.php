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

use Phossa2\Cache\CachePool;
use Phossa2\Event\Interfaces\EventInterface;
use Phossa2\Event\EventableExtensionAbstract;
use Phossa2\Cache\Interfaces\CacheItemExtendedInterface;

/**
 * DistributedExpiration
 *
 * Change expiration time by -5% to 5% to evenly distribute cache miss
 *
 * ```php
 * $ext = new DistributedExpiration([
 *     'distribution' => 30 // -3% to 3%
 * ]);
 *
 * // enable this ext
 * $cachePool->addExtension($ext);
 * ```
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionAbstract
 * @version 2.0.1
 * @since   2.0.0 added
 * @since   2.0.1 moved to EventableExtensionAbstract
 */
class DistributedExpiration extends EventableExtensionAbstract
{
    /**
     * item expiration time distribution 5% (50/1000)
     *
     * @var    int
     * @access protected
     */
    protected $distribution = 50;

    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['distributeExpire'];
    }

    /**
     * {@inheritDoc}
     */
    protected function extensionHandles()/*# : array */
    {
        // change item expire before save or saveDeferred
        return [
            [
                'event'   => CachePool::EVENT_SAVE_BEFORE,
                'handler' => ['distributeExpire', 80]
            ],
            [
                'event'   => CachePool::EVENT_DEFER_BEFORE,
                'handler' => ['distributeExpire', 80]
            ]
        ];
    }

    /**
     * Evenly distribute the expiration time
     *
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function distributeExpire(EventInterface $event)/*# : bool */
    {
        $dist = $this->distribution;
        $item = $event->getParam('item');

        if ($item instanceof CacheItemExtendedInterface) {
            // expire ttl
            $ttl = $item->getExpiration()->getTimestamp() - time();

            // percentage
            $percent = (rand(0, $dist * 2) - $dist) * 0.001;

            // new expire ttl
            $new_ttl = (int) round($ttl + $ttl * $percent);
            $item->expiresAfter($new_ttl);
        }

        return true;
    }
}
