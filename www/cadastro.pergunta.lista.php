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

if (isset($_GET['excluir'])){
	$excluir = trim($_GET['excluir']);
	if (strlen($excluir)>0){
		try {
			$banco->iniciarTransacao();
			$curs = $sessionFacade->recuperarPergunta($excluir); 
			$sessionFacade->excluirPergunta($curs);
			$banco->efetivarTransacao();
			$banco->desconecta(); 
			header("Location: cadastro.pergunta.lista.php?msg_codigo=2");
			exit;
		} catch(Exception $e) { 
			$banco->desfazerTransacao();
			array_push($msg_erro,$e->getMessage());
		}
	}
}


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Perguntas Cadastrados";
$sub_titulo="Lista das Perguntas Cadastradas";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
	if ($msg_codigo == 2){
		array_push($msg_ok,"Pergunta excluído com sucesso!");
	}
}

try {
	$perguntas = $sessionFacade->recuperarPerguntaTodosDAO('opcional');
	for($i= 0; $i < sizeof($perguntas); $i++) { 
		$model->assign_block_vars('pergunta', array(	'PERGUNTA'		=>	$perguntas[$i]->getId(),
														'CURSO'			=>	$perguntas[$i]->getTopico()->getDisciplina()->getCurso()->getNome(),
														'DISCIPLINA'	=>	$perguntas[$i]->getTopico()->getDisciplina()->getNome(),
														'TOPICO'		=>	$perguntas[$i]->getTopico()->getDescricao(),
														'TITULO'		=>	$perguntas[$i]->getTituloReduzido(20),
														'TITULO_COMPLETO'=>	$perguntas[$i]->getTitulo(),
														'TIPO_PERGUNTA'	=>	is_object($perguntas[$i]->getTipoPergunta())?$perguntas[$i]->getTipoPergunta()->getDescricao():"",
														'TIPO_IMAGEM'	=>	is_object($perguntas[$i]->getTipoPergunta())?$perguntas[$i]->getTipoPergunta()->getImagem():"",
														'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
	}
	if (count($perguntas)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhuma pergunta cadastrada!'));
	}else{
		$model->assign_block_vars('paginacao', array('CONTADOR' => "Total de perguntas cadastradas: ".$i));
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
