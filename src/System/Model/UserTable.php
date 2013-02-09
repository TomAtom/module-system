<?php

namespace System\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class UserTable extends AbstractTableGateway {

    protected $table = 'system_users';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new User('id_user', $this->table, $adapter, $this));
        $this->initialize();
    }

    public function fetchAll() {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getUser($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id_user' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserByEmail($email) {
        $rowset = $this->select(array('email' => $email));
        return $rowset->current();
    }

    public function create() {
        return new User('id_user', $this->table, $this->adapter, $this);
    }
}