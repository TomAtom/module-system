<?php

namespace System\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class RoleTable extends AbstractTableGateway
{
    protected $table ='system_roles';
    const ID_ROLE_GUEST = 1;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Role());
        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getRole($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('id_role' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getRoleByName($name) {
        $rowset = $this->select(array('name' => $name));
        return $rowset->current();
    }

    public function saveRole(Role $role)
    {
        $roleByName = $this->getRoleByName($role->name);
        if (is_object($roleByName) && $roleByName->id_role != $role->id_role) {
            throw new \System\Exception\AlreadyExistsException();
        }
        $data = array(
            'name' => $role->name,
        );
        $id = (int)$role->id_role;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getRole($id)) {
                $this->update($data, array('id_role' => $id));
            } else {
                throw new \Exception('Role id does not exist');
            }
        }
    }

    public function deleteRole($id)
    {
        if ($id != self::ID_ROLE_GUEST) {
            $this->delete(array('id_role' => $id));
        }
    }
}
