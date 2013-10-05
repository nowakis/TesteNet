<?php

	include_once "class/class.Template.inc.php";
	require_once('banco.inc.php');

	$theme = ".";
	$model = new Template($theme);
	$model->set_filenames(array('rodape' => 'rodape.htm'));

	$model->pparse('rodape');

?>




