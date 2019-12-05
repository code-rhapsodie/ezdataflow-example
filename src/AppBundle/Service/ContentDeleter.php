<?php


namespace AppBundle\Service;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;

class ContentDeleter
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var ContentService
     */
    private $contentService;
    /**
     * @var Finder
     */
    private $finder;
    /**
     * @var string
     */
    private $contentTypeIdentifier;

    public function __construct(ContentService $contentService, Repository $repository, Finder $finder, string $contentTypeIdentifier)
    {
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->finder = $finder;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    public function deleteAllExcluding(array $remoteIdExcluded, ?string $remoteIdPrefix = null): void
    {
        $this->repository->sudo(function () use ($remoteIdExcluded, $remoteIdPrefix) {
            foreach ($this->finder->findAll($this->contentTypeIdentifier) as $content) {
                if (($remoteIdPrefix !== null && 0 !== strpos($content->contentInfo->remoteId, $remoteIdPrefix)) || in_array($content->contentInfo->remoteId, $remoteIdExcluded)) {
                    continue;
                }
                $this->contentService->deleteContent($content->contentInfo);
            }
        });

    }
}