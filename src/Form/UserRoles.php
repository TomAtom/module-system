<?php

namespace System\Form;

use Zend\Form\Form;

class UserRoles extends Form
{
    
    public function __construct($name = null)
    {
        parent::__construct('rights');
        $this->setAttribute('method', 'post');
    }
    
    public function setRolesTypes(\Zend\Db\ResultSet\ResultSet $rolesTypes) {
        $fieldSet = new \Zend\Form\Fieldset('roles');
        foreach ($rolesTypes as $roleType) {
            $element = new \Zend\Form\Element\Checkbox($roleType->id_role);
            $element->setLabel($roleType->name);
            $fieldSet->add($element);
        }
        $this->add($fieldSet);
        $element = new \Zend\Form\Element\Submit('save');
        $element->setValue('Uložit');
        $this->add($element);
        $element = new \Zend\Form\Element\Submit('return');
        $element->setValue('Zpět');
        $this->add($element);
    }
    
    public function setRolesIds(array $rolesIds) {
        $rolesFieldSet = $this->get('roles');
        foreach ($rolesIds as $roleId) {
            $element = $rolesFieldSet->get($roleId);
            if (is_object($element)) {
                $element->setChecked(true);
            }
        }
    }
    
}  