<?php

namespace System\Model;

class Right
{
    public $id_role;
    public $controller;
    public $action;

    public function exchangeArray($data)
    {
        $this->id_role = (isset($data['id_role'])) ? $data['id_role'] : null;
        $this->controller = (isset($data['controller'])) ? $data['controller'] : null;
        $this->action = (isset($data['action'])) ? $data['action'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
}
