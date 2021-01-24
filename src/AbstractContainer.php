<?php
/*
 * @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\di;

/**
 * Main code for instantiate our object, magic call etc
 * Abstract, need configuration logic
 * @author Mendel <mendel@zzzlab.com>
 */
abstract class AbstractContainer extends AbstractParametersContainer implements DiInterface
{
    /**
     * Contain our configuration data
     * @var array 
     */
    protected $config = [];
    
    /**
     * Contain stored object (for services)
     * @var array
     */
    private $storage = [];
    
    private $cachedConfig = [];
        
    /**
     * Get prepared config for selected class
     * Add default value for system fields if empty
     * @param string $id 
     * @param bool $required need check if exist, and throw if not
     * @return array
     */
    protected function classConfig(string $id, bool $required = false) : ?array
    {
        if (!isset($this->cachedConfig[$id])) {
            $config = $this->internalConfig($id);
            $this->initRequired($config['#required'] ?? []);
            unset($config['#required']);
            $this->cachedConfig[$id] = $config;
        }
        if($required and is_null($this->cachedConfig[$id])) {
            throw new NotFoundException('Not exist class: '.$id);
        }
        return $this->cachedConfig[$id];
    }
    
    protected function internalConfig(string $id) : ?array
    {
        if (!isset($this->config[$id]) and !class_exists($id)) {
            return null;
        }
        $parentConfig = $this->getParentConfig($id);
        //
        $currentConfig = $this->config[$id] ?? [];
        if (empty($currentConfig['#class']) and !$this->isAlias($id)) {
            $currentConfig['#class'] = $id;
        }
        //
        $config = array_merge($parentConfig, $currentConfig);
        if (!isset($config['#singleton'])) {
            $config['#singleton'] = false;
        }
        return $config;
    }
    
    protected function getParentConfig(string $id) : array 
    {
        if ($this->isAlias($id)) {
            $parentId = $this->getAliaseParent($id);
        } else {
            $parents = class_parents($id);
            $parentId = reset($parents);
        }
        //
        if(!is_string($parentId)) {
            return [];
        }
        return $this->internalConfig($parentId);
    }

    /**
     * Singleton wrapper for internal use
     * @param string $id
     * @param array $config
     * @return mixed
     */
    protected function internalSingleton($id, array $config)
    {
        if (!isset($this->storage[$id])) {
            $this->storage[$id] = $this->internalMake($id, $config);
        } 
        return $this->storage[$id];
    }
    
    /**
     * Create new object using our core
     * @param string $id class name
     * @param array $config
     * @param array $parameters parameters from request
     * @return mixed
     * @throws ContainerException
     */
    protected function internalMake($id, array $config, array $parameters = [])
    {
        $fullParameters = array_merge($parameters, $config);
        $obj = $this->getObject($fullParameters);
        //
        if (!$this->isAlias($id) and !is_a($obj, $id)) {
            throw new ContainerException('Wrong class, will be '.$id);
        }
        return $obj;
    }

    protected function isAlias(string $id) : bool
    {
        return (substr($id, 0, 1) == '#');
    }
    
    protected function getAliaseParent(string $id) : ?string
    {
        $parts = explode(':', $id, 2);
        if(count($parts) == 1) {
            return null;
        }
        return $parts[0];
    }
    
    protected function initRequired(array $requirement) : void
    {
        foreach ($requirement as $className) {
            if (!isset($this->storage[$className])) {
                $this->singleton($className);
            }
        }
    }
    
    /**
     * Get reflector for method/function... i.e. callable
     * @2do: check if it need, maybe it can be done whithout this?
     * @param callable $callable
     * @return array
     */
    protected function getCallableDependency(callable $callable) : array
    {
        if (is_array($callable)) {
            $className = get_class($callable[0]);
            $classReflector = new \ReflectionClass($className);
            return $classReflector->getMethod($callable[1])->getParameters();
        } else {
            return (new \ReflectionFunction($callable))->getParameters();
        }
    }
    
    /**
     * Prepare parameters for callable (add dependency)
     * @param callable $callable
     * @param array $parameters assoc array of parameters
     * @return array prepared parameters
     */
    protected function callableParameters(callable $callable, array $parameters) : array
    {
        $dependency = $this->getCallableDependency($callable);
        return $this->prepareParameters($dependency, $parameters);
    }
    
    private $reflectionCache = [];
    private function getClassDependency(string $className) : array
    {
        if (!class_exists($className)) {
            // Not NotFoundException => it is missconfiguration, i.e. wrong class at config
            throw new ContainerException('Class need to instantiate not exist '.$className);
        }
        if (!isset($this->reflectionCache[$className])) {
            $reflector = new \ReflectionClass($className);
            $constructor = $reflector->getConstructor();
            if (!is_null($constructor)) {
                $this->reflectionCache[$className] = $constructor->getParameters();
            } else {
                $this->reflectionCache[$className] = [];
            }
        }
        return $this->reflectionCache[$className];
    }
    
    /**
     * Instantiate object using parameters array
     * @param array $parameters
     * @return mixed
     */
    private function getObject(array $parameters = [])
    {
        if (isset($parameters['#factory'])) {
            return call_user_func_array($parameters['#factory'], [$parameters, $this]);
        }
        $className = $parameters['#class'];
        //
        $dependency = $this->getClassDependency($className);
        if (!empty($dependency)) {
            $preparedParameters = $this->prepareParameters($dependency, $parameters);
        } else {
            $preparedParameters = [];
        }
        return new $className(...$preparedParameters);
    }
}
