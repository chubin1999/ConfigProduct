<?php
namespace AHT\ConfigProduct\Helper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\SessionFactory;
use AHT\ConfigProduct\Helper\GetSimpleProducts;
use AHT\ConfigProduct\Helper\ExportFile;
use AHT\ConfigProduct\Model\Export\ProductFactory;

class CreateConfig extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	protected $getSimpleProducts;

	protected $exportFile;

	protected $fileFactory;

    protected $csvProcessor;

    protected $directoryList;

    protected $_productCollectionFactory;

    protected $exportProductFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		GetSimpleProducts $getSimpleProducts,
		ExportFile $exportFile,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor,
    	\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
    	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    	ProductFactory $exportProductFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
		$this->getSimpleProducts = $getSimpleProducts;
		$this->exportFile = $exportFile;
		$this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->exportProductFactory = $exportProductFactory;

	}

	public function execute()
	{
		$simpleProduct = $this->getSimpleProducts->getProducts();
		$arrayCode = [];
		foreach ($simpleProduct as $key => $value) {
			$arrayCode = $value['product_simple_code'];	
		}
		$simpleProduct = $simpleProduct->getData();
		/*echo "<pre>";
		var_dump($simpleProduct = $simpleProduct->getData());
		var_dump($arrayCode);
		die;*/
		/*$valueSimple = [];
		foreach ($simpleProduct as $value) {
			echo "<pre>";
			var_dump($value['entity_id']);
		}
		$valueSimple = [$value['entity_id']];
		var_dump($valueSimple);*/
/*
		$valueSimple = [];
		foreach ($simpleProduct as $value) {
			array_push(($valueSimple), $value['entity_id']);
		}
		echo "<pre>";
		var_dump([$comma_separated = implode(",", $valueSimple)]);
		$test = explode(",",$comma_separated);
		var_dump($test);
		echo "<pre>";
		var_dump($productIdHard = array(2065,2272,2273));
		die;*/
		/*echo "<pre>";
		var_dump([($comma_separated)]);
		var_dump($productIdHard);
		die;*/

		/*echo "<pre>";
		print_r($simpleProduct);
		die;*/
		/*var_dump($value['product_simple_code']);*/
		/*$product_simple_code = $product->getResource()->getAttribute('product_simple_code')->getId();
		var_dump($product_simple_code);*/
		/*echo "<pre>";
		var_dump($simpleProduct->getData());
		die();*/
		/*print_r($simpleProduct);*/
		/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();*/
		/*$product = $objectManager->get('Magento\Catalog\Model\Product')->load(1);
		echo $product->getMetalPads();*/
		/*$color_attr_name = $product->getResource()->getAttribute('color')->getName();*/
		/*echo $color_attr_name;*/
		$attributeOne = 'color';
		$attributeTwo = 'size';
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$state = $objectManager->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		$product = $objectManager->create('Magento\Catalog\Model\Product');
		$product->setName('Configurable Product Test'); // Set Product Name
		$product->setTypeId('configurable'); // Set Product Type Id
		$product->setAttributeSetId(4); // Set Attribute Set ID
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

		$optionId = $product->getData();
		// super attribute 
		/*$size_attr_id = $configurable_product->getResource()->getAttribute('product_size')->getId();*/
		$getSkuConfig = $product->getSku();
		/*var_dump($getSkuConfig);
		die;*/
		/*$color_attr_name = $product->getResource()->getAttribute('color')->getName();*/

		//Check Sku Config with Attribute code
		/*if ($getSkuConfig == $color_attr_name) {*/
   			/*echo $product->getCustomAttribute('product_simple_code')->getValue();
   			die('ád');
   			*/
   			$color_attr_id = $product->getResource()->getAttribute($attributeOne)->getId();
   			$size_attr_id = $product->getResource()->getAttribute($attributeTwo)->getId();
			$product_simple_code = $product->getResource()->getAttribute('product_simple_code')->getId();
			/*var_dump($product_simple_code);
			die;*/
			$product->getTypeInstance()->setUsedProductAttributeIds(array($color_attr_id,$size_attr_id/*, $product_simple_code*/), $product); //attribute ID of attribute 'size_general' in my store
			$configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
			$product->setCanSaveConfigurableAttributes(true);
			$product->setConfigurableAttributesData($configurableAttributesData);
			$configurableProductsData = array();
			$product->setConfigurableProductsData($configurableProductsData);
			try {
				$product->save();
				/*$product = $this->exportFile;
				$product->exportFileToCSV();*/

				/*$exportProduct = $this->exportProductFactory->create();
  				$exportProduct->exportData($product);*/

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
				$comma_separated = explode(",",$comma_separated);

				// assign simple product ids
				/*$associatedProductIds = array(2065,2272,2273);*/
				/*$associatedProductIds = array($comma_separated);*/
				$associatedProductIds = $comma_separated;
				

				/*var_dump($associatedProductIds);
				var_dump($a);
				die();*/
				try{
				//Simple product > 0 create config
				if ($simpleProduct > 0) {
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
				    $product->setAssociatedProductIds($associatedProductIds); // Setting Associated Products

				    $product->setCanSaveConfigurableAttributes(true);
				    /*$product = $this->exportFile;*/
					/*$product->exportFileToCSV();*/
				    $product->save();
				    /*$exportProduct = $this->exportProductFactory->create();
  					$exportProduct->exportData($product);*/
				}
				
				} catch (Exception $e) {
					echo "<pre>";
					print_r($e->getMessage());
					exit;
				}
			}			

		$resultPage = $this->resultPageFactory->create();
		return $resultPage;
	}

	public function exportFileToCSV(){
       $fileName = 'csv_product.csv';
       $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
       . "/" . $fileName;

     // $customer = $this->_customerSession->getCustomer();
       $productIds = ["1","2","3","4","5","6","7"];
       $productCollection = $this->_productCollectionFactory->create()
       /*->addIdFilter($productIds)*/
       ->addMinimalPrice()
       ->addFinalPrice()
       ->addTaxPercents()
       ->addAttributeToSelect('*');

       /*echo "<pre>";
       print_r($productCollection->getData());
       die();*/

       $productlData = $this->getProductData($productCollection);

       $this->csvProcessor
       ->setDelimiter(';')
       ->setEnclosure('"')
       ->saveData(
           $filePath,
           $productlData
       );


       return $this->fileFactory->create(
           $fileName,
           [
               'type' => "filename",
               'value' => $fileName,
               'rm' => true,
           ],
           \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
           'application/octet-stream'
       );
   }

   protected function getProductData( \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection)
   {
       $result = [];

       $result[] = [
           'entity_id',
           'name',
           'image',
           'sku',
           'price',
           'is_salable',
           'description'
       ];
       foreach ($productCollection as $value) {

           $result[] = [
           $value->getEntity_id(),
           $value->getName(),
           $value->getImage(),
           $value->getSku(),
           $value->getPrice(),
           $value->getIs_salable(),
           $value->getDescription()
           ];
       }
    /*echo '<pre>';
    echo '<hr>';
    foreach ($productCollection as $value) {
     var_dump($value->getData());
        
    }*/
 

       return $result;
   }
}