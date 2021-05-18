<?php 

namespace AHT\ConfigProduct\Helper;

use AHT\ConfigProduct\Helper\CreateConfig;

class ExportFile extends \Magento\Framework\App\Action\Action
{
   protected $fileFactory;
   protected $csvProcessor;
   protected $directoryList;
   protected $_productCollectionFactory;

   public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    \Magento\Framework\File\Csv $csvProcessor,
    \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
    \Magento\Framework\View\Result\PageFactory $pageFactory,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
)
   {
       $this->fileFactory = $fileFactory;
       $this->csvProcessor = $csvProcessor;
       $this->directoryList = $directoryList;
       $this->_pageFactory = $pageFactory;
       $this->_productCollectionFactory = $productCollectionFactory;

       parent::__construct($context);
   }

   public function execute()
   {
       $getexportFileToCSV = $this->exportFileToCSV();
       return $getexportFileToCSV;
   }

   public function exportFileToCSV(){
       $fileName = 'csv_product.csv';
       $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
       . "/" . $fileName;

     // $customer = $this->_customerSession->getCustomer();
       $productIds = ["1","2","3","4","5","6","7"];
       $productCollection = $this->_productCollectionFactory->create()
       ->addIdFilter($productIds)
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
?>