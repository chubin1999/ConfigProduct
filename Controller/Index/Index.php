<?php
namespace AHT\ConfigProduct\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use AHT\ConfigProduct\Helper\GetSimple;
use AHT\ConfigProduct\Model\Export\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\ImportExport\Model\Export\Entity\ExportInfoFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $getSimpleProducts;

	protected $exportProductFactory;

	protected $productCollectionFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		GetSimple $getSimpleProducts,
		ProductFactory $exportProductFactory,
		CollectionFactory $productCollectionFactory,
		PublisherInterface $publisher = null,
		ExportInfoFactory $exportInfoFactory = null
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
		$this->getSimpleProducts = $getSimpleProducts;
		$this->exportProductFactory = $exportProductFactory;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->messagePublisher = $publisher ?:\Magento\Framework\App\ObjectManager::getInstance()
		->get(PublisherInterface::class);
		$this->exportInfoFactory = $exportInfoFactory ?:\Magento\Framework\App\ObjectManager::getInstance()
		->get(ExportInfoFactory::class);
	}

	public function execute()
	{
		$simpleProduct = $this->getSimpleProducts->getProducts();
		$simpleProduct = $simpleProduct->getData();

		/**
		 *Get Array simple product 
		 *
		 */
		$arrProduct = array();
		$attributeForConfig = [];
		foreach ($simpleProduct as $key => $value) {

			/**
			 *product_code not null and attr for config not null
			 *
			 */
			$attributeForConfig = [$value['color'],$value['size']];
			if ($value['product_simple_code'] != null && $attributeForConfig != null)
			{
				$arrProduct[$value['product_simple_code']][$value['sku']] = $value;
			}
		}

		/**
		 *Export to file csv
		 *
		 */
		$dataObject = $this->exportInfoFactory->create(
			"csv",
			"catalog_product",
			array("product_simple_code" => "Product simple code"),
			[]
		);
		$this->messagePublisher->publish('import_export.export', $arrProduct);

		$resultPage = $this->resultPageFactory->/**/create();
		return $resultPage;
	}
}