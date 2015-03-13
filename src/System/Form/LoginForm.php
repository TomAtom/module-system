<?php

namespace System\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
	 
/**
 * @uses Zend\Form\Form
 */
use Zend\Form\Form;

class LoginForm extends Form
{
    /**
     * Initialize Form
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        
        $element = new \Zend\Form\Element\Hidden('return');
        $this->add($element);

        $element = new \Zend\Form\Element\Text('email');
        $element->setLabel('Email');
        $this->add($element);
        
        $element = new \Zend\Form\Element\Password('password');
        $element->setLabel('Heslo');
        $this->add($element);
        
        $element = new \Zend\Form\Element\Submit('submit');
        $element->setValue('Přihlásit');
        $this->add($element);
        
        $element = new \Zend\Form\Element\Csrf('csrf');
        $element->setCsrfValidatorOptions(array('timeout' => 60));
        $this->add($element);
        
        $this->setInputFilter($this->createInputFilter());

    }
    
    protected function createInputFilter()
    {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));

        return $inputFilter;
    }
}