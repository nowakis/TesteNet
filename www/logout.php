<?
/******************************************************************
Script .........: Encerra a sessão
Por ............: Fabio Nowaki
Data ...........: 31/07/2006
******************************************************************/

	session_start();
	

	if( (!empty($_SESSION["login_instituicao"])) && (!empty($_SESSION["login_login"])) ) {
		if (isset($_SESSION["login_professor"])){
			if (!isset($_GET["pesquisa"])){
				header("Location: pesquisa/");
				exit;
			}
		}
		$destroi	=	session_destroy();
	}
	header("Location: ../index.php");
	exit;
?>

