<?php
	require_once("config.php");

	set_time_limit(0);
	date_default_timezone_set(config::$default_time_zone);

	$controller = isset($_GET["class"]) ? $_GET["class"] : "";
	$method = isset($_GET["method"]) ? $_GET["method"] : "";

	if ($controller != "" && file_exists("controller/".$controller."_controller.php"))
	{
		require_once("controller/controller.php");
		require_once("controller/".$controller."_controller.php");

		$class = $controller."_controller";
		$obj = new $class();
		$obj->index();
	}	
	else
	{
		header("HTTP/1.1 404 Not Found");
	}
?>