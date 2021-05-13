<?php
namespace AHT\ConfigProduct\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\SessionFactory;
use AHT\ConfigProduct\Helper\GetSimpleProducts;
use AHT\ConfigProduct\Helper\ExportFile;
use AHT\ConfigProduct\Model\Export\ProductFactory;
class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	protected $getSimpleProducts;

	protected $exportFile;

	protected $exportProductFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		GetSimpleProducts $getSimpleProducts,
		ExportFile $exportFile,
		ProductFactory $exportProductFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
		$this->getSimpleProducts = $getSimpleProducts;
		$this->exportFile = $exportFile;
		$this->exportProductFactory = $exportProductFactory;
	}

	public function execute()
	{
		/*die();*/
		$simpleProduct = $this->getSimpleProducts->getProductsColor()->getData();

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$state = $objectManager->get('Magento\Framework\App\State');
		$state->getAreaCode();
		$product = $objectManager->create('Magento\Catalog\Model\Product');
		$product->setName('Configurable Product Test'); // Set Product Name
		$product->setTypeId('configurable'); // Set Product Type Id
		$product->setAttributeSetId(4); // Set Attribute Set ID
		$product->setSku('color'); // Set SKU
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

		$optionId = $product->getData();
		// super attribute 
		/*$size_attr_id = $configurable_product->getResource()->getAttribute('product_size')->getId();*/
		$getSkuConfig = $product->getSku();
		$color_attr_name = $product->getResource()->getAttribute('color')->getName();

		//Check Sku Config with Attribute code
		if ($getSkuConfig == $color_attr_name) {
			$color_attr_id = $product->getResource()->getAttribute('color')->getId();
			/*$product_simple_code = $product->getResource()->getAttribute('product_simple_code')->getId();*/

			$product->getTypeInstance()->setUsedProductAttributeIds(array($color_attr_id/*, $product_simple_code*/), $product); //attribute ID of attribute 'size_general' in my store
			$configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
			$product->setCanSaveConfigurableAttributes(true);
			$product->setConfigurableAttributesData($configurableAttributesData);
			$configurableProductsData = array();
			$product->setConfigurableProductsData($configurableProductsData);
			try {
				/*$product->save();*/
				/*$product = $this->exportFile;
				$product->exportFileToCSV();*/

				$exportProduct = $this->exportProductFactory->create();
  				$exportProduct->exportData($product);

			} catch (Exception $ex) {
				echo '<pre>';
				print_r($ex->getMessage());
				exit;
			}

			//Check attribute config not null will add simple product to config
			if ( !empty($configurableAttributesData) ) {
				$productId = $product->getId();

				//Get Attribute by simple product
				$valueSimple = [];
				foreach ($simpleProduct as $value) {
					array_push($valueSimple, $value['entity_id']);
				}
				$comma_separated = implode(",", $valueSimple);

				// assign simple product ids
				$associatedProductIds = array($comma_separated);
				
				try{
				//Simple product > 0 create config
				if ($simpleProduct > 0) {
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
				    $product->setAssociatedProductIds($associatedProductIds); // Setting Associated Products

				    $product->setCanSaveConfigurableAttributes(true);
				    /*$product = $this->exportFile;*/
					/*$product->exportFileToCSV();*/
				    /*$product->save();*/
				    $exportProduct = $this->exportProductFactory->create();
  					$exportProduct->exportData($product);
				}
				
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