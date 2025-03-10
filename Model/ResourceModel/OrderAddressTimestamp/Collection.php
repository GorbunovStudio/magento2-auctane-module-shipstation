<?php

declare(strict_types=1);

namespace Auctane\Api\Model\ResourceModel\OrderAddressTimestamp;

use Auctane\Api\Model\OrderAddressTimestamp as Model;
use Auctane\Api\Model\ResourceModel\OrderAddressTimestamp as ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_eventPrefix = 'auctane_shipstation_order_address_timestamp_collection';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
