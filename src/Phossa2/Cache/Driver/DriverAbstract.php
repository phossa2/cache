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

use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Shared\Error\ErrorAwareTrait;
use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Shared\Error\ErrorAwareInterface;

/**
 * DriverAbstract
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     DriverInterface
 * @see     ErrorAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
abstract class DriverAbstract extends ObjectAbstract implements DriverInterface, ErrorAwareInterface
{
    use ErrorAwareTrait;

    /**
     * Constructor
     *
     * @param  array $properties
     * @access public
     * @api
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }
}
