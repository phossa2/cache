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
 * CacheItemExtendedInterface
 *
 * Added some MISSING stuff to CacheItemInterface !
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     CacheItemInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface CacheItemExtendedInterface extends CacheItemInterface
{
    /**
     * Returns the expiration time of a not-yet-expired cache item.
     *
     * If this cache item is a Cache Miss, this method MAY return the time at
     * which the item expired or the current time if that is not available.
     *
     * @return \DateTime The timestamp at which this cache item will expire.
     */
    public function getExpiration()/*# : \DateTime */;
}
