<?php
	require_once('./class/imgedit.class.php');
	$image = new edit_imagick($_GET['path']);
	$image->thump();
	$image->show();
?>