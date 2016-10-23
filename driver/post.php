<?php
	require("config.php");
	require("connect_db.php");

	/**
	 * Main class for Driver processing
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-10-23
	 * @access public
	 */
	class post	
	{
		public function new_post()
		{
			$next_post = $this->select_next_post();

			if (count($next_post) > 0)
			{
				$attempt = $next_post["attempts"] + 1;

				/* 86.400 seconds = 24 hours */
				if ($next_post["time"] < 86400)
				{
					$this->insert_log($next_post["id_destination_post_queue"], $next_post["id_destination_post"], $attempt);
					$retorno_http = $this->send_post($next_post["url"], $next_post["msg_body"], $next_post["content_type"]);
					$this->update_log($next_post["id_destination_post_queue"], $next_post["id_destination_post"], $attempt, $retorno_http);
				}
				else
				{
					$this->delete_log($next_post["id_destination_post_queue"]);
				}
			}
		}

		private function select_next_post()
		{
			$return = array();
			$strSQL = "SELECT 
							q.id_destination_post_queue,
							p.id_destination_post,
							d.id_destination,
							d.url,
							p.msg_body,
							p.content_type,
							q.attempts,
							q.dt_updated as date,
							TIME_TO_SEC(TIMEDIFF(NOW(), p.dt_created)) as time
						FROM 
							tb_destination_post_queue q 
							INNER JOIN tb_destination_post p ON p.id_destination_post = q.id_destination_post
							INNER JOIN tb_destination d ON d.id_destination = p.id_destination 
						WHERE
							q.status = 'W' AND 
							(
								(q.attempts > 0 AND DATE_ADD(q.dt_updated, INTERVAL +30 MINUTE) <= NOW()) OR 
								q.attempts = 0
							)
						ORDER BY
							date DESC
						LIMIT 0,1;";

			$connect = new connect_db();
			$connect->open();

			$stmt = $connect->getConnect()->prepare($strSQL);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (count($rows) > 0) $return = $rows[0];

			$connect->close();

			return $return;
		}

		private function insert_log($id_destination_post_queue, $id_destination_post, $attempt)
		{
			$connect = new connect_db();
			$connect->open();

			try 
			{  
				$connect->getConnect()->beginTransaction();

				$strSQL = "INSERT INTO tb_destination_post_queue_log(id_destination_post, attempt) VALUES(:id_destination_post, :attempt);";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_destination_post', $id_destination_post, PDO::PARAM_INT);
				$stmt->bindValue(':attempt', $attempt, PDO::PARAM_INT); 
				$stmt->execute();
				$id_destination_post_queue_log = $connect->getConnect()->lastInsertId();

				$strSQL = "UPDATE tb_destination_post_queue SET status = 'P' WHERE id_destination_post_queue = :id_destination_post_queue;";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_destination_post_queue', $id_destination_post_queue, PDO::PARAM_INT);
				$stmt->execute();

				$connect->getConnect()->commit();
			} 
			catch (Exception $eDb) 
			{
				$connect->getConnect()->rollBack(); 
				throw new Exception($eDb);
			}

			$connect->close();
		}

		private function update_log($id_destination_post_queue, $id_destination_post, $attempt, $http_code)
		{
			$connect = new connect_db();
			$connect->open();

			try 
			{  
				$connect->getConnect()->beginTransaction();

				$strSQL = "UPDATE tb_destination_post_queue_log SET http_return = :http_return WHERE id_destination_post = :id_destination_post AND attempt = :attempt;";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':http_return', $http_code, PDO::PARAM_INT);
				$stmt->bindValue(':id_destination_post', $id_destination_post, PDO::PARAM_INT);
				$stmt->bindValue(':attempt', $attempt, PDO::PARAM_INT); 
				$stmt->execute();

				if ($http_code == 200 || $attempt == 3)
				{
					$strSQL = "DELETE FROM tb_destination_post_queue WHERE id_destination_post_queue = :id_destination_post_queue;";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':id_destination_post_queue', $id_destination_post_queue, PDO::PARAM_INT);
					$stmt->execute();
				}
				else
				{
					$strSQL = "UPDATE tb_destination_post_queue SET status = 'W', attempts = :attempt WHERE id_destination_post_queue = :id_destination_post_queue;";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':id_destination_post_queue', $id_destination_post_queue, PDO::PARAM_INT);
					$stmt->bindValue(':attempt', $attempt, PDO::PARAM_INT);
					$stmt->execute();
				}

				$connect->getConnect()->commit();
			} 
			catch (Exception $eDb) 
			{
				$connect->getConnect()->rollBack(); 
				print_r($eDb);
				throw new Exception($eDb);
			}

			$connect->close();
		}

		private function delete_log($id_destination_post_queue)
		{
			$strSQL = "DELETE FROM tb_destination_post_queue WHERE id_destination_post_queue = :id_destination_post_queue;";

			$connect = new connect_db();
			$connect->open();

			$stmt = $connect->getConnect()->prepare($strSQL);
			$stmt->bindValue(':id_destination_post_queue', $id_destination_post_queue, PDO::PARAM_INT);
			$stmt->execute();

			$connect->close();
		}

		private function send_post($url, $msg_body, $content_type)
		{
			$contexto = stream_context_create(array(
			    'http' => array(
			        'method' => 'POST',
			        'content' => $msg_body,
			        'header' => "Content-type: ".$content_type."\r\n"
			        . "Content-Length: ".strlen($msg_body)."\r\n"
			    )
			));

			$post = file_get_contents($url, null, $contexto);

			preg_match("/[0-9]{3}/", $http_response_header[0], $http_code);

			return $http_code[0];
		}
	}

	$post = new post();
	/*while (true)
	{*/
		$post->new_post();		
	/*}*/
?>