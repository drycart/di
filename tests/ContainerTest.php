<?php
/*
 * @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\di\tests;


/**
 * Description of DiTest
 *
 * @author mendel
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    
    public function testInstantiate()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'stdClass' => ['#class'=>'stdClass']
        ]);
        $obj = $di->get('stdClass');
        $this->assertTrue(is_a($obj, 'stdClass'));
    }
    
    public function testAlias()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            '#other' => ['#class'=>'stdClass']
        ]);
        $obj = $di->get('#other');
        $this->assertTrue(is_a($obj, 'stdClass'));
    }
    
    public function testNotFoundParameters()
    {
        $di = new \drycart\di\Container();
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('Unknown parameter intDummy');
        $di->get('drycart\di\tests\DummyPlusParameter');
    }
    
    public function testDefaultParams()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyInterface' => ['#class'=>'drycart\di\tests\DummyComplex']
        ]);
        $obj = $di->get('drycart\di\tests\DummyInterface');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyInterface'));
    }

    public function testNoConfig()
    {
        $di = new \drycart\di\Container();
        $obj = $di->get('stdClass');
        $this->assertTrue(is_a($obj, 'stdClass'));
    }

    public function testNotFound()
    {
        $di = new \drycart\di\Container();
        $this->expectException(\drycart\di\NotFoundException::class);
        $di->get('NotExistClass');
    }
    
    public function testSinglton()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $obj1 = $di->get('drycart\di\tests\Dummy');
        $obj2 = $di->get('drycart\di\tests\Dummy');
        $this->assertEquals(spl_object_id($obj1), spl_object_id($obj2));
    }
    
    public function testFactory()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\Dummy' => [
                '#singleton' => false
            ]
        ]);
        $obj1 = $di->get('drycart\di\tests\Dummy');
        $obj2 = $di->get('drycart\di\tests\Dummy');
        $this->assertNotEquals(spl_object_id($obj1), spl_object_id($obj2));
    }
    
    public function testWrongClass()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyInterface' => ['#class'=>'stdClass']
        ]);
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('Wrong class, will be drycart\di\tests\DummyInterface');
        $di->get('drycart\di\tests\DummyInterface');
    }
    
    public function testSpeed()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyInterface' => ['#class'=>'drycart\di\tests\DummyComplex']
        ]);
        for ($i=0;$i<1000;$i++) {
            $obj = $di->make('drycart\di\tests\DummyInterface', ['intDummy'=>$i]);
//            $obj = new DummyComplex($i, null);
        }
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyInterface'));
    }
    
    public function testSpeed2()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'stdClass' => ['#class'=>'stdClass']
        ]);
        $className = 'stdClass';
        for ($i=0;$i<10000;$i++) {
            $obj = $di->get($className);
//            $obj = new $className;
        }
        $this->assertTrue(is_a($obj, 'stdClass'));
    }
    
}
