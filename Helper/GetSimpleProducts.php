<?php

namespace AHT\ConfigProduct\Helper;

use Magento\Framework\View\Element\Template;

class GetSimpleProducts extends Template
{
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getProducts()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('product_simple_code','1');
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->getSelect()->order('created_at', \Magento\Framework\DB\Select::SQL_DESC);
        /*$collection->getSelect()->limit('*');*/
        return $collection;
    }

    public function getProductsColor(){
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('color','1');
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->getSelect()->order('created_at', \Magento\Framework\DB\Select::SQL_DESC);
        return $collection;
    }
}
