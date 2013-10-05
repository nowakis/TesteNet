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
	
	$faturamento = addslashes(trim($_POST['faturamento']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try{
		$banco->conecta(); 
		#$banco->iniciarTransacao();
		$fat = $sessionFacade->recuperarFaturamento($faturamento); 
		if (is_object($fat)){
			$fat->gravarConferencia();
			$sessionFacade->explodirFaturamento($fat);
			$sessionFacade->gravarFaturamento($fat);
		}else{
			throw new Exception("Faturamento não encontrado!"); 
		}
		#$banco->efetivarTransacao();
		$banco->desconecta();
	}catch(Exception $e) {
		#$banco->desfazerTransacao();
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "movimento";
$titulo     = "Conferência de Nota Fiscal";
$sub_titulo = "Movimento: Conferência de NF";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('movimento.entrada.nf.conferencia' => 'movimento.entrada.nf.conferencia.htm'));


##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['faturamento']) AND strlen(trim($_GET['faturamento']))>0){

	$faturamento = trim($_GET['faturamento']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$fat = $sessionFacade->recuperarFaturamento($faturamento); 
		if (!is_object($fat)){
			array_push($msg_erro,"Faturamento não encontrado!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}
}


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

if (is_object($fat)){
	$model->assign_vars(array(		'FATURAMENTO'		=>	$fat->getId(),
									'PROPRIETARIO'		=>	(is_object($fat->getProprietario()))? $fat->getProprietario()->getNome() : "",
									'FORNECEDOR'		=>	(is_object($fat->getFornecedor()))? $fat->getFornecedor()->getNome() : "",
									'NOTA_FISCAL'		=>	$fat->getNotaFiscal(),
									'SERIE'				=>	$fat->getSerie(),
									'CFOP'				=>	$fat->getCfop(),
									'EMISSAO'			=>	$fat->getEmissao(),
									'SAIDA'				=>	$fat->getSaida(),
									'CONFERIDA'			=>	$fat->getConferida(),
									'CANCELADA'			=>	$fat->getCancelada(),
									'EXPORTADA'			=>	$fat->getExportado(),
									'TRANSPORTADORA'	=>	$fat->getTransportadora(),
									'BASE_ICMS'			=>	$fat->getBaseIcms(),
									'VALOR_ICMS'		=>	$fat->getValorIcms(),
									'BASE_IPI'			=>	$fat->getBaseIpi(),
									'VALOR_IPI'			=>	$fat->getValorIpi(),
									'TOTAL_NOTA'		=>	$fat->getTotalNota(),
									'OBSERVACAO'		=>	$fat->getObservacao()
	));	

	for ($i=0; $i<$fat->qtdeItem(); $i++){
		$qtde_inicio = $i+1;
		$model->assign_block_vars('item',array('FATURAMENTO_ITEM'	=>	$fat->getItem($i)->getId(),
												'ESPECIE'			=>	$fat->getItem($i)->getEspecie()->getNome(),
												'RACA'				=>	$fat->getItem($i)->getRaca()->getNome(),
												'QTDE'				=>	$fat->getItem($i)->getQtde(),
												'VALOR'				=>	$fat->getItem($i)->getPreco(),
												'TOTAL'				=>	$fat->getItem($i)->getPreco() * $fat->getItem($i)->getQtde(),
												'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
												'I'					=>	$i
												));
	}
}

$model->pparse('movimento.entrada.nf.conferencia');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>