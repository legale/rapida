<?php

session_start();
if(isset($_SESSION['admin'])){
	header('HTTP/1.1 200 OK');
}else{
	header('HTTP/1.1 403 Forbidden');
}
