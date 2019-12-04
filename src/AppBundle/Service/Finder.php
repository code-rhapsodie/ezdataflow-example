<?php

declare(strict_types=1);

namespace AppBundle\Service;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;

class Finder
{
    /** @var Repository */
    private $repository;

    /** @var SearchService */
    private $searchService;

    public function __construct(Repository $repository, SearchService $searchService)
    {
        $this->repository = $repository;
        $this->searchService = $searchService;
    }

    public function findAll(string $contentTypeIdentifier): iterable
    {
        yield from $this->repository->sudo(function () use ($contentTypeIdentifier) {
            yield from $this->doFindAll($contentTypeIdentifier);
        });
    }

    private function doFindAll(string $contentTypeIdentifier): iterable
    {
        $limit = 100;
        $offset = 0;

        while (true) {
            $result = $this->searchService->findContent(new Query([
                'filter' => new Query\Criterion\ContentTypeIdentifier($contentTypeIdentifier),
                'offset' => $offset,
                'limit' => $limit,
                'performCount' => false,
                'sortClauses' => [new Query\SortClause\ContentId(Query::SORT_ASC)],
            ]));

            if (empty($result->searchHits)) {
                break;
            }

            foreach ($result->searchHits as $hit) {
                yield $hit->valueObject;
            }

            $offset += $limit;
        }
    }
}
