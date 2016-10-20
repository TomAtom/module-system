<?php

namespace System\Form;

use Zend\Form\Form;

class RoleForm extends Form
{
    
    public function __construct($name = null)
    {
        parent::__construct('role');
        $this->setAttribute('method', 'post');
        
        $element = new \Zend\Form\Element\Hidden('id_role');
        $this->add($element);
        
        $element = new \Zend\Form\Element\Text('name');
        $element->setLabel('název');
        $this->add($element);
        
        $element = new \Zend\Form\Element\Submit('submit');
        $this->add($element);
        
        $element = new \Zend\Form\Element\Submit('return');
        $element->setValue('Zpět');
        $this->add($element);
    }
    
}