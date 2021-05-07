<?php 

namespace AHT\ConfigProduct\Helper;

class Data
{
	public function __construct(        
		
	){        
		
	} 

	public function getAllManufacturer(){
		$manufacturerOptions = $this->_productAttributeRepository->get('product_simple_code')->getOptions();       
		$values = array();
		foreach ($manufacturerOptions as $manufacturerOption) { 
       //$manufacturerOption->getValue();  // Value
        $values[] = $manufacturerOption->getLabel();  // Label
    	}
    	return $values;
	} 
}