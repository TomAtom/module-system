<?php
namespace SystemTest\Model;

use System\Model\UserTable;
use System\Model\User;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class UserTableTest extends PHPUnit_Framework_TestCase
{
    public function testFetchAllReturnsAllAlbums()
    {
        $resultSet = new ResultSet();
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                                           array('select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSet));

        $userTable = new UserTable($mockTableGateway);

        $this->assertSame($resultSet, $userTable->fetchAll());
    }
}
