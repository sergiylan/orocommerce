<?php

namespace Oro\Bundle\ShippingBundle\Context;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\CustomerBundle\Entity\CustomerOwnerAwareInterface;
use Oro\Bundle\LocaleBundle\Model\AddressInterface;
use Oro\Bundle\ShippingBundle\Context\LineItem\Collection\ShippingLineItemCollectionInterface;

interface ShippingContextInterface extends CustomerOwnerAwareInterface
{
    /**
     * @return ShippingLineItemCollectionInterface
     */
    public function getLineItems();

    /**
     * @return AddressInterface|null
     */
    public function getBillingAddress();

    /**
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * @return AddressInterface
     */
    public function getShippingOrigin();

    /**
     * @return String|null
     */
    public function getPaymentMethod();

    /**
     * @return String
     */
    public function getCurrency();

    /**
     * @return Price
     */
    public function getSubtotal();

    /**
     * @return object
     */
    public function getSourceEntity();

    /**
     * @return mixed
     */
    public function getSourceEntityIdentifier();
}
