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

use Phossa2\Cache\Interfaces\DriverInterface;
use Phossa2\Cache\Interfaces\FallbackAwareInterface;

/**
 * FallbackAwareTrait
 *
 * @package Phossa2\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     FallbackAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait FallbackAwareTrait
{
    /**
     * Fallback cache driver (normally slower)
     *
     * @var    DriverInterface
     * @access protected
     */
    protected $fallback;

    /**
     * {@inheritDoc}
     */
    public function hasFallback()/*# : bool */
    {
        if (!is_null($this->fallback) && $this->fallback->ping()) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getFallback()/*# : DriverInterface */
    {
        return $this->fallback;
    }

    /**
     * {@inheritDoc}
     */
    public function setFallback(DriverInterface $driver)
    {
        $this->fallback = $driver;
        return $this;
    }
}
