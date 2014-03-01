<?php

namespace CSanquer\FakeryGenerator\Model;

use \Spyrit\Datalea\Faker\Dump\Dumper;
use CSanquer\ColibriCsv\CsvWriter;

/**
 * Config
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Config
{
    /**
     *
     * @var string
     */
    protected $locale;

    /**
     *
     * @var int
     */
    protected $seed;

    /**
     *
     * @var string
     */
    protected $className;

    /**
     *
     * @var array
     */
    protected $formats = array();

    /**
     *
     * @var int
     */
    protected $fakeNumber;

    /**
     *
     * @var array of Column
     */
    protected $columns = array();

    /**
     *
     * @var array of Variable
     */
    protected $variables = array();

    /**
     *
     * @var \CSanquer\ColibriCsv\Dialect
     */
    protected $csvDialect;

    public function __construct()
    {
        $this->columns = array();
        $this->csvDialect = \CSanquer\ColibriCsv\Dialect::createExcelDialect();
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function hasSeed()
    {
        return $this->seed !== null && $this->seed != '';
    }

    public function generateSeed()
    {
        return $this->setSeed(mt_rand(0, 50000));
    }

    /**
     *
     * @return int
     */
    public function getSeed()
    {
        return $this->seed;
    }

    /**
     *
     * @param  int                                $seed
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setSeed($seed)
    {
        $this->seed = $seed !== null && $seed != '' ? (int) $seed : null;

        return $this;
    }

    /**
     *
     * @param  bool   $withoutSlashes
     * @return string
     */
    public function getClassName($withoutSlashes = false)
    {
        return $withoutSlashes ? str_ireplace('\\', '_', $this->className) : $this->className;
    }

    /**
     *
     * @return string
     */
    public function getClassNameLastPart()
    {
        $res = preg_match('/([a-zA-Z0-9]+)$/', $this->className, $matches);
        if ($res) {
            return $matches[1];
        }

        return $this->getClassName(true);
    }

    public function setClassName($className)
    {
        $this->className = preg_replace('/[^a-zA-Z0-9_\\\\]/', '', $className);

        return $this;
    }

    public function addFormat($format)
    {
        if (in_array($format, array_keys(Dumper::getAvailableFormats())) && !in_array($format, $this->formats)) {
            $this->formats[] = $format;
        }

        return $this;
    }

    /**
     *
     * @param string
     * @return boolean
     */
    public function removeFormat($format)
    {
        $key = array_search($format, $this->formats, true);

        if ($key !== false) {
            unset($this->formats[$key]);

            return true;
        }

        return false;
    }

    /**
     *
     * @param  string $format
     * @return bool
     */
    public function hasFormat($format)
    {
        return in_array($format, $this->formats);
    }

    public function getFormats()
    {
        return $this->formats;
    }

    /**
     *
     * @param  array                              $formats
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;

        return $this;
    }

    public function getFakeNumber()
    {
        return $this->fakeNumber;
    }

    public function setFakeNumber($fakeNumber)
    {
        $this->fakeNumber = (int) $fakeNumber;

        return $this;
    }

    public function getCsvDialect()
    {
        return $this->csvDialect;
    }

    public function setCsvDialect(\CSanquer\ColibriCsv\Dialect $csvDialect)
    {
        $this->csvDialect = $csvDialect;
        
        return $this;
    }
        
    /**
     * create a CSV writer from CSV format options
     *
     * @return \CSanquer\ColibriCsv\CsvWriter
     */
    public function createCsvWriter()
    {
        return new CsvWriter($this->csvDialect ?: \CSanquer\ColibriCsv\Dialect::createExcelDialect());
    }

    /**
     *
     * @param  string       $name
     * @return Column
     */
    public function getColumn($name)
    {
        return isset($this->columns[$name]) ? $this->columns[$name] : null ;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param  array                              $columns
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param  \Spyrit\Datalea\Faker\Model\Column $column
     * @return \Spyrit\Datalea\Faker\Model\Config
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
     * @param  \Spyrit\Datalea\Faker\Model\Column $column
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

    public function getVariables()
    {
        return $this->variables;
    }

    /**
     *
     * @param  string         $name
     * @return Variable
     */
    public function getVariable($name)
    {
        return isset($this->variables[$name]) ? $this->variables[$name] : null ;
    }

    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param  \Spyrit\Datalea\Faker\Model\Variable $variable
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function addVariable(Variable $variable)
    {
        $name = $variable->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('The variable config must have a name.');
        }

        $this->variables[$name] = $variable;

        return $this;
    }

    /**
     *
     * @param  \Spyrit\Datalea\Faker\Model\Column $column
     * @return boolean
     */
    public function removeVariable(Variable $variable)
    {
        $key = array_search($variable, $this->columns, true);

        if ($key !== false) {
            unset($this->variables[$key]);

            return true;
        }

        return false;
    }

    /**
     * if no column configs, generate a column config for each variable config
     */
    public function generateColumns()
    {
        if (empty($this->columns) && is_array($this->variables) && !empty($this->variables)) {
            foreach ($this->variables as $variable) {
                $this->addColumn(new Column($variable->getName(), $variable->getVarName()));
            }
        }
    }
}
