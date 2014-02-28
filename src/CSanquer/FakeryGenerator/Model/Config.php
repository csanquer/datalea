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
     * @var array of ColumnConfig
     */
    protected $columnConfigs = array();

    /**
     *
     * @var array of VariableConfig
     */
    protected $variableConfigs = array();

    /**
     *
     * @var \CSanquer\ColibriCsv\Dialect
     */
    protected $csvDialect;

    public function __construct()
    {
        $this->columnConfigs = array();
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
     * @return ColumnConfig
     */
    public function getColumnConfig($name)
    {
        return isset($this->columnConfigs[$name]) ? $this->columnConfigs[$name] : null ;
    }

    public function getColumnConfigs()
    {
        return $this->columnConfigs;
    }

    /**
     * @param  array                              $columnConfigs
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setColumnConfigs(array $columnConfigs)
    {
        $this->columnConfigs = $columnConfigs;

        return $this;
    }

    /**
     * @param  \Spyrit\Datalea\Faker\Model\ColumnConfig $columnConfig
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function addColumnConfig(ColumnConfig $columnConfig)
    {
        $name = $columnConfig->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('The column config must have a name.');
        }

        $this->columnConfigs[$name] = $columnConfig;

        return $this;
    }

    /**
     *
     * @param  \Spyrit\Datalea\Faker\Model\ColumnConfig $columnConfig
     * @return boolean
     */
    public function removeColumnConfig(ColumnConfig $columnConfig)
    {
        $key = array_search($columnConfig, $this->columnConfigs, true);

        if ($key !== false) {
            unset($this->columnConfigs[$key]);

            return true;
        }

        return false;
    }

    public function getVariableConfigs()
    {
        return $this->variableConfigs;
    }

    /**
     *
     * @param  string         $name
     * @return VariableConfig
     */
    public function getVariableConfig($name)
    {
        return isset($this->variableConfigs[$name]) ? $this->variableConfigs[$name] : null ;
    }

    public function setVariableConfigs($variableConfigs)
    {
        $this->variableConfigs = $variableConfigs;
    }

    /**
     * @param  \Spyrit\Datalea\Faker\Model\VariableConfig $variableConfig
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function addVariableConfig(VariableConfig $variableConfig)
    {
        $name = $variableConfig->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('The variable config must have a name.');
        }

        $this->variableConfigs[$name] = $variableConfig;

        return $this;
    }

    /**
     *
     * @param  \Spyrit\Datalea\Faker\Model\ColumnConfig $columnConfig
     * @return boolean
     */
    public function removeVariableConfig(VariableConfig $variableConfig)
    {
        $key = array_search($variableConfig, $this->columnConfigs, true);

        if ($key !== false) {
            unset($this->variableConfigs[$key]);

            return true;
        }

        return false;
    }

    /**
     * if no column configs, generate a column config for each variable config
     */
    public function generateColumns()
    {
        if (empty($this->columnConfigs) && is_array($this->variableConfigs) && !empty($this->variableConfigs)) {
            foreach ($this->variableConfigs as $variableConfig) {
                $this->addColumnConfig(new ColumnConfig($variableConfig->getName(), $variableConfig->getVarName()));
            }
        }
    }
}
