<?
	require_once("Rest.class.php");
	require_once("DatabaseConnection.class.php");
	
	class TaskManagerRestApi extends Rest
	{	
		private $connection = NULL;

		public function __construct()
		{
			parent::__construct();
			$this->connection = DatabaseConnection::getInstance();
		}
		
		public function execute()
		{
			if (empty($_REQUEST["x"]))
				$this->response("", 404);

			$method = strtolower(trim(str_replace("/", "", $_REQUEST["x"])));
			
			if ((int)method_exists($this, $method) > 0)
				$this->$method();
			else
				$this->response("", 404);
		}
				
		private function getAlltasks()
		{	
			if ($this->getRequestMethod() != "GET") {
				$this->response("", 406);
			}
			
			$query = "SELECT tarefa_id,
			                 tarefa_titulo,
							 tarefa_status,
							 tarefa_data_criacao,
							 tarefa_data_edicao
				         FROM tarefa
						 WHERE tarefa_removida = 0
						ORDER BY tarefa_data_criacao DESC";

			$result = $this->connection->query($query);

			if ($result->num_rows > 0) {
				$data = array();
				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				
				$this->response($this->jsonEncode($data), 200);
			}
			
			$this->response("", 204);
		}
		
		private function getTask()
		{
			if ($this->getRequestMethod() != "GET") {
				$this->response("", 406);
			}
			
			$id = (int)$this->request['id'];
			
			if ($id > 0){	
				$query = "SELECT *
				             FROM tarefa
							WHERE tarefa_id = $id";

				$result = $this->connection->query($query);
				
				if ($result->num_rows > 0) {
					$data = $result->fetch_assoc();	
					$this->response($this->jsonEncode($data), 200);
				}
			}
			
			$this->response("", 204);
		}
		
		private function insertTask()
		{
			if ($this->getRequestMethod() != "POST") {
				$this->response("", 406);
			}

			$task = json_decode(file_get_contents("php://input"), true);
			$columnsNames = array('tarefa_titulo', 'tarefa_descricao', 'tarefa_status');
			$keys = array_keys($task);
			$columns = '';
			$values = '';
			
			foreach ($columnsNames as $key) {
			   if (!in_array($key, $keys)) {
			   		$$key = '';
				} else {
					$$key = $task[$key];
				}
				$columns = $columns.$key . ",";
				$values = $values . "'" . $$key . "',";
			}
			
			$query = "INSERT INTO tarefa (" . trim($columns, ",") . ") VALUES (" . trim($values, ",") . ")";
			
			if (!empty($task)) {
				$result = $this->connection->query($query);
				$success = array("status" => "Sucesso", "msg" => "Tarefa criada com sucesso.", "data" => $task);
				$this->response($this->jsonEncode($success), 200);
			} else {
				$this->response("", 204);
			}
		}

		private function editTask()
		{
			if ($this->getRequestMethod() != "POST") {
				$this->response("", 406);
			}
			
			$task = json_decode(file_get_contents("php://input"), true);
			$task_id = (int)$task['tarefa_id'];
			$columnsNames = array('tarefa_titulo', 'tarefa_descricao', 'tarefa_status');
			$keys = array_keys($task['tarefa']);
			$columns = '';
			$values = '';
			
			foreach ($columnsNames as $key) {
			   if (!in_array($key, $keys)) {
			   		$$key = '';
				} else {
					$$key = $task['tarefa'][$key];
				}
				$columns = $columns.$key . " = '" . $$key . "',";
			}

			if ($task['tarefa']['tarefa_status'] == "1") {
				$columns .= "tarefa_data_conclusao = now()";
			}

			$query = "UPDATE tarefa SET " . trim($columns, ",") . ", tarefa_data_edicao = now() WHERE tarefa_id = $task_id";

			if(!empty($task)){
				$result = $this->connection->query($query);
				$success = array("status" => "Sucesso", "msg" => "Tarefa " . $task_id . " alterada com sucesso.", "data" => $task);
				$this->response($this->jsonEncode($success), 200);
			} else {
				$this->response("", 204);
			}
		}
		
		private function deleteTask() {
			if ($this->getRequestMethod() != "DELETE") {
				$this->response("", 406);
			}
			
			$id = (int)$this->request['id'];
			if ($id > 0) {
				$query = "UPDATE tarefa SET tarefa_removida = 1, tarefa_data_remocao = now() WHERE tarefa_id = $id";
				$result = $this->connection->query($query);
				$success = array("status" => "Sucesso", "msg" => "Tarefa apagada com sucesso.");
				$this->response($this->jsonEncode($success), 200);
			} else {
				$this->response("", 204);
			}
		}
		
		private function jsonEncode($data)
		{
			if (is_array($data)) {
				return json_encode($data);
			}
		}
	}