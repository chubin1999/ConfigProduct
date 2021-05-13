<?php
/**
* Copyright © Magento, Inc. All rights reserved.
* See COPYING.txt for license details.
*/
namespace AHT\ConfigProduct\Model\Export;

class Product extends \Magento\CatalogImportExport\Model\Export\Product
{

    protected $_fileFactory;
    protected $_csvProcessor;
    protected $_directoryList;
    protected $_product;

    public function __construct(
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface  $localeDate,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetColFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $optionColFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeColFactory,
        \Magento\CatalogImportExport\Model\Export\Product\Type\Factory $_typeFactory,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\CatalogImportExport\Model\Export\RowCustomizerInterface $rowCustomizer,
        array $dateAttrCodes = [],
        \Magento\CatalogImportExport\Model\Export\ProductFilterInterface $filter = null
    )
    {
        $this->_fileFactory = $fileFactory;
        $this->_csvProcessor = $csvProcessor;
        $this->_directoryList = $directoryList;

        parent::__construct($localeDate,$config,$resource,$storeManager,$logger,$collectionFactory,$exportConfig,$productFactory,$attrSetColFactory,$categoryColFactory,$itemFactory,$optionColFactory,$attributeColFactory,$_typeFactory,$linkTypeProvider,$rowCustomizer,$dateAttrCodes,$filter);
    }

    protected function loadCollection(): array
    {
        $data = [];       

        foreach (array_keys($this->_storeIdToCode) as $storeId) {
            $data[1][$storeId] = $this->_product;
        }

        return $data;
    }

    public function exportData(\Magento\Catalog\Model\Product $product)
    {
        $this->_product = $product;
        $exportCSVData = [];

        $fileName = $product->getSku().'.csv';
        $filePath = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
        . "/" . $fileName;


        $exportData = $this->getExportData()[0];
        
        // echo '<pre>';

        $exportCSVHeader = array_keys($exportData);
        $exportCSVValue  = array_values($exportData);

        $exportCSVData[] = $exportCSVHeader;
        $exportCSVData[] = $exportCSVValue;

        // var_dump($exportCSVHeader);

        // var_dump($exportCSVValue);

        // var_dump($exportData);

        // die();
        
        $this->_csvProcessor
        ->setDelimiter(',')
        ->setEnclosure('"')
        ->saveData(
         $filePath,
         $exportCSVData
        );


        return $this->_fileFactory->create(
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


}
