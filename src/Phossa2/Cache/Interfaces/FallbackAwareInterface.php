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
 * FallbackAwareInterface
 *
 * Aware of fallback cache driver
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface FallbackAwareInterface
{
    /**
     * Returns true if fallback driver is set and responding to ping()
     *
     * @return bool
     * @access public
     * @api
     */
    public function hasFallback()/*# : bool */;

    /**
     * Get the fallback driver.
     *
     * Always run `hasFallback()` before `getFallback()`
     *
     * @return DriverInterface
     * @access public
     * @api
     */
    public function getFallback()/*# : DriverInterface */;

    /**
     * Set the fallback driver
     *
     * @param  DriverInterface $driver the fallback driver
     * @return $this
     * @access public
     * @api
     */
    public function setFallback(DriverInterface $driver);
}
