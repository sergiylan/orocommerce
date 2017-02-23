<?php

namespace Oro\Bundle\SEOBundle\Tools;

use Oro\Bundle\SEOBundle\Provider\SitemapUrlProviderRegistry;
use Oro\Component\SEO\Tools\SitemapDumperInterface;
use Oro\Component\Website\WebsiteInterface;

class SitemapDumper implements SitemapDumperInterface
{
    const SITEMAP_FILENAME_TEMPLATE = 'sitemap-%s-%s.xml';

    /**
     * @var SitemapUrlProviderRegistry
     */
    private $providerRegistry;

    /**
     * @var SitemapStorageFactory
     */
    private $sitemapStorageFactory;

    /**
     * @var SitemapFileWriter
     */
    private $fileWriter;

    /**
     * @param SitemapUrlProviderRegistry $providerRegistry
     * @param SitemapStorageFactory $sitemapStorageFactory
     * @param SitemapFileWriter $fileWriter
     */
    public function __construct(
        SitemapUrlProviderRegistry $providerRegistry,
        SitemapStorageFactory $sitemapStorageFactory,
        SitemapFileWriter $fileWriter
    ) {
        $this->providerRegistry = $providerRegistry;
        $this->sitemapStorageFactory = $sitemapStorageFactory;
        $this->fileWriter = $fileWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(WebsiteInterface $website, $version, $type = null)
    {
        if ($type) {
            $providers[$type] = $this->providerRegistry->getProviderByName($type);
        } else {
            $providers = $this->providerRegistry->getProviders();
        }

        foreach ($providers as $providerType => $provider) {
            $urlsStorage = $this->sitemapStorageFactory->createUrlsStorage();

            $fileNumber = 1;
            foreach ($provider->getUrlItems($website) as $urlItem) {
                $itemAdded = $urlsStorage->addUrlItem($urlItem);
                if (!$itemAdded) {
                    $this->fileWriter->saveSitemap(
                        $urlsStorage,
                        $this->createFileName($providerType, $fileNumber++)
                    );

                    $urlsStorage = $this->sitemapStorageFactory->createUrlsStorage();
                    $urlsStorage->addUrlItem($urlItem);
                }
            }

            $this->fileWriter->saveSitemap($urlsStorage, $this->createFileName($providerType, $fileNumber));
        }
    }

    /**
     * @param string $providerType
     * @param string $fileNumber
     * @return string
     */
    private function createFileName($providerType, $fileNumber)
    {
        return sprintf(static::SITEMAP_FILENAME_TEMPLATE, $providerType, $fileNumber);
    }
}
