<?php

namespace System\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class RightTable extends AbstractTableGateway {

  protected $table = 'system_rights';

  public function __construct(Adapter $adapter) {
    $this->adapter = $adapter;
    $this->resultSetPrototype = new ResultSet();
    $this->resultSetPrototype->setArrayObjectPrototype(new Right());
    $this->initialize();
  }

  public function fetchAll() {
    $resultSet = $this->select();
    return $resultSet;
  }

  public function getRightsByRole($idRole) {
    return $this->select('id_role = ' . (int) $idRole);
  }

  public function getRight($idRole, $controller, $action) {
    $id = (int) $idRole;
    $rowset = $this->select(array('id_role' => $id,
      'controller' => $controller,
      'action' => $action));
    $row = $rowset->current();
    return $row;
  }

  public function addRight(Right $right) {
    $data = array(
      'id_role' => (int) $right->id_role,
      'controller' => $right->controller,
      'action' => $right->action,
    );
    if (!$this->getRight($data['id_role'], $data['controller'], $data['action'])) {
      $this->insert($data);
    }
  }

  public function deleteRightsByRole($idRole) {
    $this->delete('id_role = ' . (int) $idRole);
  }

}