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
include_once "class/class.Template.inc.php";
include_once "funcoes.php";
require_once('banco.inc.php');


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

if (isset($_POST["Acessar"]) or isset($_GET["key"])) {

	if (isset($_POST["Acessar"])){
		$login	=	addslashes(trim($_POST["login"]));
		$senha	=	addslashes(trim($_POST["senha"]));
	}

	if (isset($_GET["key"])){
		$key		=	trim($_GET["key"]);
		$professor	=	trim($_GET["p"]);
		$aluno		=	trim($_GET["a"]);

		/* LOGIN PROFESSOR */
		if (strlen($professor)>0){
			$query = "	SELECT tbl_professor.senha,
								tbl_professor.login,
								tbl_professor.email
						FROM   tbl_professor
						WHERE  tbl_professor.professor = $professor ";
			$rSet = $db->Query($query);
			if ($db->NumRows($rSet) > 0 ){
				$row = $db->FetchArray($rSet);
				$email = $row['email'];
				if (md5($professor.$email) == $key){
					$login = $row['login'];
					$senha = $row['senha'];
				}
			}
		}

		/* LOGIN ALUNO */
		if (strlen($aluno)>0){
			$query = "	SELECT tbl_aluno.senha,
								tbl_aluno.email
						FROM   tbl_aluno
						WHERE  tbl_aluno.aluno = $aluno ";
			$rSet = $db->Query($query);
			if ($db->NumRows($rSet) > 0 ){
				$row = $db->FetchArray($rSet);
				$email = $row['email'];
				if (md5($aluno.$email) == $key){
					$login = $email;
					$senha = trim($row['senha']);
				}
			}
		}
	}


	/* LOGIN PROFESSOR */
	$query = "SELECT	tbl_instituicao_professor.instituicao,
						tbl_professor.professor,
						tbl_professor.nome,
						tbl_professor.login,
						tbl_professor.email,
						tbl_professor.ativo
			FROM tbl_professor
			JOIN tbl_instituicao_professor ON tbl_instituicao_professor.professor = tbl_professor.professor
			WHERE tbl_professor.login = '$login' 
			AND	  tbl_professor.senha = '$senha' ";

	$rSet = $db->Query($query);
	if ($db->NumRows($rSet) > 0 ){
		$row = $db->FetchArray($rSet);
		if ($row['ativo']){
			$_SESSION["login_instituicao"]	= $row['instituicao'];
			$_SESSION["login_professor"]	= $row['professor'];
			$_SESSION["login_nome"]			= $row['nome'];
			$_SESSION["login_login"]		= $row['login'];
			$_SESSION["login_email"]		= $row['email'];
			$_SESSION["login_data_logado"]	= date("d/m/Y H:i");
			$_SESSION["login_md5"]			= md5($row['instituicao']."prova".$row['login']);

			$query = "INSERT INTO tbl_log_acesso (professor,ip,programa) VALUES (".$row['professor'].",'".getRealIpAddr()."','".$_SERVER["PHP_SELF"]."') ";
			$rSet = $db->Query($query);

			$destino = "index.php";

			if (isset($_GET["redirec"]) AND strlen($_GET["redirec"])>0) {
				$destino = $_GET["redirec"];
			}

			echo "<script language='JavaScript'>";
			echo "window.location = '".$destino."'";
			echo "</script>";
			exit();
		}else{
			header("Location: ../index.php?msg_erro=404&login=".$login);
			exit();
		}
	}

	/* LOGIN ALUNO */
	$query = "SELECT	tbl_aluno.aluno,
						tbl_aluno.instituicao,
						tbl_aluno.nome,
						tbl_aluno.email         AS login,
						tbl_aluno.email,
						tbl_aluno.ativo
			FROM tbl_aluno
			WHERE tbl_aluno.email = '$login' 
			AND	  tbl_aluno.senha = '$senha'";

	$rSet = $db->Query($query);
	if ($db->NumRows($rSet) > 0 ){
		$row = $db->FetchArray($rSet);
		if ($row['ativo']){
			$_SESSION["login_aluno"]		= $row['aluno'];
			$_SESSION["login_instituicao"]	= $row['instituicao'];
			$_SESSION["login_nome"]			= $row['nome'];
			$_SESSION["login_login"]		= $row['login'];
			$_SESSION["login_email"]		= $row['email'];
			$_SESSION["login_data_logado"]	= date("d/m/Y H:i");
			$_SESSION["login_md5"]			= md5($row['instituicao']."prova".$row['login']);

			$query = "INSERT INTO tbl_log_acesso (aluno,ip,programa) VALUES (".$row['aluno'].",'".getRealIpAddr()."','".$_SERVER["PHP_SELF"]."') ";
			$rSet = $db->Query($query);

			echo "<script language='JavaScript'>";
			echo "window.location = 'index.php'";
			echo "</script>";
			exit();
		}else{
			header("Location: ../index.php?msg_erro=404&login=".$login);
			exit();
		}
	}

	if (isset($_GET["key"]) AND isset($_GET["email"])){
		if (strlen($_GET["email"])>0){
			header("Location: ".$_GET["redirec"]."?email=".$_GET["email"]);
			exit();
		}
	}

	/* Se chegou aqui, login ou senha invalidos */
	header("Location: ../index.php?msg_erro=402&login=".$login);
	exit();
}else{
	header("Location: ../index.php");
	exit();
}
exit;

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


