<?php
	/**
	 * Class of connection with data base 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-10-22
	 * @access public
	 */
	class connect_db 
	{
		protected $host;
		protected $user;
		protected $password;
		protected $db;
		protected $connect;

		public function getConnect() { return $this->connect; }

		/**
		 * Construct - Start variables of connection
		 * @access public
		 * @return void
		 */
		function __construct() 
		{
			$this->host = config::$connection_db["host"];
			$this->user = config::$connection_db["user"];
			$this->password = config::$connection_db["password"];
			$this->db = config::$connection_db["database_name"];
		}

		/**
		 * Open connection with data base
		 * @access public
		 * @return void
		 */
		public function open() 
		{
			try
			{
				$this->connect = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db, $this->user, $this->password);
				$this->connect->exec("set names utf8");
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/**
		 * Unset variable connection
		 * @access public
		 * @return void
		 */
		public function close() 
		{
			$this->connect = null;
		}
	}