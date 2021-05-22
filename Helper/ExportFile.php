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

      /* echo "<pre>";
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
           'sku',
           'store_view_code',
           'attribute_set_code',
           'product_type',
           'categories',
           'product_websites',
           'name',
           'description',
           'short_description',
           'weight',
           'product_online',
           'tax_class_name',
           'visibility',
           'price',
           'special_price',
           'special_price_from_date',
           'special_price_to_date',
           'url_key',
           'meta_title',
           'meta_keywords',
           'meta_description',
           'base_image',
           'base_image_label',
           'small_image',
           'small_image_label',
           'thumbnail_image',
           'thumbnail_image_label',
           'swatch_image',
           'swatch_image_label',
           'created_at',
           'updated_at',
           'new_from_date',
           'new_to_date',
           'display_product_options_in',
           'map_price',
           'msrp_price',
           'map_enabled',
           'gift_message_available',
           'msrp_display_actual_price_type',
           'country_of_manufacture',
           'additional_attributes',
           'qty',
           'out_of_stock_qty',
           'use_config_min_qty',
           'is_qty_decimal',
           'allow_backorders',
           'use_config_backorders',
           'min_cart_qty',
           'use_config_min_sale_qty',
           'max_cart_qty',
           'use_config_max_sale_qty',
           'is_in_stock',
           'notify_on_stock_below',
           'use_config_notify_stock_qty',
           'manage_stock',
           'use_config_manage_stock',
           'use_config_qty_increments',
           'qty_increments',
           'use_config_enable_qty_inc',
           'enable_qty_increments',
           'is_decimal_divided',
           'website_id',
           'additional_images',
           'configurable_variations',
           'configurable_variation_labels'
       ];
       foreach ($productCollection as $value) {

           $result[] = [
           $value->getSku(),
           $value->getStore_view_code(),
           $value->getAttribute_set_code(),
           $value->getEntity_id(),
           $value->getName(),
           $value->getImage(),
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