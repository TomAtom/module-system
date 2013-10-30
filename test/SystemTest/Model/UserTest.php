<?php

namespace SystemTest\Model;

use PHPUnit_Framework_TestCase;

class UserTest extends PHPUnit_Framework_TestCase {

  public function testExchangeArraySetsPropertiesCorrectly() {
    $sm = \SystemTest\Bootstrap::getServiceManager();
    $user = $sm->get('System\Model\User');
    $data = array('id_user' => 1,
        'name' => 'name',
        'surname' => 'surname',
        'email' => 'email',
        'last_login' => 'last_login',
        'id_role' => 123,
        'is_admin' => true);
    $user->exchangeArray($data);
    $this->assertSame($data['name'], $user->name, '"name" was not set correctly');
    $this->assertSame($data['surname'], $user->surname, '"surname" was not set correctly');
    $this->assertSame($data['email'], $user->email, '"email" was not set correctly');
    $this->assertSame($data['last_login'], $user->last_login, '"last_login" was not set correctly');
    $this->assertSame($data['id_role'], $user->id_role, '"id_role" was not set correctly');
    $this->assertSame($data['is_admin'], $user->is_admin, '"is_admin" was not set correctly');
  }

  public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent() {
    $sm = \SystemTest\Bootstrap::getServiceManager();
    $user = $sm->get('System\Model\User');
    $data = array('id_user' => 1,
        'name' => 'name',
        'surname' => 'surname',
        'email' => 'email',
        'last_login' => 'last_login',
        'id_role' => 123,
        'is_admin' => true);
    $user->exchangeArray($data);
    $user->exchangeArray(array('id_user' => 1));
    $this->assertNull($user->name, '"name" should have defaulted to null');
    $this->assertNull($user->surname, '"surname" should have defaulted to null');
    $this->assertNull($user->email, '"email" should have defaulted to null');
    $this->assertNull($user->last_login, '"last_login" should have defaulted to null');
    $this->assertObjectNotHasAttribute('id_role', $user, '"id_role" should hnot exist');
    $this->assertObjectNotHasAttribute('is_admin', $user, '"is_admin" should not exist');
  }

}