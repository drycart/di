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
class CallTest extends \PHPUnit\Framework\TestCase
{
    public function testSimpleCall()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\DummyInterface' => ['#class'=>'drycart\di\tests\DummyComplex']
        ]);
        $obj = $di->get('drycart\di\tests\DummyInterface');
        $dummy = $di->call([$obj, 'method'], ['i'=>null]);
        $this->assertTrue(is_a($dummy, 'drycart\di\tests\Dummy'));
    }
    
    public function testFunctionCall()
    {
        $function = function(int $i) : int
        {
            return $i;
        };
        //
        $di = new \drycart\di\Container();
        $i = $di->call($function, ['i'=>999]);
        //
        $this->assertEquals($i, 999);
    }
}
