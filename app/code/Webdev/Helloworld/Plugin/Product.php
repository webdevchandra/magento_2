<?php
namespace Webdev\Helloworld\Plugin;

// use Magento\Quote\Model\Quote as MainProduct;
// use Magento\Catalog\Model\Product as MainProduct;

class Product
{
    /** Before plugin */ 
    // public function aroundAddProduct(MainProduct $subject,$productInfo,$requestInfo=null)
    // {
    //     $requested['qty'] = 5;
    //     return [$productInfo,$requestInfo];
    // }
    
    /** After plugin */ 
    // public function afterGetName(MainProduct $subject,$result)
    // {
    //     return $result.'product name changed';
    // }

    /** Around plugin */ 
    // public function aroundAddProduct(MainProduct $subject,\Closure $proceed,$productInfo,$requestInfo=null)
    // {
    //     $requested['qty'] = 5;
    //     if(cond==true){
    //         $result = $proceed($productInfo,$requestInfo);
    //         return $result;
    //     }
    //     return null ;
    // }
}