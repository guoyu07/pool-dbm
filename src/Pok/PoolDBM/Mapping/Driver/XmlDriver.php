<?php

namespace Pok\PoolDBM\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;

/**
 * XmlDriver.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class XmlDriver extends FileDriver
{
    const DEFAULT_FILE_EXTENSION = '.multi.xml';

    /**
     * {@inheritDoc}
     */
    public function __construct($locator, $fileExtension = self::DEFAULT_FILE_EXTENSION)
    {
        parent::__construct($locator, $fileExtension);
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ClassMetadata $class)
    {
        /* @var \Pok\PoolDBM\Mapping\ClassMetadata $class */
        /* @var $xmlRoot SimpleXMLElement */
        $xmlRoot = $this->getElement($className);
        if (!$xmlRoot) {
            return;
        }

        if (isset($xmlRoot['repository-class'])) {
            $class->setCustomRepositoryClass((string) $xmlRoot['repository-class']);
        }

        foreach ($xmlRoot->model as $model) {
            $this->addModel($class, $model);
        }

        // mandatory, after register models
        $this->setModelReference($class, $xmlRoot->{'model-reference'});
    }

    /**
     * @param ClassMetadata     $class
     * @param \SimpleXMLElement $reference
     */
    protected function setModelReference(\Pok\PoolDBM\Mapping\ClassMetadata $class, \SimpleXMLElement $reference)
    {
        $parameters = $reference->attributes();

        $class->setIdentifier((string) $parameters['manager'], (string) $parameters['field']);
    }

    /**
     * @param ClassMetadata     $class
     * @param \SimpleXMLElement $model
     *
     * @throws \InvalidArgumentException When the definition cannot be parsed
     */
    protected function addModel(ClassMetadata $class, \SimpleXMLElement $model)
    {
        $fields = array();

        foreach ($model as $field) {
            if ('field' === $field->getName()) {
                $fields[] = (string) $field['name'];
            } else {
                throw new \InvalidArgumentException(sprintf('Unable to parse tag "%s"', $field->getName()));
            }
        }

        $class->addModel((string) $model['manager'], (string) $model['name'], $fields, (isset($model['repository-method'])? (string) $model['repository-method'] : null));
    }

    /**
     * Validates a loaded XML file.
     *
     * @param \DOMDocument $dom A loaded XML file
     *
     * @throws \InvalidArgumentException When XML doesn't validate its XSD schema
     */
    protected function validate(\DOMDocument $dom)
    {
        $location = __DIR__.'/schema/multi-1.0.xsd';

        $current = libxml_use_internal_errors(true);
        libxml_clear_errors();

        if (!$dom->schemaValidate($location)) {
            throw new \InvalidArgumentException(implode("\n", $this->getXmlErrors($current)));
        }
        libxml_use_internal_errors($current);
    }

    /**
     * {@inheritDoc}
     */
    protected function loadMappingFile($file)
    {
        $xmlElement = simplexml_load_file($file);

        return array((string) $xmlElement['model'] => $xmlElement);
    }
}
