<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 07/01/2008
********************************************************************************************/

##############################################################################
## INCLUDES E CONEXÔES BANCO
##############################################################################

session_start();
include_once "class.Template.inc.php";
require_once('banco.inc.php');


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

if (isset($_POST["Acessar"])) {
	$login	=	addslashes(trim($_POST["txtlogin"]));
	$senha	=	md5(addslashes(trim($_POST["txtsenha"])));
	$query = "SELECT tbl_admin.admin,
								tbl_admin.login,
								tbl_admin.senha,
								tbl_admin.nome,
								tbl_admin.email,
								tbl_admin.obs,
								tbl_admin.data,
								tbl_admin.nivel,
								tbl_admin.ativo,
								tbl_fazenda.fazenda AS fazenda,
								tbl_fazenda.nome as fazenda_nome 
						FROM tbl_admin 
						JOIN tbl_fazenda USING(fazenda)
						WHERE tbl_admin.login = '$login' 
						AND	  tbl_admin.senha = '$senha' ";
	$rSet = $db->Query($query);
	if ($db->NumRows($rSet) == 1){
		$row = $db->FetchArray($rSet);
		if ($row['ativo']){
			$_SESSION["login_fazenda"]		=	$row['fazenda'];
			$_SESSION["login_fazenda_nome"]	=	$row['fazenda_nome'];
			$_SESSION["login_admin"]		=	$row['admin'];
			$_SESSION["login_login"]		=	$row['login'];
			$_SESSION["login_nome"]			=	$row['nome'];
			$_SESSION["login_email"]		=	$row['email'];
			$_SESSION["login_data"]			=	$row['data'];
			$_SESSION["login_nivel"]		=	$row['nivel'];
			$_SESSION["login_data_logado"]	=	date("H:M:S - d/m/Y");

			$_SESSION["login_master"]		=	date("H:M:S - d/m/Y");
			//header("Location: index.php".$_SERVER['QUERY_STRING']);
			
			echo "<script language='JavaScript'>";
			echo "window.location = 'index.php'";
			echo "</script>";
			exit();
		}else{
			header("Location: /nowakis/pecus/login.php?msg_erro=404&login=".$login);
			exit();
		}
	}else {
		header("Location: /nowakis/pecus/login.php?msg_erro=402&login=".$login);
		exit();
	}
}else{
	header("Location: /nowakis/pecus/login.php");
	exit();
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout ="";
$titulo ="Acesso ao Sistema";
$sub_titulo = "Faça o Login";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array('login' => 'login.htm'));


##############################################################################
##############                       INICIO                   	##############
##############################################################################	
	
	$msg_erro="";
	if (isset($_GET['msg_erro'])){
		$msg_erro=$_GET['msg_erro'];
		$msg_erro = 'Nome de usuário ou senha incorretos!';
	}
	$msg="";

				
$model->pparse('login');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

?>


