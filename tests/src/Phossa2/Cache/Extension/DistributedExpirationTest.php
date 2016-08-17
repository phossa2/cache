<?php

namespace Phossa2\Cache\Extension;

use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;
use Phossa2\Cache\CachePool;

/**
 * DistributedExpiration test case.
 */
class DistributedExpirationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachePool
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
        $this->object = new CachePool($driver);
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
     * Tests CachePool->getItem()
     *
     * @cover Phossa2\Cache\CachePool::addExtension()
     * @cover Phossa2\Cache\Extension\DistributedExpiration::distributeExpire()
     */
    public function testGetItem1()
    {
        // add extension
        $this->object->addExtension(new DistributedExpiration());

        // save item
        $time = time();
        $item = $this->object->getItem('test');
        $item->set(2)->expiresAfter(1000);
        $this->assertTrue($this->object->save($item));

        $diff = abs($item->getExpiration()->getTimestamp() - $time - 1000);

        $this->assertTrue($diff >= 0 && $diff < 51);

        // delete item
        $this->object->deleteItem('test');
    }
}

