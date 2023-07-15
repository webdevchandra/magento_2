<?php
namespace Webdev\Helloworld\Api\Data;
use Magento\Framework\Api\SearchResultsInterface;
interface DataSearchResultsInterface extends SearchResultsInterface
{
   
    public function getItems();
    
    public function setItems( $items);
}