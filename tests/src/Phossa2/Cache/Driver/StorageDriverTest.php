<?php

namespace Phossa2\Cache\Driver;

use Phossa2\Storage\Storage;
use Phossa2\Storage\Filesystem;
use Phossa2\Storage\Driver\LocalDriver;

/**
 * StorageDriver test case.
 */
class StorageDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var StorageDriver
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new StorageDriver(
            new Storage('/', new Filesystem(new LocalDriver(
                sys_get_temp_dir()
        ))), '/cache_test');
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
     * Tests StorageDriver->has()
     *
     * @cover Phossa2\Cache\Driver\StorageDriver::has()
     */
    public function testHas()
    {
        $this->assertEquals([], $this->object->has('test'));
    }

    /**
     * Tests StorageDriver->get()
     *
     * @cover Phossa2\Cache\Driver\StorageDriver::get()
     */
    public function testGet()
    {
        $this->assertEquals(null, $this->object->get('test'));
    }
}

