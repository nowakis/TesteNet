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
require_once("autentica_usuario.php");
include_once "funcoes.php";

include_once "class.banco.php";
include_once "class.SessionFacade.php";

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
	
	$raca					= addslashes(trim($_POST['raca']));
	$nome					= addslashes(trim($_POST['nome']));
	$descricao				= addslashes(trim($_POST['descricao']));
	$codigo					= addslashes(trim($_POST['codigo']));
	$observacao				= addslashes(trim($_POST['observacao']));
	$ativo					= addslashes(trim($_POST['ativo']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_raca = new Raca();
		$obj_raca->setId($raca);
		$obj_raca->setNome($nome);
		$obj_raca->setDescricao($descricao);
		$obj_raca->setCodigo($codigo);
		$obj_raca->setObservacao($observacao);
		$obj_raca->setAtivo($ativo);

		$sessionFacade->gravarRaca($obj_raca);
		$banco->desconecta(); 
		header("Location: ".$PHP_SELF."?raca=".$obj_raca->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desconecta(); 
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout		= "cadastro";
$titulo		= "Cadastro de Raças";
$sub_titulo	= "Cadastro: Raça";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.raca' => 'cadastro.raca.htm'));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['raca']) AND strlen(trim($_GET['raca']))>0){

	$raca = trim($_GET['raca']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$obj_raca = $sessionFacade->recuperarRaca($raca); 

		if ( $obj_raca->getId() > 0){
			$raca			=	$obj_raca->getId();
			$codigo			=	$obj_raca->getCodigo();
			$nome			=	$obj_raca->getNome();
			$descricao		=	$obj_raca->getDescricao();
			$data			=	$obj_raca->getData();
			$ativo			=	$obj_raca->getAtivo();
			$observacao		=	$obj_raca->getObservacao();
		}else{
			array_push($msg_erro,"Raça não encontrado!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

	$model->assign_vars(array(		'RACA'			=>	$raca,
									'NOME'			=>	$nome,
									'CODIGO'		=>	$codigo,
									'DESCRICAO'		=>	$descricao,
									'OBSERVACAO'	=>	$observacao,
									'ATIVO'			=>	$ativo==1?"checked":"",
									'INATIVO'		=>	$ativo!=1?"checked":""
									));	
				
}

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$racas = $sessionFacade->recurarRacaTodosDAO();
	for($i= 0; $i < sizeof($racas); $i++) { 
		$model->assign_block_vars('raca', array('RACA'		=>	$racas[$i]->getId(),
												'CODIGO'	=>	$racas[$i]->getCodigo(),
												'NOME'		=>	$racas[$i]->getNome(),
												'DESCRICAO'	=>	$racas[$i]->getDescricao(),
												'DATA'		=>	$racas[$i]->getData(),
												'OBSERVACAO'=>	$racas[$i]->getObservacao(),
												'ATIVO'		=>	$racas[$i]->getAtivo()==1?"ATIVO":"INATIVO",
												'CLASSE'	=>	$i%2==0?"class='odd'":""
												));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}



$model->pparse('cadastro.raca');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
