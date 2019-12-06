<?php
declare(use_strict=1);

namespace AppBundle\Controller;


use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\MVC\Symfony\View\View;

class HomeController
{
    public function __invoke(View $view, SearchService $searchService, ContentService $contentService)
    {
        /** @var ContentView $view */
        $query = new LocationQuery([
            'filter' => new LogicalAnd([
                new Query\Criterion\ParentLocationId(2),
                new Query\Criterion\ContentTypeIdentifier('folder'),
                new Query\Criterion\Visibility(Query\Criterion\Visibility::VISIBLE),
            ]),
            'sortClauses' => [new Query\SortClause\Location\Priority(Query::SORT_ASC)]
        ]);
        $models = $searchService->findLocations($query);
        $listes = [];
        foreach ($models->searchHits as $hit) {
            $listes[] = $contentService->loadContentByContentInfo($hit->valueObject->contentInfo);
        }
        $view->addParameters(['childrens'=> $listes]);
        return $view;
    }
}
