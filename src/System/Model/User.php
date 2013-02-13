<?php

namespace System\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User extends \Zend\Db\RowGateway\RowGateway implements InputFilterAwareInterface
{
    protected $inputFilter;
    protected $userTable;
    
    public function __construct($primaryKeyColumn, $table, $adapterOrSql, $userTable) {
        $this->userTable = $userTable;
        parent::__construct($primaryKeyColumn, $table, $adapterOrSql);
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
        $dataFiltered['last_login']  = (isset($data['last_login'])) ? $data['last_login'] : null;
        if (array_key_exists('id_role', $data)) {
            $dataFiltered['id_role']  =  $data['id_role'];
        }
        if (array_key_exists('is_admin', $data)) {
            $dataFiltered['is_admin']  =  $data['is_admin'];
        }
        return $dataFiltered;
    }
    
    public function save() {
        $userByEmail = $this->userTable->getUserByEmail($this->email);
        if (is_object($userByEmail) && $userByEmail->id_user != $this->id_user) {
            throw new \System\Exception\AlreadyExistsException();
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
                            'max'      => 20,
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
                            'max'      => 20,
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
                            'max'      => 25,
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
