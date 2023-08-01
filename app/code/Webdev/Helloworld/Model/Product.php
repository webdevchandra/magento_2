<?php
namespace Webdev\Helloworld\Model;

class Product extends \Magento\Catalog\Model\Product{
    public function getName()
    {
        return 'test';
    }
}