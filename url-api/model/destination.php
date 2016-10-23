<?php
	require_once("connect_db.php");

	/**
	 * Class of url model
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-10-22
	 * @access public
	 */
	class destination
	{
		public $id;
		public $url;

		/**
		 * Add a new url
		 * @access public
		 * @return void
		 */ 
		public function add()
		{
			try
			{
				$strSQL = "INSERT INTO tb_destination(url) VALUES(:url);";

				$connect = new connect_db();
				$connect->open();

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':url', $this->url, PDO::PARAM_STR);
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
		 * Get a url
		 * @access public
		 * @return void
		 */ 
		public function get($id = 0, $url = "")
		{
			try
			{
				if ($id > 0 || $url != "")
				{
					$strJoin = "";
					if ($id > 0) $strJoin = " AND id_destination = :id_destination";
					if ($url != "") $strJoin = " AND url = :url";
					if ($strJoin != "") $strJoin = " WHERE ".substr($strJoin, 4);

					$strSQL = "SELECT id_destination, url FROM tb_destination ".$strJoin;

					$connect = new connect_db();
					$connect->open();

					$stmt = $connect->getConnect()->prepare($strSQL);
					if ($id > 0) $stmt->bindValue(':id_destination', $id, PDO::PARAM_INT);
					if ($url != "") $stmt->bindValue(':url', $url, PDO::PARAM_STR);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

					if (count($rows) > 0)
					{
						$this->id = (int)$rows[0]["id_destination"];
						$this->url = $rows[0]["url"];
					}

					$connect->close();
				}
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/**
		 * List url
		 * @access public
		 * @return void
		 */ 
		public function select()
		{
			$return = array();
			try
			{
				$strSQL = "SELECT id_destination, url FROM tb_destination ORDER BY url;";

				$connect = new connect_db();
				$connect->open();

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (count($rows) > 0)
				{
					foreach ($rows as $row) 
					{
						$destination = new destination();
						$destination->id = (int)$row["id_destination"];
						$destination->url = $row["url"]; 
						$return[] = $destination;
					}
				}

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $return;
		}

		/**
		 * Delete a  url
		 * @access public
		 * @return void
		 */ 
		public function delete($id)
		{
			try
			{
				$message = new message();
				$message->delete($id);
				
				$strSQL = "DELETE FROM tb_destination WHERE id_destination = :id_destination;";

				$connect = new connect_db();
				$connect->open();

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_destination', $id, PDO::PARAM_INT);
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