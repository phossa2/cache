<?php

namespace Phossa2\Cache\Extension;

use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;
use Phossa2\Cache\CachePool;

/**
 * StampedeProtection test case.
 */
class StampedeProtectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachePool
     */
    private $object;
    private $storage;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->storage = new Storage('/',
            new Filesystem(new LocalDriver(sys_get_temp_dir() . '/xxx'))
        );
        $driver = new StorageDriver($this->storage, '/cache_test');
        $this->object = new CachePool($driver);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->storage->del('/cache_test');
        $this->storage = null;
        $this->object  = null;
        parent::tearDown();
    }

    /**
     * no stampede extension
     *
     * @cover Phossa2\Cache\CachePool::addExtension()
     * @cover Phossa2\Cache\Extension\StampedeProtection::stampedeProtect()
     */
    public function testGetItem1()
    {
        // save item
        $time = time();
        $item = $this->object->getItem('test');
        $item->set(2)->expiresAfter(100);
        $this->assertTrue($this->object->save($item));

        $this->assertTrue($this->object->hasItem('test'));

        // delete item
        $this->object->deleteItem('test');
    }

    /**
     * Tests stampede extension
     *
     * @cover Phossa2\Cache\CachePool::addExtension()
     * @cover Phossa2\Cache\Extension\StampedeProtection::stampedeProtect()
     */
    public function testGetItem2()
    {
        // add extension
        $this->object->addExtension(new StampedeProtection([
            'probability' => 1000
        ]));

        // save item
        $time = time();
        $item = $this->object->getItem('test');
        $item->set(2)->expiresAfter(100);
        $this->assertTrue($this->object->save($item));

        $this->assertFalse($this->object->hasItem('test'));

        $this->expectOutputString('Stampede protection for "test"');
        echo $this->object->getError();

        // delete item
        $this->object->deleteItem('test');
    }
}

