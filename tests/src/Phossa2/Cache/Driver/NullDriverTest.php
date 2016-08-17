<?php
namespace Phossa2\Cache\Driver;

/**
 * NullDriver test case.
 */
class NullDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var NullDriver
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new NullDriver();
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
     * Tests NullDriver->has()
     *
     * @cover Phossa2\Cache\Driver\NullDriver::has()
     */
    public function testHas()
    {
        $this->assertEquals([], $this->object->has('test'));
    }

    /**
     * Tests NullDriver->get()
     *
     * @cover Phossa2\Cache\Driver\NullDriver::get()
     */
    public function testGet()
    {
        $this->assertEquals(null, $this->object->get('test'));
    }

    /**
     * Tests NullDriver->commit()
     *
     * @cover Phossa2\Cache\Driver\NullDriver::commit()
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->commit());
    }

    /**
     * Tests NullDriver->clear()
     *
     * @cover Phossa2\Cache\Driver\NullDriver::clear()
     */
    public function testClear()
    {
        $this->assertTrue($this->object->clear());
    }

    /**
     * Tests NullDriver->ping()
     *
     * @cover Phossa2\Cache\Driver\NullDriver::ping()
     */
    public function testPing()
    {
        $this->assertTrue($this->object->ping());
    }
}
