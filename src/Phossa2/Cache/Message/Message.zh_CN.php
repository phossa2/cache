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

use Phossa2\Cache\Message\Message;

/*
 * Provide zh_CN translation
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
return [
    Message::CACHE_INVALID_KEY => '缓存钥匙  "%s" 格式错误',
    Message::CACHE_EXT_BYPASS => '跳过缓存',
    Message::CACHE_EXT_STAMPEDE => '缓存预过期保护 "%s"',
];
