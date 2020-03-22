<?php

namespace TBoxDbModelFactory;

use PHPUnit_Framework_TestCase as TestCase;
use TBoxDbModelFactory\DbModelFactory;


class DbModelFactoryTest extends TestCase
{

	private $_dbmodel;

	private function getDbConnection()
	{
		return  [
	        'driver'            => 'Pdo',
	        'dsn'               => 'mysql:dbname=trip_dynapack;host=192.168.1.15',
	        'driver_options'    => [
	            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	        ],
	        'username'          => 'trip',
	        'password'          => 'trippassword'
	    ];
	}

	private function getEntityData()
	{
		return [
				'Name' 			=> 'db-model-phpunit' . microtime(true),
				'LoginUrl' 		=> 'test',
				'UIF'			=> 'test',
				'PrivateKey' 	=> 'test',
				'BaseRole' 		=> 'test'
		];
	}

    public function setUp()
    {
    	$dbModelFactory = new DbModelFactory();
    	$this->_dbmodel = $dbModelFactory('arba_apps',null,$this->getDbConnection());
    }

    public function test_create()
    {
    	$data = $this->getEntityData();
    	$this->_dbmodel->create($data);

		$result = $this->_dbmodel->fetch(array('Name' => $data['Name']))->current();
		$this->assertNotEmpty($result);
		$this->assertEquals($result['Name'],$data['Name']);

    	return $data['Name'];
    }


    /**
     * @depends test_create
     */
    public function test_remove($id)
    {
    	$result = $this->_dbmodel->remove(array('Name' => $id));
    	$this->assertNotEmpty($result);

    	$result = $this->_dbmodel->fetch(array('Name' => $id))->current();
    	$this->assertEmpty($result);

    }

}