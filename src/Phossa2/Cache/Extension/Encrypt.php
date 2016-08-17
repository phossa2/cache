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

namespace Phossa2\Cache\Extension;

use Phossa2\Cache\CachePool;
use Phossa2\Event\Interfaces\EventInterface;

/**
 * Encrypt
 *
 * Encrypt the seralized value, decrypt it after get.
 *
 * MUST USE WITH the `Serialize` extension !!
 *
 *
 * ```php
 * $cachePool->addExtension(new Serialize())
 *           ->addExtension(new Encrypt());
 * ```
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Encrypt extends CacheExtensionAbstract
{
    /**
     * @var    callable
     * @access protected
     */
    protected $encrypt = 'base64_encode';

    /**
     * @var    callable
     * @access protected
     */
    protected $decrypt = 'base64_decode';

    /**
     * Set ecrypt/decrypt function/callable
     *
     * @param  callable $encrypt
     * @param  callable $decrypt
     * @access public
     */
    public function __construct(
        callable $encrypt = null,
        callable $decrypt = null
    ) {
        if (is_callable($encrypt)) {
            $this->encrypt = $encrypt;
        }
        if (is_callable($decrypt)) {
            $this->decrypt = $decrypt;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['doEncrypt', 'doDecrypt'];
    }

    /**
     * {@inheritDoc}
     */
    protected function cacheEvents()/*# : array */
    {
        // encrypt after serialize, and decrypt before unserlize
        return [
            ['event' => CachePool::EVENT_SAVE_BEFORE, 'handler' => ['doEncrypt', 40]],
            ['event' => CachePool::EVENT_DEFER_BEFORE, 'handler' => ['doEncrypt', 40]],
            ['event' => CachePool::EVENT_GET_AFTER, 'handler' => ['doDecrypt', 60]],
        ];
    }

    /**
     * Encrypt data
     *
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function doEncrypt(EventInterface $event)/*# : bool */
    {
        /* @var CacheItem $item */
        $item = $event->getParam('item');
        $func = $this->encrypt;
        $item->setStrVal($func($item->getStrVal()));
        return true;
    }

    /**
     * Decrypt data
     *
     * @param  EventInterface $event
     * @return bool
     * @access public
     */
    public function doDecrypt(EventInterface $event)/*# : bool */
    {
        /* @var CacheItem $item */
        $item = $event->getParam('item');
        $func = $this->decrypt;
        $item->setStrVal($func($item->getStrVal()));
        return true;
    }
}
