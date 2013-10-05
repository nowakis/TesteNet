<?
require_once('banco.inc.php');

if (isset($_SESSION['login_nivel'])){
	if ($_SESSION['login_nivel']!='100')
	    header("Location: login.php?".$_SERVER['QUERY_STRING']);
}
else header("Location: login.php".$_SERVER['QUERY_STRING']);
?>