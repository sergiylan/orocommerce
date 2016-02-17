<?php

namespace OroB2B\Bundle\PricingBundle\Tests\Unit\Layout\DataProvider;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Oro\Component\Layout\LayoutContext;
use Oro\Component\Testing\Unit\EntityTrait;

use OroB2B\Bundle\PricingBundle\Entity\PriceList;
use OroB2B\Bundle\PricingBundle\Model\FrontendPriceListRequestHandler;
use OroB2B\Bundle\PricingBundle\Provider\UserCurrencyProvider;
use OroB2B\Bundle\ProductBundle\Entity\Product;
use OroB2B\Bundle\ShoppingListBundle\Entity\LineItem;
use OroB2B\Bundle\ShoppingListBundle\Entity\ShoppingList;
use OroB2B\Bundle\ShoppingListBundle\Layout\DataProvider\FrontendShoppingListProductsUnitsDataProvider;

class FrontendShoppingListProductsUnitsDataProviderTest extends \PHPUnit_Framework_TestCase
{
    use EntityTrait;

    const TEST_CURRENCY = 'USD';

    /**
     * @var FrontendShoppingListProductsUnitsDataProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Registry
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FrontendPriceListRequestHandler
     */
    protected $requestHandler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UserCurrencyProvider
     */
    protected $userCurrencyProvider;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestHandler = $this
            ->getMockBuilder('OroB2B\Bundle\PricingBundle\Model\FrontendPriceListRequestHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->userCurrencyProvider = $this->getMockBuilder('OroB2B\Bundle\PricingBundle\Provider\UserCurrencyProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->provider = new FrontendShoppingListProductsUnitsDataProvider(
            $this->registry,
            $this->requestHandler,
            $this->userCurrencyProvider
        );
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetIdentifier()
    {
        $this->provider->getIdentifier();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Undefined data item index: shoppingList.
     */
    public function testGetDataWithEmptyContext()
    {
        $context = new LayoutContext();
        $this->provider->getData($context);
    }

    /**
     * @dataProvider getDataDataProvider
     * @param ShoppingList $shoppingList
     * @param array $expected
     */
    public function testGetData(ShoppingList $shoppingList, array $expected)
    {
        $context = new LayoutContext();
        $context->data()->set('shoppingList', null, $shoppingList);

        /** @var PriceList $priceList */
        $priceList = $this->getEntity('OroB2B\Bundle\PricingBundle\Entity\PriceList', ['id'=> 42]);
        $this->requestHandler->expects($this->once())
            ->method('getPriceList')
            ->willReturn($priceList);

        $repository = $this->getMockBuilder('OroB2B\Bundle\PricingBundle\Entity\Repository\ProductPriceRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('getProductsUnitsByPriceList')
            ->with()
            ->willReturn($expected);

        $em = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $em->expects($this->once())
            ->method('getRepository')
            ->with('OroB2BPricingBundle:ProductPrice')
            ->willReturn($repository);

        $this->registry->expects($this->once())
            ->method('getManagerForClass')
            ->with('OroB2BPricingBundle:ProductPrice')
            ->willReturn($em);

        $this->userCurrencyProvider->expects($this->once())
            ->method('getUserCurrency')
            ->willReturn(self::TEST_CURRENCY);

        $actual = $this->provider->getData($context);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getDataDataProvider()
    {
        /** @var Product $product1 */
        $product1 = $this->getEntity('OroB2B\Bundle\ProductBundle\Entity\Product', ['id'=> 123]);
        /** @var Product $product2 */
        $product2 = $this->getEntity('OroB2B\Bundle\ProductBundle\Entity\Product', ['id'=> 321]);

        $lineItem1 = new LineItem();
        $lineItem1->setProduct($product1);

        $lineItem2 = new LineItem();
        $lineItem2->setProduct($product2);

        $shoppingList = new ShoppingList();
        $shoppingList->addLineItem($lineItem1);
        $shoppingList->addLineItem($lineItem2);

        return [
            [
                'shoppingList' => $shoppingList,
                'expected' => [
                    '123' => ['liter', 'bottle'],
                    '321' => ['piece' ]
                ]
            ]
        ];
    }
}
