<?php

declare(strict_types=1);

namespace Auctane\Api\Test\Unit\Model\Action;

use Auctane\Api\Helper\Data;
use Auctane\Api\Model\Action\Export;
use Auctane\Api\Model\Weight;
use Auctane\Api\Model\WeightAdapter;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\GiftMessage\Helper\Message;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

class ExportTest extends TestCase
{
    private const PARENT_AMOUNTS = [
        'base_price' => '20.0000',
        'price' => '25.0000',
        'base_tax_amount' => '1.6000',
        'tax_amount' => '2.0000',
    ];

    public static function priceTypeProvider(): array
    {
        return [
            'base currency' => ['1', 'base_price', 'base_tax_amount'],
            'store currency' => ['0', 'price', 'tax_amount'],
        ];
    }

    public static function configurableChildWithoutTaxProvider(): array
    {
        $cases = [];
        foreach (self::priceTypeProvider() as $mode => [$priceType, $priceKey, $taxKey]) {
            foreach (['zero' => '0.0000', 'null' => null] as $taxLabel => $childTax) {
                $cases["$mode, $taxLabel child tax"] = [$priceType, $priceKey, $taxKey, $childTax];
            }
        }

        return $cases;
    }

    #[DataProvider('configurableChildWithoutTaxProvider')]
    public function testConfigurableChildWithoutTaxUsesParentTaxAmount(
        string $priceType,
        string $priceKey,
        string $taxKey,
        ?string $childTax
    ): void {
        $parent = $this->createItem(['product_type' => 'configurable', 'name' => 'Parent'] + self::PARENT_AMOUNTS);
        $child = $this->createItem([
            'product_type' => 'simple',
            'sku' => 'CHILD-SKU',
            'name' => 'Child',
            'base_price' => '0.0000',
            'price' => '0.0000',
            'base_tax_amount' => $childTax,
            'tax_amount' => $childTax,
        ], $parent);

        $items = $this->exportItems($priceType, [$parent, $child]);

        $this->assertCount(1, $items);
        $this->assertSame('CHILD-SKU', (string)$items[0]->SKU);
        $this->assertSame('Parent', (string)$items[0]->Name);
        $this->assertSame(self::PARENT_AMOUNTS[$priceKey], (string)$items[0]->UnitPrice);
        $this->assertSame(self::PARENT_AMOUNTS[$taxKey], (string)$items[0]->TaxAmount);
    }

    #[DataProvider('priceTypeProvider')]
    public function testConfigurableChildWithOwnTaxKeepsItsTaxAndPrice(
        string $priceType,
        string $priceKey,
        string $taxKey
    ): void {
        $childAmounts = [
            'base_price' => '18.0000',
            'price' => '22.5000',
            'base_tax_amount' => '1.4400',
            'tax_amount' => '1.8000',
        ];
        $parent = $this->createItem(['product_type' => 'configurable', 'name' => 'Parent'] + self::PARENT_AMOUNTS);
        $child = $this->createItem(['product_type' => 'simple', 'sku' => 'CHILD-SKU'] + $childAmounts, $parent);

        $items = $this->exportItems($priceType, [$parent, $child]);

        $this->assertCount(1, $items);
        $this->assertSame($childAmounts[$priceKey], (string)$items[0]->UnitPrice);
        $this->assertSame($childAmounts[$taxKey], (string)$items[0]->TaxAmount);
    }

    #[DataProvider('priceTypeProvider')]
    public function testSimpleItemKeepsItsTaxAndPrice(string $priceType, string $priceKey, string $taxKey): void
    {
        $amounts = [
            'base_price' => '10.0000',
            'price' => '12.5000',
            'base_tax_amount' => '0.8000',
            'tax_amount' => '1.0000',
        ];
        $item = $this->createItem(['product_type' => 'simple', 'sku' => 'SIMPLE-SKU', 'name' => 'Simple'] + $amounts);

        $items = $this->exportItems($priceType, [$item]);

        $this->assertCount(1, $items);
        $this->assertSame('Simple', (string)$items[0]->Name);
        $this->assertSame($amounts[$priceKey], (string)$items[0]->UnitPrice);
        $this->assertSame($amounts[$taxKey], (string)$items[0]->TaxAmount);
    }

    #[DataProvider('priceTypeProvider')]
    public function testSimpleItemWithoutTaxExportsZeroTax(string $priceType, string $priceKey, string $taxKey): void
    {
        $amounts = [
            'base_price' => '10.0000',
            'price' => '12.5000',
            'base_tax_amount' => '0.0000',
            'tax_amount' => '0.0000',
        ];
        $item = $this->createItem(['product_type' => 'simple', 'sku' => 'SIMPLE-SKU'] + $amounts);

        $items = $this->exportItems($priceType, [$item]);

        $this->assertCount(1, $items);
        $this->assertSame($amounts[$priceKey], (string)$items[0]->UnitPrice);
        $this->assertSame($amounts[$taxKey], (string)$items[0]->TaxAmount);
    }

    private function createItem(array $data, ?Item $parent = null): Item
    {
        $item = $this->createStub(Item::class);
        $item->method('getProductType')->willReturn($data['product_type']);
        $item->method('getSku')->willReturn($data['sku'] ?? '');
        $item->method('getName')->willReturn($data['name'] ?? '');
        $item->method('getBasePrice')->willReturn($data['base_price']);
        $item->method('getPrice')->willReturn($data['price']);
        $item->method('getBaseTaxAmount')->willReturn($data['base_tax_amount']);
        $item->method('getTaxAmount')->willReturn($data['tax_amount']);
        $item->method('getIsVirtual')->willReturn(false);
        $item->method('getQtyOrdered')->willReturn(1.0);
        $item->method('getWeight')->willReturn(1.0);
        $item->method('getParentItem')->willReturn($parent);

        return $item;
    }

    /**
     * @param Item[] $orderItems
     * @return SimpleXMLElement[]
     */
    private function exportItems(string $priceType, array $orderItems): array
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            static fn (string $path) => $path === 'shipstation_general/shipstation/export_price' ? $priceType : null
        );

        $weightAdapter = $this->createStub(WeightAdapter::class);
        $weightAdapter->method('toForeignWeight')->willReturn(
            new Weight([Weight::VALUE_KEY => 1.0, Weight::UNIT_KEY => 'Pounds'])
        );

        $export = new Export(
            $this->createStub(CollectionFactory::class),
            $scopeConfig,
            $this->createStub(Config::class),
            $this->createStub(Data::class),
            $this->createStub(Message::class),
            $weightAdapter,
            $this->createStub(RegionCollectionFactory::class),
            $this->createStub(LoggerInterface::class)
        );

        $order = $this->createStub(Order::class);
        $order->method('getItems')->willReturn($orderItems);

        (new \ReflectionMethod(Export::class, '_orderItem'))->invoke($export, $order);
        $xmlData = (new \ReflectionProperty(Export::class, '_xmlData'))->getValue($export);

        $xml = new SimpleXMLElement('<Items>' . $xmlData . '</Items>', LIBXML_NOCDATA);

        return iterator_to_array($xml->Item, false);
    }
}
