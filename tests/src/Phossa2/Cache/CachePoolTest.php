<?php

namespace Phossa2\Cache;

use Psr\Cache\CacheItemInterface;
use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;

/**
 * CachePool test case.
 */
class CachePoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
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
     * @cover Phossa2\Cache\CachePool::getItem()
     */
    public function testGetItem1()
    {
        $item = $this->object->getItem('test');
        $this->assertTrue($item instanceof CacheItemInterface);
    }

    /**
     * Tests CachePool->getItem()
     *
     * @cover Phossa2\Cache\CachePool::getItem()
     * @expectedException Psr\Cache\InvalidArgumentException
     * @expectedExceptionCode Phossa2\Cache\Message\Message::CACHE_INVALID_KEY
     */
    public function testGetItem2()
    {
        $item = $this->object->getItem(1);
    }

    /**
     * Tests CachePool->getItems()
     *
     * @cover Phossa2\Cache\CachePool::getItems()
     */
    public function testGetItems1()
    {
        $items = $this->object->getItems(['t1', 't2']);
        foreach ($items as $item) {
            $this->assertTrue($item instanceof CacheItemInterface);
        }
    }

    /**
     * Tests CachePool->getItems()
     *
     * @cover Phossa2\Cache\CachePool::getItems()
     * @expectedException Psr\Cache\InvalidArgumentException
     * @expectedExceptionCode Phossa2\Cache\Message\Message::CACHE_INVALID_KEY
     */
    public function testGetItems2()
    {
        $items = $this->object->getItems([2, 't2']);
        foreach ($items as $item) {
            $this->assertTrue($item instanceof CacheItemInterface);
        }
    }

    /**
     * Tests CachePool->hasItem()
     *
     * @cover Phossa2\Cache\CachePool::hasItem()
     */
    public function testHasItem()
    {
        $this->assertFalse($this->object->hasItem('test'));
    }

    /**
     * Tests CachePool->clear()
     *
     * @cover Phossa2\Cache\CachePool::clear()
     */
    public function testClear()
    {
        $this->assertTrue($this->object->clear());
    }

    /**
     * Tests CachePool->deleteItem()
     *
     * @cover Phossa2\Cache\CachePool::deleteItem()
     */
    public function testDeleteItem1()
    {
        $this->assertTrue($this->object->deleteItem('test'));
    }

    /**
     * Tests CachePool->deleteItem()
     *
     * @cover Phossa2\Cache\CachePool::deleteItem()
     * @expectedException Psr\Cache\InvalidArgumentException
     * @expectedExceptionCode Phossa2\Cache\Message\Message::CACHE_INVALID_KEY
     */
    public function testDeleteItem2()
    {
        $this->assertTrue($this->object->deleteItem(3));
    }

    /**
     * Tests CachePool->deleteItems()
     *
     * @cover Phossa2\Cache\CachePool::deleteItems()
     */
    public function testDeleteItems()
    {
        $this->assertTrue($this->object->deleteItems(['t1', 't2']));
    }

    /**
     * Tests CachePool->save()
     *
     * @cover Phossa2\Cache\CachePool::save()
     * @cover Phossa2\Cache\CachePool::getItem()
     * @cover Phossa2\Cache\CachePool::saveDeferred()
     */
    public function testSave()
    {
        $key = 'test';
        $val = 'wow';

        $item1 = $this->object->getItem($key);
        $item1->set($val);
        $this->assertTrue($this->object->save($item1));

        $item2 = $this->object->getItem($key);

        $this->assertEquals('wow', $item2->get());

        $this->assertTrue($this->object->deleteItem($key));
    }

    /**
     * Tests CachePool->commit()
     *
     * @cover Phossa2\Cache\CachePool::commit()
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->commit());
    }
}

