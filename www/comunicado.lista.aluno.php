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

$msg_erro	= array();
$msg_ok		= array();
$msg		= "";

if (isset($_GET['filtro'])){
	$filtro = $_GET['filtro'];
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Comunicados Cadastrados";
$sub_titulo="Lista de Comunicados";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.'.php'));

##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

try {
	if ($filtro <> 'novos' AND $filtro <> 'obrigatorio'){
		$filtro = '';
	}

	$comunicados = $sessionFacade->recuperarComunicadoTodosDAO($filtro);
	for($i= 0; $i < count($comunicados); $i++) { 
		$model->assign_block_vars('comunicado', array(	'COMUNICADO'		=>	$comunicados[$i]->getId(),
														'CURSO'				=>	is_object($comunicados[$i]->getCurso())?$comunicados[$i]->getCurso()->getNome():"",
														'TITULO_COMUNICADO'	=>	$comunicados[$i]->getTitulo(),
														'DATA'				=>	$comunicados[$i]->getData(),
														'COMENTARIO'		=>	$comunicados[$i]->getComentario(),
														'OBRIGATORIO'		=>	($comunicados[$i]->getObrigatorio()=="1")?"Sim":"Não",
														'CLASSE'			=>	$i%2==0?"class='odd'":""
		));
	}

	if (count($comunicados)>0 AND $filtro == 'obrigatorio'){
		array_push($msg_ok,"Atenção: há comunicados obrigatórios que devem ser lidos.");
	}

	if (count($comunicados)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhum comunicado encontrado!'));
	}
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>