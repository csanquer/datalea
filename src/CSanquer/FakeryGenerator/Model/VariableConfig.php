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
        return array_key_exists($order, $this->methodArguments) && $this->methodArguments[$order] !== null && $this->methodArguments[$order] !== '';
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

     /**
     *
     * @param \Faker\Generator $faker
     * @param array            $values          generated value will be inserted into this array
     * @param array            $variableConfigs other variable configs to be replaced in faker method arguments if used
     * @param bool             $force           force generating value even if it already exists
     * @param bool             $useIncrement    use increment suffix or add increment
     * @param bool             $resetIncrement  reset current variable increment
     */
    public function generateValue(Generator $faker, array &$values, array $variableConfigs = array(), $force = false, $useIncrement = false, $resetIncrement = false)
    {
        if ($resetIncrement) {
            $this->increment = 0;
        }

        if (!isset($values[$this->getName()]) || $force) {
            $value = $this->generate($faker, $values, $variableConfigs);
            if ($useIncrement) {
                $this->increment++;
                $value = is_numeric($value) ? $value+1 : $value.'_'.$this->increment;
            }
            $values[$this->getName()] = $value;
        }
    }

    /**
     *
     * @param  \Faker\Generator $faker
     * @param  array            $values          generated value will be inserted into this array
     * @param  array            $variableConfigs other variable configs to be replaced in faker method arguments if used
     * @return string
     */
    protected function generate(Generator $faker, array &$values, array $variableConfigs = array())
    {
        $method = $this->getFakerMethod();

        $args = array();

        switch ($method) {
            case 'randomElement':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = array_map(
                        'trim',
                        explode(
                            ',',
                            $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs)
                        )
                    );

                    if ($this->hasFakerMethodArg2()) {
                        $args[] = $this->replaceVariables($this->getFakerMethodArg2(), $faker, $values, $variableConfigs);
                        if ($this->hasFakerMethodArg3()) {
                            $args[] = $this->replaceVariables($this->getFakerMethodArg3(), $faker, $values, $variableConfigs);
                        }
                    }
                }

                $value = call_user_func_array(array($faker, $method), $args);
                break;

            case 'dateTime':
            case 'dateTimeAD':
            case 'dateTimeThisCentury':
            case 'dateTimeThisDecade':
            case 'dateTimeThisYear':
            case 'dateTimeThisMonth':
                if ($this->hasFakerMethodArg1()) {
                    $format = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                }

                $format = empty($format) ? 'Y-m-d H:i:s' : $format;
                $datetime = call_user_func_array(array($faker, $method), $args);
                $value = $datetime->format($format);
                break;

            case 'dateTimeBetween':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                    if ($this->hasFakerMethodArg2()) {
                        $args[] = $this->replaceVariables($this->getFakerMethodArg2(), $faker, $values, $variableConfigs);
                        if ($this->hasFakerMethodArg3()) {
                            $format = $this->replaceVariables($this->getFakerMethodArg3(), $faker, $values, $variableConfigs);
                        }
                    }
                }

                $format = empty($format) ? 'Y-m-d H:i:s' : $format;
                $datetime = call_user_func_array(array($faker, $method), $args);
                $value = $datetime->format($format);
                break;

            case 'words':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                }

                $value = implode(' ', call_user_func_array(array($faker, $method), $args));
                break;
            case 'sentences':
            case 'paragraphs':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                }

                $value = implode("\n", call_user_func_array(array($faker, $method), $args));
                break;

            default:
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                    if ($this->hasFakerMethodArg2()) {
                        $args[] = $this->replaceVariables($this->getFakerMethodArg2(), $faker, $values, $variableConfigs);
                        if ($this->hasFakerMethodArg3()) {
                            $args[] = $this->replaceVariables($this->getFakerMethodArg3(), $faker, $values, $variableConfigs);
                        }
                    }
                }

                try {
                    $value = call_user_func_array(array($faker, $method), $args);
                } catch (\InvalidArgumentException $e) {
                    // if the method doesn't exists in Faker set an empty string as value
                    $value = '';
                }
                break;
        }

        return $value;
    }

    /**
     * replace variable in faker method arguments
     *
     * @param  string           $str
     * @param  \Faker\Generator $faker
     * @param  array            $values
     * @param  array            $variableConfigs
     * @return string
     */
    protected function replaceVariables($str, Generator $faker, array &$values, array $variableConfigs = array())
    {
        return preg_replace_callback('/%([a-zA-Z0-9_]+)%/',
            function($matches) use (&$values) {
                if (!isset($values[$matches[1]]) && isset($variableConfigs[$matches[1]])) {
                    $variableConfigs[$matches[1]]->generateValue($faker, $values, $variableConfigs);
                }

                return isset($values[$matches[1]]) ? $values[$matches[1]] : $matches[0];
            },
            $str
        );
    }
    
}
