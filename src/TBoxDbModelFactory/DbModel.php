<?php


namespace TBoxDbModelFactory;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;

class DbModel extends TableGateway implements DbModelInterface
{

	public function create(array $data)
	{
		return $this->insert($data);
	}

	public function updateEntity(array $set,$where = null)
	{
		return $this->update($set,$where);
	}

	public function fetchEntity(array $where)
	{
         $rowset = $this->select($where);
         $row = $rowset->current();
         return $row;
	}

	public function fetch(array $where)
	{
		return $this->select($where);
         
	}

	public function fetchAll()
	{
		return $this->select();
	}

	public function remove(array $where)
	{
     	return $this->delete($where);
	}

	public function truncate()
	{
	    $query = $this->getAdapter()->query('TRUNCATE TABLE '.$this->getTable());
    	return $query->execute();
	}

	public function customFetch($where = array(),$joins = array(),$columns = array(),$order = null,$limit = null,$offset = null,$groupBy = null)
	{
        $sql = new Sql($this->Adapter);
        $select = new Select();
        $select->from(array('main' => $this->getTable()));
        if( isset($joins) && !empty($joins) )
        {
        	foreach ($joins as $table => $join) 
        	{
        		if( !isset($join['expression']) || empty($join['expression']) ){
        			continue;
        		}
        		$joinColumns = array();
                if( isset($join['columns']) && !empty($join['columns']) ){
                    $joinColumns = $join['columns'];
                }
                $type = Select::JOIN_INNER;
        		if( isset($join['type']) && !empty($join['type']) ){
        			$type = $join['type'];
        		}
        		$select->join($table,new Expression($join['expression']),$joinColumns,$type );
        	}
        }
        if( isset($where) && !empty($where) ){
        	$whereObject = new Where();
        	foreach($where as $condition => $parameters){
        		foreach ($parameters as $parameter) {
        			call_user_func_array(array($whereObject,$condition),$parameter);
        		}
        	}
        	$select->where($whereObject);
        }
        if( isset($columns) && !empty($columns) ){
        	$select->columns($columns);
        }
        if( isset($order) && !empty($order) ){
        	$select->order($order);
        }
        if( isset($limit) && !empty($limit) ){
        	$select->limit($limit);
        }
        if( isset($offset) && !empty($offset) ){
        	$select->offset($offset);
        }
        if( isset($groupBy) && !empty($groupBy) ){
        	$select->group($groupBy);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $this->getResultSetPrototype();
        $resultSet->setArrayObjectPrototype(new \ArrayObject());
        $resultSet->initialize($statement->execute());

        return $resultSet;

	}

    public function runBulkInsert(array $tableColumns,array $bulkValues,$truncateFirst = false)
    {
        if($truncateFirst)
        {
            if(($this->truncate()) == FALSE)
            {
                return false;
            }
        }
        $columns = array_flip($tableColumns);
        $insertValues = array();
        foreach($bulkValues as $value)
        {
            $intersection = array_intersect_key($value, $columns);
            $insert = array_intersect_key(array_replace($columns, $intersection), $intersection);
            $insertValues[] = "('" . implode("','", array_map('addslashes', $insert)) . "')";
        }
        if( !empty($insertValues) )
        {
            $strColumns = '`' . implode("`,`", $tableColumns) . '`';
            $values = implode(' , ', $insertValues);
            $table = $this->getTable();
            $query = "  INSERT INTO {$table} ({$strColumns}) VALUES {$values};";
            return $this->execute($query);
        }

        return false;
    }

    private function execute($query)
    {
        $adapter = $this->getAdapter();
        $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        return $query;
    }

    public function getConnection()
    {
        return $this->getAdapter()->getDriver()->getConnection();
    }
}