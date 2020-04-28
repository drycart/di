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
class ExtendedTest extends \PHPUnit\Framework\TestCase
{
    public function testMake()
    {
        $di = new \drycart\di\Container();
        $obj = $di->make('drycart\di\tests\DummyPlusParameter', ['intDummy'=> 0]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyPlusParameter'));
    }
    
    public function testMakeException()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('drycart\di\tests\Dummy is singlton!');
        $di->make('drycart\di\tests\Dummy');
    }
    
    public function testMakeParamsNoType()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyInterface' => [
                '#class'=>'drycart\di\tests\DummyComplex',
                'noTypeParameter' => 5
            ]
        ]);
        $obj = $di->get('drycart\di\tests\DummyInterface');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyInterface'));
    }
    
    public function testMakePreparedParameters()
    {
        $di = new \drycart\di\Container();
        $dummy = $di->get('drycart\di\tests\Dummy');
        $obj = $di->make('drycart\di\tests\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=>$dummy
        ]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyPlusParameter'));
    }
    
    public function testMakeFromArrayParameter()
    {
        $di = new \drycart\di\Container();
        $obj = $di->make('drycart\di\tests\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=>['#class'=>'drycart\di\tests\Dummy']
        ]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyPlusParameter'));
    }
    
    public function testTransformer()
    {
        $di = new \drycart\di\Container();
        $di->addTransformer(function($value, string $className, \drycart\di\DiInterface $container) {
            return $container->get($className);
        });
        $obj = $di->make('drycart\di\tests\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=> new \stdClass()
        ]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyPlusParameter'));
    }
    
    public function testParent()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $obj = $di->singleton('drycart\di\tests\DummyExtended');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyExtended'));
    }
    
    public function testFactory1()
    {
        $di = new \drycart\di\Container();
        $di->addClass(\stdClass::class, [
            '#factory' => function($config, $container) {
            unset($config['#factory']);
            unset($config['#class']);
            unset($config['#singleton']);
            $obj = (object) $config;
            $obj->meta = 'meta';
            return $obj;
        }]);
        $obj = $di->make(\stdClass::class, [
            'id'=> 555,
        ]);
        $this->assertEquals($obj->id, 555);
        $this->assertEquals($obj->meta, 'meta');
    }
    
    public function testFactory2()
    {
        $di = new \drycart\di\Container();
        $di->addClass(\drycart\di\tests\Dummy::class, [
            '#factory' => function($config, $container) {
            $className = $config['#class'];
            $obj = new $className;
            $obj->meta = 'meta';
            return $obj;
        }]);
        $obj = $di->make('drycart\di\tests\DummyExtended');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\DummyExtended'));
        $this->assertEquals($obj->meta, 'meta');
    }
    
    public function testMakeWrongParameterTypeException()
    {
        $di = new \drycart\di\Container();
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('Wrong type of value for parameter');
        $di->make('drycart\di\tests\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=> new \stdClass()
        ]);
    }
    
    public function testSingletoneException()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\Dummy' => [
                '#singleton' => false
            ]
        ]);
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('drycart\di\tests\Dummy NOT singlton!');
        $di->singleton('drycart\di\tests\Dummy');
    }
    
    public function testMakeConfig()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyMakeConfig' => [
                'dummy' => ['i'=>5],
                'intDummy'=> 5
            ],
            'drycart\di\tests\DummyInterface' => [
                '#class'=>'drycart\di\tests\DummyToMake'
            ]
        ]);
        $obj = $di->get('drycart\di\tests\DummyMakeConfig');
        $this->assertTrue(is_a($obj->dummy, 'drycart\di\tests\DummyToMake'));
    }
    public function testService()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $obj1 = $di->singleton('drycart\di\tests\Dummy');
        $obj2 = $di->get('drycart\di\tests\Dummy');
        $this->assertEquals(spl_object_id($obj1), spl_object_id($obj2));
    }
    
    public function testSingletonDefault()
    {
        // For reset if not new.. Yes, static is not tested, but....
        \drycart\di\Di::setInstance(null);
        //
        $di1 = \drycart\di\Di::getInstance();
        $di2 = \drycart\di\Di::getInstance();
        $this->assertEquals(spl_object_id($di1), spl_object_id($di2));
    }
    
    public function testSingleton()
    {
        $di = new \drycart\di\Container();
        \drycart\di\Di::setInstance($di);
        //
        $di1 = \drycart\di\Di::getInstance();
        $di2 = \drycart\di\Di::getInstance();
        $this->assertEquals(spl_object_id($di1), spl_object_id($di2));
    }
    
    public function testRequired()
    {
        $this->assertFalse(defined('DI_REQUIRED_TEST_CONST'));
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyInterface' => [
                '#class'=>'drycart\di\tests\DummyServiceProvider',
                '#singleton' => true,
                'answer' => 42
            ],
            'drycart\di\tests\Dummy' => [
                '#required'=> [
                    'drycart\di\tests\DummyInterface'
                ]
            ]
        ]);
        $di->get('drycart\di\tests\Dummy');
        $this->assertTrue(defined('DI_REQUIRED_TEST_CONST'));
        $this->assertEquals(DI_REQUIRED_TEST_CONST, 42);
    }
}
