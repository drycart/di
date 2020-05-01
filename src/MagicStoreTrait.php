<?php
/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\di;

/**
 * Magic for store dependency
 * 
 * @author mendel
 */
trait MagicStoreTrait
{
    /**
     * Used at constructor. Store all constructor parameters as object variables
     * If exist optional parameter $keys - store only this variables (for example if
     * call parent constructor which same this methods call)
     * CALL ONLY FROM CONSTRUCTOR
     * 
     * $this->_store(get_defined_vars());
     * 
     * @param array $data
     * @param array $keys
     * @return void
     */
    protected function _store(array $data, ?array $keys = null) : void
    {
        foreach ($keys ?? array_keys($data) as $key) {
            $this->$key = $data[$key] ?? null;
        }
    }    
}
