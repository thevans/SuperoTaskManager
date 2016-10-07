<?php
	class Rest
	{
		private $code = 200;
		public $contentType = "application/json";
		public $request = array();
		
		public function __construct()
		{
			$this->inputs();
		}
		
		public function response($data, $status)
		{
			$this->code = ($status) ? $status : 200;
			$this->setHeaders();
			
			echo $data;
			exit;
		}
		
		private function getStatusMessage()
		{
			$status = array(
						200 => 'OK',
						201 => 'Created',  
						204 => 'No Content',  
						404 => 'Not Found',  
						406 => 'Not Acceptable');
						
			return ($status[$this->code]) ? $status[$this->code] : $status[500];
		}
		
		public function getRequestMethod()
		{
			return $_SERVER['REQUEST_METHOD'];
		}
		
		private function inputs()
		{
			switch ($this->getRequestMethod()) {
				case "POST":
					$this->request = $this->cleanInputs($_POST);
					break;
				case "GET":
				case "DELETE":
					$this->request = $this->cleanInputs($_GET);
					break;
				case "PUT":
					parse_str(file_get_contents("php://input"), $this->request);
					$this->request = $this->cleanInputs($this->request);
					break;
				default:
					$this->response("", 406);
					break;
			}
		}		
		
		private function cleanInputs($data)
		{
			$cleanInput = array();
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					$cleanInput[$key] = $this->cleanInputs($value);
				}
			} else {
				if (get_magic_quotes_gpc()) {
					$data = trim(stripslashes($data));
				}
				
				$data = strip_tags($data);
				$cleanInput = trim($data);
			}
			
			return $cleanInput;
		}		
		
		private function setHeaders(){
			header("HTTP/1.1 " . $this->code . " " . $this->getStatusMessage());
			header("Content-Type:" . $this->contentType);
		}
	}
	