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

$qtde_item = 15;

##############################################################################
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$faturamento		= addslashes(trim($_POST['faturamento']));
	$proprietario		= addslashes(trim($_POST['proprietario']));
	$fornecedor			= addslashes(trim($_POST['fornecedor']));
	$nota_fiscal		= addslashes(trim($_POST['nota_fiscal']));
	$serie				= addslashes(trim($_POST['serie']));
	$cfop				= addslashes(trim($_POST['cfop']));
	$emissao			= addslashes(trim($_POST['emissao']));
	$base_icms			= addslashes(trim($_POST['base_icms']));
	$valor_icms			= addslashes(trim($_POST['valor_icms']));
	$base_ipi			= addslashes(trim($_POST['base_ipi']));
	$valor_ipi			= addslashes(trim($_POST['valor_ipi']));
	$total_nota			= addslashes(trim($_POST['total_nota']));
	$observacao			= addslashes(trim($_POST['observacao']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_proprietario          = $sessionFacade->recuperarProprietario($proprietario);
		$obj_fornecedor            = $sessionFacade->recuperarProprietario($fornecedor);

		$fat = new Faturamento();
		$fat->setId($faturamento);
		$fat->setProprietario($obj_proprietario);
		$fat->setFornecedor($obj_fornecedor);
		$fat->setNotaFiscal($nota_fiscal);
		$fat->setSerie($serie);
		$fat->setCfop($cfop);
		$fat->setEmissao($emissao);
		$fat->setBaseIcms($base_icms);
		$fat->setValorIcms($valor_icms);
		$fat->setBaseIpi($base_ipi);
		$fat->setValorIpi($valor_ipi);
		$fat->setTotalNota($total_nota);
		$fat->setObservacao($observacao);

		for ($i=0; $i<$qtde_item; $i++){

			$faturamento_item	= addslashes(trim($_POST['faturamento_item_'.$i]));
			$especie			= addslashes(trim($_POST['especie_'.$i]));
			$raca				= addslashes(trim($_POST['raca_'.$i]));
			$qtde				= addslashes(trim($_POST['qtde_'.$i]));
			$valor				= addslashes(trim($_POST['valor_'.$i]));
			$total				= addslashes(trim($_POST['total_'.$i]));

			if (strlen($especie)==0 OR strlen($raca)==0 OR strlen($qtde)==0 OR strlen($valor)==0){
				continue;
			}

			$obj_especie    = $sessionFacade->recuperarEspecie($especie);
			$obj_raca       = $sessionFacade->recuperarRaca($raca);

			$fatItem = new FaturamentoItem();
			$fatItem->setId($faturamento_item);
			$fatItem->setEspecie($obj_especie);
			$fatItem->setRaca($obj_raca);
			$fatItem->setQtde($qtde);
			$fatItem->setPreco($valor);

			$fat->addItem($fatItem);
		}

		$sessionFacade->gravarFaturamento($fat);
		$banco->desconecta(); 
		#header("Location: ".$PHP_SELF."?faturamento=".$fat->getId()."&msg_codigo=1");
		header("location: movimento.entrada.nf.conferencia.php?faturamento=".$fat->getId());
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

$layout     = "movimento";
$titulo     = "Entrada de Nota Fiscal";
$sub_titulo = "Movimento: Entrada de NF";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('movimento.entrada.nf' => 'movimento.entrada.nf.htm'));


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

		if ( is_object($fat)){
			$faturamento		= $fat->getId();
			$proprietario		= (is_object($fat->getProprietario()))? $fat->getProprietario()->getId() : "";
			$fornecedor			= (is_object($fat->getFornecedor()))? $fat->getFornecedor()->getId() : "";
			$nota_fiscal		= $fat->getNotaFiscal();
			$serie				= $fat->getSerie();
			$cfop				= $fat->getCfop();
			$emissao			= $fat->getEmissao();
			$saida				= $fat->getSaida();
			$conferida			= $fat->getConferida();
			$cancelada			= $fat->getCancelada();
			$exportada			= $fat->getExportado();
			$transportadora		= $fat->getTransportadora();
			$total_nota			= $fat->getTotalNota();
			$base_icms			= $fat->getBaseIcms();
			$base_ipi			= $fat->getBaseIpi();
			$valor_icms			= $fat->getValorIcms();
			$valor_ipi			= $fat->getValorIpi();
			$observacao			= $fat->getObservacao();
		}else{
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


/*        PROPRIETARIOS        */
$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco);
try {
	$banco->conecta();
	$proprietarios = $sessionFacade->recuperarProprietarioTodosDAO();
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

/*        FORNECEDOR        */
$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco);
try {
	$banco->conecta();
	$fornecedores = $sessionFacade->recuperarFornecedorTodosDAO();
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

/*        RAÇAS        */
$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$racas = $sessionFacade->recurarRacaTodosDAO();
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}


/*        ESPECIE        */
$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco); 
try {
	$banco->conecta(); 
	$especies = $sessionFacade->recurarEspecieTodosDAO();
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

function optionFornecedor($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}

function optionProprietario($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}

function optionRacas($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}

function optionEspecies($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}

$model->assign_vars(array(		'FATURAMENTO'		=>	$faturamento,
								'PROPRIETARIO'		=>	optionProprietario($proprietarios,$proprietario),
								'FORNECEDOR'		=>	optionFornecedor($fornecedores,$fornecedor),
								'NOTA_FISCAL'		=>	$nota_fiscal,
								'SERIE'				=>	$serie,
								'CFOP'				=>	$cfop,
								'EMISSAO'			=>	$emissao,
								'BASE_ICMS'			=>	$base_icms,
								'VALOR_ICMS'		=>	$valor_icms,
								'BASE_IPI'			=>	$base_ipi,
								'VALOR_IPI'			=>	$valor_ipi,
								'TOTAL_NOTA'		=>	$total_nota,
								'OBSERVACAO'		=>	$observacao
));	


$qtde_inicio = 0;

if (is_object($fat)){
	for ($i=0; $i<$fat->qtdeItem(); $i++){
		$qtde_inicio = $i+1;
		$model->assign_block_vars('item',array('FATURAMENTO_ITEM'	=>	$fat->getItem($i)->getId(),
												'ESPECIE'			=>	optionEspecies($especies,$fat->getItem($i)->getEspecie()->getId()),
												'RACA'				=>	optionRacas($racas,$fat->getItem($i)->getRaca()->getId()),
												'QTDE'				=>	$fat->getItem($i)->getQtde(),
												'VALOR'				=>	$fat->getItem($i)->getPreco(),
												'TOTAL'				=>	$fat->getItem($i)->getPreco() * $fat->getItem($i)->getQtde(),
												'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
												'I'					=>	$i
												));
	}
}

for ($i=$qtde_inicio; $i<$qtde_item; $i++){
	$model->assign_block_vars('item',array('ESPECIE'	=>	optionEspecies($especies),
											'RACA'		=>	optionRacas($racas),
											'CLASSE'	=>  ($i%2==0)?"class='odd'":"",
											'I'			=>	$i
											));
}
			
$model->pparse('movimento.entrada.nf');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
