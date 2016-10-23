<?php
	require_once("model/destination.php");
	require_once("model/message.php");

	/**
	 * Class that contains the start of actions 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-10-26
	 * @access public
	 */
	class destination_controller extends controller 
	{
		/** 
		 * Construct - initialize error_messages and post_vars variables 
		 * @access public
		 */
		function __construct()
		{
			$this->error_messages = array();
			$this->post_vars = json_decode(file_get_contents("php://input"), true);
		}

		public function index()
		{
			switch ($_SERVER['REQUEST_METHOD']) 
			{
				case 'DELETE':
					$this->delete();
					break;
				case 'GET':
					$this->get();
					break;
				case 'POST':
					$this->post();
					break;	
				case 'PUT':
					$this->add();
					break;
				default:
					header("HTTP/1.1 405 ".return_api::status_http(405));
					break;
			}
		}

		/** 
		 * Add a URL
		 * @access public
		 */
		private function add()
		{
			try
			{
				$cod_http_status = 200;
				$data_return = array();
				$status_return = "";
				$error_messages = array();

				$this->validade_param_str($error_messages, "url", 255);
				$this->validate_url($error_messages, $this->post_vars["url"]);

				if (count($error_messages) == 0)
				{
					$destination = new destination();
					$destination->get(0, $this->post_vars["url"]);

					if ((int)$destination->id == 0)
					{
						$destination->url = $this->post_vars["url"];
						$destination->add();

						if ((int)$destination->id > 0)
						{
							/* return url data */
							$data_return = array(
								"id" => (int)$destination->id,
								"url" => $destination->url 
							);
						}
						else
						{
							$data_return = array("'url' not registered");
							$status_return = "error";
						}
					}
					else
					{
						$data_return = array("'url' already registered");
						$status_return = "error";
					}
				}
				else
				{
					$data_return = $error_messages;
					$status_return = "error";
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			$this->print_return_api($cod_http_status, $data_return, $status_return);
		}

		/** 
		 * List URL
		 * @access public
		 */
		private function get()
		{
			try
			{
				$cod_http_status = 200;
				$data_return = array();
				$status_return = "";
				$error_messages = array();

				$destination = new destination();
				$list_url = $destination->select();

				$data_return = $list_url;
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			$this->print_return_api($cod_http_status, $data_return, $status_return);
		}

		/** 
		 * Delete a URL
		 * @access public
		 */
		private function delete()
		{
			try
			{
				$cod_http_status = 200;
				$data_return = array();
				$status_return = "";
				$error_messages = array();

				if ($this->validade_param_int($error_messages, "id"))
				{
					$destination = new destination();
					$destination->delete($this->post_vars["id"]);
				}
				else
				{
					$data_return = $error_messages;
					$status_return = "error";
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			$this->print_return_api($cod_http_status, $data_return, $status_return);
		}

		/** 
		 * Post a message to URL
		 * @access public
		 */
		private function post()
		{
			try
			{
				$cod_http_status = 200;
				$data_return = array();
				$status_return = "";
				$error_messages = array();

				$this->validade_param_int($error_messages, "id");
				$this->validade_param_str($error_messages, "msg-body");
				$this->validate_content_type($error_messages, $this->post_vars["content-type"]);
				
				if (count($error_messages) == 0)
				{
					$destination = new destination();
					$destination->get($this->post_vars["id"]);

					if ((int)$destination->id > 0)
					{
						$message = new message();
						$message->destination = $destination;
						$message->msg_body = $this->post_vars["msg-body"];
						$message->content_type = $this->post_vars["content-type"];
						$message->add();

						if ((int)$message->id > 0)
						{
							$message->add_queue();

							$data_return = array(
								"destination" => $destination, 
								"msg-body" => $message->msg_body,
								"content-type" => $message->content_type
							);
						}
						else
						{
							$data_return = array("'message' not registered");
							$status_return = "error";
						}
					}
					else
					{
						$data_return = array("'id' not registered");
						$status_return = "error";
					}
				}
				else
				{
					$data_return = $error_messages;
					$status_return = "error";
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			$this->print_return_api($cod_http_status, $data_return, $status_return);
		}

		private function validade_param_str(&$error_messages, $param, $limit = 0)
		{
			if (is_array($this->post_vars))
			{
				if (array_key_exists($param, $this->post_vars))
				{
					if (trim($this->post_vars[$param]) != "")
					{
						if (strlen($this->post_vars[$param]) <= $limit || $limit == 0)
							return true;
						else
							$error_messages[] = "Parameter '".$param."' is long, must have a maximum of ".$limit." characters";		
					}
					else
					{
						$error_messages[] = "Parameter '".$param."' is empty";
					}
				}
				else
				{
					$error_messages[] = "Parameter '".$param."' not sent";
				}
			}
			else
			{
				$error_messages[] = "No parameters sent";
			}
			return false;
		}

		private function validade_param_int(&$error_messages, $param)
		{
			if (is_array($this->post_vars))
			{
				if (array_key_exists($param, $this->post_vars))
				{
					if ((int)$this->post_vars[$param] > 0)
						return true;
					else
						$error_messages[] = "Parameter '".$param."' is invalid";
				}
				else
				{
					$error_messages[] = "Parameter '".$param."' not sent";
				}
			}
			else
			{
				$error_messages[] = "No parameters sent";
			}
			return false;
		}

		private function validate_url(&$error_messages, $url)
		{
			if (!filter_var($url, FILTER_VALIDATE_URL) === false) 
			{
			    return true;
			}
			else
			{
				$error_messages[] = "Parameter 'url' is invalid"; 
			    return false;
			}
		}

		private function validate_content_type(&$error_messages, $content_type)
		{
			switch ($content_type) 
			{
			 	case 'application/json': return true;
			 	case 'application/x-www-form-urlencoded': return true;
			 	default:
			 		$error_messages[] = "Parameter 'content-type' is invalid";
			 		return false;
			 } 
		}
	}
?>