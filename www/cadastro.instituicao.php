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
	
	$instituicao	= addslashes(trim($_POST['instituicao']));
	$nome			= addslashes(trim($_POST['nome']));
	$endereco		= addslashes(trim($_POST['endereco']));
	$numero			= addslashes(trim($_POST['numero']));
	$complemento	= addslashes(trim($_POST['complemento']));
	$bairro			= addslashes(trim($_POST['bairro']));
	$cidade			= addslashes(trim($_POST['cidade']));
	$estado			= addslashes(trim($_POST['estado']));
	$cep			= addslashes(trim($_POST['cep']));

	try {
		$instit = new Instituicao();
		$instit->setId($instituicao);
		$instit->setNome($nome);
		$instit->setUnificado($_login_unificado);
		$instit->setEndereco($endereco);
		$instit->setNumero($numero);
		$instit->setComplemento($complemento);
		$instit->setBairro($bairro);
		$instit->setCidade($cidade);
		$instit->setEstado($estado);
		$instit->setCep($cep);

		$sessionFacade->gravarInstituicao($instit);
		$banco->desconecta(); 
		header("Location: cadastro.instituicao.php?instituicao=".$instit->getId()."&msg_codigo=1");
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
$titulo     = "Cadastro de Instituição";
$sub_titulo = "Instituição: Cadastrar";

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
	
if (isset($_GET['instituicao']) AND strlen(trim($_GET['instituicao']))>0){

	$instituicao = trim($_GET['instituicao']);
	try {
		$inst = $sessionFacade->recuperarInstituicao($instituicao); 
		if ( is_object($inst)){
			$instituicao		= $inst->getId();
			$nome				= $inst->getNome();
			$endereco			= $inst->getEndereco();
			$numero				= $inst->getNumero();
			$complemento		= $inst->getComplemento();
			$bairro				= $inst->getBairro();
			$cidade				= $inst->getCidade();
			$estado				= $inst->getEstado();
			$cep				= $inst->getCep();
		}else{
			array_push($msg_erro,"Instituicao não encontrado!");
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

if (strlen($instituicao)==0){
	array_push($msg_ok,"Cadastre um nova Instituição. Preencha os dados abaixo e clique em 'Gravar'");
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->assign_vars(array(		'INSTITUICAO'	=>	$instituicao,
								'NOME'			=>	$nome,
								'ENDERECO'		=>	$endereco,
								'NUMERO'		=>	$numero,
								'COMPLEMENTO'	=>	$complemento,
								'BAIRRO'		=>	$bairro,
								'CIDADE'		=>	$cidade,
								'ESTADO'		=>	$estado,
								'CEP'			=>	$cep,
								'BTN_NOME'		=>  (strlen($instituicao)>0)?"Confirmar Alterações":"Gravar"
));	


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
