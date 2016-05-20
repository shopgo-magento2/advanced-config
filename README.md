Advanced Config
===============


#### Contents
*   [Synopsis](#syn)
*   [Overview](#over)
*   [Installation](#install)
*   [Tests](#tests)
*   [Contributors](#contrib)
*   [License](#lic)


## <a name="syn"></a>Synopsis

A utility module that extends Magento 2 core config module and adds more features to it.

## <a name="over"></a>Overview

Advanced Config utility module extends Magento 2 core config module by adding the ability
to read from file based config.
The module also provides functions to read from DB based config and to write in it, too.

## <a name="install"></a>Installation

Below, you can find two ways to install the advanced config module.

### 1. Install via Composer (Recommended)
First, make sure that Composer is installed: https://getcomposer.org/doc/00-intro.md

Make sure that Packagist repository is not disabled.

Run Composer require to install the module:

    php <your Composer install dir>/composer.phar require shopgo/advanced-config:*

### 2. Clone the advanced-config repository
Clone the <a href="https://github.com/shopgo-magento2/advanced-config" target="_blank">advanced-config</a> repository using either the HTTPS or SSH protocols.

### 2.1. Copy the code
Create a directory for the advanced config module and copy the cloned repository contents to it:

    mkdir -p <your Magento install dir>/app/code/ShopGo/AdvancedConfig
    cp -R <advanced-config clone dir>/* <your Magento install dir>/app/code/ShopGo/AdvancedConfig

### Update the Magento database and schema
If you added the module to an existing Magento installation, run the following command:

    php <your Magento install dir>/bin/magento setup:upgrade

### Verify the module is installed and enabled
Enter the following command:

    php <your Magento install dir>/bin/magento module:status

The following confirms you installed the module correctly, and that it's enabled:

    example
        List of enabled modules:
        ...
        ShopGo_AdvancedConfig
        ...

## <a name="tests"></a>Tests

TODO

## <a name="contrib"></a>Contributors

Ammar (<ammar@shopgo.me>)

## <a name="lic"></a>License

[Open Source License](LICENSE.txt)
