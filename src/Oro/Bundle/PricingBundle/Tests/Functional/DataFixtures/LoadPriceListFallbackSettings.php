<?php

namespace Oro\Bundle\PricingBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\CustomerBundle\Tests\Functional\DataFixtures\LoadCustomers;
use Oro\Bundle\CustomerBundle\Tests\Functional\DataFixtures\LoadGroups;
use Oro\Bundle\PricingBundle\Entity\PriceListCustomerFallback;
use Oro\Bundle\PricingBundle\Entity\PriceListCustomerGroupFallback;
use Oro\Bundle\PricingBundle\Entity\PriceListWebsiteFallback;
use Oro\Bundle\WebsiteBundle\Entity\Website;
use Oro\Bundle\WebsiteBundle\Tests\Functional\DataFixtures\LoadWebsiteData;

class LoadPriceListFallbackSettings extends AbstractFixture implements DependentFixtureInterface
{
    const WEBSITE_CUSTOMER_FALLBACK_1 = 'US_customer_1_1_price_list_fallback';
    const WEBSITE_CUSTOMER_FALLBACK_2 = 'US_customer_1_3_price_list_fallback';
    const WEBSITE_CUSTOMER_FALLBACK_3 = 'US_customer_1_2_price_list_fallback';
    const WEBSITE_CUSTOMER_FALLBACK_4 = 'Canada_customer_1_1_price_list_fallback';
    const WEBSITE_CUSTOMER_FALLBACK_5 = 'Canada_customer_1_3_price_list_fallback';
    const WEBSITE_CUSTOMER_FALLBACK_6 = 'Canada_customer_1_2_price_list_fallback';

    /**
     * @var array
     */
    protected $fallbackSettings = [
        'customer' => [
            LoadWebsiteData::WEBSITE1 => [
                [
                    'reference' => self::WEBSITE_CUSTOMER_FALLBACK_1,
                    'customer' => 'customer.level_1_1',
                    'fallback' => PriceListCustomerFallback::ACCOUNT_GROUP,
                ],
                [
                    'reference' => self::WEBSITE_CUSTOMER_FALLBACK_2,
                    'customer' => 'customer.level_1.3',
                    'fallback' => PriceListCustomerFallback::ACCOUNT_GROUP,
                ],
                [
                    'reference' => self::WEBSITE_CUSTOMER_FALLBACK_3,
                    'customer' => 'customer.level_1.2',
                    'fallback' => PriceListCustomerFallback::CURRENT_ACCOUNT_ONLY,
                ],
            ],
            LoadWebsiteData::WEBSITE2 => [
                [
                    'reference' => self::WEBSITE_CUSTOMER_FALLBACK_4,
                    'customer' => 'customer.level_1_1',
                    'fallback' => PriceListCustomerFallback::CURRENT_ACCOUNT_ONLY,
                ],
                [
                    'reference' => self::WEBSITE_CUSTOMER_FALLBACK_5,
                    'customer' => 'customer.level_1.3',
                    'fallback' => PriceListCustomerFallback::ACCOUNT_GROUP,
                ],
                [
                    'reference' => self::WEBSITE_CUSTOMER_FALLBACK_6,
                    'customer' => 'customer.level_1.2',
                    'fallback' => PriceListCustomerFallback::CURRENT_ACCOUNT_ONLY,
                ],
            ],
        ],
        'customerGroup' => [
            LoadWebsiteData::WEBSITE1 => [
                'customer_group.group1' => PriceListCustomerGroupFallback::WEBSITE,
                'customer_group.group2' => PriceListCustomerGroupFallback::CURRENT_ACCOUNT_GROUP_ONLY,
            ],
            LoadWebsiteData::WEBSITE2 => [
                'customer_group.group1' => PriceListCustomerGroupFallback::WEBSITE,
                'customer_group.group2' => PriceListCustomerGroupFallback::CURRENT_ACCOUNT_GROUP_ONLY,
            ],
        ],
        'website' => [
            'US' => PriceListWebsiteFallback::CONFIG,
            'Canada' => PriceListWebsiteFallback::CURRENT_WEBSITE_ONLY,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadWebsiteData::class,
            LoadCustomers::class,
            LoadGroups::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->fallbackSettings['customer'] as $websiteReference => $fallbackSettings) {
            /** @var Website $website */
            $website = $this->getReference($websiteReference);
            foreach ($fallbackSettings as $fallbackData) {
                /** @var Customer $customer */
                $customer = $this->getReference($fallbackData['customer']);

                $priceListCustomerFallback = new PriceListCustomerFallback();
                $priceListCustomerFallback->setCustomer($customer);
                $priceListCustomerFallback->setWebsite($website);
                $priceListCustomerFallback->setFallback($fallbackData['fallback']);

                $manager->persist($priceListCustomerFallback);
                $this->setReference($fallbackData['reference'], $priceListCustomerFallback);
            }
        }

        foreach ($this->fallbackSettings['customerGroup'] as $websiteReference => $fallbackSettings) {
            /** @var Website $website */
            $website = $this->getReference($websiteReference);
            foreach ($fallbackSettings as $customerGroupReference => $fallbackValue) {
                /** @var CustomerGroup $customerGroup */
                $customerGroup = $this->getReference($customerGroupReference);

                $priceListCustomerGroupFallback = new PriceListCustomerGroupFallback();
                $priceListCustomerGroupFallback->setCustomerGroup($customerGroup);
                $priceListCustomerGroupFallback->setWebsite($website);
                $priceListCustomerGroupFallback->setFallback($fallbackValue);

                $manager->persist($priceListCustomerGroupFallback);
            }
        }

        foreach ($this->fallbackSettings['website'] as $websiteReference => $fallbackValue) {
            /** @var Website $website */
            $website = $this->getReference($websiteReference);

            $priceListWebsiteFallback = new PriceListWebsiteFallback();
            $priceListWebsiteFallback->setWebsite($website);
            $priceListWebsiteFallback->setFallback($fallbackValue);

            $manager->persist($priceListWebsiteFallback);
        }
        $manager->flush();
    }
}
