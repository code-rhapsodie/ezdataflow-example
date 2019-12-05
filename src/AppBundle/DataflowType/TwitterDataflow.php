<?php
declare(strict_types=1);

namespace AppBundle\DataflowType;

use AppBundle\Service\ContentDeleter;
use AppBundle\Service\RssReader;
use AppBundle\Service\TwitterReader;
use CodeRhapsodie\DataflowBundle\DataflowType\AbstractDataflowType;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder;
use CodeRhapsodie\DataflowBundle\DataflowType\Result;
use CodeRhapsodie\EzDataflowBundle\Factory\ContentStructureFactoryInterface;
use CodeRhapsodie\EzDataflowBundle\Writer\ContentWriter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwitterDataflow extends AbstractDataflowType
{
    /**
     * @var ContentWriter
     */
    private $contentWriter;

    /**
     * @var RssReader
     */
    private $reader;

    /**
     * @var string[]
     */
    private $actualRemoteIds;
    /**
     * @var ContentDeleter
     */
    private $twitDeleter;
    /**
     * @var ContentStructureFactoryInterface
     */
    private $contentStructureFactory;

    /**
     * @var string
     */
    private $remoteIdPrefix;

    public function __construct(ContentWriter $contentWriter, ContentDeleter $twitDeleter, TwitterReader $reader, ContentStructureFactoryInterface $contentStructureFactory)
    {
        $this->contentWriter = $contentWriter;
        $this->reader = $reader;
        $this->actualRemoteIds = [];
        $this->twitDeleter = $twitDeleter;
        $this->contentStructureFactory = $contentStructureFactory;
    }

    protected function buildDataflow(DataflowBuilder $builder, array $options): void
    {
        $this->remoteIdPrefix = sprintf('twit-%s', $options['username']);
        $builder->setReader($this->reader->read($options['username'], $options['max_content']))
            ->addStep(function ($data) use ($options) {
                $newData = [
                    'id' => $data['id'],
                    'text' => $data['text'],
                    'username' => $data['user']['screen_name'],
                    'created_at' => new \DateTime($data['created_at']),
                    'source' => ['keyword'=>'twitter'],
                ];

                return $newData;
            })
            ->addStep(function ($data) use ($options) {

                $remoteId = sprintf('%s-%s', $this->remoteIdPrefix, $data['id']);
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

        $this->twitDeleter->deleteAllExcluding($this->actualRemoteIds, $this->remoteIdPrefix);

        return $result;
    }


    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'username' => null,
            'content_type' => null,
            'parent_location_id' => null,
            'language' => 'eng-GB',
            'max_content' => 10,
        ]);
        $optionsResolver->setRequired(['username', 'content_type', 'parent_location_id']);
    }

    public function getLabel(): string
    {
        return 'Import Twitter';
    }

    public function getAliases(): iterable
    {
        return ['it'];
    }
}
