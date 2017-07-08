<?php

namespace System\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;

class UserForm extends Form implements InputFilterAwareInterface {

  protected $inputFilter;

  public function __construct($name = null) {
    // we want to ignore the name passed
    parent::__construct('user');
    $this->setAttribute('method', 'post');

    $element = new \Zend\Form\Element\Hidden('id_user');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('name');
    $element->setLabel('jméno');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('surname');
    $element->setLabel('příjmení');
    $this->add($element);

    $element = new \Zend\Form\Element\Text('email');
    $element->setLabel('email');
    $this->add($element);

    $element = new \Zend\Form\Element\Password('password');
    $element->setLabel('heslo');
    $this->add($element);

    $element = new \Zend\Form\Element\Password('password2');
    $element->setLabel('heslo (zopakujte)');
    $this->add($element);

    $element = new \Zend\Form\Element\Checkbox('is_admin');
    $element->setCheckedValue('1');
    $element->setUncheckedValue('0');
    $element->setLabel('administrátor systému');
    $this->add($element);

    $isActive = new \Zend\Form\Element\Checkbox('is_active');
    $isActive->setCheckedValue('1');
    $isActive->setUncheckedValue('0');
    $isActive->setValue('1');
    $isActive->setLabel('může se přihlásit');
    $this->add($isActive);

    $element = new \Zend\Form\Element\Submit('submit');
    $this->add($element);

    $element = new \Zend\Form\Element\Submit('return');
    $element->setValue('Zpět');
    $this->add($element);
  }

  public function getInputFilter() {
    if (!$this->inputFilter) {
      $inputFilter = new InputFilter();
      $factory = new InputFactory();

      $inputFilter->add($factory->createInput(array(
                'name' => 'id_user',
                'required' => true,
                'filters' => array(
                  array('name' => 'Int'),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'StringLength',
                    'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 1,
                      'max' => 50,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'surname',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'StringLength',
                    'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 1,
                      'max' => 50,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'EmailAddress',
                    'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 5,
                      'max' => 100,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'StringLength',
                    'options' => array(
                      'encoding' => 'UTF-8',
                      'min' => 2,
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'password2',
                'required' => true,
                'filters' => array(
                  array('name' => 'StripTags'),
                  array('name' => 'StringTrim'),
                ),
                'validators' => array(
                  array(
                    'name' => 'Identical',
                    'options' => array(
                      'token' => 'password'
                    ),
                  ),
                ),
      )));

      $inputFilter->add($factory->createInput(array(
                'name' => 'is_admin',
                'required' => true
      )));
      
      $inputFilter->add($factory->createInput(array(
                'name' => 'is_active',
                'required' => true
      )));

      $this->inputFilter = $inputFilter;
    }

    return $this->inputFilter;
  }

}