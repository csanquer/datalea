<?php

namespace CSanquer\FakeryGenerator\Model;

/**
 * ColumnContainer
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
abstract class ColumnContainer
{
    /**
     *
     * @var array of Column
     */
    protected $columns = [];

    /**
     *
     * @param array $columns default = array(), array of Column
     */
    public function __construct(array $columns = [])
    {
        $this->setColumns($columns);
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
     * @return int
     */
    public function countColumns()
    {
        return count($this->columns);
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
     * @param  array of Column                        $columns
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
     * @param  Column  $column
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
     * get Columns Names list as a flat array or multidimensional array
     * 
     * @param bool $asFlat default = false
     * @return array
     */
    public function getColumnNames($asFlat = false)
    {
        $names = [];
        foreach ($this->columns as $column) {
            if ($column->countColumns() > 0) {
                if ($asFlat) {
                    $tmpNames = $column->getColumnNames($asFlat);
                    foreach ($tmpNames as $tmpName) {
                        $name = $column->getName().'-'.$tmpName;
                        $names[$name] = $name;
                    } 
                } else {
                    $names[$column->getName()] = $column->getColumnNames($asFlat);
                }

            } else {
                $names[$column->getName()] = $column->getName();
            }
        }
        
        return $names;
    }
}
