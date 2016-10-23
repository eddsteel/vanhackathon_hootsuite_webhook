<?php
	/**
	 * Class that contains the start of actions 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-10-21
	 * @access public
	 */
	class controller
	{
		/**
		 * Stores HTML POST
		 * @access protected
		 * @var array 
		 */
		protected $post_vars;

		/**
		 * Default function to print results of API
		 * @access protected
		 * @var $cod_http_status int
		 * @var $data_return array
		 * @var $status string
		 */
		protected function print_return_api($cod_http_status, $data_return, $status = "success")
		{
			try
			{
				if ($status == "") $status = "success";

				$view = array(
					"http_status_code" => $cod_http_status,
					"http_status_msg" => self::status_http($cod_http_status),
					"data" => array("status" => $status, "data" => $data_return)
				);

				require("view/return_api_view.php");
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/**
		 * Format return HTTP 
		 * @access protected
		 * @var $cod_http_status int
		 * @return string 
		 */
		protected static function status_http($cod_http_status) 
	    {
	    	try
	    	{
		        $status_http = array(  
		            200 => 'OK',             
		            400 => 'An unhandled user exception occurred', 
		            403 => 'You don\'t have access', 
		            404 => 'Not Found', 
		            405 => 'Method Not Allowed', 
		            500 => 'Internal Server Error'
		        ); 
		        return (array_key_exists($cod_http_status, $status_http) ? $status_http[$cod_http_status] : $status_http[500]); 
	        }
			catch(Exception $e)
			{
				throw new Exception($e);
			}
	    } 
	}
?>