<?php

namespace CSanquer\FakeryGenerator\Model;

use Faker\Generator;

/**
 * Variable
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class Variable
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
     * @var int|null
     */
    protected $optional;

    /**
     *
     * @var int
     */
    protected $increment = 0;
    
    /**
     *
     * @var \DateTime
     */
    protected $maxTimestamp;

    /**
     *
     * @param string $name
     * @param string $method
     * @param array  $methodArguments
     * @param bool   $unique
     * @param float|bool $optional
     * @param \DateTime|int|string $maxTimestamp
     */
    public function __construct($name = null, $method = null, array $methodArguments = [], $unique = false, $optional = false, $maxTimestamp = 'now')
    {
        $this->setName($name);
        $this->setMethod($method);
        $this->setMethodArguments($methodArguments);
        $this->setUnique($unique);
        $this->setOptional($optional);
        $this->setMaxTimestamp($maxTimestamp);
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
     * @param  string                                   $name
     * @return \CSanquer\FakeryGenerator\Model\Variable
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
     * @param  string                                   $method
     * @return \CSanquer\FakeryGenerator\Model\Variable
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

    public function getOptional()
    {
        return $this->optional;
    }

    public function setOptional($optional)
    {
        $this->optional = null;

        if ($optional === true) {
            $optional = 0.5;
        }

        if (is_numeric($optional)) {
            if ($optional > 1.0) {
                $optional = 1.0;
            }
            $this->optional = (float) $optional;
        }

        return $this;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getMaxTimestamp()
    {
        return $this->maxTimestamp;
    }

    /**
     * 
     * @param \DateTime|int|string $maxTimestamp $maxTimestamp default = 'now'
     * @return \CSanquer\FakeryGenerator\Model\Variable
     */
    public function setMaxTimestamp($maxTimestamp = 'now')
    {
        $this->maxTimestamp = $maxTimestamp instanceof \DateTime ? $maxTimestamp : new \DateTime($maxTimestamp);
        
        return $this;
    }

        /**
     *
     * @param \Faker\Generator $faker
     * @param array            $values         generated value will be inserted into this array
     * @param array            $variables      other variable configs to be replaced in faker method arguments if used
     * @param bool             $force          force generating value even if it already exists
     * @param bool             $useIncrement   use increment suffix or add increment
     * @param bool             $resetIncrement reset current variable increment
     */
    public function generateValue(Generator $faker, array &$values, array $variables = [], $force = false, $useIncrement = false, $resetIncrement = false)
    {
        if ($resetIncrement) {
            $this->increment = 0;
        }

        if (!isset($values[$this->getName()]) || $force) {
            $value = $this->generate($faker, $values, $variables);
            if ($useIncrement) {
                $this->increment++;
                $value['raw'] = is_numeric($value['raw']) ? $value['raw']+1 : $value['raw'].'_'.$this->increment;
                $value['flat'] = is_numeric($value['flat']) ? $value['flat']+1 : $value['flat'].'_'.$this->increment;
            }
            $values[$this->getName()] = $value;
        }
    }

    /**
     *
     * @param  \Faker\Generator $faker
     * @param  array            $values    generated value will be inserted into this array
     * @param  array            $variables other variable configs to be replaced in faker method arguments if used
     * @return string
     */
    protected function generate(Generator $faker, array &$values, array $variables = [])
    {
        try {
            $method = $this->getMethod();

            // prepare arguments , replace variables with generated random values
            $args = [];
            foreach ($this->getMethodArguments() as $methodArgument) {
                $args[] = $this->replaceVariables($methodArgument, $faker, $values, $variables);
            }

            $dateTimeFormat = 'Y-m-d H:i:s';
            $arraySeparator = ',';

            // apply specific process and generate random value
            switch ($method) {
                case 'randomElement':
                    if (isset($args[0])) {
                        $args[0] = array_map('trim', explode(',', $args[0]));
                    }
                    break;

                case 'date':
                    if (empty($args[0])) {
                        $args[0] = 'Y-m-d';
                    }
                    
                    if (empty($args[1]) || $args[1] == 'now') {
                        $args[1] = $this->maxTimestamp->format('Y-m-d H:i:s');
                    }
                    break;
                    
                case 'time':
                    if (empty($args[0])) {
                        $args[0] = 'H:i:s';
                    }
                    
                    if (empty($args[1]) || $args[1] == 'now') {
                        $args[1] = $this->maxTimestamp->format('Y-m-d H:i:s');
                    }
                    break;
                    
                case 'dateTime':
                case 'dateTimeAD':
                case 'dateTimeThisCentury':
                case 'dateTimeThisDecade':
                case 'dateTimeThisYear':
                case 'dateTimeThisMonth':
                    // first arg is the datetime format (not a real Faker method argument)
                    $format = array_shift($args);
                    if (!empty($format)) {
                        $dateTimeFormat = $format;
                    }
                    
                case 'unixTime':
                case 'iso8601':
                case 'amPm':
                case 'dayOfMonth':
                case 'dayOfWeek':
                case 'month':
                case 'monthName':
                case 'year':
                    if (empty($args[0]) || $args[0] == 'now') {
                        $args[0] = $this->maxTimestamp->format('Y-m-d H:i:s');
                    }
                    break;
                    
                case 'dateTimeBetween':
                    // first arg is the datetime format (not a real Faker method argument)
                    $format = array_shift($args);
                    if (!empty($format)) {
                        $dateTimeFormat = $format;
                    }
                    
                    if (empty($args[0])) {
                        $args[0] = '-30 years';
                    }
                    
                    if (empty($args[1]) || $args[1] == 'now') {
                        $args[1] = $this->maxTimestamp->format('Y-m-d H:i:s');
                    }
                    break;
                    
                case 'words':
                    $arraySeparator = ' ';
                    break;
                case 'creditCardDetails';
                case 'rgbColorAsArray';
                    $arraySeparator = ',';
                    break;
                case 'sentences':
                case 'paragraphs':
                    $arraySeparator = "\n";
                    break;

                default:
            }

            $generator = $faker;

            // chain generator modifiers
            if ($this->isUnique()) {
                $generator = $generator->unique();
            }

            if ($this->getOptional() !== null) {
                $generator = $generator->optional($this->getOptional());
            }

            // generate value
            $raw = call_user_func_array([$generator, $method], $args);

            // format value
            if ($raw instanceof \DateTime) {
                $flat = $raw->format($dateTimeFormat);
            } elseif (is_array($raw)) {
                $flat = implode($arraySeparator, $raw);
            } else {
                $flat = $raw;
            }

        } catch (\InvalidArgumentException $e) {
            // if the method doesn't exists in Faker set an empty string as value
            $raw = null;
            $flat = '';
        }

        return [
            'raw' => $raw,
            'flat' => $flat,
        ];
    }

    /**
     * replace variable in faker method arguments
     *
     * @param  string           $str
     * @param  \Faker\Generator $faker
     * @param  array            $values
     * @param  array            $variables
     * @return string
     */
    protected function replaceVariables($str, Generator $faker, array &$values, array $variables = [])
    {
        return preg_replace_callback('/%([a-zA-Z0-9_]+)%/',
            function($matches) use (&$values, $faker, $variables) {
                if (!isset($values[$matches[1]]) && isset($variables[$matches[1]])) {
                    $variables[$matches[1]]->generateValue($faker, $values, $variables);
                }

                return isset($values[$matches[1]]['flat']) ? $values[$matches[1]]['flat'] : $matches[0];
            },
            $str
        );
    }

}
