<?php

namespace TBoxDbModelFactory;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;

class DbModelFactory
{
    public function __invoke($table,AdapterInterface $adapter = null,$adapterConfig = null ,$prototype = null)
    {

        if( !isset($adapter) || empty($adapter) )
        {
        	if( !isset($adapterConfig) || empty($adapterConfig) ){
        		throw new \Exception('Unable to obtain adapter');
        	}
            $adapter = new Adapter($adapterConfig);
        }

        $resultSetPrototype = new ResultSet();
        if (isset($prototype) && !empty($prototype)) {
        	$resultSetPrototype->setArrayObjectPrototype($prototype);
        }
        


        $model = new DbModel($table, $adapter, null, $resultSetPrototype);

        return $model;
    }
}