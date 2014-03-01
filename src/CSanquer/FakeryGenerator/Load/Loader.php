<?php

namespace CSanquer\FakeryGenerator\Loader;

use \CSanquer\FakeryGenerator\Model\Column;
use \CSanquer\FakeryGenerator\Model\Config;
use \CSanquer\FakeryGenerator\Model\CsvFormat;
use \CSanquer\FakeryGenerator\Model\VariableConfig;

/**
 * Loader
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Loader
{
    /**
     *
     * @param  string $file filename
     * @return Config
     */
    public function loadXmlFakerConfig($file)
    {
        $root = simplexml_load_file($file, '\\CSanquer\\FakeryGenerator\\XML\\CdataSimpleXMLElement', LIBXML_NOCDATA);

        $config = new Config();
        if (isset($root['classname'])) {
            $config->setClassName((string) $root['classname']);
        }
        if (isset($root['fakenumber'])) {
            $config->setFakeNumber((string) $root['fakenumber']);
        }
        if (isset($root['locale'])) {
            $config->setLocale((string) $root['locale']);
        }
        if (isset($root['seed'])) {
            $config->setSeed((string) $root['seed']);
        }

        if (isset($root->formats->format)) {
            $config->setFormats((array) $root->formats->format);
        }

        if (isset($root->formatOptions)) {
            if (isset($root->formatOptions->csv)) {
                $config->setCsvFormat(new CsvFormat(
                    (string) $root->formatOptions->csv->delimiter,
                    (string) $root->formatOptions->csv->enclosure,
                    (string) $root->formatOptions->csv->encoding,
                    (string) $root->formatOptions->csv->eol,
                    (string) $root->formatOptions->csv->escape
                ));
            }
        }

        if (isset($root->variables->variable)) {
            foreach ($root->variables->variable as $variable) {
                $variable = new VariableConfig();
                $variable->setName($variable['name']);
                $variable->setFakerMethod((string) $variable->method);
                $variable->setFakerMethodArg1((string) $variable->argument1);
                $variable->setFakerMethodArg2((string) $variable->argument2);
                $variable->setFakerMethodArg3((string) $variable->argument3);
                $config->addVariableConfig($variable);
            }
        }

        if (isset($root->columns->column)) {
            foreach ($root->columns->column as $column) {
                $column = new Column();
                $column->setName($column['name']);
                $column->setUnique(!empty($column['unique']));
                $column->setValue((string) $column->value);
                $column->setConvertMethod((string) $column->convert);
                $config->addColumn($column);
            }
        }
        return $config;
    }
}
