<?php

namespace System\Model;

use Zend\Db\TableGateway\TableGateway;

class UserRoleTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getRolesIdsByUser($idUser) {
        $rolesIds = array();
        $resultSet = $this->tableGateway->select(array('id_user' => $idUser));
        foreach ($resultSet as $row) {
            $rolesIds[] = $row->id_role;
        }
        return $rolesIds;
    }
    
    public function setUserRoles($idUser, array $rolesIds) {
        $this->tableGateway->delete(array('id_user' => $idUser));
        foreach ($rolesIds as $idRole) {
            $data = array(
                'id_user' => $idUser,
                'id_role' => $idRole
            );
            $this->tableGateway->insert($data);
        }
    }

}