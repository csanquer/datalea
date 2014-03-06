<?php

namespace CSanquer\FakeryGenerator\Model;

use CSanquer\FakeryGenerator\Helper\Converter;

/**
 * Column
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class Column
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
    protected $value;

    /**
     *
     * @var string
     */
    protected $convertMethod;

    /**
     *
     * @var array of Column
     */
    protected $columns = [];
    
    /**
     *
     * @param string $name          default = null
     * @param string $value         default = null
     * @param string $convertMethod default = null
     * @param array  $columns       default = array(), array of Column
     */
    public function __construct($name = null, $value = null, $convertMethod = null, array $columns = [])
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setConvertMethod($convertMethod);
        $this->setColumns($columns);
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
     * @param string $name
     * @return Column
     */
    public function setName($name)
    {
        $this->name = preg_replace('/[^a-zA-Z0-9\-\s_]/', '', $name);

        return $this;
    }

    /**
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 
     * @param mixed $value
     * @return Column
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getConvertMethod()
    {
        return $this->convertMethod;
    }

    /**
     * 
     * @param string $convertMethod
     * @return Column
     */
    public function setConvertMethod($convertMethod)
    {
        $this->convertMethod = (string) $convertMethod;

        return $this;
    }
    
    /**
     * 
     * @return array of Column
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * 
     * @return Column
     */
    public function getColumn($name)
    {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }

    /**
     * 
     * @param array of Column $columns
     * @return \CSanquer\FakeryGenerator\Model\Column
     */
    public function setColumns(array $columns)
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        
        return $this;
    }

    /**
     * @param  Column $column
     * @return Column
     */
    public function addColumn(Column $column)
    {
        $name = $column->getName();
        if ($name === null || $name === '') {
            throw new \InvalidArgumentException('The column must have a name.');
        }

        $this->columns[$name] = $column;

        return $this;
    }

    /**
     *
     * @param  Column $column
     * @return boolean
     */
    public function removeColumn(Column $column)
    {
        $key = array_search($column, $this->columns, true);

        if ($key !== false) {
            unset($this->columns[$key]);

            return true;
        }

        return false;
    }
    
    /**
     *
     * @param  array  $availableVariables
     * @return string
     */
    public function replaceVariable(array $availableVariables)
    {
        $value = preg_replace_callback('/%([a-zA-Z0-9_]+)%/',
            function($matches) use ($availableVariables) {
                return isset($availableVariables[$matches[1]]) ? $availableVariables[$matches[1]] : $matches[0];
            },
            $this->getValue()
        );

        return Converter::convert($this->getConvertMethod(), $value);
    }
}
