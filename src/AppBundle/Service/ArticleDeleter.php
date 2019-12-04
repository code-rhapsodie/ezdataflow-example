<?php


namespace AppBundle\Service;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;

class ArticleDeleter
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

    public function __construct(ContentService $contentService, Repository $repository, Finder $finder)
    {
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->finder = $finder;
    }

    public function deleteAllExcluding(array $remoteIdExcluded): void {
        $this->repository->sudo(function() use ($remoteIdExcluded) {
            foreach ($this->finder->findAll('short_news') as $content) {
                if (in_array($content->contentInfo->remoteId, $remoteIdExcluded)) {
                    continue;
                }

                $this->contentService->deleteContent($content->contentInfo);
            }
        });

    }
}