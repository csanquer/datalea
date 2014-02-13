<?php

namespace CSanquer\FakeryGenerator\Model;

use Faker\Generator;

/**
 * VariableConfig
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class VariableConfig
{

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $method;

    /**
     *
     * @var array
     */
    protected $methodArguments;

    /**
     *
     * @var bool
     */
    protected $unique;

    /**
     *
     * @var int
     */
    protected $increment = 0;

    /**
     * 
     * @param string $name
     * @param string $method
     * @param array $methodArguments
     * @param bool $unique
     */
    public function __construct($name = null, $method = null, array $methodArguments = array(), $unique = false)
    {
        $this->setName($name);
        $this->setMethod($method);
        $this->setMethodArguments($methodArguments);
        $this->setUnique($unique);
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 
     * @return string
     */
    public function getVarName()
    {
        return '%'.$this->name.'%';
    }
    
    /**
     * 
     * @param string $name
     * @return \CSanquer\FakeryGenerator\Model\VariableConfig
     */
    public function setName($name)
    {
        $this->name = preg_replace('/[^a-zA-Z0-9\-\s_]/', '', $name);
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * 
     * @param string $method
     * @return \CSanquer\FakeryGenerator\Model\VariableConfig
     */
    public function setMethod($method)
    {
        $this->method = $method;
        
        return $this;
    }
    
    public function getMethodArguments()
    {
        return $this->methodArguments;
    }

    public function hasMethodArguments()
    {
        return (bool) count($this->methodArguments);
    }
    
    public function setMethodArguments($methodArguments)
    {
        $this->methodArguments = $methodArguments;
        
        return $this;
    }
    
    public function getMethodArgument($order)
    {
        return $this->hasMethodArgument($order) ? $this->methodArguments[$order] : null;
    }
    
    public function hasMethodArgument($order)
    {
        return array_key_exists($order, $this->methodArgument) && $this->methodArguments[$order] !== null && $this->methodArguments[$order] !== '';
    }
    
    public function addMethodArgument($methodArgument)
    {
        $this->methodArguments[] = $methodArgument;
        
        return $this;
    }
    
    public function setMethodArgument($order, $methodArgument)
    {
        $this->methodArguments[$order] = $methodArgument;
        
        return $this;
    }
    
    public function isUnique()
    {
        return $this->unique;
    }
    
    public function setUnique($unique)
    {
        $this->unique = (bool) $unique;
        
        return $this;
    }

}
