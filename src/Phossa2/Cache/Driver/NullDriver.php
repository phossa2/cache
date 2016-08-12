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

/**
 * NullDriver
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     DriverAbstract
 * @version 2.0.0
 * @since   2.0.0 added
 */
class NullDriver extends DriverAbstract
{
    /**
     * {inheritDoc}
     */
    public function has(/*# string */ $key)/*# : bool */
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)/*# : string */
    {
        return serialize(null);
    }

    /**
     * {@inheritDoc}
     */
    public function save(CacheItemInterface $item)/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */
    {
        return true;
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
    public function delete(/*# string */ $key)/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()/*# : bool */
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function purge(/*# int */ $maxlife)/*# : bool */
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
}
