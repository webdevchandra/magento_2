<?php
namespace Webdev\Helloworld\Controller\Index;

    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Exception\CouldNotSaveException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\View\Result\PageFactory;
    use Webdev\Helloworld\Api\DataRepositoryInterface;
    use Webdev\Helloworld\Api\Data\DataInterface;

    class Index extends Action
    {
        protected $_pageFactory;
        protected $_dataRepository;
        protected $_dataModel;

        public function __construct(
            Context $context,
            PageFactory $pageFactory,
            DataRepositoryInterface $dataRepository,
            DataInterface $dataInterface
        ) {
            $this->_pageFactory = $pageFactory;
            $this->_dataRepository=$dataRepository;
            $this->_dataModel = $dataInterface;
            return parent::__construct($context);
        }

        public function execute()
        {
            /** INSERT */
            
                // $this->_dataModel->setStudentName("test");
                // $this->_dataModel->setStudentRollNo("111111");
                // $this->_dataModel->setStudentStatus(1);
                // try{
                //     $this->_dataRepository->save($this->_dataModel);
                //     echo 'Record Inserted';
                // }catch(\Exception $e){
                //     die($e->getMessage());
                // }
                // $this->_dataRepository->save($this->_dataModel);


            /** SELECT / READ */

                // $data = $this->_dataRepository->getById(1);
                // echo '<pre>';print_r($data->getData());

            /** UPDATE */

                // $data = $this->_dataRepository->getById(1);
                // $data->setStudentName('webdeev chandra');
                // try{
                //     $this->_dataRepository->save($data);
                //     echo 'Record Updated';
                // }catch(\Exception $e){
                //     die($e->getMessage());
                // }
                // $this->_dataRepository->deleteById(1);

            /** DELETE */

                // $this->_dataRepository->deleteById(2);

            die('end here');
        }
    }