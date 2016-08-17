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

use Phossa2\Shared\Extension\ExtensionAbstract;
use Phossa2\Event\Interfaces\ListenerInterface;

/**
 * CacheExtensionAbstract
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @see     ListenerInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
abstract class CacheExtensionAbstract extends ExtensionAbstract
{
    /**
     * Constructor
     *
     * @param  array $properties
     * @access public
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * Register events with cache pool
     *
     * {@inheritDoc}
     */
    protected function bootExtension()
    {
        /* @var ListenerInterface $pool */;
        $pool = $this->server;

        foreach ($this->cacheEvents() as $evt) {
            $pool->registerEvent($evt['event'], $evt['handler']);
        }
    }

    /**
     * Return event info of this extension handling
     *
     * @return array
     * @access protected
     */
    abstract protected function cacheEvents()/*# : array */;
}
