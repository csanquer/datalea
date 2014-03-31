<?php

namespace CSanquer\FakeryGenerator\Model;

use CSanquer\FakeryGenerator\Helper\Converter;

/**
 * Column
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class Column extends ColumnContainer
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
     * @param string $name          default = null
     * @param string $value         default = null
     * @param string $convertMethod default = null
     * @param array  $columns       default = array(), array of Column
     */
    public function __construct($name = null, $value = null, $convertMethod = null, array $columns = [])
    {
        parent::__construct($columns);
        $this->setName($name);
        $this->setValue($value);
        $this->setConvertMethod($convertMethod);
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
     * @param  string $name
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
     * @param  mixed  $value
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
     * @param  string $convertMethod
     * @return Column
     */
    public function setConvertMethod($convertMethod)
    {
        $this->convertMethod = (string) $convertMethod;

        return $this;
    }

    /**
     *
     * @param  array  $availableVariables
     * @return string
     */
    public function replaceVariable(array $availableVariables)
    {
        if ($this->countColumns() > 0) {
            $result = [];
            foreach ($this->columns as $column) {
                $result[$column->getName()] = $column->replaceVariable($availableVariables);
            }

            return $result;
        }

        $value = preg_replace_callback('/%([a-zA-Z0-9_]+)%/',
            function($matches) use ($availableVariables) {
                return isset($availableVariables[$matches[1]]['flat']) ? $availableVariables[$matches[1]]['flat'] : $matches[0];
            },
            $this->getValue()
        );

        return Converter::convert($this->getConvertMethod(), $value);
    }
}
