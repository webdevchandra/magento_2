<?php
namespace Webdev\Helloworld\Model\ResourceModel\Data;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Webdev\Helloworld\Model\Data', 'Webdev\Helloworld\Model\ResourceModel\Data');
	}

}