<?php

namespace TBoxDbModelFactory;

interface DbModelInterface
{
	public function create(array $data);
	public function fetch(array $where);
	public function fetchAll();
	public function remove(array $where);
	public function truncate();
	public function fetchEntity(array $where);
 	public function updateEntity(array $set,$where = null);
	public function customFetch($where = array(),$joins = array(),$columns = array(),$order = null,$limit = null,$offset = null,$groupBy = null);
    public function runBulkInsert(array $tableColumns,array $bulkValues,$truncateFirst = false);
    public function getConnection();

}