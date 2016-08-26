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

use Phossa2\Shared\Extension\ExtensionAbstract;
use Phossa2\Shared\Extension\ExtensionInterface;
use Phossa2\Cache\Interfaces\UtilityAwareInterface;

/**
 * UtilityAwareTrait
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     UtilityAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait UtilityAwareTrait
{
    /**
     * Add utility
     *
     * @param  ExtensionAbstract $util
     * @access public
     * @api
     */
    public function addUtility(ExtensionAbstract $util)
    {
        return $this->addExtension($util);
    }

    /**
     * from Phossa2\Shared\Extension\ExtensionAwareInterface
     */
    abstract public function addExtension(
        ExtensionInterface $ext,
        /*# bool */ $forceOverride = false
    );
}
