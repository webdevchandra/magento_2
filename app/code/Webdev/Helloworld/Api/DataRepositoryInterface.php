<?php
namespace Webdev\Helloworld\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Webdev\Helloworld\Api\Data\DataInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface DataRepositoryInterface
{
    public function save(DataInterface $data);


    public function getById($id);
    public function getList(SearchCriteriaInterface $criteria);

    public function delete(DataInterface $data);

    public function deleteById($id);
}