<?php

class SimpleUsers {
	
	private $_storageType = "";
	
	//Encryption & Decryption
	private $_storageKey = "";
	private $_opensslMethod = "bf-ecb";
	
	//MySQL
	private $_MySQLUser = "";
	private $_MySQLPass = "";
	private $_MySQLAddress = "";
	private $_MySQLPort = "";
	private $_MySQLDb = "";
	private $_MySQLTable = "";
	
	private $_MySQLConnection;
	
	public function __construct($storageType = "MySQL", $storageOptions = []){
		if(!function_exists("openssl_encrypt")){
			echo "<b>The openssl library is not installed, this library is required. View the documentation for more info.</b>";
			die();
		}
		
		if($this->_IsAssoc($storageOptions) && $storageOptions != []){
			if(!isset($storageOptions["storageKey"])){
				echo "<b>No storageKey value specified in storageOptions, this value is required. View the documentation for more info.</b>";
				die();
			}else{
				$this->_storageKey = $storageOptions["storageKey"];
			}
		}else{
			echo "<b>The second argument isn't a Array, this argument is required. View the documentation for more info.</b>";
			die();
		}
		
		switch($storageType){
			case "MySQL":
				if(isset($storageOptions["user"]) && isset($storageOptions["pass"]) && isset($storageOptions["address"]) && isset($storageOptions["port"]) && isset($storageOptions["db"]) && isset($storageOptions["table"])){
					if(!function_exists("mysqli_connect")){
						echo "<b>The mysqli library is not installed, this library is required if you use MySQL as storageType. View the documentation for more info.</b>";
						die();
					}
					
					$this->_MySQLUser = $storageOptions["user"];
					$this->_MySQLPass = $storageOptions["pass"];
					$this->_MySQLAddress = $storageOptions["address"];
					$this->_MySQLPort = $storageOptions["port"];
					$this->_MySQLDb = $storageOptions["db"];
					$this->_MySQLTable = $storageOptions["table"];
				}else{
					echo "<b>The following arguments are required if the storageType is set to 'MySQL': user, pass, address, port, db, table. View the documentation for more info.</b>";
					die();
				}
				
				$this->_storageType = $storageType;
				break;
			default:
				echo "<b>The storage type specified (".$storageType.") is not a option, the only option is MySQL for now. View the documentation for more info.</b>";
				die();
		}
	}
	
	public function addUser($name, $password, $data = []){
		if(!isset($name) || empty($name)){
			echo "<b>The argument name is required. View the documentation for more info.</b>";
			die();
		}
		
		if(!isset($password) || empty($password)){
			echo "<b>The argument password is required. View the documentation for more info.</b>";
			die();
		}
		
		return $this->_addUser($name, $password, $data);
	}
	
	public function removeUser($UserID){
		if(!isset($UserID) || empty($UserID)){
			echo "<b>The argument UserID is required. View the documentation for more info.</b>";
			die();
		}
		
		return $this->_removeUser($UserID);
	}
	
	public function validateLogin($name, $password){
		if(!isset($name) || empty($name)){
			echo "<b>The argument name is required. View the documentation for more info.</b>";
			die();
		}
		
		if(!isset($password) || empty($password)){
			echo "<b>The argument password is required. View the documentation for more info.</b>";
			die();
		}
		
		return $this->_validateLogin($name, $password);
	}
	
	public function getUserId($name){
		if(!isset($name) || empty($name)){
			echo "<b>The argument name is required. View the documentation for more info.</b>";
			die();
		}
		
		return $this->_getUserID($name);
	}
	
	public function getUserData($UserID){
		if(!isset($UserID) || empty($UserID)){
			echo "<b>The argument UserID is required. View the documentation for more info.</b>";
			die();
		}
		
		return $this->_getUserData($UserID);
	}
	
	public function setUserData($UserID, $key, $value){
		if(!isset($UserID) || empty($UserID)){
			echo "<b>The argument UserID is required. View the documentation for more info.</b>";
			die();
		}
		
		if(!isset($key) || empty($key)){
			echo "<b>The argument key is required. View the documentation for more info.</b>";
			die();
		}
		
		if(!isset($value)){
			echo "<b>The argument value is required. View the documentation for more info.</b>";
			die();
		}
		return $this->_setUserData($UserID, $key, $value);
	}
	
	public function userExists($name){
		if(!isset($name) || empty($name)){
			echo "<b>The argument name is required. View the documentation for more info.</b>";
			die();
		}
		
		return $this->_userExists($name);
	}
	
	public function setupMySQLTable(){
		return $this->_setupMySQLTable();
	}
	
	private function _addUser($username, $password, $data){
		switch($this->_storageType){
			case "MySQL":
				if($this->_userExists($username)){
					return false;
				}
				
				$this->_MySQLConnect();
				
				$name = $this->_MySQLConnection->real_escape_string($this->_encryptData($username));
				$password = $this->_MySQLConnection->real_escape_string($this->_hashPassword($password));
				$data = $this->_MySQLConnection->real_escape_string($this->_encryptData(json_encode($data)));
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQuery("INSERT INTO $table (name, password, data) VALUES ('$name', '$password', '$data')");
				
				if($query){
					$this->_MySQLCloseConnection();	
					return $this->_getUserID($username);
				}else{
					$this->_MySQLCloseConnection();
				}
				return false;
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function _removeUser($UserID){
		switch($this->_storageType){
			case "MySQL":
				$this->_MySQLConnect();
				
				$id = $this->_MySQLConnection->real_escape_string($UserID);
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQuery("DELETE FROM $table WHERE id=$id");
				
				if($query){
					$this->_MySQLCloseConnection();	
					return true;
				}else{
					$this->_MySQLCloseConnection();
				}
				return false;
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function _validateLogin($name, $password){
		switch($this->_storageType){
			case "MySQL":
				$this->_MySQLConnect();
				
				$name = $this->_MySQLConnection->real_escape_string($this->_encryptData($name));
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQueryResult("SELECT password FROM $table WHERE name='$name'");
				
				if($query->num_rows === 0){
					$this->_MySQLCloseConnection();
					return false;
				}
				
				$row = $query->fetch_array(MYSQLI_ASSOC);
				
				if($this->_verifyHash($row["password"], $password)){
					$this->_MySQLCloseConnection();	
					return true;
				}else{
					$this->_MySQLCloseConnection();
				}
				return false;
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function _getUserID($name){
		switch($this->_storageType){
			case "MySQL":
				$this->_MySQLConnect();
				
				$name = $this->_MySQLConnection->real_escape_string($this->_encryptData($name));
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQueryResult("SELECT id FROM $table WHERE name='$name'");
				
				if($query->num_rows === 0){
					$this->_MySQLCloseConnection();
					return false;
				}
				
				$row = $query->fetch_array(MYSQLI_ASSOC);
				
				$this->_MySQLCloseConnection();
				
				return $row["id"];
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function _getUserData($UserID){
		switch($this->_storageType){
			case "MySQL":
				$this->_MySQLConnect();
				
				$id = $this->_MySQLConnection->real_escape_string($UserID);
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQueryResult("SELECT data FROM $table WHERE id=$id");
				
				if($query->num_rows === 0){
					$this->_MySQLCloseConnection();
					return false;
				}
				
				$row = $query->fetch_array(MYSQLI_ASSOC);
				
				$this->_MySQLCloseConnection();
				
				return json_decode($this->_decryptData($row["data"]), true);
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function _setUserData($UserID, $key, $value){
		switch($this->_storageType){
			case "MySQL":
				$this->_MySQLConnect();
				
				$id = $this->_MySQLConnection->real_escape_string($UserID);
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQueryResult("SELECT data FROM $table WHERE id=$id");
				
				if($query->num_rows === 0){
					$this->_MySQLCloseConnection();
					return false;
				}
				
				$row = $query->fetch_array(MYSQLI_ASSOC);
				
				$data = json_decode($this->_decryptData($row["data"]), true);
				
				$data[$key] = $value;
				
				$data = $this->_MySQLConnection->real_escape_string($this->_encryptData(json_encode($data)));
				
				$query = $this->_MySQLRunQuery("UPDATE $table SET data='$data' WHERE id=$id");
				
				if($query){
					$this->_MySQLCloseConnection();	
					return true;
				}else{
					$this->_MySQLCloseConnection();
				}
				return false;
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	private function _setupMySQLTable(){
		if($this->_storageType == "MySQL"){
			$this->_MySQLConnect();
			
			$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
			$query = $this->_MySQLRunQuery("CREATE TABLE $table (id int NOT NULL AUTO_INCREMENT, name MEDIUMTEXT, password MEDIUMTEXT, data MEDIUMTEXT, PRIMARY KEY (id))");
				
			if($query){
				$this->_MySQLCloseConnection();	
				return true;
			}else{
				$this->_MySQLCloseConnection();
			}
			
			return false;
		}else{
			return false;
		}
	}
	
	private function _userExists($name){
		switch($this->_storageType){
			case "MySQL":
				$this->_MySQLConnect();
				
				$name = $this->_MySQLConnection->real_escape_string($this->_encryptData($name));
				$table = $this->_MySQLConnection->real_escape_string($this->_MySQLTable);
				
				$query = $this->_MySQLRunQueryResult("SELECT data FROM $table WHERE name='$name'");
				
				if($query->num_rows === 0){
					$this->_MySQLCloseConnection();
					return false;
				}else{
					$this->_MySQLCloseConnection();
					return true;
				}
				
				break;
			default:
				return false;
		}
		
		return false;
	}
	
	//MySQL functions
	private function _MySQLConnect(){
		$this->_MySQLConnection = new mysqli($this->_MySQLAddress, $this->_MySQLUser, $this->_MySQLPass, $this->_MySQLDb, $this->_MySQLPort);
	
		if($this->_MySQLConnection->connect_error){
    		echo "<b>An error occured while attempting to connect to the specified MySQL server: ".$this->_MySQLConnection->connect_error."</b>";
    		die();
		} 
	}
	
	private function _MySQLCloseConnection(){
		$this->_MySQLConnection->close();
	}
	
	private function _MySQLRunQuery($query){
		if($this->_MySQLConnection->query($query) === TRUE){
		    return true;
		}else{
		    return false;
		}
	}
	
	private function _MySQLRunQueryResult($query){
		return $this->_MySQLConnection->query($query);
	}
	
	//Hash functions
	private function _hashPassword($password){
		return password_hash($password, PASSWORD_DEFAULT);
	}
	
	private function _verifyHash($hash, $password){
		return password_verify($password, $hash);
	}
	
	//Encrypt & decrypt functions
	private function _encryptData($string){
		return openssl_encrypt($string, $this->_opensslMethod, $this->_storageKey);
	}
	
	private function _decryptData($string){
		return openssl_decrypt($string, $this->_opensslMethod, $this->_storageKey);
	}
	
	//Other functions
	private function _IsAssoc($array){
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
	}
}

?>
