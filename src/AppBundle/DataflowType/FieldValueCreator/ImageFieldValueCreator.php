<?php

declare(strict_types=1);

namespace AppBundle\DataflowType\FieldValueCreator;

use CodeRhapsodie\EzDataflowBundle\Core\Field\FieldValueCreatorInterface;
use eZ\Publish\Core\FieldType\Image\Type;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\Value;
use Symfony\Component\Mime\MimeTypes;

class ImageFieldValueCreator implements FieldValueCreatorInterface
{
    /**
     * @var Type
     */
    private $imageType;

    public function __construct(Type $imageType)
    {
        $this->imageType = $imageType;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezimage' === $fieldTypeIdentifier;
    }

    public function createValue(string $fieldTypeIdentifier, $hash): Value
    {
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($hash);
        if (0 !== strpos($mimeType, 'image/')) {
            return $this->imageType->getEmptyValue();
        }

        register_shutdown_function(function () use ($hash) {
            unlink($hash);
        });

        return new ImageValue(['inputUri' => $hash, 'fileName' => 'IMG-'.uniqid().'.'.($mimeTypes->getExtensions($mimeType)[0])]);
    }
}
