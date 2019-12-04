<?php

declare(strict_types=1);

namespace AppBundle\DataflowType\FieldValueCreator;

use CodeRhapsodie\EzDataflowBundle\Core\Field\FieldValueCreatorInterface;
use eZ\Publish\Core\FieldType\Url\Value as UrlValue;
use eZ\Publish\Core\FieldType\Value;

class UrlFieldValueCreator implements FieldValueCreatorInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezurl' === $fieldTypeIdentifier;
    }

    public function createValue(string $fieldTypeIdentifier, $hash): Value
    {
        return new UrlValue($hash);
    }
}
