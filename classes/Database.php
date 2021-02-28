<?php 

class Database {
	private static $instance = null;
	private $pdo;
	private $query;
	private $error;
	private $results;
	private $count;

	private function __construct()
	{
		try {
			$this->pdo = new PDO('mysql:host=localhost; dbname=test2', 'mad', '');
		} catch (PDOException $exception) {
			die($exeption->getMessage());
		}
	}

	public static function getInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new Database();
		}
		return self::$instance;
	}

	public function query($sql, $params = []) {
		// var_dump($params); die;
		$this->error = false;
		$this->query = $this->pdo->prepare($sql);

		if(count($params)) {
			$i = 1;
			foreach ($params as $param) {				
				$this->query->bindValue($i, $param);
				$i++;
			}			
		}

		if(!$this->query->execute()) {
			$this->error = true;
		}
		
		$this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
		$this->count = $this->query->rowCount();
		return $this;
	}

	public function error() {
		return $this->error;
	}

	public function results() {
		return $this->results;
	}

	public function count() {
		return $this->count;
	}

	public function get($table, $where = []) {		

		return $this->action('SELECT *', $table, $where);
	}

	public function delete($table, $where = []) {		

		return $this->action('DELETE', $table, $where);
	}


	public function action($action, $table, $where = []) {

		if(count($where) === 3 ) {

			$operators = ['=', '>', '<', '>=', "<="];

			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];

			if(in_array($operator, $operators)) {
				$sql= "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				
				if(!$this->query($sql, [$value])->error()) {
					return $this;
				}
			}
		}
		return false;
	}

	public function insert($table, $fields = []) {
		$values = '';
		foreach ($fields as $field) {
			$values .="?,";
		}
		$values = rtrim($values, ",");
		$sql = "INSERT INTO {$table} (`" . implode('`,`', array_keys($fields)) . "`) VALUES (" . $values. ")";
		// var_dump($sql); die;
		if(!$this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

	public function update($table, $id, $fields = []) {
		$set = "";
		foreach ($fields as $key => $field) {
			$set .="{$key} = ?,";
		}
		$set = rtrim($set, ",");

		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

		if($this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

	public function first() {
		return $this->results()[0];
	}
}


// Database::getInstance()->query('SELECT * FROM users');
// Database::getInstance()->query('SELECT * FROM users WHERE username =?', ['Rakhim']);

// $users = Database::getInstance()->get('users', ['name', '=', 'marlin']);
// $users = Database::getInstance()->get('users', ['age', '>', '20']);
// $users = Database::getInstance()->insert('users', ['age' => '20', 'name'=>'marlin']);