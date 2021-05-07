<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/app/bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$product = $objectManager->create('Magento\Catalog\Model\Product');

try {
    $product->setName('Sample Product');
    $product->setTypeId('simple');
    $product->setAttributeSetId(4);
    $product->setSku('sample-product');
    $product->setWebsiteIds([1]);
    $product->setVisibility(4);
    $product->setPrice([1]);
    $product->setImage('/sample/test.jpg');
    $product->setSmallImage('/sample/test.jpg');
    $product->setThumbnail('/sample/test.jpg');
    $product->setStockData(
        [
            'use_config_manage_stock' => 0,
            'manage_stock' => 1,
            'min_sale_qty' => 1,
            'max_sale_qty' => 2,
            'is_in_stock' => 1,
            'qty' => 100
        ]
    );
    $product->save();
    /**
     * For Add Custom Options
     */
    $options = [
        [
            "sort_order" => 1,
            "title" => "Custom Option 1",
            "price_type" => "fixed",
            "price" => "10",
            "type" => "field",
            "is_require" => 0
        ],
        [
            "sort_order" => 2,
            "title" => "Custom Option 2",
            "price_type" => "fixed",
            "price" => "20",
            "type" => "field",
            "is_require" => 0
        ]
    ];
    foreach ($options as $customOptions) {
        $product->setHasOptions(1);
        $product->getResource()->save($product);
        $option = $objectManager->create('\Magento\Catalog\Model\Product\Option')
            ->setProductId($product->getId())
            ->setStoreId($product->getStoreId())
            ->addData($customOptions);
        $option->save();
        $product->addOption($option);
    }
} catch (Exception $ex) {
    echo $e->getMessage();
}