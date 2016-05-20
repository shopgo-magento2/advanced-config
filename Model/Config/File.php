<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedConfig\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * File configuration reader model
 */
class File extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $varDirectory;

    /**
     * @var string
     */
    protected $vendorConfigFile;

    /**
     * @var string
     */
    protected $varConfigFile;

    /**
     * @var string
     */
    protected $vendorConfigPath;

    /**
     * @var string
     */
    protected $varConfigPath;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $schemaFile;

    /**
     * @var \DomDocument
     */
    protected $dom;

    /**
     * @var \DOMXPath
     */
    protected $domXpath;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\Model\Config\Structure\Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $vendorConfigPath
     * @param string $varConfigPath
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Config\Model\Config\Structure\Converter $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $vendorConfigPath = '',
        $varConfigPath = '',
        $fileName = '',
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    ) {
        $this->filesystem = $filesystem;
        $this->fileName   = $fileName;
        $this->validationState = $validationState;

        $this->vendorConfigPath = $vendorConfigPath;
        $this->varConfigPath    = $varConfigPath;

        $this->setRootDirectory();
        $this->setVarDirectory();

        $this->schemaFile = $schemaLocator->getSchema();

        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Set Root directory
     */
    protected function setRootDirectory()
    {
        $this->rootDirectory = $this->filesystem
            ->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Set Var directory
     */
    protected function setVarDirectory()
    {
        $this->varDirectory = $this->filesystem
            ->getDirectoryRead(DirectoryList::VAR_DIR);
    }

    /**
     * Get Vendor config file absolute path
     *
     * @return string
     */
    protected function getVendorConfigFileAbsolutePath()
    {
        return $this->rootDirectory->getAbsolutePath(
            $this->vendorConfigPath . $this->fileName
        );
    }

    /**
     * Get Var config file absolute path
     *
     * @return string
     */
    protected function getVarConfigFileAbsolutePath()
    {
        return $this->varDirectory->getAbsolutePath(
            $this->varConfigPath . $this->fileName
        );
    }

    /**
     * Get config file absolute path
     *
     * @return string
     */
    protected function getConfigFileXmlContent()
    {
        $config = '';

        if ($this->vendorConfigFile) {
            $config = $this->rootDirectory->readFile(
                $this->vendorConfigPath . $this->fileName
            );
        }

        if (!$config && $this->varConfigFile) {
            $config = $this->varDirectory->readFile(
                $this->varConfigPath . $this->fileName
            );
        }

        return $config;
    }

    /**
     * Set DOM
     */
    protected function setDom()
    {
        $this->dom = new \DOMDocument();
        $this->dom->preserveWhiteSpace = false;
        $this->dom->loadXML($this->getConfigFileXmlContent());
    }

    /**
     * Set DOM XPath
     */
    protected function setDomXpath()
    {
        $this->domXpath = new \DOMXPath($this->dom);
    }

    /**
     * Check whether config file exists
     *
     * @return bool
     */
    protected function configFileExists()
    {
        if (!$this->vendorConfigPath && !$this->varConfigPath) {
            return false;
        }

        $this->vendorConfigFile = $this->rootDirectory->isFile(
            $this->vendorConfigPath . $this->fileName
        );

        $this->varConfigFile = $this->varDirectory->isFile(
            $this->varConfigPath . $this->fileName
        );

        return $this->vendorConfigFile || $this->varConfigFile;
    }

    /**
     * Validate DOM
     *
     * @return bool
     */
    protected function validateDom()
    {
        $result = true;

        if ($this->validationState->isValidationRequired() && $this->schemaFile) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get DOM XPath value
     *
     * @param string $xpath
     * @return string
     */
    protected function getDomXpathValue($xpath)
    {
        return $this->domXpath->query($xpath);
    }

    /**
     * Get config xpath
     *
     * @param array $element
     * @return string
     */
    protected function getConfigXpath($element)
    {
        $xpath = '/';

        foreach ($element as $_element => $data) {
            $attributesText = '';
            $valueText = '';

            switch (true) {
                case isset($data['attributes']):
                    foreach ($data['attributes'] as $attrKey => $attrVal) {
                        $attributesText .= '[@' . $attrKey . '="' . $attrVal . '"]';
                    }
                    break;
                case isset($data['value']):
                    $valueText .= '[.="' . $data['value'] . '"]';
                    break;
            }

            $xpath .= '/' . $_element . $attributesText . $valueText;
        }

        return $xpath;
    }

    /**
     * Get config element
     *
     * @param array|string $element
     * @return \DOMElement|null
     */
    public function getConfigElement($element)
    {
        if (!$this->configFileExists() || !$this->validateDom()) {
            return null;
        }

        $this->setDom();
        $this->setDomXpath();

        if (gettype($element) == 'string') {
            $element = explode('/', $element);
            $element = [
                $element[0] => [],
                $element[1] => [],
                $element[2] => []
            ];
        }

        $element = $this->getDomXpathValue($this->getConfigXpath($element));

        return $element;
    }

    /**
     * Get config element attribute
     *
     * @param array|string|\DOMElement $element
     * @param string $attributeName
     * @return string|null
     */
    public function getConfigElementAttribute($element, $attributeName)
    {
        if ($element instanceof \DOMElement) {
            $configElement = $element;
        } else {
            $configElement = $this->getConfigElement($element);
            $configElement = $configElement ? $configElement->item(0) : null;
        }

        return $configElement !== null
            ? $configElement->getAttribute($attributeName)
            : null;
    }

    /**
     * Get config element value
     *
     * @param array|string|\DOMElement $element
     * @return string|null
     */
    public function getConfigElementValue($element)
    {
        if ($element instanceof \DOMElement) {
            $configElement = $element;
        } else {
            $configElement = $this->getConfigElement($element);
            $configElement = $configElement ? $configElement->item(0) : null;
        }

        return $configElement !== null
            ? $configElement->nodeValue
            : null;
    }
}
