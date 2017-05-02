<?php

namespace SystemTest\Model;

use System\Model\UserTable;
use Zend\Db\ResultSet\ResultSet;

class UserTableTest extends \PHPUnit\Framework\TestCase {

  public function testFetchAllReturnsAllUsers() {
    $resultSet = new ResultSet();
    $mockTableGateway = $this->createMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
    $mockTableGateway->expects($this->once())
            ->method('select')
            ->with()
            ->will($this->returnValue($resultSet));

    $userTable = new UserTable($mockTableGateway);

    $this->assertSame($resultSet, $userTable->fetchAll());
  }

}
