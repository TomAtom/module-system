<?php

namespace System\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\ServiceManager\ServiceLocatorAwareInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class User extends \Zend\Db\RowGateway\RowGateway implements InputFilterAwareInterface, ServiceLocatorAwareInterface
{
    protected $inputFilter;
    protected $serviceLocator;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
 
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
    
    public function getArrayCopy() {
        return $this->toArray();
    }
    
    public function exchangeArray($data)
    {
        parent::exchangeArray($this->sanitizeData($data));
    }
    
    public function sanitizeData($data) {
        $dataFiltered = array();
        $dataFiltered['id_user'] = (isset($data['id_user'])) ? $data['id_user'] : null;
        $dataFiltered['name'] = (isset($data['name'])) ? $data['name'] : null;
        $dataFiltered['surname']  = (isset($data['surname'])) ? $data['surname'] : null;
        $dataFiltered['email']  = (isset($data['email'])) ? $data['email'] : null;
        if (array_key_exists('last_login', $data)) {
            $dataFiltered['last_login']  =  $data['last_login'];
        }
        if (array_key_exists('is_admin', $data)) {
            $dataFiltered['is_admin']  =  $data['is_admin'];
        }
        if (array_key_exists('is_active', $data)) {
            $dataFiltered['is_active']  =  $data['is_active'];
        }
        return $dataFiltered;
    }
    
    public function save() {
        $userByEmail = $this->serviceLocator->get('System\Model\UserTable')->getUserByEmail($this->email);
        if (is_object($userByEmail) && $userByEmail->id_user != $this->id_user) {
            throw new \System\Exception\AlreadyExistsException();
        }
        if (!$this->rowExistsInDatabase()) {
          $this->datetime_create = date('Y-m-d H:i:s');
        }
        parent::save();
    }
    
    public function setPassword($pass) {
        $this->password = md5($pass);
        $this->save();
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id_user',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 50,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'surname',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 50,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'EmailAddress',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'password2',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        ),
                    ),
                ),
            )));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}