<?php
/*
 * @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */

namespace drycart\di\tests;
use drycart\di\MagicStoreTrait;

/**
 * Description of Dummy3
 *
 * @author mendel
 */
class DummyComplex implements DummyInterface
{
    use MagicStoreTrait;
    
    public function __construct(?int $intDummy, ?string $notExist, $noTypeParameter = 1, string $defaultString = 'DefaultString')
    {
        $this->_store(get_defined_vars());
    }
        
    public function method(Dummy $dummy, ?int $i) : Dummy
    {
        return $dummy;
    }
    
    public function whoAmI()
    {
        return get_class($this);
    }
}
