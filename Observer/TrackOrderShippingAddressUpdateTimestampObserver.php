<?php

declare(strict_types=1);

namespace Auctane\Api\Observer;

use Auctane\Api\Api\OrderAddressTimestampRepositoryInterface;

use DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Address;

class TrackOrderShippingAddressUpdateTimestampObserver implements ObserverInterface
{
    public function __construct(
        private OrderAddressTimestampRepositoryInterface $orderAddressTimestampRepository)
    {
    }

    public function execute(Observer $observer)
    {
        /** @var OrderAddressInterface $orderAddress */
        $orderAddress = $observer->getEvent()->getAddress();

        if ($orderAddress->getAddressType() !== Address::TYPE_SHIPPING) {
            return;
        }

        if (!$orderAddress->getEntityId()) {
            return;
        }

        $origData = $orderAddress->getOrigData();
        $newData = $orderAddress->getData();

        if ($origData === $newData) {
            return;
        }

        $timestamp = $this->orderAddressTimestampRepository->find(
            (int) $orderAddress->getEntityId()
        );

        $timestamp->setId((int) $orderAddress->getEntityId());
        $timestamp->setUpdatedAt((new DateTime())->format('Y-m-d H:i:s'));

        $this->orderAddressTimestampRepository->save($timestamp);
    }
}
