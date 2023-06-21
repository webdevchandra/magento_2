<?php
namespace Webdev\Helloworld\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{   
    protected $_pageFactory;
    protected $_dataFactory;
    public function __construct
    (
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Webdev\Helloworld\Model\DataFactory $dataFactory
    ){
        $this->_pageFactory = $pageFactory;
        $this->_dataFatory = $dataFactory;
        return parent::__construct($context);
    }
    public function execute(){  
        $collection =   $this->_dataFatory->create();
        // insert
        // $collection->setStudentName('webdev chandra');
        // $collection->setStudentRollNo('123456');
        // $collection->setStudentStatus('1');
        // $collection->save();
        // $data = $collection->getCollection();
        // echo '<pre>';print_r($data->getData());
        // die();
        // delete
        // $collection->load(2);
        // $collection->delete();
        // update
        $collection->load(4);
        $collection->setStudentName('webdev Magento2');
        $collection->save();

        // return $this->_pageFactory->create();
    }
}

