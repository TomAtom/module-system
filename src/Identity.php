<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace System;

class Identity implements \Zend\Stdlib\ArraySerializableInterface {

  public $id_user;
  public $name;
  public $surname;
  public $email;
  public $last_login;
  public $is_admin;
  public $is_active;
  public $rolesIds = [];

  public function getArrayCopy(): array {
    return \get_object_vars($this);
  }

  public function exchangeArray(array $data) {
    $this->id_user = (isset($data['id_user'])) ? $data['id_user'] : null;
    $this->name = (isset($data['name'])) ? $data['name'] : null;
    $this->surname = (isset($data['surname'])) ? $data['surname'] : null;
    $this->email = (isset($data['email'])) ? $data['email'] : null;
    $this->last_login = (isset($data['last_login'])) ? $data['last_login'] : null;
    $this->is_admin = (isset($data['is_admin'])) ? $data['is_admin'] : null;
    $this->is_active = (isset($data['is_active'])) ? $data['is_active'] : null;
  }

}