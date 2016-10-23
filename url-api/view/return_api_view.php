<?php
	header("HTTP/1.1 " . $view["http_status_code"] . " " . $view["http_status_msg"]);
    echo(json_encode($view["data"]));
?>