<?php
namespace Webdev\Helloworld\Model\ResourceModel;
 
class Data extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	protected function _construct()
	{
		$this->_init('students', 'student_id');
	}
}