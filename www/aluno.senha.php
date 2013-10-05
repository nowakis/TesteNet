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
	
	$aluno			= addslashes(trim($_POST['aluno']));
	$senha_atual	= addslashes(trim($_POST['senha_atual']));
	$senha1			= addslashes(trim($_POST['senha1']));
	$senha2			= addslashes(trim($_POST['senha2']));

	try {

		if ($senha1 <> $senha2) {
			throw new Exception("A senha de confirmação não são iguais.",0);
		}

		$banco->iniciarTransacao();
		
		$alu = $sessionFacade->recuperarAluno($aluno);
		$sessionFacade->gravarNovaSenhaAluno($alu,$senha_atual,$senha1);

		$banco->efetivarTransacao();
		$banco->desconecta();
		header("Location: aluno.senha.php?aluno=".$alu->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desfazerTransacao();
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Cadastro de Aluno";
$sub_titulo = "Aluno: Cadastrar";

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

$_GET['aluno'] = $_login_aluno;

if (isset($_GET['aluno']) AND strlen(trim($_GET['aluno']))>0){

	$aluno = trim($_GET['aluno']);
	try {
		$alu = $sessionFacade->recuperarAluno($aluno); 

		if ( is_object($alu)){
			$aluno		= $alu->getId();
			$nome		= $alu->getNome();
			$ra			= $alu->getRa();
			$email		= $alu->getEmail();
			$senha		= $alu->getSenha();
			$ativo		= $alu->getAtivo();
			$endereco	= $alu->getEndereco();
			$numero		= $alu->getNumero();
			$complemento= $alu->getComplemento();
			$bairro		= $alu->getBairro();
			$cidade		= $alu->getCidade();
			$estado		= $alu->getEstado();
			$cep		= $alu->getCep();
		}else{
			array_push($msg_erro,"Aluno não encontrado!");
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


$model->assign_vars(array(	'ALUNO'			=>	$aluno,
							'NOME'			=>	$nome,
							'RA'			=>	$ra,
							'EMAIL'			=>	$email,
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
							'BTN_NOME'		=>  (strlen($aluno)>0)?"Confirmar Alterações":"Gravar"
));	

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
