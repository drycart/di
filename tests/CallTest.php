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
class CallTest extends TestCase
{
    public function testSimpleCall()
    {
        $di = new \drycart\di\Container();
        $di->setConfig([
            'drycart\di\tests\dummy\DummyInterface' => ['#class'=>'drycart\di\tests\dummy\DummyComplex']
        ]);
        $obj = $di->get('drycart\di\tests\dummy\DummyInterface');
        $dummy = $di->call([$obj, 'method'], ['i'=>null]);
        $this->assertTrue(is_a($dummy, 'drycart\di\tests\dummy\Dummy'));
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
