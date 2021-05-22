<?php
namespace AHT\ConfigProduct\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use AHT\ConfigProduct\Helper\GetSimpleProducts;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\ImportExport\Model\Export\Entity\ExportInfoFactory;

class CreateConfig extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	protected $getSimpleProducts;

	private $messagePublisher;

	private $exportInfoFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		GetSimpleProducts $getSimpleProducts,
		PublisherInterface $publisher = null,
		ExportInfoFactory $exportInfoFactory = null
	){
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
		$this->getSimpleProducts = $getSimpleProducts;
		$this->messagePublisher = $publisher ?:\Magento\Framework\App\ObjectManager::getInstance()
		->get(PublisherInterface::class);
		$this->exportInfoFactory = $exportInfoFactory ?:\Magento\Framework\App\ObjectManager::getInstance()
		->get(ExportInfoFactory::class);
	}

	public function execute()
	{
		$simpleProduct = $this->getSimpleProducts->getProducts();
		$arrayCode = [];
		foreach ($simpleProduct as $key => $value) {
			$arrayCode = $value['product_simple_code'];	
		}
		$simpleProduct = $simpleProduct->getData();

		/**
		 *Check simple product > 0
		 *
		 */
		if ($simpleProduct > 0) {
			$attributeOne = 'color';
			$attributeTwo = 'size';
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$state = $objectManager->get('Magento\Framework\App\State');
			$state->getAreaCode();
			$product = $objectManager->create('Magento\Catalog\Model\Product');
			$product->setName('Configurable Product Test'); // Set Product Name
			$product->setTypeId('configurable'); // Set Product Type Id
			$product->setAttributeSetId(19); // Set Attribute Set ID
			$product->setSku($arrayCode); // Set SKU
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
			        'qty' => 2000
			    ]
			);
			$product->setUrlKey('product_config_attribute');

			/**
			 *Setting the attribute list for configurable product
			 * 
			 */
	   		$color_attr_id = $product->getResource()->getAttribute($attributeOne)->getId();
	   		$size_attr_id = $product->getResource()->getAttribute($attributeTwo)->getId();
			$product->getTypeInstance()->setUsedProductAttributeIds(array($color_attr_id,$size_attr_id), $product); 
			//attribute ID of attribute 'size_general' in my store
			$configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
			$product->setCanSaveConfigurableAttributes(true);
			$product->setConfigurableAttributesData($configurableAttributesData);
			$configurableProductsData = array();
			$product->setConfigurableProductsData($configurableProductsData);
			try {
				/*$product->save();*/
				/*$product = $this->exportFile;
				$product->exportFileToCSV();*/
				/*
				$exportProduct = $this->exportProductFactory->create();
	  			$exportProduct->exportData($product);*/
	  			/*$dataObject = $this->exportInfoFactory->create(
					"csv",
					"catalog_product",
					array("product_simple_code" => "Product simple code"),
					[]
				);
				$this->messagePublisher->publish('import_export.export', $dataObject);*/
				/*die("asdada");*/

				} catch (Exception $ex) {
					echo '<pre>';
					print_r($ex->getMessage());
				exit;
			}

			/**
			 *Check attribute config not null will add simple product to config
			 * 
			 */
			if (!empty($configurableAttributesData) ) {
				$productId = $product->getId();

				$valueSimple = [];
				foreach ($simpleProduct as $value) {
					array_push($valueSimple, $value['entity_id']);
				}
				$comma_separated = implode(",", $valueSimple);
				$comma_separated = explode(",",$comma_separated);

				// assign simple product ids
				$associatedProductIds = $comma_separated;

				try{
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
				    $product->setAssociatedProductIds($associatedProductIds); // Setting Associated Products

				    $product->setCanSaveConfigurableAttributes(true);
				    /*$product = $this->exportFile;*/
					/*$product->exportFileToCSV();*/
				    /*$product->save();*/
				    /*$exportProduct = $this->exportProductFactory->create();
  					$exportProduct->exportData($product);*/
				
				} catch (Exception $e) {
					echo "<pre>";
					print_r($e->getMessage());
					exit;
				}
			}	
		}
		$resultPage = $this->resultPageFactory->create();
		return $resultPage;
	}
}