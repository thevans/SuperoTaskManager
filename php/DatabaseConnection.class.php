<?
	class DatabaseConnection 
	{
		const HOSTNAME = "localhost";
		const USERNAME = "root";
		const PASSWORD = "";
		const DATABASE = "gerenciador_tarefas";
		
		public static $instance;
		
		public static function getInstance() {
			if (!isset(self::$instance)) {
				self::$instance = new mysqli(self::HOSTNAME, self::USERNAME, self::PASSWORD, self::DATABASE);
			}
			
			return self::$instance;
		}
	}
