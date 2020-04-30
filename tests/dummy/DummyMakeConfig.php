<?php
/*
 * @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */

namespace drycart\di\tests\dummy;
use drycart\di\MagicStoreTrait;

/**
 * Description of Dummy2
 *
 * @author mendel
 */
class DummyMakeConfig
{
    public $dummy;
    
    use MagicStoreTrait;
    
    public function __construct(DummyToMake $dummy, int $intDummy)
    {
        $this->_store(get_defined_vars());
    }
    
    public function method(?int $i, DummyToMake $dummy) : DummyToMake
    {
        return $dummy;
    }
}
