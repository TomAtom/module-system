<?php

namespace SystemTest\Model;

class UserTest extends \PHPUnit\Framework\TestCase {

  public function testExchangeArraySetsPropertiesCorrectly() {
    $user = new \System\Entity\User();
    $data = array('id_user' => 1,
        'name' => 'name',
        'surname' => 'surname',
        'email' => 'email',
        'last_login' => 'last_login',
        'is_admin' => true,
        'is_active' => true);
    $user->exchangeArray($data);
    $this->assertSame($data['name'], $user->name, '"name" was not set correctly');
    $this->assertSame($data['surname'], $user->surname, '"surname" was not set correctly');
    $this->assertSame($data['email'], $user->email, '"email" was not set correctly');
    $this->assertSame($data['last_login'], $user->last_login, '"last_login" was not set correctly');
    $this->assertSame($data['is_admin'], $user->is_admin, '"is_admin" was not set correctly');
    $this->assertSame($data['is_active'], $user->is_active, '"is_active" was not set correctly');
  }

  public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent() {
    $user = new \System\Entity\User();
    $data = array('id_user' => 1,
        'name' => 'name',
        'surname' => 'surname',
        'email' => 'email',
        'last_login' => 'last_login',
        'is_admin' => true);
    $user->exchangeArray($data);
    $user->exchangeArray(array('id_user' => 1));
    $this->assertNull($user->name, '"name" should have defaulted to null');
    $this->assertNull($user->surname, '"surname" should have defaulted to null');
    $this->assertNull($user->email, '"email" should have defaulted to null');
    $this->assertNotNull($user->last_login, '"last_login" should not exist');
    $this->assertNotNull($user->is_admin, '"is_admin" should not exist');
  }

}
