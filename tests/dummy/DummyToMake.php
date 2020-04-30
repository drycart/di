<?php
/*
 * @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */

namespace drycart\di\tests\dummy;
use drycart\di\MagicStoreTrait;

/**
 * Description of Dummy3
 *
 * @author mendel
 */
class DummyToMake implements DummyInterface
{
    use MagicStoreTrait;
    
    public function __construct(?int $i, Dummy $dummy)
    {
        $this->_store(get_defined_vars());
    }
}
