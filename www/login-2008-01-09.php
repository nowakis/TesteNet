<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
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

if (isset($_POST["Logar"])) {
	$login =	addslashes(trim($_POST["txtlogin"]));
	$senha	 =	 md5(addslashes(trim($_POST["txtsenha"])));
  	$rSet = $db->Query("SELECT tbl_admin.admin,
								tbl_admin.login,
								tbl_admin.senha,
								tbl_admin.nome,
								tbl_admin.email,
								tbl_admin.obs,
								tbl_admin.data,
								tbl_admin.nivel,
								tbl_fazenda.fazenda AS fazenda,
								tbl_fazenda.nome as fazenda_nome 
								FROM tbl_admin 
								JOIN tbl_fazenda USING(fazenda)
								WHERE login='$login' AND senha='$senha'");
   	if ($db->NumRows($rSet) == 1){
  	   $row = $db->FetchArray($rSet);	   
	   $_SESSION["login_fazenda"]	=	$row['fazenda'];		  
	   $_SESSION["login_fazenda_nome"]	=	$row['fazenda_nome'];		  	   	   
	   $_SESSION["login_admin"]	=	$row['admin'];		  
	   $_SESSION["login_login"]	=	$row['login'];		  		  
//	   $_SESSION["login_senha"]	=	$row['senha'];
	   $_SESSION["login_nome"]	=	$row['nome'];	   
	   $_SESSION["login_email"]	=	$row['email'];	   
//	   $_SESSION["login_obs"]	=	$row['obs'];	   	   	   
	   $_SESSION["login_data"]	=	$row['data'];	   
	   $_SESSION["login_nivel"]	=	$row['nivel'];	   	   
	   $_SESSION["login_data_logado"]	=	date("H:M:S - d/m/Y");	   	   
	   header("Location: index.php".$_SERVER['QUERY_STRING']);
	   exit();		   
 	}
	else {
	      header("Location: login.php?msg_erro=402");
		  exit();
    }	   
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="";
$titulo="Acesso ao Sistema";
$sub_titulo="Faça o Login";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
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

include "rodape.php";


?>


