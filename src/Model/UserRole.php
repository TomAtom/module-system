<?php

namespace System\Model;

class UserRole {

  public $id_role;
  public $id_user;

  public function exchangeArray($data) {
    $this->id_role = (!empty($data['id_role'])) ? $data['id_role'] : null;
    $this->id_user = (!empty($data['id_user'])) ? $data['id_user'] : null;
  }

}