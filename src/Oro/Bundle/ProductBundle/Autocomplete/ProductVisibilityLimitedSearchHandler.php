<?php

namespace Oro\Bundle\ProductBundle\Autocomplete;

use Symfony\Component\HttpFoundation\RequestStack;

use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Oro\Bundle\FormBundle\Autocomplete\SearchHandler;
use Oro\Bundle\ProductBundle\Form\Type\ProductSelectType;
use Oro\Bundle\ProductBundle\Entity\Manager\ProductManager;
use Oro\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Oro\Bundle\ProductBundle\Search\ProductRepository as ProductSearchRepository;

class ProductVisibilityLimitedSearchHandler extends SearchHandler
{
    /** @var RequestStack */
    protected $requestStack;

    /** @var  ProductRepository */
    protected $entityRepository;

    /** @var  ProductManager */
    protected $productManager;

    /** @var  FrontendHelper */
    protected $frontendHelper;

    /** @var \Oro\Bundle\ProductBundle\Search\ProductRepository */
    protected $searchRepository;

    /**
     * @param string $entityName
     * @param array $properties
     * @param RequestStack $requestStack
     * @param ProductManager $productManager
     */
    public function __construct(
        $entityName,
        array $properties,
        RequestStack $requestStack,
        ProductManager $productManager
    ) {
        $this->requestStack = $requestStack;
        $this->productManager = $productManager;
        parent::__construct($entityName, $properties);
    }

    /**
     * {@inheritdoc}
     */
    protected function checkAllDependenciesInjected()
    {
        if (!$this->entityRepository || !$this->idFieldName) {
            throw new \RuntimeException('Search handler is not fully configured');
        }
    }

    /**
     * @param FrontendHelper $frontendHelper
     */
    public function setFrontendHelper(FrontendHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }

    /**
     * @param \Oro\Bundle\ProductBundle\Search\ProductRepository $searchRepository
     */
    public function setSearchRepository(ProductSearchRepository $searchRepository)
    {
        $this->searchRepository = $searchRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function searchEntities($search, $firstResult, $maxResults)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request || !$params = $request->get(ProductSelectType::DATA_PARAMETERS)) {
            $params = [];
        }

        if (null === $this->frontendHelper || (false === $this->frontendHelper->isFrontendRequest($request))) {
            return $this->searchEntitiesUsingOrm($search, $firstResult, $maxResults, $params);
        }

        return $this->searchEntitiesUsingIndex($search, $firstResult, $maxResults);
    }

    /**
     * @param $search
     * @param $firstResult
     * @param $maxResults
     * @param $params
     * @return array
     */
    protected function searchEntitiesUsingOrm($search, $firstResult, $maxResults, $params)
    {
        $queryBuilder = $this->entityRepository->getSearchQueryBuilder($search, $firstResult, $maxResults);
        $this->productManager->restrictQueryBuilder($queryBuilder, $params);
        $query = $this->aclHelper->apply($queryBuilder);

        return $query->getResult();
    }

    /**
     * @param $search
     * @param $firstResult
     * @param $maxResults
     * @return \Oro\Bundle\SearchBundle\Query\Result\Item[]
     */
    protected function searchEntitiesUsingIndex($search, $firstResult, $maxResults)
    {
        $searchQuery = $this->searchRepository->getFilterSkuQuery([$search]);
        $searchQuery->setFirstResult($firstResult);
        $searchQuery->setMaxResults($maxResults);
        $this->productManager->restrictSearchQuery($searchQuery->getQuery());
        $result = $searchQuery->getResult();

        return $result->getElements();
    }
}
