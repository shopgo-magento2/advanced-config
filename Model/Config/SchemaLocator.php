<?php
/**
 * Configuration schema locator
 *
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedConfig\Model\Config;

use Magento\Framework\Module\Dir;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $schema = null;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $perFileSchema = null;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param string $moduleName
     * @param string $fileName
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        $moduleName = '',
        $fileName = ''
    ) {
        $etcDir = $moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR, $moduleName
        );
        $this->schema = $etcDir . '/' . $fileName;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
