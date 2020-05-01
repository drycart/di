<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\di\tests\dummy;

/**
 * @author mendel
 */
class MagicClass
{
    use \drycart\di\MagicServiceLocatorTrait;
    
    public function getDummy() : Dummy
    {
        return $this->serviceLocator(Dummy::class);
    }
    
    public function getDummyPlus() : DummyPlusParameter
    {
        return $this->serviceLocator(DummyPlusParameter::class,['intDummy'=>5]);
    }
}
