<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedConfig\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * System config model
 */
class System extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $configFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configFactory = $configFactory;
        $this->cacheTypeList = $cacheTypeList;
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
        $path,
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($path, $scope, $scopeCode);
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
        try {
            $path = explode('/', $path);

            $group = [
                $path[1] => [
                    'fields' => [
                        $path[2] => [
                            'value' => $value
                        ]
                    ]
                ]
            ];

            $configData = [
                'section' => $path[0],
                'website' => $scope,
                'store'   => $scopeCode,
                'groups'  => $group
            ];

            /** @var \Magento\Config\Model\Config $configModel */
            $configModel = $this->configFactory->create(['data' => $configData]);
            $configModel->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        } catch (\Exception $e) {}
    }

    /**
     * Clear config cache
     *
     * @return void
     */
    public function clearConfigCache()
    {
        $this->cacheTypeList->cleanType('config');
    }
}
