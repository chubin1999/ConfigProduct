<?php

use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setName('Configurable Product Test'); // Set Product Name
$product->setTypeId('configurable'); // Set Product Type Id
$product->setAttributeSetId(10); // Set Attribute Set ID
$product->setSku('configurable-product'); // Set SKU
$product->setStatus(1); // Set Status
$product->setWeight(5); // Set Weight
$product->setTaxClassId(2); // Set Tax Class Id
$product->setWebsiteIds([1]); // Set Website Ids
$product->setVisibility(4);
$product->setCategoryIds([3]); // Assign Category Ids
$product->setPrice(100); // Product Price
$product->setImage('/configurable/test.jpg'); // Image Path
$product->setSmallImage('/configurable/test.jpg'); // Small Image Path
$product->setThumbnail('/configurable/test.jpg'); // Thumbnail Image Path
$product->setStockData(
    [
        'use_config_manage_stock' => 0, // Use Config Settings Checkbox
        'manage_stock' => 1, // Manage Stock
        'is_in_stock' => 1, // Stock Availability
    ]
);

$size_attr_id = $product->getResource()->getAttribute('size')->getId();
$color_attr_id = $product->getResource()->getAttribute('color')->getId();
$product->getTypeInstance()->setUsedProductAttributeIds([$color_attr_id, $size_attr_id], $product);
$configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
$product->setCanSaveConfigurableAttributes(true);
$product->setConfigurableAttributesData($configurableAttributesData);
$configurableProductsData = [];
$product->setConfigurableProductsData($configurableProductsData);
try {
    $product->save();
} catch (Exception $ex) {
    echo '<pre>';
    print_r($ex->getMessage());
    exit;
}
$productId = $product->getId();
$associatedProductIds = [2044, 2045]; // Add Your Associated Product Ids.
try {
    $configurable_product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
    $configurable_product->setAssociatedProductIds($associatedProductIds); // Setting Associated Products
    $configurable_product->setCanSaveConfigurableAttributes(true);
    $configurable_product->save();
} catch (Exception $e) {
    echo "<pre>";
    print_r($e->getMessage());
    exit;
}