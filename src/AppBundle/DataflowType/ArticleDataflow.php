<?php
declare(strict_types=1);

namespace AppBundle\DataflowType;

use CodeRhapsodie\DataflowBundle\DataflowType\AbstractDataflowType;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder;
use CodeRhapsodie\EzDataflowBundle\Model\ContentCreateStructure;
use CodeRhapsodie\EzDataflowBundle\Model\ContentUpdateStructure;
use CodeRhapsodie\EzDataflowBundle\Writer\ContentWriter;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
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

    public function __construct(ContentWriter $contentWriter, ContentService $contentService)
    {
        $this->contentWriter = $contentWriter;
        $this->contentService = $contentService;
    }

    protected function buildDataflow(DataflowBuilder $builder, array $options): void
    {
        $builder->setReader()
            ->addStep(function ($data) {
                if (!isset($data['id'])) {
                    return false;
                }

                $remoteId = sprintf('article-%d', $data['id']);

                unset($data['id']);

                $parent_location_for_new_article = 54;

                $lang = 'eng-GB';
                try {
                    $content = $this->contentService->loadContentByRemoteId($remoteId);
                    $struct = ContentUpdateStructure::createForContentId($content->id, $lang, $data);
                } catch (NotFoundException $e) {
                    $struct = new ContentCreateStructure(
                        'article2',
                        $lang,
                        [$parent_location_for_new_article],
                        $data,
                        $remoteId
                    );
                }

                return $struct;
            })
            ->addWriter($this->contentWriter);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(['option1' => 1]);
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
