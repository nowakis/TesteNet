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
	
	$marca				= addslashes(trim($_POST['marca']));
	$codigo				= addslashes(trim($_POST['codigo']));
	$proprietario		= addslashes(trim($_POST['proprietario']));
	$observacao			= addslashes(trim($_POST['observacao']));
	$ativo				= addslashes(trim($_POST['ativo']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_marca = new Marca();
		$obj_marca->setId($marca);
		$obj_marca->setCodigo($codigo);
		$obj_marca->setProprietario($proprietario);
		$obj_marca->setObservacao($observacao);
		$obj_marca->setAtivo($ativo);

		$sessionFacade->gravarMarca($obj_marca);
		$banco->desconecta(); 
		header("Location: ".$PHP_SELF."?marca=".$obj_marca->getId()."&msg_codigo=1");
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

$layout="cadastro";
$titulo="Cadastro de Marcas";
$sub_titulo="Cadastro: Marcas";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.marca' => 'cadastro.marca.htm'));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['marca']) AND strlen(trim($_GET['marca']))>0){

	$marca = trim($_GET['marca']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$obj_marca = $sessionFacade->recuperarMarca($marca); 

		if ( $obj_marca->getId() > 0){
			$marca			=	$obj_marca->getId();
			$codigo			=	$obj_marca->getCodigo();
			$proprietario	=	$obj_marca->getProprietario();
			$observacao		=	$obj_marca->getObservacao();
			$ativo			=	$obj_marca->getAtivo();
		}else{
			array_push($msg_erro,"Marca não encontrada!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}
}

/*        PROPRIETARIOS        */

$proprietarioOption  = "";
$proprietarioOption .= "<option value=''></option>";	
$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco);
try {
	$banco->conecta();
	$proprietarios = $sessionFacade->recuperarProprietarioTodosDAO();
	for($i= 0; $i < sizeof($proprietarios); $i++) { 
		if (is_object($proprietario)){
			$temp = $proprietario->getId()==$proprietarios[$i]->getId()?"selected":"";
		}
		$proprietarioOption .= "<option value='".$proprietarios[$i]->getId()."' ".$temp.">".$proprietarios[$i]->getNome()."</option>";	
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}


$model->assign_vars(array(		'MARCA'			=>	$marca,
								'CODIGO'		=>	$codigo,
								'PROPRIETARIO'	=>	$proprietarioOption,
								'OBSERVACAO'	=>	$observacao,
								'ATIVO'			=>	$ativo==1?"checked":"",
								'INATIVO'		=>	$ativo!=1?"checked":""
								));	
	
##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$marcas = $sessionFacade->recurarMarcaTodosDAO();
	for($i= 0; $i < sizeof($marcas); $i++) { 
		$model->assign_block_vars('marca', array(	'MARCA'			=>	$marcas[$i]->getId(),
													'CODIGO'		=>	$marcas[$i]->getCodigo(),
													'PROPRIETARIO'	=>	$marcas[$i]->getProprietario()->getNome(),
													'OBSERVACAO'	=>	$marcas[$i]->getObservacao(),
													'ATIVO'			=>	$marcas[$i]->getAtivo()==1?"ATIVO":"INATIVO",
													'CLASSE'		=>	$i%2==0?"class='odd'":""
													));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->pparse('cadastro.marca');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
