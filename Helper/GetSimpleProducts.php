<?php

namespace AHT\ConfigProduct\Helper;

use Magento\Framework\View\Element\Template;

class GetSimpleProducts extends Template
{
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = [],
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    public function getProducts()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('product_simple_code','*');
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->getSelect()->order('created_at', \Magento\Framework\DB\Select::SQL_DESC);
        return $collection;
    }

    public function getAllProducts()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

        return $collection->getItems();
    }


    public function getProductsColor(){
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('color','1');
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->getSelect()->order('created_at', \Magento\Framework\DB\Select::SQL_DESC);
        return $collection;
    }
}
