<?php
declare(strict_types=1);

namespace AppBundle\Controller;


use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\MVC\Symfony\View\View;



class FolderController
{
    public function __invoke(View $view, SearchService $searchService, ContentService $contentService)
    {
        /** @var $view ContentView */
        $query = new LocationQuery([
            'filter' => new LogicalAnd([
                new Query\Criterion\ParentLocationId($view->getContent()->contentInfo->mainLocationId),
                new Query\Criterion\Visibility(Query\Criterion\Visibility::VISIBLE),
            ]),
            'sortClauses' => [new Query\SortClause\Location\Priority(Query::SORT_ASC)]
        ]);
        $childLocation = $searchService->findLocations($query);
        $chidrens = [];
        foreach ($childLocation->searchHits as $searchHit) {
            $chidrens[] = $contentService->loadContentByContentInfo($searchHit->valueObject->contentInfo);
        }
        array_filter($chidrens);
        $view->addParameters(['childrens'=> $chidrens]);

        return $view;
    }
}