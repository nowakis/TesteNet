<?
$msg_erro="";

if (!isset($_SESSION['login_instituicao'])){
	#header("Location: ../login.php".$_SERVER['QUERY_STRING']);
}

global $_login_instituicao;
global $_login_instituicao_nome;
global $_login_professor;
global $_login_aluno;
global $_login_nome;
global $_login_login;
global $_login_email;
global $_login_data_logado;
global $_login_unificado;

$_login_instituicao		= $_SESSION['login_instituicao'];
$_login_professor		= $_SESSION['login_professor'];
$_login_aluno			= $_SESSION['login_aluno'];
$_login_login			= $_SESSION['login_login'];
$_login_email			= $_SESSION['login_email'];
$_login_data_logado		= $_SESSION['login_data_logado'];

if ($_SESSION["login_md5"] != md5($_login_instituicao."prova".$_login_login)){
	#header("Location: ../index.php?msg_erro=501&".$_SERVER['QUERY_STRING']);
}

if (strlen($_login_instituicao)==0){
	#header("Location: ../index.php?msg_erro=502&".$_SERVER['QUERY_STRING']);
}

try {
	if (strlen($_login_instituicao)>0){
		$obj_instituicao = $sessionFacade->recuperarInstituicao($_login_instituicao);
		if (strlen($_login_professor)>0){
			$obj_professor   = $sessionFacade->recuperarProfessor($_login_professor);
		}
		if (strlen($_login_aluno)>0){
			$obj_aluno       = $sessionFacade->recuperarAluno($_login_aluno);
		}

		if (is_object($obj_instituicao) and strlen($_login_professor)>0){
			$_login_instituicao_nome	= $obj_instituicao->getNome();
			$_login_unificado			= $obj_instituicao->getUnificado();
		}

		if (is_object($obj_professor)){
			$_login_nome	= $obj_professor->getNome();
			$_login_login	= $obj_professor->getLogin();
			$_login_email	= $obj_professor->getEmail();
			$sessionFacade->logAcessoProfessor($obj_instituicao,$obj_professor);
		}
		
		if (is_object($obj_aluno)){
			$_login_nome	= $obj_aluno->getNome();
			$_login_login	= $obj_aluno->getRA();
			$_login_emai	= $obj_aluno->getEmail();
			$sessionFacade->logAcessoAluno($obj_instituicao,$obj_aluno);
		}
	}
}catch(Exception $e) { 
	print_r ($e);
	header("Location: ../index.php?msg_erro=503&$e");
	exit;
}

?>
<?php
/*
$var = 'foo';
$new_var = 'asdf';

class function1{

	function function1(){
		global $new_var;
		echo $new_var;
	}
	function function2(){
		
		echo $new_var;
	}
}

$testee = new function1();
$testee->function1();
exit;
*/
?>