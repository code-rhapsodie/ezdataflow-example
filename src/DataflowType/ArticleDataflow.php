<?php
declare(strict_types=1);

namespace App\DataflowType;

use CodeRhapsodie\DataflowBundle\DataflowType\AbstractDataflowType;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder;
use CodeRhapsodie\EzDataflowBundle\Model\ContentCreateStructure;
use CodeRhapsodie\EzDataflowBundle\Model\ContentUpdateStructure;
use CodeRhapsodie\EzDataflowBundle\Writer\ContentWriter;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleDataflow extends AbstractDataflowType
{
    public function __construct(private readonly ContentWriter $contentWriter, private readonly ContentService $contentService)
    {
    }

    protected function buildDataflow(DataflowBuilder $builder, array $options): void
    {
        $builder->setReader([
            ['id' => 1, 'title' => 'article 1', 'intro' => 'my great article'],
            ['id' => 2, 'title' => 'article 2', 'intro' => 'my new <b>great</b> article'],
        ])
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
                } catch (NotFoundException) {
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
