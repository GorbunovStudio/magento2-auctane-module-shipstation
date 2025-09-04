<?php

declare(strict_types=1);

namespace Auctane\Api\Model;

use Auctane\Api\Data\OrderAddressTimestampInterface;
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

    /** @return string[] */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        $value = parent::getId();

        if ($value === null) {
            return null;
        }

        return (int) $value;
    }

    public function setId($value)
    {
        return parent::setId($value);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(string $value): self
    {
        $this->setData(self::UPDATED_AT, $value);

        return $this;
    }
}
