<?php
/*
 * @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\di\tests;
use PHPUnit\Framework\TestCase;

/**
 * Description of DiTest
 *
 * @author mendel
 */
class ExtendedTest extends TestCase
{
    public function testMake()
    {
        $di = new \drycart\di\Container();
        $obj = $di->make('drycart\di\tests\dummy\DummyPlusParameter', ['intDummy'=> 0]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyPlusParameter'));
    }
    
    public function testMakeException()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('drycart\di\tests\dummy\Dummy is singlton!');
        $di->make('drycart\di\tests\dummy\Dummy');
    }
    
    public function testMakeParamsNoType()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\DummyInterface' => [
                '#class'=>'drycart\di\tests\dummy\DummyComplex',
                'noTypeParameter' => 5
            ]
        ]);
        $obj = $di->get('drycart\di\tests\dummy\DummyInterface');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyInterface'));
    }
    
    public function testMakePreparedParameters()
    {
        $di = new \drycart\di\Container();
        $dummy = $di->get('drycart\di\tests\dummy\Dummy');
        $obj = $di->make('drycart\di\tests\dummy\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=>$dummy
        ]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyPlusParameter'));
    }
    
    public function testMakeFromArrayParameter()
    {
        $di = new \drycart\di\Container();
        $obj = $di->make('drycart\di\tests\dummy\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=>['#class'=>'drycart\di\tests\dummy\Dummy']
        ]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyPlusParameter'));
    }
    
    public function testTransformer()
    {
        $di = new \drycart\di\Container();
        $di->addTransformer(function($value, string $className, \drycart\di\DiInterface $container) {
            return $container->get($className);
        });
        $obj = $di->make('drycart\di\tests\dummy\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=> new \stdClass()
        ]);
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyPlusParameter'));
    }
    
    public function testParent()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $obj = $di->singleton('drycart\di\tests\dummy\DummyExtended');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyExtended'));
    }
    
    public function testParentAlias()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            '#other' => ['#class'=>'stdClass'],
            '#other:children' => []
        ]);
        $obj = $di->get('#other:children');
        $this->assertTrue(is_a($obj, 'stdClass'));
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
        $di->addClass(\drycart\di\tests\dummy\Dummy::class, [
            '#factory' => function($config, $container) {
            $className = $config['#class'];
            $obj = new $className;
            $obj->meta = 'meta';
            return $obj;
        }]);
        $obj = $di->make('drycart\di\tests\dummy\DummyExtended');
        $this->assertTrue(is_a($obj, 'drycart\di\tests\dummy\DummyExtended'));
        $this->assertEquals($obj->meta, 'meta');
    }
    
    public function testMakeWrongParameterTypeException()
    {
        $di = new \drycart\di\Container();
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('Wrong type of value for parameter');
        $di->make('drycart\di\tests\dummy\DummyPlusParameter', [
            'intDummy'=> 0,
            'dummy'=> new \stdClass()
        ]);
    }
    
    public function testSingletoneException()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\Dummy' => [
                '#singleton' => false
            ]
        ]);
        $this->expectException(\drycart\di\ContainerException::class);
        $this->expectExceptionMessage('drycart\di\tests\dummy\Dummy NOT singlton!');
        $di->singleton('drycart\di\tests\dummy\Dummy');
    }
    
    public function testMakeConfig()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\DummyMakeConfig' => [
                'dummy' => ['i'=>5],
                'intDummy'=> 5
            ],
            'drycart\di\tests\dummy\DummyInterface' => [
                '#class'=>'drycart\di\tests\dummy\DummyToMake'
            ]
        ]);
        $obj = $di->get('drycart\di\tests\dummy\DummyMakeConfig');
        $this->assertTrue(is_a($obj->dummy, 'drycart\di\tests\dummy\DummyToMake'));
    }
    public function testService()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\Dummy' => [
                '#singleton' => true
            ]
        ]);
        $obj1 = $di->singleton('drycart\di\tests\dummy\Dummy');
        $obj2 = $di->get('drycart\di\tests\dummy\Dummy');
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
            'drycart\di\tests\dummy\DummyInterface' => [
                '#class'=>'drycart\di\tests\dummy\DummyServiceProvider',
                '#singleton' => true,
                'answer' => 42
            ],
            'drycart\di\tests\dummy\Dummy' => [
                '#required'=> [
                    'drycart\di\tests\dummy\DummyInterface'
                ]
            ]
        ]);
        $di->get('drycart\di\tests\dummy\Dummy');
        $this->assertTrue(defined('DI_REQUIRED_TEST_CONST'));
        $this->assertEquals(DI_REQUIRED_TEST_CONST, 42);
    }
}
