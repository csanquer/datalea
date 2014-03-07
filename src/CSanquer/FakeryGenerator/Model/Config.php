<?php

namespace CSanquer\FakeryGenerator\Model;

use CSanquer\ColibriCsv\Dialect;
use CSanquer\FakeryGenerator\Dump\Dumper;
use Faker\Generator;

/**
 * Config
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Config extends ColumnContainer
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
    protected $formats = [];

    /**
     *
     * @var int
     */
    protected $fakeNumber = 0;

    /**
     *
     * @var array of Variable
     */
    protected $variables = [];

    /**
     *
     * @var Dialect
     */
    protected $csvDialect;

    public function __construct()
    {
        parent::__construct([]);
        $this->generateSeed();
        $this->csvDialect = Dialect::createExcelDialect();
    }

    /**
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param  string $locale
     * @return Config
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     *
     * @return Config
     */
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
     * @param  int    $seed
     * @return Config
     */
    public function setSeed($seed)
    {
        $this->seed = (int) $seed;

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
        $res = preg_match('/([a-zA-Z0-9]+)[^a-zA-Z0-9]*$/', $this->className, $matches);

        return $res ? $matches[1] : $this->getClassName(true);
    }

    /**
     *
     * @param  string $className
     * @return Config
     */
    public function setClassName($className)
    {
        $this->className = preg_replace('/[^a-zA-Z0-9_\\\\]/', '', $className);

        return $this;
    }

    /**
     *
     * @param  string $format
     * @return Config
     */
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

    /**
     *
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     *
     * @param  array  $formats
     * @return Config
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getFakeNumber()
    {
        return $this->fakeNumber;
    }

    /**
     *
     * @param  int    $fakeNumber
     * @return Config
     */
    public function setFakeNumber($fakeNumber)
    {
        $this->fakeNumber = (int) $fakeNumber;

        return $this;
    }

    /**
     *
     * @return Dialect
     */
    public function getCsvDialect()
    {
        return $this->csvDialect;
    }

    /**
     *
     * @param  Dialect $csvDialect
     * @return Config
     */
    public function setCsvDialect(Dialect $csvDialect)
    {
        $this->csvDialect = $csvDialect;

        return $this;
    }

    /**
     *
     * @return array of Variable
     */
    public function getVariables()
    {
        return $this->variables;
    }
    
    /**
     *
     * @return int
     */
    public function countVariables()
    {
        return count($this->variables);
    }

    /**
     *
     * @param  string   $name
     * @return Variable
     */
    public function getVariable($name)
    {
        return isset($this->variables[$name]) ? $this->variables[$name] : null ;
    }

    /**
     *
     * @param  array                                  $variables array of Variable
     * @return Config
     */
    public function setVariables($variables)
    {
        foreach ($variables as $variable) {
            $this->addVariable($variable);
        }

        return $this;
    }

    /**
     * @param  Variable $variable
     * @return Config
     */
    public function addVariable(Variable $variable)
    {
        $name = $variable->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('The variable must have a name.');
        }

        $this->variables[$name] = $variable;

        return $this;
    }

    /**
     *
     * @param  Column  $column
     * @return boolean
     */
    public function removeVariable(Variable $variable)
    {
        $key = array_search($variable, $this->variables, true);

        if ($key !== false) {
            unset($this->variables[$key]);

            return true;
        }

        return false;
    }

    /**
     * if no column configs, generate a column config for each variable config
     */
    public function createDefaultColumns()
    {
        if (empty($this->columns) && is_array($this->variables) && !empty($this->variables)) {
            foreach ($this->variables as $variable) {
                $this->addColumn(new Column($variable->getName(), $variable->getVarName()));
            }
        }
    }

    /**
     * 
     * @param Generator $faker
     * @param array $values (by reference)
     */
    public function generateVariableValues(Generator $faker, array &$values)
    {
        foreach ($this->variables as $variable) {
            $variable->generateValue($faker, $values, $this->variables, false, false, true);
        }
    }
    
    /**
     * 
     * @param array $values
     */
    public function generateColumnValues(array $values)
    {
        $data = [];
        foreach ($this->columns as $column) {
            $data[$column->getName()] = $column->replaceVariable($values);
        }
        
        return $data;
    }
}
