<?php

namespace OroB2B\Bundle\AccountBundle\EventListener;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\Event\ConfigSettingsUpdateEvent;

use OroB2B\Bundle\AccountBundle\Storage\CategoryVisibilityStorage;

class SystemConfigCategoryVisibilityListener
{
    const SECTION = 'oro_b2b_account';
    const SETTING = 'category_visibility';

    /**
     * @var CategoryVisibilityStorage
     */
    protected $categoryVisibilityStorage;

    /**
     * @param CategoryVisibilityStorage $categoryVisibilityStorage
     */
    public function __construct(CategoryVisibilityStorage $categoryVisibilityStorage)
    {
        $this->categoryVisibilityStorage = $categoryVisibilityStorage;
    }

    public function onSettingsSaveBefore(ConfigSettingsUpdateEvent $event)
    {
        $settingsKey = $this->getSettingsKey();
        $settings = $event->getSettings();
        if (is_array($settings) && array_key_exists($settingsKey, $settings)) {
            $this->categoryVisibilityStorage->clearData();
        }
    }

    /**
     * @return string
     */
    protected function getSettingsKey()
    {
        return implode(ConfigManager::SECTION_VIEW_SEPARATOR, [self::SECTION, self::SETTING]);
    }
}
