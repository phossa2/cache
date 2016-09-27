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
     * Get the expiration
     *
     * @return \DateTime
     * @access public
     */
    public function getExpiration()/*# : \DateTime */;

    /**
     * Set stringized value
     *
     * @param  string|null $strval
     * @return $this
     * @access public
     */
    public function setStrVal($strval);

    /**
     * Get stringed value
     *
     * @return string|null
     * @access public
     */
    public function getStrVal();

    /**
     * Returns the stringized item value
     *
     * @return string
     * @access public
     */
    public function __toString()/*# : string */;
}
