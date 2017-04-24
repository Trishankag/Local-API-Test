<?php
error_reporting(0);
 	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
		const DB = "phpapi";

		private $db = NULL;
		private $mysqli = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		/*
		 *  Connect to Database
		*/
		private function dbConnect(){
			$this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
		}
		
		/*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		}
				
		private function login(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$email = $this->_request['email'];		
			$password = $this->_request['pwd'];
			if(!empty($email) and !empty($password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$query="SELECT uid, name, email FROM users WHERE email = '$email' AND password = '".md5($password)."' LIMIT 1";
					$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

					if($r->num_rows > 0) {
						$result = $r->fetch_assoc();	
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json($result), 200);
					}
					$this->response('', 204);	// If no records "No Content" status
				}
			}
			
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}
		
		private function users(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.id, c.Name, c.Email, c.created_date, c.isadmin FROM user_info c order by c.id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
		private function user(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct c.id, c.Name, c.Email, c.created_date, c.isadmin FROM user_info c where c.id=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function insertUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$user = json_decode(file_get_contents("php://input"),true);
			//print_r($user);
			$column_names = array('Name', 'Email' , 'created_date', 'isadmin');
			$keys = array_keys($user);
			$value = array_values($user);
			//print_r($value);
			$columns = '';
			$values = '';
			$values1 = '';
			foreach($column_names as $desired_key){ 
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $user[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				
				$values = $values."'".$desired_key."',";
				//echo $values;
			}
			
			foreach($column_names as $desired_key1){ 
			   if(!in_array($desired_key1, $keys)) {
			   		$desired_key1 = '';
				}else{
					$desired_key1 = $user[$desired_key1];
				}
				
				$values1 = $values1."'".$desired_key1."',";
//echo $values1."some value";
				//echo $values;
			}
			
			$query = "INSERT INTO user_info(".trim($columns,',').") VALUES(".trim($values1,',').")";
			//echo $query;
			if(!empty($user)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "User Created Successfully.", "data" => $user);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}
		
		
		private function updateUser(){
			if($this->get_request_method() != "PUT"){
				$this->response('',406);
			}
			$customer = json_decode(file_get_contents("php://input"),true);
			//print_r($customer);
			//$id = (int)$customer['id'];
			$id = $_GET['id'];
			//echo $id;
			$column_names = array('Name', 'Email' , 'created_date', 'isadmin');
			$keys = array_keys($customer['customer']);
			$columns = '';
			$values = '';
			$values1 = '';
			foreach($column_names as $desired_key){ 
			   //if(!in_array($desired_key, $keys)) {
			   	//	$$desired_key = '';
				//}else{
				//	$$desired_key = $customer['customer'][$desired_key];
				//}
                $$desired_key = $customer[$desired_key];
				$columns = $columns.$desired_key."='".$$desired_key."',";
			
			}
		
			
			$query = "UPDATE user_info SET ".trim($columns,',')." WHERE id=$id";
			//echo $query;
			if(!empty($customer)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "User ".$id." Updated Successfully.", "data" => $customer);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}
		
		private function deleteUser(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM user_info WHERE id = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}
		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>