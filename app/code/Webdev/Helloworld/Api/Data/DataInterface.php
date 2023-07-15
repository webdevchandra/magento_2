<?php
namespace Webdev\Helloworld\Api\Data;

interface DataInterface
{

    public function getStudentId();


    public function setStudentId($studentId);

    public function getStudentName();

    public function setStudentName($studentName);

    public function getStudentRollNo();

    public function setStudentRollNo($studentRollNo);

    public function getStudentStatus();

 
    public function setStudentStatus($studentStatus);

    public function getCreatedAt();
  
    public function setCreatedAt($createdAt);

    public function getUpdatedAt();
 
    public function setUpdatedAt($updatedAt);

}