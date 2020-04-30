<?php
/*
 * @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\di;

/**
 * Prepare parameters for call, using reflection
 * @see: AbstractCoreContainer
 * @author Mendel <mendel@zzzlab.com>
 */
abstract class AbstractParametersContainer
{
    /**
     * Contain array of callable for try transform parameters
     * @var array 
     */
    protected $transformers = [];
    
    /**
     * Prepare parameters using parameters array
     * @param array $dependency
     * @param array $parameters
     * @return array
     */
    protected function prepareParameters(array $dependency, array $parameters) : array
    {
        $preparedParameters = [];
        foreach ($dependency as $paramReflector) {
            $name = $paramReflector->name;
            $type = $paramReflector->getType();
            if (isset($parameters[$name])) {
                $value = $parameters[$name];
                $preparedParameters[] = $this->prepareValue($type, $value);
            } else {
                $preparedParameters[] = $this->getParameter($paramReflector);
            }
        }
        return $preparedParameters;
    }
    
    private function prepareValue($type, $value)
    {
        if (empty($type) or $type->isBuiltIn() or is_a($value, $type->getName())) {
            return $value;
        } elseif (is_array($value)) {
            $className = $value['#class'] ?? $type->getName();
            return $this->make($className, $value);
        } else {
            return $this->tryTransformValue($type->getName(), $value);
        }
    }

    /**
     * Try get parameter - from container or default
     * @param \ReflectionParameter $param
     * @return mixed
     */
    private function getParameter(\ReflectionParameter $param)
    {
        $type = $param->getType();
        if (!empty($type) and !$type->isBuiltIn()) {
            return $this->get($type->getName());
        } elseif ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }elseif ($type->allowsNull()) {
            return null;
        } else {
            throw new ContainerException('Unknown parameter '.$param->name);
        }
    }

    private function tryTransformValue(string $className, $value)
    {
        foreach ($this->transformers as $transformer) {
            $value = $transformer($value, $className, $this);
            if (is_a($value, $className)) {
                return $value;
            }
        }
        throw new ContainerException('Wrong type of value for parameter. Need: '.$className);
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
    abstract public function get($id);
    
    /**
     * Create new object
     * @param string $id class name
     * @param array $parameters parameters from request
     * @return mixed
     * @throws ContainerException
     */
    abstract public function make(string $id, array $parameters = []);
}