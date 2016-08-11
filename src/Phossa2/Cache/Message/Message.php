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

namespace Phossa2\Cache\Message;

use Phossa2\Shared\Message\Message as BaseMessage;

/**
 * Message class for Phossa2\Cache
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa2\Shared\Message\Message
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Message extends BaseMessage
{
    /*
     * Package const "%s"
     */
    const PACKAGE_CONST = 1607191058;

    /**
     * {@inheritDoc}
     */
    protected static $messages = [
        self::PACKAGE_CONST => 'Package const "%s"',
    ];
}
