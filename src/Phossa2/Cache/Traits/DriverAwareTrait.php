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

namespace Phossa2\Cache\Traits;

use Phossa2\Cache\Driver\NullDriver;
use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Cache\Interfaces\DriverAwareInterface;
use Phossa2\Cache\Interfaces\FallbackAwareInterface;

/**
 * DriverAwareTrait
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     DriverAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait DriverAwareTrait
{
    /**
     * Cache driver
     *
     * @var    DriverInterface
     * @access protected
     */
    protected $driver;

    /**
     * {@inheritDoc}
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDriver()/*# : DriverInterface */
    {
        // not found
        if (is_null($this->driver)) {
            return new NullDriver();
        }

        // driver ok
        if ($this->driver->ping()) {
            return $this->driver;
        }

        // use fallback
        return $this->tryFallback();
    }

    /**
     * Try fallback driver. if fail, use NullDriver
     *
     * @return DriverInterface
     * @access protected
     */
    protected function tryFallback()/*# : DriverInterface */
    {
        if ($this instanceof FallbackAwareInterface && $this->hasFallback()) {
            return $this->getFallback();
        } else {
            return new NullDriver();
        }
    }
}
