<?php

declare(strict_types=1);

namespace Auctane\Api\Api;

use Auctane\Api\Api\Data\OrderAddressTimestampInterface;
use Auctane\Api\Api\Data\OrderAddressTimestampSearchResultsInterface;

use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderAddressTimestampRepositoryInterface
{
    /**
     * Save OrderAddressTimestamp
     *
     * @param \Auctane\Api\Api\Data\OrderAddressTimestampInterface $entity
     * @return \Auctane\Api\Api\Data\OrderAddressTimestampInterface
     */
    public function save(OrderAddressTimestampInterface $entity): OrderAddressTimestampInterface;

    /**
     * Get OrderAddressTimestamp by ID
     *
     * @param int $id
     * @return \Auctane\Api\Api\Data\OrderAddressTimestampInterface
     */
    public function get(int $id): OrderAddressTimestampInterface;

    /**
     * Delete OrderAddressTimestamp
     *
     * @param \Auctane\Api\Api\Data\OrderAddressTimestampInterface $entity
     * @return bool
     */
    public function delete(OrderAddressTimestampInterface $entity): bool;

    /**
     * Delete OrderAddressTimestamp by ID
     *
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id): bool;

    /**
     * Get empty model
     *
     * @return \Auctane\Api\Api\Data\OrderAddressTimestampInterface
     */
    public function getEmptyModel(): OrderAddressTimestampInterface;

    /**
     * Get list of OrderAddressTimestamp
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Auctane\Api\Api\Data\OrderAddressTimestampSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderAddressTimestampSearchResultsInterface;
}
