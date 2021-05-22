<?php

namespace AHT\ConfigProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class GetSimple extends AbstractHelper
{
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    public function getProducts()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect(['product_simple_code','color','size'],'*');
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->getSelect()->order('created_at', \Magento\Framework\DB\Select::SQL_DESC);
        return $collection;
    }
}
