<?php

namespace CSanquer\FakeryGenerator\Config;

use CSanquer\FakeryGenerator\Model\Config;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * ConfigSerializer
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class ConfigSerializer
{
    /**
     *
     * @var Serializer
     */
    private $serializer;

    /**
     *
     * @param string $cacheDir
     * @param string $metadataDir
     * @param bool   $debug
     */
    public function __construct($cacheDir, $metadataDir, $debug = false)
    {
        $serializerBuilder = SerializerBuilder::create();

        $serializerBuilder
            ->setCacheDir($cacheDir)
            ->setDebug($debug)
            ->addMetadataDir($metadataDir)
            ;

        $propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $serializerBuilder->setPropertyNamingStrategy($propertyNamingStrategy);

        $serializerBuilder->addDefaultSerializationVisitors();
        $serializerBuilder->addDefaultDeserializationVisitors();

        $jsonSerializationVisitor = new JsonSerializationVisitor($propertyNamingStrategy);
        $jsonSerializationVisitor->setOptions(JSON_PRETTY_PRINT);

        $xmlSerializationVisitor = new XmlSerializationVisitor($propertyNamingStrategy);
        $xmlSerializationVisitor->setDefaultRootName('config');

        $serializerBuilder->setSerializationVisitor('json', $jsonSerializationVisitor);
        $serializerBuilder->setSerializationVisitor('xml', $xmlSerializationVisitor);

        $this->serializer = $serializerBuilder->build();
    }

    /**
     *
     * @param \CSanquer\FakeryGenerator\Model\Config $config
     * @param string                                 $dir
     * @param string                                 $format
     * @return string filename
     */
    public function dump(Config $config, $dir, $format = 'json')
    {
        $format = in_array($format, ['json', 'xml']) ? $format : 'json';

        $serialized = $this->serializer->serialize($config, $format);
        $filename = $dir.'/'.$config->getClassName(true).'_fakery_generator_config_'.date('Y-m-d_H-i-s').'.'.$format;
        file_put_contents($filename, $serialized);

        return $filename;
    }

    /**
     *
     * @param string $filename
     *
     * @return \CSanquer\FakeryGenerator\Model\Config
     *
     * @throws \InvalidArgumentException
     */
    public function load($filename)
    {
        $file = new \SplFileInfo($filename);
        if (!in_array($file->getExtension(), ['json', 'xml'])) {
            throw new \InvalidArgumentException('The config file must be an XML or a JSON file.');
        }

        if (!file_exists($file->getRealPath())) {
            throw new \InvalidArgumentException('The config file must exist.');
        }

        return $this->serializer->deserialize(file_get_contents($file->getRealPath()), 'CSanquer\\FakeryGenerator\\Model\\Config', $file->getExtension());
    }
}
