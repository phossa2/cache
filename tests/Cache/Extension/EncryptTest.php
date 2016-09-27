<?php

namespace Phossa2\Cache\Extension;

use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;
use Phossa2\Cache\CachePool;

/**
 * Encrypt test case.
 */
class EncryptTest extends \PHPUnit_Framework_TestCase
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
     * @cover Phossa2\Cache\Extension\Serialize::doSerialize()
     * @cover Phossa2\Cache\Extension\Serialize::doUnserialize()
     */
    public function testGetItem1()
    {
        // add serialize extension
        $this->object->addExtension(new Serialize());

        // add encrypt extension
        $this->object->addExtension(new Encrypt());

        // save item with serialized value
        $item = $this->object->getItem('test');
        $item->set(2);
        $this->assertTrue($this->object->save($item));

        $this->assertEquals('aToyOw==', $item->getStrVal());

        // get value (unserliazed)
        $item = $this->object->getItem('test');
        $this->assertEquals(2, $item->get());

        // delete item
        $this->object->deleteItem('test');
    }
}

