<?php
/*
 * @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\di\tests;
use PHPUnit\Framework\TestCase;


/**
 * @author mendel
 */
class MagicTest extends TestCase
{
    public function testSimpleCall()
    {
        $magic = dummy\MagicClass::make();
        $dummy = $magic->getDummy();
        $dummyPlus = $magic->getDummyPlus();
        $this->assertTrue(is_a($magic, 'drycart\di\tests\dummy\MAgicClass'));
        $this->assertTrue(is_a($dummy, 'drycart\di\tests\dummy\Dummy'));
        $this->assertTrue(is_a($dummyPlus, 'drycart\di\tests\dummy\DummyPlusParameter'));
    }
}
