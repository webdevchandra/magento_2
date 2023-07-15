<?php
namespace Webdev\Helloworld\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Webdev\Helloworld\Api\Data\DataInterface;
use Webdev\Helloworld\Model\ResourceModel\Data as ResourceModel;

class Data extends AbstractModel implements DataInterface, IdentityInterface
{
    const CACHE_TAG = 'students';

    protected function _construct() 
    {
        $this->_init(ResourceModel::class);
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getStudentId()];
    }
    public function getStudentId()
    {
        return $this->getData('student_id');
    }
    public function setStudentId($studentId)
    {
        return $this->setData('student_id', $studentId);
    }
    public function getStudentName()
    {
        return $this->getData('student_name');
    }
    public function setStudentName($studentName)
    {
        return $this->setData('student_name', $studentName);
    }
    public function getStudentRollNo()
    {
        return $this->getData('student_roll_no');
    }
    public function setStudentRollNo($studentRollNo)
    {
        return $this->setData('student_roll_no', $studentRollNo);
    }
    public function getStudentStatus()
    {
        return $this->getData('student_status');
    }
    public function setStudentStatus($studentStatus)
    {
        return $this->setData('student_status', $studentStatus);
    }
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }
    public function setCreatedAt($createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }
    public function setUpdatedAt($udpateddAt)
    {
        return $this->setData('updated_at', $udpateddAt);
    }

}
