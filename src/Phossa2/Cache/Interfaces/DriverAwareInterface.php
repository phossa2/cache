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

namespace Phossa2\Cache\Interfaces;

/**
 * DriverAwareInterface
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface DriverAwareInterface
{
    /**
     * Set the cache driver.
     *
     * @param  DriverInterface $driver the cache driver object
     * @return $this
     * @access public
     * @api
     */
    public function setDriver(DriverInterface $driver);

    /**
     * Get cache driver. Always setDriver() first
     *
     * @return DriverInterface
     * @access public
     * @api
     */
    public function getDriver()/*# : DriverInterface */;
}
