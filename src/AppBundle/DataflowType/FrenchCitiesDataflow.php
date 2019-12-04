<?php
declare(strict_types=1);

namespace AppBundle\DataflowType;

use AppBundle\Service\GeoClient;
use CodeRhapsodie\DataflowBundle\DataflowType\AbstractDataflowType;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder;
use CodeRhapsodie\EzDataflowBundle\Factory\ContentStructureFactory;
use CodeRhapsodie\EzDataflowBundle\Writer\ContentWriter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrenchCitiesDataflow extends AbstractDataflowType
{
    /**
     * @var ContentWriter
     */
    private $contentWriter;

    /**
     * @var ContentStructureFactory
     */
    private $contentStructureFactory;

    /**
     * @var GeoClient
     */
    private $geoClient;

    public function __construct(
        GeoClient $geoClient,
        ContentWriter $contentWriter,
        ContentStructureFactory $contentStructureFactory
    ) {
        $this->geoClient = $geoClient;
        $this->contentWriter = $contentWriter;
        $this->contentStructureFactory = $contentStructureFactory;
    }

    protected function buildDataflow(DataflowBuilder $builder, array $options): void
    {
        $builder->setReader($this->geoClient->read($options['url']))
            // This step is a filter
            ->addStep(function ($data) {
                if (empty($data['codesPostaux']) || empty($data['population'])) {
                    //reject data
                    return false;
                }

                return $data;
            })
            // This step transform the data in content structure
            ->addStep(function ($data) use ($options) {
                $remoteId = sprintf('french-city-%d', $data['code']);

                unset($data['code'], $data['centre']);

                $data['codesPostaux'] = implode(',', $data['codesPostaux']);

                return $this->contentStructureFactory->transform(
                    $data,
                    $remoteId,
                    $options['language'],
                    $options['content_type'],
                    $options['parent_location_id'],
                    ContentStructureFactory::MODE_INSERT_ONLY
                );
            })
            ->addWriter($this->contentWriter);
    }

    public function getLabel(): string
    {
        return 'French cities';
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

    public function getAliases(): iterable
    {
        return ['fc'];
    }
}
