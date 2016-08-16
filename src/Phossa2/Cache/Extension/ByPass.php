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
use Phossa2\Cache\Message\Message;
use Phossa2\Event\Interfaces\EventInterface;

/**
 * ByPass extension
 *
 * Bypass the cache if URL has 'nocache=true' set
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     CacheExtensionAbstract
 * @see     CachePool
 * @version 2.0.0
 * @since   2.0.0 added
 */
class ByPass extends CacheExtensionAbstract
{
    /**
     * bypass trigger in the url
     *
     * @var    string
     * @access protected
     */
    protected $trigger = 'nocache';

    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['byPassCache'];
    }

    /**
     * {@inheritDoc}
     */
    protected function cacheEvents()/*# : array */
    {
        return [
            ['event' => 'cache.*', 'handler' => ['byPassCache', 100]]
        ];
    }

    /**
     * Skip the cache
     *
     * 1. $this->trigger = '', always bypass the cache
     * 2. if sees $this->trigger in $_REQUEST, bypass the cache
     *
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function byPassCache(EventInterface $event)/*# : bool */
    {
        /* @var CachePool $pool */
        $pool = $event->getTarget();

        if ($this->trigger === '' || isset($_REQUEST[$this->trigger])) {
            return $pool->setError(
                Message::get(Message::CACHE_EXT_BYPASS),
                Message::CACHE_EXT_BYPASS
            );
        } else {
            return true;
        }
    }
}
