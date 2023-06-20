<?php
namespace Webdev\Helloworld\Model;

use Magento\Framework\Model\AbstractModel;
class Data extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Webdev\Helloworld\Model\ResourceModel\Data');
    }
} 