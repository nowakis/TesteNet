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
	
	$especie				= addslashes(trim($_POST['especie']));
	$nome					= addslashes(trim($_POST['nome']));
	$descricao				= addslashes(trim($_POST['descricao']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_especie = new Especie();
		$obj_especie->setId($especie);
		$obj_especie->setNome($nome);
		$obj_especie->setDescricao($descricao);

		$sessionFacade->gravarEspecie($obj_especie);
		$banco->desconecta(); 
		header("Location: ".$PHP_SELF."?especie=".$obj_especie->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desconecta(); 
		array_push($msg_erro,$e->getMessage());
	}
}


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout		= "cadastro";
$titulo		= "Cadastro de Espécie";
$sub_titulo	= "Cadastro: Espécie";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.especie' => 'cadastro.especie.htm'));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['especie']) AND strlen(trim($_GET['especie']))>0){

	$especie = trim($_GET['especie']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$obj_especie = $sessionFacade->recuperarEspecie($especie); 

		if ( $obj_especie->getId() > 0){
			$especie		=	$obj_especie->getId();
			$nome			=	$obj_especie->getNome();
			$descricao		=	$obj_especie->getDescricao();
		}else{
			array_push($msg_erro,"Espécie não encontrado!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

	$model->assign_vars(array(	'ESPECIE'		=>	$especie,
								'NOME'			=>	$nome,
								'DESCRICAO'		=>	$descricao
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
	$especies = $sessionFacade->recurarEspecieTodosDAO();
	for($i= 0; $i < sizeof($especies); $i++) { 
		$model->assign_block_vars('especie', array(	'ESPECIE'	=>	$especies[$i]->getId(),
													'NOME'		=>	$especies[$i]->getNome(),
													'DESCRICAO'	=>	$especies[$i]->getDescricao(),
													'CLASSE'	=>	$i%2==0?"class='odd'":""
													));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

$model->pparse('cadastro.especie');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>
