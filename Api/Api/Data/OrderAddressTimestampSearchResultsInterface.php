<?php

declare(strict_types=1);

namespace Auctane\Api\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderAddressTimestampSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of OrderAddressTimestamp items
     *
     * @return \Auctane\Api\Api\Data\OrderAddressTimestampInterface[]
     */
    public function getItems();

    /**
     * Set list of OrderAddressTimestamp items
     *
     * @param \Auctane\Api\Api\Data\OrderAddressTimestampInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
