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
require_once "funcoes.php";
require_once "class/class.Template.inc.php";
require_once "class/class.SessionFacade.php";
require_once "banco.con.php";
require_once "autentica_usuario.php";

#$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

if ($_login_unificado == "1"){
	$_GET['professor'] = $_login_professor;
}

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}

##############################################################################
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$professor		= addslashes(trim($_POST['professor']));
	$nome			= addslashes(trim($_POST['nome']));
	$email			= addslashes(trim($_POST['email']));
	$login			= addslashes(trim($_POST['login']));
	$senha			= addslashes(trim($_POST['senha']));
	$ativo			= addslashes(trim($_POST['ativo']));
	$endereco		= addslashes(trim($_POST['endereco']));
	$numero			= addslashes(trim($_POST['numero']));
	$complemento	= addslashes(trim($_POST['complemento']));
	$bairro			= addslashes(trim($_POST['bairro']));
	$cidade			= addslashes(trim($_POST['cidade']));
	$estado			= addslashes(trim($_POST['estado']));
	$cep			= addslashes(trim($_POST['cep']));

	try {
		$prof = new Professor();
		$prof->setId($professor);
		$prof->setNome($nome);
		$prof->setEmail($email);
		$prof->setLogin($login);
		$prof->setSenha($senha);
		$prof->setAtivo($ativo);
		$prof->setEndereco($endereco);
		$prof->setNumero($numero);
		$prof->setComplemento($complemento);
		$prof->setBairro($bairro);
		$prof->setCidade($cidade);
		$prof->setEstado($estado);
		$prof->setCep($cep);

		$sessionFacade->gravarProfessor($prof);
		$banco->desconecta(); 
		header("Location: cadastro.professor.php?professor=".$prof->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Cadastro de Professor";
$sub_titulo = "Professor: Cadastrar";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));


##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	

if (isset($_GET['professor']) AND strlen(trim($_GET['professor']))>0){

	$professor = trim($_GET['professor']);
	try {
		$prof = $sessionFacade->recuperarProfessor($professor); 

		if ( is_object($prof)){
			$professor		= $prof->getId();
			$nome			= $prof->getNome();
			$email			= $prof->getEmail();
			$login			= $prof->getLogin();
			$senha			= $prof->getSenha();
			$ativo			= $prof->getAtivo();
			$endereco		= $prof->getEndereco();
			$numero			= $prof->getNumero();
			$complemento	= $prof->getComplemento();
			$bairro			= $prof->getBairro();
			$cidade			= $prof->getCidade();
			$estado			= $prof->getEstado();
			$cep			= $prof->getCep();
		}else{
			array_push($msg_erro,"Professor não encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->assign_vars(array(	'PROFESSOR'		=>	$professor,
							'NOME'			=>	$nome,
							'EMAIL'			=>	$email,
							'LOGIN'			=>	$login,
							'SENHA'			=>	$senha,
							'ATIVO'			=>	$ativo==1?"checked":"",
							'INATIVO'		=>	$ativo!=1?"checked":"",
							'ENDERECO'		=>	$endereco,
							'NUMERO'		=>	$numero,
							'COMPLEMENTO'	=>	$complemento,
							'BAIRRO'		=>	$bairro,
							'CIDADE'		=>	$cidade,
							'ESTADO'		=>	$estado,
							'CEP'			=>	$cep,
							'BTN_NOME'		=>  (strlen($professor)>0)?"Confirmar Alterações":"Gravar"
));	


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>