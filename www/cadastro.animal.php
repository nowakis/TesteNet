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
	
	$animal					= addslashes(trim($_POST['animal']));
	$numero					= addslashes(trim($_POST['numero']));
	$apelido				= addslashes(trim($_POST['apelido']));
	$especie				= addslashes(trim($_POST['especie']));
	$raca					= addslashes(trim($_POST['raca']));
	$marca					= addslashes(trim($_POST['marca']));
	$nascimento				= addslashes(trim($_POST['nascimento']));
	$obito					= addslashes(trim($_POST['obito']));
	$entrada				= addslashes(trim($_POST['entrada']));
	$pai					= addslashes(trim($_POST['pai']));
	$animal_pai				= addslashes(trim($_POST['animal_pai']));
	$mae					= addslashes(trim($_POST['mae']));
	$animal_mae				= addslashes(trim($_POST['animal_mae']));
	$sexo					= addslashes(trim($_POST['sexo']));
	$tipo_criacao			= addslashes(trim($_POST['tipo_criacao']));
	$proprietario			= addslashes(trim($_POST['proprietario']));
	$crias					= addslashes(trim($_POST['crias']));
	$valor_compra			= addslashes(trim($_POST['valor_compra']));
	$previsao_valor_venda	= addslashes(trim($_POST['previsao_valor_venda']));
	$previsao_data_venda	= addslashes(trim($_POST['previsao_data_venda']));
	$valor_venda			= addslashes(trim($_POST['valor_venda']));
	$desmamado				= addslashes(trim($_POST['desmamado']));
	$observacao				= addslashes(trim($_POST['observacao']));
	$peso					= addslashes(trim($_POST['peso']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_proprietario          = $sessionFacade->recuperarProprietario($proprietario);
		$obj_marca                 = $sessionFacade->recuperarMarca($marca);
		$obj_especie               = $sessionFacade->recuperarEspecie($especie);
		$obj_raca                  = $sessionFacade->recuperarRaca($raca);
		$obj_tipo_criacao          = $sessionFacade->recuperarTipoCriacao($tipo_criacao);

		$cabeca = new Animal();
		$cabeca->setId($animal);
		$cabeca->setNumero($numero);
		$cabeca->setApelido($apelido);
		$cabeca->setEspecie($obj_especie);
		$cabeca->setRaca($obj_raca);
		$cabeca->setMarca($obj_marca);
		$cabeca->setNascimento($nascimento);
		$cabeca->setObito($obito);
		$cabeca->setEntrada($entrada);
		$cabeca->setPai($pai);
		$cabeca->setAnimalPai($animal_pai);
		$cabeca->setMae($mae);
		$cabeca->setAnimalMae($animal_mae);
		$cabeca->setSexo($sexo);
		$cabeca->setTipoCriacao($obj_tipo_criacao);
		$cabeca->setProprietario($obj_proprietario);
		$cabeca->setCrias($crias);
		$cabeca->setValorCompra($valor_compra);
		$cabeca->setPrevisaoValorVenda($previsao_valor_venda);
		$cabeca->setPrevisaoDataVenda($previsao_data_venda);
		$cabeca->setValorVenda($valor_venda);
		$cabeca->setDesmamado($desmamado);
		$cabeca->setObservacao($observacao);
		$cabeca->setPeso($peso);

		$sessionFacade->gravarAnimal($cabeca);
		$banco->desconecta(); 
		header("Location: ".$PHP_SELF."?animal=".$cabeca->getId()."&msg_codigo=1");
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

$layout     = "cadastro";
$titulo     = "Cadastro de Animal";
$sub_titulo = "Cadastro: Animal";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.animal' => 'cadastro.animal.htm'));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['animal']) AND strlen(trim($_GET['animal']))>0){

	$animal = trim($_GET['animal']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$cabeca = $sessionFacade->recuperarAnimal($animal); 

		if ( $cabeca->getId() > 0){
			$animal					=	$cabeca->getId();
			$entrada				=	$cabeca->getEntrada();
			$numero					=	$cabeca->getNumero();
			$especie				=	(is_object($cabeca->getEspecie()))?$cabeca->getEspecie()->getId():"";
			$raca					=	(is_object($cabeca->getRaca()))?$cabeca->getRaca()->getId():"";
			$marca					=	(is_object($cabeca->getMarca()))?$cabeca->getMarca()->getId():"";
			$nascimento				=	$cabeca->getNascimento();
			$obito					=	$cabeca->getObito();
			$apelido				=	$cabeca->getApelido();
			$pai					=	$cabeca->getPai();
			$mae					=	$cabeca->getMae();
			$animal_pai				=	$cabeca->getAnimalPai();
			$animal_mae				=	$cabeca->getAnimalMae();
			$sexo					=	$cabeca->getSexo();
			$crias					=	$cabeca->getCrias();
			$tipo_criacao			=	(is_object($cabeca->getTipoCriacao()))?$cabeca->getTipoCriacao()->getId():"";
			$proprietario			=	(is_object($cabeca->getProprietario()))?$cabeca->getProprietario()->getId():"";
			$valor_compra			=	$cabeca->getValorCompra();
			$previsao_valor_venda	=	$cabeca->getPrevisaoValorVenda();
			$previsao_data_venda	=	$cabeca->getPrevisaoDataVenda();
			$desmamado				=	$cabeca->getDesmamado();
			$observacao				=	$cabeca->getObservacao();
			$peso					=	$cabeca->getPeso();
		}else{
			array_push($msg_erro,"Animal não encontrado!");
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

	/*        TIPO DE CRIAÇÃO        */
	$query = "	SELECT tipo_criacao,
						descricao,
						codigo
				FROM tbl_tipo_criacao
				WHERE fazenda = $login_fazenda";
	$rSet = $db->Query($query);
	$temp="";
	$tipo_criacaoOption  = "";
	$tipo_criacaoOption .= "<option value=''></option>";
	while ($linha = $db->FetchArray($rSet)){
		$temp = ($tipo_criacao==$linha['tipo_criacao'])?"selected":"";
		$tipo_criacaoOption .= "<option value='".$linha['tipo_criacao']."' $temp>".$linha['descricao']."</option>";
		$temp = "";
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
			$temp = $proprietario==$proprietarios[$i]->getId()?"selected":"";
			$proprietarioOption .= "<option value='".$proprietarios[$i]->getId()."' ".$temp.">".$proprietarios[$i]->getNome()."</option>";	
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

	/*        ESPECIES        */

	$especieOption  = "";
	$especieOption .= "<option value=''></option>";	
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 
	try {
		$banco->conecta(); 
		$especies = $sessionFacade->recurarEspecieTodosDAO();
		for($i= 0; $i < sizeof($especies); $i++) { 
			$temp = ($especie==$especies[$i]->getId())?"selected":"";
			$especieOption .= "<option value='".$especies[$i]->getId()."' ".$temp.">".$especies[$i]->getNome()."</option>";	
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

	/*        RAÇAS        */

	$racaOption  = "";
	$racaOption .= "<option value=''></option>";	
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 
	try {
		$banco->conecta(); 
		$racas = $sessionFacade->recurarRacaTodosDAO();
		for($i= 0; $i < sizeof($racas); $i++) { 
			$temp = ($raca==$racas[$i]->getId())?"selected":"";
			$racaOption .= "<option value='".$racas[$i]->getId()."' ".$temp.">".$racas[$i]->getNome()."</option>";	
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

		
	/*        MARCAS        */

	$marcaOption  = "";
	$marcaOption .= "<option value=''></option>";	
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 
	try {
		$banco->conecta(); 
		$marcas = $sessionFacade->recurarMarcaTodosDAO();
		for($i= 0; $i < sizeof($marcas); $i++) { 
			$temp = ($marca==$marcas[$i]->getId())?"selected":"";
			$marcaOption .= "<option value='".$marcas[$i]->getId()."' ".$temp.">".$marcas[$i]->getCodigo()."</option>";	
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}
		
	/*        TIPO CRIAÇÃO        */

	$tipo_criacaoOption  = "";
	$tipo_criacaoOption .= "<option value=''></option>";	
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 
	try {
		$banco->conecta(); 
		$tipo_criacoes = $sessionFacade->recurarTipoCriacaoTodosDAO();
		for($i= 0; $i < sizeof($tipo_criacoes); $i++) { 
			$temp = ($tipo_criacao==$tipo_criacoes[$i]->getId())?"selected":"";
			$tipo_criacaoOption .= "<option value='".$tipo_criacoes[$i]->getId()."' ".$temp.">".$tipo_criacoes[$i]->getCodigo()."</option>";	
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}

	
	$model->assign_vars(array(		'ANIMAL'			=>	$animal,
									'ENTRADA'			=>	$entrada,
									'NUMERO'			=>	$numero,
									'NASCIMENTO'		=>	$nascimento,
									'APELIDO'			=>	$apelido,
									'PAI'				=>	$pai,
									'MAE'				=>	$mae,
									'SEXOM'				=>	($sexo=="M")?' CHECKED ':'',
									'SEXOF'				=>	($sexo=="F")?' CHECKED ':'',
									'CRIAS'				=>	$crias,
									'ESPECIE'			=>	$especieOption,
									'RACA'				=>	$racaOption,
									'MARCA'				=>	$marcaOption,
									'TIPO_CRIACAO'		=>	$tipo_criacaoOption,
									'PROPRIETARIO'		=>	$proprietarioOption,
									'VALOR_COMPRA'		=>	$valor_compra,
									'PREVISAO_VALOR_VENDA'=>$previsao_valor_venda,
									'PREVISAO_DATA_VENDA'=>	$previsao_data_venda,
									'OBSERVACAO'		=>	$observacao,
									'PESO'				=>	$peso
									));	
				

	fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse('cadastro.animal');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
