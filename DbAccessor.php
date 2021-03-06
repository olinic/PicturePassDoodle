<?php
require_once('DoodleWorker.php');

class DbAccessor {
	protected $pdo;
	protected $dsn; // used to make db connection
	protected $dWorker;

	function __construct() {
		// connect to the database
		$details = $this->loadDetails('db.ini');
		$dsn = "$details[dbType]:dbname=$details[dbName];host=$details[host]";

		try {
			$this->pdo = new PDO($dsn, $details['user'], $details['pass']);
		} catch (PDOException $e) {
			echo "Error connecting to database";
		}

		$this->dWorker = new DoodleWorker();
	}

	function loadDetails($fileName) {
		// returns an associative array
		return parse_ini_file($fileName);
	}

	function userExists($username) {
		$sql = "SELECT COUNT(username) FROM users WHERE username = ?";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(array($username));

		$count = $stmt->fetch()[0];
		return $count >= 1;	// should not be greater than 1, but just in case
	}

	function addUser($username, $pass, $doodle) {

		// make password hash
		$hash = password_hash($pass, PASSWORD_DEFAULT);

		$date = date("Y-m-d");

		// insert into db
		$sql = "INSERT INTO users (username, password, doodle, registrationDate) VALUES (?, ?, ?, ?)";
		$stmt = $this->pdo->prepare($sql);

		// execute statement
		$success = false;
		try {
			$success = $stmt->execute(array($username, $hash, $doodle, $date));

		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		return $success; // return true or false
	}



	function authPassword($username, $pass) {
		// returns true / false - whether user is authenticated by the given password

		// get hash from db
		if ($this->userExists($username)) {
			$sql = "SELECT password FROM users WHERE username = ?";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($username));
			$hash = $stmt->fetch()['password'];

		} else {
			// user does not exist
			return false;
		}
		// check
		return password_verify($pass, $hash);

	}

	function authDoodle($username, $doodle) {
		// returns true / false - whether user is authenticated by the given doodle


		if ($this->userExists($username)) {
			$sql = "SELECT doodle FROM users WHERE username = ?";
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($username));
			$original = $stmt->fetch()['doodle'];

		} else {
			// user does not exist
			return false;
		}

		return $this->dWorker->verifyDoodle($doodle, $original);

	}

	function getWorker() {
		return $this->dWorker;
	}

}

?>
