<?php

namespace System\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable {

  protected $gateway;

  public function __construct(TableGateway $gateway) {
    $this->gateway = $gateway;
  }

  /**
   * @return \Zend\Db\TableGateway\TableGateway
   */
  public function getGateway() {
    return $this->gateway;
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
  
  public function setPassword($idUser, $pass) {
    $this->getGateway()->update(['password' => \md5($pass)], array('id_user' => $idUser));
  }

  public function saveUser(\System\Model\User $user) {
    $data = array(
      'id_user' => $user->id_user,
      'name' => $user->name,
      'surname' => $user->surname,
      'email' => $user->email,
      'last_login' => $user->last_login,
      'is_admin' => $user->is_admin,
      'is_active' => $user->is_active,
      'datetime_create' => $user->datetime_create,
    );
    $userByEmail = $this->getUserByEmail($user->email);
    if (is_object($userByEmail) && $userByEmail->id_user != $user->id_user) {
      throw new \System\Exception\AlreadyExistsException();
    }
    $id = (int) $user->id_user;
    if ($id == 0) {
      $data['datetime_create'] = date('Y-m-d H:i:s');
      $this->getGateway()->insert($data);
      $user->id_user = $this->getGateway()->getLastInsertValue();
    } else {
      if ($this->getUser($id)) {
        $this->getGateway()->update($data, array('id_user' => $id));
      } else {
        throw new \Exception('Photo id does not exist');
      }
    }
  }

  public function deleteUser($id) {
    $this->getGateway()->delete(array('id_user' => $id));
  }

}