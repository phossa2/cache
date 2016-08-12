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

namespace Phossa2\Cache\Exception;

use Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * InvalidArgumentException for Phossa2\Cache
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExceptionInterface
 * @see     \InvalidArgumentException
 * @see     \Psr\Cache\InvalidArgumentException
 * @version 2.0.0
 * @since   2.0.0 added
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface, PsrInvalidArgumentException
{
}
