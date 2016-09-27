<?php

namespace Phossa2\Cache;

use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;

/**
 * CacheItem test case.
 */
class CacheItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var CacheItem
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $driver = new StorageDriver(
            new Storage('/', new Filesystem(new LocalDriver(sys_get_temp_dir()))),
            '/cache_test'
            );
        $pool = new CachePool($driver);

        $this->object = new CacheItem('test', $pool);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }

    /**
     * Tests CacheItem->getKey()
     *
     * @cover Phossa2\Cache\CacheItem::getKey()
     */
    public function testGetKey()
    {
        $this->assertEquals('test', $this->object->getKey());
    }

    /**
     * Tests CacheItem->get()
     *
     * @cover Phossa2\Cache\CacheItem::get()
     */
    public function testGet()
    {
        $this->assertEquals(null, $this->object->get());
    }

    /**
     * Tests CacheItem->isHit()
     *
     * @cover Phossa2\Cache\CacheItem::isHit()
     */
    public function testIsHit()
    {
        $this->assertFalse($this->object->isHit());
    }

    /**
     * Tests CacheItem->set()
     *
     * @cover Phossa2\Cache\CacheItem::set()
     */
    public function testSet()
    {
        $val = 3;
        $this->object->set($val);
        $this->assertEquals($val, $this->object->get());
    }

    /**
     * Tests CacheItem->expiresAt()
     *
     * @cover Phossa2\Cache\CacheItem::expiresAt()
     * @cover Phossa2\Cache\CacheItem::getExpiration()
     */
    public function testExpiresAt()
    {
        $time = time();
        $datetime = new \DateTime('@' . $time);

        // use DateTime
        $this->object->expiresAt($datetime);
        $this->assertEquals($time, $this->object->getExpiration()->getTimestamp());

        // use int
        $this->object->expiresAt($time);
        $this->assertEquals($time, $this->object->getExpiration()->getTimestamp());
    }

    /**
     * Tests CacheItem->expiresAfter()
     *
     * @cover Phossa2\Cache\CacheItem::expiresAfter()
     * @cover Phossa2\Cache\CacheItem::getExpiration()
     */
    public function testExpiresAfter()
    {
        $time = time();
        $diff = 10;
        $this->object->expiresAfter($diff);

        $this->assertEquals($time, $this->object->getExpiration()->getTimestamp() - $diff);
    }

    /**
     * Tests CacheItem->__toString()
     *
     * @cover Phossa2\Cache\CacheItem::__toString()
     * @cover Phossa2\Cache\CacheItem::setStrVal()
     */
    public function test__toString()
    {
        $this->object->set(3);
        $this->assertEquals("i:3;", (string) $this->object);

        $this->object->set('3');
        $this->assertEquals("3", (string) $this->object);

        $this->object->setStrVal('3x');
        $this->assertEquals("3x", (string) $this->object);
    }
}

