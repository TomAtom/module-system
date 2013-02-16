<?php

namespace System\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable {

    protected $gateway;

    public function __construct(TableGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function fetchAll() {
        $resultSet = $this->gateway->select();
        return $resultSet;
    }

    public function getUser($id) {
        $id = (int) $id;
        $rowset = $this->gateway->select(array('id_user' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserByEmail($email) {
        $rowset = $this->gateway->select(array('email' => $email));
        return $rowset->current();
    }
}