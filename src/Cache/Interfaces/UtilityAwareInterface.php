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

use Phossa2\Shared\Extension\ExtensionAbstract;

/**
 * UtilityAwareInterface
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface UtilityAwareInterface
{
    /**
     * Add utility
     *
     * @param  ExtensionAbstract $util
     * @return $this
     * @access public
     * @api
     */
    public function addUtility(ExtensionAbstract $util);
}
