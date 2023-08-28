<?php
namespace Webdev\Helloworld\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddProduct implements ObserverInterface
{
    public function execute(Observer $observer)
    {
       
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('Observer executed'.print_r($observer->getEvent()->debug(),true));
    }
}