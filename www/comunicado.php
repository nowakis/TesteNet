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
$msg		= "";

#$_descricao_programa = basename($_SERVER['PHP_SELF'],'.php');


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
	
	$comunicado			= addslashes(trim($_POST['comunicado']));
	$curso				= addslashes(trim($_POST['curso']));
	$titulo_comunicado	= addslashes(trim($_POST['titulo']));
	$comentario			= addslashes(trim($_POST['comentario']));
	$obrigatorio		= addslashes(trim($_POST['obrigatorio']));

	try {
		$banco->iniciarTransacao();

		$obj_instituicao= $sessionFacade->recuperarInstituicao($_login_instituicao);
		$obj_curso		= $sessionFacade->recuperarCurso($curso);
		$obj_professor	= $sessionFacade->recuperarProfessor($_login_professor);

		$comun = new Comunicado();
		$comun->setId($comunicado);
		$comun->setInstituicao($obj_instituicao);
		$comun->setCurso($obj_curso);
		$comun->setProfessor($obj_professor);
		$comun->setTitulo($titulo_comunicado);
		$comun->setData(date("d/m/Y H:i"));
		$comun->setComentario($comentario);
		$comun->setObrigatorio($obrigatorio);

		$sessionFacade->gravarComunicado($comun);
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: comunicado.php?comunicado=".$comun->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desfazerTransacao();
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Cadastro de Comunicado";
$sub_titulo = "Comunicado: Cadastrar";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['comunicado']) AND strlen(trim($_GET['comunicado']))>0){

	$comunicado = trim($_GET['comunicado']);
	try {
		$comun = $sessionFacade->recuperarComunicado($comunicado); 

		if (is_object($comun)){
			$comunicado			= $comun->getId();
			$curso				= is_object($comun->getCurso())?$comun->getCurso()->getId():"";
			$titulo_comunicado	= $comun->getTitulo();
			$data				= $comun->getData();
			$comentario			= $comun->getComentario();
			$obrigatorio		= $comun->getObrigatorio();
		}else{
			array_push($msg_erro,"Comunicado não encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

if (strlen($comunicado)==0){
	array_push($msg_ok,"Cadastre um novo Comunicado!");
	#array_push($msg_ok,"Preencha com os dados abaixo e clique em 'Gravar'.");
	array_push($msg_ok,"Selecione para qual curso deseja direcionar o Comunicado. Se o comunicado for para todos os cursos, selecione TODOS OS CURSOS");
	array_push($msg_ok,"Selecione 'Leitura Obrigatória SIM' caso queira que o comunicado seja de leitura obrigatório pelos Alunos");
}


/*         CURSO         */
try {
	$cursos		 = $sessionFacade->recuperarCursoTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

$model->assign_vars(array(	'COMUNICADO'		=>	$comunicado,
							'CURSO'				=>	optionCursoComunicado($cursos,$curso),
							'TITULO_COMUNICADO'	=>	$titulo_comunicado,
							'DATA'				=>	$data,
							'COMENTARIO'		=>	$comentario,
							'OBRIGATORIO'		=>	$obrigatorio==1?"checked":"",
							'NAO_OBRIGATORIO'	=>	$obrigatorio!=1?"checked":"",
							'BTN_NOME'			=>  (strlen($comunicado)>0)?"Confirmar Alterações":"Gravar"
));	

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

			
$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>