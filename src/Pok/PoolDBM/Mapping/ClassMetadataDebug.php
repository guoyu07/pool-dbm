<?php

namespace Pok\PoolDBM\Mapping;

/**
 * ClassMetadataDebug.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ClassMetadataDebug extends ClassMetadata
{
    /**
     * {@inheritDoc}
     */
    public function setIdentifier($manager, $field)
    {
        if (!count($this->getFieldManagerNames())) {
            throw new \RuntimeException('ClassMetadata::setIdentifier must to be call after addModel.');
        }

        parent::setIdentifier($manager, $field);
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldMapping($fieldName)
    {
        if (!isset($this->fieldMappings[$fieldName])) {
            throw MappingException::mappingNotFound($this->name, $fieldName);
        }

        return parent::getFieldMapping($fieldName);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifierValue($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(sprintf('You must flush a object model, you given type "%s".', gettype($model)));
        }

        $method = 'get' . ucfirst($this->identifierField);
        if (!method_exists($model, $method)) {
            throw new \RuntimeException(sprintf('You model "%s" must be implement method "%s".', get_class($model), $method));
        }

        return parent::getIdentifierValue($model);
    }
}