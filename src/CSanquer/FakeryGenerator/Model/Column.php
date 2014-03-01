<?php

namespace CSanquer\FakeryGenerator\Model;

use CSanquer\FakeryGenerator\Helper\Converter;
use Faker\Generator;

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
     * @param string $name          default = null
     * @param string $value         default = null
     * @param string $convertMethod default = null
     */
    public function __construct($name = null, $value = null, $convertMethod = null)
    {
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
     * @return array
     */
    public function getUsedVariables()
    {
        if (empty($this->usedVariables)) {
            if (preg_match_all('/%([a-zA-Z0-9_]+)%/', $this->getValue(), $matches, PREG_PATTERN_ORDER)) {
                if (isset($matches[1])) {
                    $this->usedVariables = $matches[1];
                }
            }
        }

        return $this->usedVariables;
    }
    
    /**
     *
     * @param Generator $faker
     * @param array            $values          generated value will be inserted into this array
     * @param array            $variables other variable configs to be replaced in faker method arguments if used
     */
    public function generateValues(Generator $faker, array &$values, array $variables = array())
    {
        $usedVariables = $this->getUsedVariables();


        foreach ($usedVariables as $usedVariable) {
            if (isset($variables[$usedVariable])) {
                $variables[$usedVariable]->generateValue($faker, $values, $variables);
            }
        }

        var_dump($columnValue);
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

        switch ($this->getConvertMethod()) {
            case 'lowercase':
                $value = Converter::tolower($value, 'UTF-8');
                break;
            case 'uppercase':
                $value = Converter::toupper($value, 'UTF-8');
                break;
            case 'capitalize':
                $value = Converter::ucfirst($value);
                break;
            case 'capitalize_words':
                $value = Converter::ucwords($value);
                break;
            case 'absolute':
                $value = abs($value);
                break;
            case 'remove_accents':
                $value = Converter::removeAccents($value);
                break;
            case 'remove_accents_lowercase':
                $value = Converter::tolower(Converter::removeAccents($value), 'UTF-8');
                break;
            case 'remove_accents_uppercase':
                $value = Converter::toupper(Converter::removeAccents($value), 'UTF-8');
                break;
            case 'remove_accents_capitalize':
                $value = Converter::ucfirst(Converter::removeAccents($value));
                break;
            case 'remove_accents_capitalize_words':
                $value = Converter::ucwords(Converter::removeAccents($value));
                break;
            case 'as_bool':
                $value = (bool) $value;
                break;
            case 'as_int':
                $value = (int) $value;
                break;
            case 'as_float':
                $value = (float) $value;
                break;
            case 'as_string':
                $value = (string) $value;
                break;
            default:
                break;
        }

        return $value;
    }
}
