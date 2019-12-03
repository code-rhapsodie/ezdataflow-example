<?php

declare(strict_types=1);

namespace AppBundle\DataflowType\FieldValueCreator;

use CodeRhapsodie\EzDataflowBundle\Core\Field\FieldValueCreatorInterface;
use eZ\Publish\Core\FieldType\Value;
use EzSystems\EzPlatformRichText\eZ\RichText\InputHandlerInterface;
use EzSystems\EzPlatformRichText\eZ\FieldType\RichText\Value as RichTextValue;

class RichTextFieldValueCreator implements FieldValueCreatorInterface
{
    /** @var InputHandlerInterface */
    private $inputHandler;

    /**
     * RichTextFieldValueCreator constructor.
     *
     * @param InputHandlerInterface $inputHandler
     */
    public function __construct(InputHandlerInterface $inputHandler)
    {
        $this->inputHandler = $inputHandler;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezrichtext' === $fieldTypeIdentifier;
    }

    public function createValue(string $fieldTypeIdentifier, $hash): Value
    {
        return new RichTextValue($this->inputHandler->fromString('<?xml version="1.0" encoding="UTF-8"?><section xmlns="http://ez.no/namespaces/ezpublish5/xhtml5/edit">'.$hash.'</section>'));
    }
}
