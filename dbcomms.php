<?php

class dbcomms {
	public $isConnected;
	protected $datab;

	// connect to database
	public function __construct($user = '', $pass = '', $host = '', $dbname = '', $options = []) {
		try {
			$this->datab = new PDO("mysql:host=".$host."; dbname=".$dbname."; charset=utf8", $user, $pass, $options);
			$this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

			$this->isConnected = TRUE;
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	// disconnect from database
	public function Disconnect() {
		try {
			$this->datab = NULL;
			$this->isConnected = FALSE;
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	// get single row
	public function getRow($table, $where, $operators, $params) {
		$where = explode(",", $where);
		$params = explode(",", $params);
		$operators = explode(",", $operators);
		$paramArray = array();

		if((is_array($where) && is_array($params) && is_array($operators)) && ((count($where) === count($params)) && (count($where) === count($operators)))) {
			try {
				$tmpQuery = "SELECT * FROM ".$table." WHERE ";

				for($i = 0; $i < count($where); $i++) {
					$tmpQuery .= $table."_".$where[$i].$operators[$i].":".$table."_".$where[$i];

					if(($i + 1) < count($where)) {
						$tmpQuery .= " && ";
					}

					$tmpQuery .= " LIMIT 1";

					$paramArray[":".$table."_".$where[$i]] = $params[$i];

					$stmt = $this->datab->prepare($tmpQuery);
					$stmt->execute($paramArray);

					return $stmt->fetch();
				}
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			return NULL;
		}
	}

	// get multiple rows
	public function getRows($table, $where, $operators, $params, $orderBy = 'id', $ascOrDesc = 'ASC') {
		$where = explode(",", $where);
		$params = explode(",", $params);
		$operators = explode(",", $operators);
		$paramArray = array();

		if((is_array($where) && is_array($params) && is_array($operators)) && (count($where) === count($params)) && (count($where) === count($operators))) {
			try {
				$tmpQuery = "SELECT * FROM ".$table." WHERE ";

				for($i = 0; $i < count($where); $i++) {
					$tmpQuery .= $table."_".$where[$i].$operators[$i].":".$table."_".$where[$i];

					if(($i + 1) < count($where)) {
						$tmpQuery .= " && ";
					}

					$paramArray[":".$table."_".$where[$i]] = $params[$i];
				}

				$tmpQuery .= " ORDER BY ".$table."_".$orderBy." ".$ascOrDesc;

				$stmt = $this->datab->prepare($tmpQuery);
				$stmt->execute($paramArray);

				return $stmt->fetchAll();
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			return NULL;
		}
	}

	// insert a row
	public function insertRow($table, $columns, $params) {
		$columns = explode(",", $columns);
		$params = explode(",", $params);
		$paramArray = array();

		$query = NULL;
		$values = NULL;

		if((is_array($columns) && is_array($params)) && (count($columns) === count($params))) {
			try {
				for($i = 0; $i < count($columns); $i++) {
					$query .= $table."_".$columns[$i];
					if(($i + 1) < count($columns)) $query .= ",";

					$values .= ":".$table."_".$columns[$i];
					if(($i + 1) < count($columns)) $values .= ",";

					$paramArray[":".$table."_".$columns[$i]] = $params[$i];
				}

				$this->datab->beginTransaction();

				$stmt = $this->datab->prepare("INSERT INTO ".$table." (".$query.") VALUES(".$values.")");
				$stmt->execute($paramArray);

				$this->datab->commit();
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			return NULL;
		}
	}

	// update a row
	public function updateRow($table, $row, $value, $where, $operators, $params) {
		$where = explode(",", $where);
		$operators = explode(",", $operators);
		$params = explode(",", $params);
		$paramArray = array();

		if((is_array($where) && is_array($operators) && is_array($params)) && (count($where) === count($operators) && count($where) === count($params))) {
			try {
				$query = "UPDATE ".$table." SET ".$table."_".$row."=\"".$value."\" ";

				$query .= " WHERE ";

				if(count($where) != count($params)) {
					return 0;
				} else {
					for($i = 0; $i < count($where); $i++) {
						$query .= $table."_".$where[$i].$operators[$i].":".$table."_".$where[$i];
						if(($i + 1) < count($where)) $query .= " && ";
						$paramArray[":".$table."_".$where[$i]] = $params[$i];
					}
				}

				$stmt = $this->datab->prepare($query);
				$stmt->execute($paramArray);

			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			return NULL;
		}
	}

	// delete a row
	public function deleteRow($table, $where, $operators, $params) {
		$where = explode(",", $where);
		$operators = explode(",", $operators);
		$params = explode(",", $params);
		$paramArray = array();

		if((is_array($where) && is_array($operators) && is_array($params)) && (count($where) === count($operators) && count($where) === count($params))) {
			try {
				$query = "DELETE FROM ".$table." WHERE ";

				for($i = 0; $i < count($where); $i++) {
					$query .= $table."_".$where[$i].$operators[$i].":".$table."_".$where[$i];

					if(($i + 1) < count($where)) {
						$query .= " && ";
					}

					$paramArray[":".$table."_".$where[$i]] = $params[$i];
				}

				$stmt = $this->datab->prepare($query);
				$stmt->execute($paramArray);
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			return NULL;
		}
	}

	// count the number of rows
	public function countRows($table, $column, $where, $operators, $params) {
		$where = explode(",", $where);
		$operators = explode(",", $operators);
		$params = explode(",", $params);
		$paramArray = array();

		if((is_array($where) && is_array($operators) && is_array($params)) && (count($where) === count($operators) && count($where) === count($params))) {
			try {
				$query = "SELECT ".$table."_".$column." FROM ".$table." WHERE ";

				for($i = 0; $i < count($where); $i++) {
					$query .= $table."_".$where[$i].$operators[$i].":".$table."_".$where[$i];
					if(($i + 1) < count($where)) $query .= " && ";
					$paramArray[":".$table."_".$where[$i]] = $params[$i];
				}

				$stmt = $this->datab->prepare($query);
				$stmt->execute($paramArray);

				return $stmt->rowCount();
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	}
}
