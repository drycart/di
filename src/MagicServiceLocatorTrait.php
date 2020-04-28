<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\di;

/**
 * Sugar functions for service locator use for DI
 * i.e. instantiate some services, and selfmake
 * 
 * Best pattern is use DI directly, because its flexible,
 * but sometimes it easy to use as service locator 
 * 
 * @author mendel
 */
trait MagicServiceLocatorTrait
{
    /**
     * Self make, i.e. instantiate current class using DI
     * @return mixed
     */
    public static function make()
    {
        $di = Di::getInstance();
        return $di->get(static::class);
    }
    
    /**
     * Instantiate some service or other object using our DI
     * @param string $id
     * @param array $parameters
     * @return type
     */
    protected function serviceLocator(string $id, ?array $parameters = null)
    {
        $di = Di::getInstance();
        if(is_null($parameters)) {
            return $di->get($id);
        } else {
            return $di->make($id, $parameters);
        }
    }
}
