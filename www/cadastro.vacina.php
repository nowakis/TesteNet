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
	
	$vacina					= addslashes(trim($_POST['vacina']));
	$nome					= addslashes(trim($_POST['nome']));
	$descricao				= addslashes(trim($_POST['descricao']));
	$custo					= addslashes(trim($_POST['custo']));
	$ativo					= addslashes(trim($_POST['ativo']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_vacina = new Vacina();
		$obj_vacina->setId($vacina);
		$obj_vacina->setNome($nome);
		$obj_vacina->setDescricao($descricao);
		$obj_vacina->setCusto($custo);
		$obj_vacina->setAtivo($ativo);

		$sessionFacade->gravarVacina($obj_vacina);
		$banco->desconecta(); 
		header("Location: ".$PHP_SELF."?vacina=".$obj_vacina->getId()."&msg_codigo=1");
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
$titulo="Cadastro de Vacinas";
$sub_titulo="Cadastro: Vacinas";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.vacina' => 'cadastro.vacina.htm'));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['vacina']) AND strlen(trim($_GET['vacina']))>0){

	$vacina = trim($_GET['vacina']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$obj_vacina = $sessionFacade->recuperarVacina($vacina); 

		if ( $obj_vacina->getId() > 0){
			$vacina			=	$obj_vacina->getId();
			$nome			=	$obj_vacina->getNome();
			$descricao		=	$obj_vacina->getDescricao();
			$custo			=	$obj_vacina->getCusto();
			$ativo			=	$obj_vacina->getAtivo();
		}else{
			array_push($msg_erro,"Vacina não encontrada!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

	$model->assign_vars(array(		'VACINA'		=>	$vacina,
									'NOME'			=>	$nome,
									'DESCRICAO'		=>	$descricao,
									'CUSTO'			=>	$custo,
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
	$vacinas = $sessionFacade->recurarVacinaTodosDAO();
	for($i= 0; $i < sizeof($vacinas); $i++) { 
		$model->assign_block_vars('vacina', array(	'VACINA'	=>	$vacinas[$i]->getId(),
													'NOME'		=>	$vacinas[$i]->getNome(),
													'DESCRICAO'	=>	$vacinas[$i]->getDescricao(),
													'CUSTO'		=>	$vacinas[$i]->getCusto(),
													'ATIVO'		=>	$vacinas[$i]->getAtivo()==1?"ATIVO":"INATIVO",
													'CLASSE'	=>	$i%2==0?"class='odd'":""
													));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}



$model->pparse('cadastro.vacina');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>