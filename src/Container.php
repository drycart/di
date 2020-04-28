<?php
/*
 * @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\di;

/**
 * Full container functionality DI
 *
 * @author Mendel <mendel@zzzlab.com>
 */
class Container extends AbstractContainer
{
    /**
     * Set config
     * @param array $config
     */
    public function setConfig(array $config) : void
    {
        $this->config = $config;
    }
    
    /**
     * Add config for one class
     * @param string $id
     * @param array $config
     * @return void
     */
    public function addClass(string $id, array $config) : void
    {
        $this->config[$id] = $config;
    }
    
    /**
     * Add transformer closure for transform parameters
     * @param callable $transformer
     * @return void
     */
    public function addTransformer(callable $transformer) : void
    {
        $this->transformers[] = $transformer;
    }
    
    /**
     * Call object method using parameter and add some parameters if need
     * @param callable $callable
     * @param array $parameters
     * @return mixed
     */
    public function call(callable $callable, array $parameters)
    {
        if(is_array($callable) and is_string($callable[0])) {
            $callable[0] = $this->get($callable[0]);
        }
        $preparedParameters = $this->callableParameters($callable, $parameters);
        return call_user_func_array($callable, $preparedParameters);
    }
        
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id class name
     *
     * @throws NotFoundException No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed
     */
    public function get($id)
    {
        $config = $this->classConfig($id);
        //
        if($config['#singleton']) {
            return $this->internalSingleton($id, $config);
        } else {
            return $this->internalMake($id, $config);
        }
    }
    
    /**
     * Create new object, throw if singleton
     * @param string $id class name
     * @param array $parameters parameters from request
     * @return mixed
     * @throws ContainerException
     */
    public function make(string $id, array $parameters = [])
    {
        $config = $this->classConfig($id);
        //
        if($config['#singleton']) {
            throw new ContainerException($id. ' is singlton!');
        }
        //
        return $this->internalMake($id, $config, $parameters);
    }
    
    /**
     * Get singleton, throw if not singleton
     * @param string $id class name
     * @return mixed
     * @throws ContainerException
     */
    public function singleton(string $id)
    {
        $config = $this->classConfig($id);
        //
        if(!$config['#singleton']) {
            throw new ContainerException($id. ' NOT singlton!');
        }
        //
        return $this->internalSingleton($id, $config);
    }
    
    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * *Implement* container interface method
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return !empty($this->classConfig($id));
    }

}
