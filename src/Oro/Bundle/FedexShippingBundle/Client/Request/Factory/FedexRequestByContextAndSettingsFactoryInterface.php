<?php

namespace Oro\Bundle\FedexShippingBundle\Client\Request\Factory;

use Oro\Bundle\FedexShippingBundle\Client\Request\FedexRequestInterface;
use Oro\Bundle\FedexShippingBundle\Entity\FedexIntegrationSettings;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;

interface FedexRequestByContextAndSettingsFactoryInterface
{
    /**
     * @param FedexIntegrationSettings $settings
     * @param ShippingContextInterface $context
     *
     * @return FedexRequestInterface|null
     */
    public function create(FedexIntegrationSettings $settings, ShippingContextInterface $context);
}
