<?php

namespace CSanquer\FakeryGenerator\Model;

/**
 * ColumnCollection
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class ColumnCollection
{
    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var array of Column
     */
    protected $columns = array();
    
    /**
     *
     * @param string $name          default = null
     * @param array of Columns $columns
     * 
     */
    public function __construct($name = null, $columns = array())
    {
        $this->setName($name);
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
     * @return \CSanquer\FakeryGenerator\Model\Column
     */
    public function setName($name)
    {
        $this->name = preg_replace('/[^a-zA-Z0-9\-\s_]/', '', $name);

        return $this;
    }

    /**
     * 
     * @return array of column
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * 
     * @param array of column $columns
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
        if (empty($name)) {
            throw new \InvalidArgumentException('The column config must have a name.');
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
}
