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
            // Insert
            // select/Read
            // Update
            // Delete
            $data = $this->_dataRepository->getById(6);
            
            try {
                $this->_dataRepository->save($data);
            } catch (CouldNotSaveException $e) {
                echo $e->getMessage();
            }
           
        }
    }