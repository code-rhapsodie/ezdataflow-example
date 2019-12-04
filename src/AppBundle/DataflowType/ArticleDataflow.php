<?php
declare(strict_types=1);

namespace AppBundle\DataflowType;

use AppBundle\Service\ArticleDeleter;
use AppBundle\Service\RssReader;
use Cloudinary\Search;
use CodeRhapsodie\DataflowBundle\DataflowType\AbstractDataflowType;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder;
use CodeRhapsodie\DataflowBundle\DataflowType\Result;
use CodeRhapsodie\EzDataflowBundle\Factory\ContentStructureFactoryInterface;
use CodeRhapsodie\EzDataflowBundle\Model\ContentCreateStructure;
use CodeRhapsodie\EzDataflowBundle\Model\ContentUpdateStructure;
use CodeRhapsodie\EzDataflowBundle\Writer\ContentWriter;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\Repository\SearchService;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleDataflow extends AbstractDataflowType
{
    /**
     * @var ContentWriter
     */
    private $contentWriter;

    /**
     * @var ContentService
     */
    private $contentService;
    /**
     * @var RssReader
     */
    private $reader;

    /**
     * @var string[]
     */
    private $actualRemoteIds;
    /**
     * @var ArticleDeleter
     */
    private $articleDeleter;
    /**
     * @var ContentStructureFactoryInterface
     */
    private $contentStructureFactory;

    public function __construct(ContentWriter $contentWriter, ContentService $contentService, ArticleDeleter $articleDeleter, RssReader $reader, ContentStructureFactoryInterface $contentStructureFactory)
    {
        $this->contentWriter = $contentWriter;
        $this->contentService = $contentService;
        $this->reader = $reader;
        $this->actualRemoteIds = [];
        $this->articleDeleter = $articleDeleter;
        $this->contentStructureFactory = $contentStructureFactory;
    }

    protected function buildDataflow(DataflowBuilder $builder, array $options): void
    {
        $builder->setReader($this->reader->read($options['url']))
            ->addStep(static function ($item) {
                /** @var $item \SimplePie_Item */
                $data = [
                    'id' => $item->get_id(true),
                    'title' => $item->get_title(),
                    'description' => $item->get_description(),
                    'original_url' => $item->get_link(),
                    'published_date' => $item->get_date('U'),
                    'image' => $item->image_path, // image property is dynamically added by the reader.
                ];
                return $data;
            })->addStep(function ($data) use ($options) {

                $remoteId = sprintf('article-%s', $data['id']);
                $this->actualRemoteIds[] = $remoteId;
                unset($data['id']);

                return $this->contentStructureFactory->transform(
                    $data,
                    $remoteId,
                    $options['language'],
                    $options['content_type'],
                    $options['parent_location_id'],
                    ContentStructureFactoryInterface::MODE_INSERT_ONLY
                );
            })
            ->addWriter($this->contentWriter);
    }

    public function process(array $options): Result
    {
        $result = parent::process($options);

        $this->articleDeleter->deleteAllExcluding($this->actualRemoteIds);

        return $result;
    }


    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'url' => null,
            'content_type' => null,
            'parent_location_id' => null,
            'language' => 'eng-GB',
        ]);
        $optionsResolver->setRequired(['url', 'content_type', 'parent_location_id']);
    }

    public function getLabel(): string
    {
        return 'Import article';
    }

    public function getAliases(): iterable
    {
        return ['ia'];
    }
}
