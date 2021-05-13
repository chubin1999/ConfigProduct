<?php
namespace AHT\ConfigProduct\Helper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\SessionFactory;
use AHT\ConfigProduct\Helper\GetSimpleProducts;
use AHT\ConfigProduct\Helper\ExportFile;

class CreateConfig extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	protected $getSimpleProducts;

	protected $exportFile;

	protected $fileFactory;

    protected $csvProcessor;

    protected $directoryList;

    protected $_productCollectionFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		GetSimpleProducts $getSimpleProducts,
		ExportFile $exportFile,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor,
    	\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
    	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
		$this->getSimpleProducts = $getSimpleProducts;
		$this->exportFile = $exportFile;
		$this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_productCollectionFactory = $productCollectionFactory;
	}

	public function execute()
	{
		/*die();*/
		$simpleProduct = $this->getSimpleProducts->getProductsColor()->getData();
		/*$a = $this->exportFile;
		$a->exportFileToCSV();*/
		/*die();*/
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$state = $objectManager->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
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
				$getexportFileToCSV = $this->exportFileToCSV();
				/*  $product->save();*/
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
				    $getexportFileToCSV = $this->exportFileToCSV();
				    /*$product->save();*/
				}
				
				} catch (Exception $e) {
					echo "<pre>";
					print_r($e->getMessage());
					exit;
				}
			}			
		}
		/*$a = $this->exportFile;
		$a->exportFileToCSV();*/
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