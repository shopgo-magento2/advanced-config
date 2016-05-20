<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedConfig\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Config model
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var Config\System
     */
    protected $systemConfig;

    /**
     * @var Config\File
     */
    protected $fileConfig;

    /**
     * @param Config\System $systemConfig
     * @param \ShopGo\AdvancedConfig\Model\Config\File $fileConfig
     */
    public function __construct(
        Config\System $systemConfig,
        \ShopGo\AdvancedConfig\Model\Config\File $fileConfig
    ) {
        $this->systemConfig = $systemConfig;
        $this->fileConfig   = $fileConfig;
    }

    /**
     * Get system config
     *
     * @return Config\System
     */
    public function getSystemConfig()
    {
        return $this->systemConfig;
    }

    /**
     * Get file config
     *
     * @return \ShopGo\AmazonSns\Model\Config\File
     */
    public function getFileConfig()
    {
        return $this->fileConfig;
    }

    /**
     * Get config value
     *
     * @param string $path
     * @param string $scope
     * @param null|string $scopeCode
     * @return mixed
     */
    public function getValue(
        $path = null,
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        $value = $this->fileConfig->getConfigElementValue($path);

        if (!isset($value) || !$value) {
            $value = $this->systemConfig->getValue($path, $scope, $scopeCode);
        }

        return $value;
    }

    /**
     * Set config value
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     * @return void
     */
    public function setValue(
        $path,
        $value,
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        $this->systemConfig->setValue($path, $value, $scope, $scopeCode);
        $this->systemConfig->clearConfigCache();
    }
}
