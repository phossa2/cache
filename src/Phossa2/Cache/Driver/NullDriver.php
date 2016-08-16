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
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Cache\Interfaces\DriverInterface;

/**
 * NullDriver
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     DriverInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class NullDriver extends ObjectAbstract implements DriverInterface
{
    /**
     * {inheritDoc}
     */
    public function has(/*# string */ $key)/*# : array */
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key)
    {
        return null;
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
    public function delete(CacheItemInterface $item)/*# : bool */
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
    public function clear()/*# : bool */
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
