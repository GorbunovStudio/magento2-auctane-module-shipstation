<?php

declare(strict_types=1);

namespace Auctane\Api\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OrderAddressTimestamp extends AbstractDb
{
    protected $_isPkAutoIncrement = false;

    protected function _construct()
    {
        $this->_init('auctane_shipstation_order_address_timestamp', 'entity_id');
    }
}
