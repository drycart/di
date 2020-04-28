<?php
/*
 * @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */

namespace drycart\di\tests;
use drycart\di\MagicStoreTrait;

/**
 * Description of Dummy2
 *
 * @author mendel
 */
class DummyPlusParameter
{
    use MagicStoreTrait;
    
    public function __construct(Dummy $dummy, int $intDummy)
    {
        $this->_store(get_defined_vars());
    }
}
