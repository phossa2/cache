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

use Psr\Cache\CacheItemInterface;

/**
 * DriverInterface
 *
 * Cache driver interface
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface DriverInterface
{
    /**
     * Returns the meta data of the key.
     *
     * If not found, returns empty []
     *
     * @param  string $key the key
     * @return array
     * @access public
     * @api
     */
    public function has(/*# string */ $key)/*# : array */;

    /**
     * Get data from storage base on the key
     *
     * ALWAYS CALL call has() before get() !!!
     *
     * @param  string $key the key
     * @return string|null
     * @access public
     * @api
     */
    public function get(/*# string */ $key);

    /**
     * Save item to the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function save(CacheItemInterface $item)/*# : bool */;

    /**
     * Save item (deferred) to the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function saveDeferred(CacheItemInterface $item)/*# : bool */;

    /**
     * Delete item from the pool. return false on error
     *
     * @param  CacheItemInterface $item
     * @return bool
     * @access public
     * @api
     */
    public function delete(CacheItemInterface $item)/*# : bool */;

    /**
     * Commit deferred to the pool. return false on error.
     *
     * @return bool
     * @access public
     * @api
     */
    public function commit()/*# : bool */;

    /**
     * Clear the entire cache pool. return false on error
     *
     * @return bool
     * @access public
     * @api
     */
    public function clear()/*# : bool */;

    /**
     * Purge stale items in the pool
     *
     * @return bool
     * @access public
     * @api
     */
    public function purage()/*# : bool */;

    /**
     * Ping driver, FALSE means driver failed
     *
     * @return bool
     * @access public
     * @api
     */
    public function ping()/*# : bool */;
}
