<?php
	require_once("connect_db.php");
	require_once("destination.php");

	/**
	 * Class of url model
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-10-22
	 * @access public
	 */
	class message
	{
		public $id;
		public $destination;
		public $msg_body;
		public $content_type;

		/**
		 * Add a new message
		 * @access public
		 * @return void
		 */ 
		public function add()
		{
			try
			{
				$strSQL = "INSERT INTO tb_destination_post(id_destination, msg_body, content_type) VALUES(:id_destination, :msg_body, :content_type);";

				$connect = new connect_db();
				$connect->open();

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_destination', $this->destination->id, PDO::PARAM_INT);
				$stmt->bindValue(':msg_body', $this->msg_body, PDO::PARAM_STR);
				$stmt->bindValue(':content_type', $this->content_type, PDO::PARAM_STR);
				$stmt->execute();

				$this->id = $connect->getConnect()->lastInsertId();

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/**
		 * Add a new message on queue
		 * @access public
		 * @return void
		 */ 
		public function add_queue()
		{
			try
			{
				$strSQL = "INSERT INTO tb_destination_post_queue(id_destination_post) VALUES(:id_destination_post);";

				$connect = new connect_db();
				$connect->open();

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_destination_post', $this->id, PDO::PARAM_INT);
				$stmt->execute();

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/**
		 * Add a new message
		 * @access public
		 * @return void
		 */ 
		public function delete($id_destination)
		{
			try
			{
				$strSQL = "DELETE FROM tb_destination_post WHERE id_destination = :id_destination;";

				$connect = new connect_db();
				$connect->open();

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_destination', $id_destination, PDO::PARAM_INT);
				$stmt->execute();

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}
	}
?>