<?php

declare(strict_types=1);

namespace Auctane\Api\Model;

use Auctane\Api\Api\OrderAddressTimestampRepositoryInterface;
use Auctane\Api\Api\Data\OrderAddressTimestampInterface;
use Auctane\Api\Api\Data\OrderAddressTimestampInterfaceFactory;
use Auctane\Api\Api\Data\OrderAddressTimestampSearchResultsInterface;
use Auctane\Api\Api\Data\OrderAddressTimestampSearchResultsInterfaceFactory;
use Auctane\Api\Model\ResourceModel\OrderAddressTimestamp as OrderAddressTimestampResource;
use Auctane\Api\Model\ResourceModel\OrderAddressTimestamp\CollectionFactory;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\ValidatorException;

class OrderAddressTimestampRepository implements OrderAddressTimestampRepositoryInterface
{
    public function __construct(
        private OrderAddressTimestampResource $resource,
        private OrderAddressTimestampInterfaceFactory $entityFactory,
        private CollectionFactory $entityCollectionFactory,
        private CollectionProcessorInterface $collectionProcessor,
        private OrderAddressTimestampSearchResultsInterfaceFactory $searchResultsFactory,
    ) {
    }

    public function save(OrderAddressTimestampInterface $entity): OrderAddressTimestampInterface
    {
        try {
            $this->resource->save($entity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not save item.'), $e);
        }

        return $entity;
    }

    public function get(int $id): OrderAddressTimestampInterface
    {
        $entity = $this->entityFactory->create();

        $this->resource->load($entity, $id);

        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Could not find item with id: %id.', ['id' => $id]));
        }

        return $entity;
    }

    public function find(int $id): OrderAddressTimestampInterface
    {
        $entity = $this->entityFactory->create();

        $this->resource->load($entity, $id);

        return $entity;
    }

    public function delete(OrderAddressTimestampInterface $entity): bool
    {
        try {
            $this->resource->delete($entity);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('The "%1" item couldn\'t be removed.', $entity->getId()), $e);
        }
        return true;
    }

    public function deleteById(int $id): bool
    {
        $entity = $this->get($id);

        return $this->delete($entity);
    }

    public function getEmptyModel(): OrderAddressTimestampInterface
    {
        return $this->entityFactory->create();
    }

    public function getList(SearchCriteriaInterface $searchCriteria): OrderAddressTimestampSearchResultsInterface
    {
        $collection = $this->entityCollectionFactory->create();

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $this->collectionProcessor->process($searchCriteria, $collection);

        if ($searchCriteria->getPageSize()) {
            $searchResults->setTotalCount($collection->getSize());
        } else {
            $searchResults->setTotalCount(count($collection));
        }

        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
