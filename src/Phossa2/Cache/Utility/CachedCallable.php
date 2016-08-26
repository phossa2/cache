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

namespace Phossa2\Cache\Utility;

use Phossa2\Cache\CachePool;
use Phossa2\Cache\CacheItem;
use Phossa2\Shared\Extension\ExtensionAbstract;

/**
 * CachedCallable
 *
 * Cache result of a callable
 *
 * ```php
 * $cachePool->addUtility(new CachedCallable());
 *
 * // get result of callable [$object, 'method'] with parameters ...
 * // cache the result for 3600 sec.
 * $res = $cachePool->callableCache(3600, [$object, 'method'], $param1);
 * ```
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @see     CacheItem
 * @see     CachePool
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CachedCallable extends ExtensionAbstract
{
    /**
     * default ttl for the cached callable result
     *
     * @var    int
     * @access protected
     */
    protected $ttl = 86400;

    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['callableCache'];
    }

    /**
     * Complete cycle of get cached result of a callable
     *
     * 1. if first argument is int, it is a TTL
     * 2. otherwise the first argument is a callable
     * 3. the remaining are the arguments for the callable
     *
     * @return mixed
     * @throws \Exception except from callable execution
     * @access public
     * @api
     */
    public function callableCache()
    {
        // get method arguments
        $args = func_get_args();

        // get ttl and uniq key
        list($ttl, $key) = $this->getTtlAndKey($args);

        /* @var $cache CachePool */
        $cache = $this->server;

        /* @var $item CacheItem */
        $item = $cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            $func = array_shift($args);
            $val  = call_user_func_array($func, $args);
            $cache->save($item->set($val)->expiresAfter($ttl));
            return $val;
        }
    }

    /**
     * The real boot method
     *
     * @access protected
     */
    protected function bootExtension()
    {
    }

    /**
     * Get TTL and unique key
     *
     * @param  array &$args
     * @return array
     * @access protected
     */
    protected function getTtlAndKey(array &$args)/*# : array */
    {
        if (is_numeric($args[0])) {
            $ttl = (int) array_shift($args);
            $key = $this->generateKey($args);
        } else {
            $key = $this->generateKey($args);
            $ttl = $this->ttl;
        }
        return [$ttl, $key];
    }

    /**
     * Generate key base on input
     *
     * @param  mixed $reference reference data
     * @return string
     * @access protected
     */
    protected function generateKey($reference)/*# : string */
    {
        if (is_array($reference)) {
            $reference = $this->flatReference($reference);
        }
        $md5 = md5(serialize($reference));
        return sprintf("/%s/%s/%s", $md5[0], $md5[1], $md5);
    }

    /**
     * flat the reference array to make it easy for serialize
     *
     * @param  array $reference reference data
     * @return array flattered array
     * @access protected
     */
    protected function flatReference(array $reference)/*# : array */
    {
        reset($reference);
        foreach ($reference as $key => $value) {
            if (is_object($value)) {
                $reference[$key] = get_class($value);
            } elseif (is_array($value)) {
                $reference[$key] = $this->flatReference($value);
            }
        }
        ksort($reference);
        return $reference;
    }
}
