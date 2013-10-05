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
include_once "class.animal.php";

header("Content-Type: text/html;  charset=ISO-8859-1",true);

$lista_ordem="";
$desc=" ASC";
$query_adicional = ""; 

$msg_erro="";
$msg="";

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout = "movimento";
$titulo = "Notas Fiscais";
$sub_titulo = "Lista das Notas Fiscais";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array('movimento.entrada.nf.lista' => 'movimento.entrada.nf.lista.htm'));


##############################################################################
##############                   PAGINA                       	##############
##############################################################################	


fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$faturamentos = $sessionFacade->recuperarFaturamentoTodosDAO();
	for($i= 0; $i < sizeof($faturamentos); $i++) {
		$model->assign_block_vars('faturamento', array('FATURAMENTO'		=>	$faturamentos[$i]->getId(),
														'NOTA_FISCAL'		=>	$faturamentos[$i]->getNotaFiscal(),
														'EMISSAO'			=>	$faturamentos[$i]->getEmissao(),
														'PROPRIETARIO'		=>	$faturamentos[$i]->getProprietario()->getNome(),
														'FORNECEDOR'		=>	$faturamentos[$i]->getFornecedor()->getNome(),
														'SAIDA'				=>	$faturamentos[$i]->getSaida(),
														'CONFERIDA'			=>	$faturamentos[$i]->getConferida(),
														'EXPORTADA'			=>	$faturamentos[$i]->getExportado(),
														'TOTAL'				=>	$faturamentos[$i]->getTotalNota(),
														'CLASSE'			=>	$i%2==0?"class='odd'":""
														));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

	


$model->pparse('movimento.entrada.nf.lista');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>