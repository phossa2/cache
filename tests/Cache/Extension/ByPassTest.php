<?php

namespace Phossa2\Cache\Extension;

use Phossa2\Cache\Driver\StorageDriver;
use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;
use Phossa2\Cache\CachePool;
use Phossa2\Cache\Message\Message;

/**
 * ByPass test case.
 */
class ByPassTest extends \PHPUnit_Framework_TestCase
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
     * @cover Phossa2\Cache\Extension\ByPass::byPassCache()
     */
    public function testGetItem1()
    {
        // trigger for bypass
        $_REQUEST['nocache'] = 1;

        // add bypass extension
        $this->object->addExtension(new ByPass());

        // try it
        $item = $this->object->getItem('test');
        $this->assertFalse($item->isHit());

        $this->assertEquals(
            Message::get(Message::CACHE_EXT_BYPASS),
            $this->object->getError()
        );
    }
}

