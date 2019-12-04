<?php

declare(strict_types=1);

namespace AppBundle\DataflowType\FieldValueCreator;

use CodeRhapsodie\EzDataflowBundle\Core\Field\FieldValueCreatorInterface;
use eZ\Publish\Core\FieldType\DateAndTime\Value as DateAndTimeValue;
use eZ\Publish\Core\FieldType\Value;

class DateTimeFieldValueCreator implements FieldValueCreatorInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezdatetime' === $fieldTypeIdentifier;
    }

    public function createValue(string $fieldTypeIdentifier, $hash): Value
    {
        return DateAndTimeValue::fromTimestamp($hash);
    }
}
