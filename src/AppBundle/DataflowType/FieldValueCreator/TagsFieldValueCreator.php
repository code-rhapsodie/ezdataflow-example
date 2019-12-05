<?php

declare(strict_types=1);

namespace AppBundle\DataflowType\FieldValueCreator;

use CodeRhapsodie\EzDataflowBundle\Core\Field\FieldValueCreatorInterface;
use Kaliop\eZMigrationBundle\Core\Matcher\TagMatcher;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\FieldType\Tags\Type;
use Netgen\TagsBundle\Core\FieldType\Tags\Value as TagsValue;
use eZ\Publish\Core\FieldType\Value;

class TagsFieldValueCreator implements FieldValueCreatorInterface
{
    /**
     * @var Type
     */
    private $type;
    /**
     * @var TagMatcher
     */
    private $tagMatcher;

    public function __construct(Type $type, TagMatcher $tagMatcher)
    {
        $this->type = $type;
        $this->tagMatcher = $tagMatcher;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'eztags' === $fieldTypeIdentifier;
    }

    /**
     * Expected $hash content format :
     * array(
     *    'keyword'   => 'my key word',
     *    'remote_id' => 'keyword_remote_id',
     *    'id'        => 'keyword_id',
     * )
     *
     */
    public function createValue(string $fieldTypeIdentifier, $hash): Value
    {
        if (!is_array($hash)) {
            return $this->type->getEmptyValue();
        }
        $tags = [];
        foreach ($hash as $type => $tagIdentifier) {

            foreach ($this->tagMatcher->match([$type=>$tagIdentifier]) as $id => $tag) {
                $tags[$id] = $tag;
            }
        }
        return new TagsValue(array_values($tags));
    }
}
