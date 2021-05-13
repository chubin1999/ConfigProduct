<?php 
namespace AHT\ConfigProduct\Helper;

class ExportFileCF extends \Magento\Framework\App\Action\Action
{
 protected $fileFactory;
 protected $csvProcessor;
 protected $directoryList;
 protected $_productFactory;

 protected $_exportProductFactory;

 public function __construct(
  \Magento\Framework\App\Action\Context $context,
  \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
  \Magento\Framework\File\Csv $csvProcessor,
  \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
  \Magento\Framework\View\Result\PageFactory $pageFactory,
  \Magento\Catalog\Model\ProductFactory $productFactory,
  \AHT\ConfigProduct\Model\Export\ProductFactory $exportProductFactory
)
 {
   $this->_pageFactory = $pageFactory;
   $this->_productFactory = $productFactory;

   $this->_exportProductFactory = $exportProductFactory;


   parent::__construct($context);
 }

 public function execute()
 {
  
  $product = $this->_productFactory->create()->load(1);

  $exportProduct = $this->_exportProductFactory->create();

  $exportProduct->exportData($product);
 }
}
?>