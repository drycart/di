<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\di;
use \Psr\Container\ContainerInterface;

/**
 *
 * @author mendel
 */
interface DiInterface extends ContainerInterface
{
    /**
     * Call object method using parameter and add some parameters if need
     * @param callable $callable
     * @param array $parameters
     * @return mixed
     */
    public function call(callable $callable, array $parameters);
    
    
    /**
     * Create new object, throw if singleton
     * @param string $id class name
     * @param array $parameters parameters from request
     * @return mixed
     * @throws ContainerException
     */
    public function make(string $id, array $parameters = []);
    
    /**
     * Get singleton, throw if not singleton..
     * @param string $id class name
     * @return mixed
     * @throws ContainerException
     */
    public function singleton(string $id);
}
