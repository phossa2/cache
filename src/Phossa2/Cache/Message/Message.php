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
     * Invalide cache key "%s"
     */
    const CACHE_INVALID_KEY = 1608121019;

    /*
     * Bypass cache
     */
    const CACHE_EXT_BYPASS = 1608121020;

    /*
     * Stampede protection for "%s"
     */
    const CACHE_EXT_STAMPEDE = 1608121021;

    /**
     * {@inheritDoc}
     */
    protected static $messages = [
        self::CACHE_INVALID_KEY => 'Invalide cache key "%s"',
        self::CACHE_EXT_BYPASS => 'Bypass cache',
        self::CACHE_EXT_STAMPEDE => 'Stampede protection for "%s"',
    ];
}
