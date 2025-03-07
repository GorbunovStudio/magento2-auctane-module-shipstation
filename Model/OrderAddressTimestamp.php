<?php

declare(strict_types=1);

namespace Auctane\Api\Model;

use Auctane\Api\Api\Data\OrderAddressTimestampInterface;
use Auctane\Api\Model\ResourceModel\OrderAddressTimestamp as Resource;

use Magento\Framework\Model\AbstractModel;

class OrderAddressTimestamp extends AbstractModel implements OrderAddressTimestampInterface
{
    const CACHE_TAG = 'auctane_shipstation_order_address_timestamp';

    protected $_eventPrefix = 'auctane_shipstation_order_address_timestamp';

    protected function _construct()
    {
        $this->_cacheTag = self::CACHE_TAG;
        $this->_init(Resource::class);
    }

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        $value = parent::getId();

        if ($value === null) {
            return null;
        }

        return (int) $value;
    }

    /**
     * Set entity ID
     *
     * @param int $value
     * @return $this
     */
    public function setId(int $value): self
    {
        return parent::setId($value);
    }

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt(string $value): self
    {
        $this->setData(self::UPDATED_AT, $value);

        return $this;
    }
}
