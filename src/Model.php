<?php

namespace Slim\Mvc;

class Model extends \Illuminate\Database\Eloquent\Model
{
	public $timestamps = FALSE;

	/**
	 *
	 */
	public function __construct()
	{
		$this->doBeforeConfigure();
		parent::__construct();
		$this->doAfterConfigure();
	}

	/**
 	 * Overwrite  insert method, to get id as return
 	 * @return ID inserted 
	 */
	public function insert()
	{
		$args = func_get_args();

		parent::insert(...$args);

		try {
			$id = parent::getConnection()->getPdo()->lastInsertId();
		}
		catch(\Exception $e) {

			// if the erro was because table doesnt have auto increment columns, just pass
			if($e->getCode() == 55000) {
				$id = NULL;
			}
			else {
				throw $e;
			}
		}
		
		return $id;
	}

	/**
	 * 
	 */
	static public function executeQuery($query, $params=[])
	{
		$capsule = new \Illuminate\Database\Capsule\Manager();

		$connection = $capsule->connection();
		$db = $connection->getPdo();
		$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 1);

		$execution = $db->prepare($query);
		$execution->execute($params);
		$result = $execution->fetchAll(\PDO::FETCH_ASSOC);

		return $result;
	}

	/**
	 * Hooks
	 */
	public function doBeforeConfigure() {}
	public function doAfterConfigure() {}
}
