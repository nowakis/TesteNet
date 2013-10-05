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

#$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');


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

if (strlen($_login_aluno)> 0){
	$_GET['aluno'] = $_login_aluno;
}

##############################################################################
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$aluno		= addslashes(trim($_POST['aluno']));
	$nome		= addslashes(trim($_POST['nome']));
	$ra			= addslashes(trim($_POST['ra']));
	$email		= addslashes(trim($_POST['email']));
	$senha		= addslashes(trim($_POST['senha']));
	$ativo		= addslashes(trim($_POST['ativo']));
	$endereco	= addslashes(trim($_POST['endereco']));
	$numero		= addslashes(trim($_POST['numero']));
	$complemento= addslashes(trim($_POST['complemento']));
	$bairro		= addslashes(trim($_POST['bairro']));
	$cidade		= addslashes(trim($_POST['cidade']));
	$estado		= addslashes(trim($_POST['estado']));
	$cep		= addslashes(trim($_POST['cep']));


	try {

		$banco->iniciarTransacao();

		$alu = new Aluno();
		$alu->setId($aluno);
		$alu->setNome($nome);
		$alu->setRa($ra);
		$alu->setEmail($email);
		$alu->setSenha($senha);
		$alu->setAtivo($ativo);
		$alu->setEndereco($endereco);
		$alu->setNumero($numero);
		$alu->setComplemento($complemento);
		$alu->setBairro($bairro);
		$alu->setCidade($cidade);
		$alu->setEstado($estado);
		$alu->setCep($cep);

		/* Disciplinas */
		$qtde_item = 20;
		for ($i=0; $i<$qtde_item;$i++){
			$disciplina = addslashes(trim($_POST['disciplina_'.$i]));
			if (strlen($disciplina)>0){
				$disc = $sessionFacade->recuperarDisciplina($disciplina); 
				if ( is_object($disc)){
					$alu->addDisciplina($disc);
				}
			}
		}
		$sessionFacade->gravarAluno($alu);
		$sessionFacade->gravarAlunoDisciplina($alu);
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: cadastro.aluno.php?aluno=".$alu->getId()."&msg_codigo=1");
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

$layout     = "relatorio";
$titulo     = "Relatório de Frequência de Acessos";
$sub_titulo = "Relatório: Frequência de Acessos";

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
	
if (isset($_GET['aluno']) AND strlen(trim($_GET['aluno']))>0){

	$aluno = trim($_GET['aluno']);
	try {
		$alu = $sessionFacade->recuperarAluno($aluno); 
		$alu = $sessionFacade->recuperarAlunoDisciplina($alu); 

		if ( is_object($alu)){
			$aluno		= $alu->getId();
			$nome		= $alu->getNome();
			$ra			= $alu->getRa();
			$email		= $alu->getEmail();
			$senha		= $alu->getSenha();
			$ativo		= $alu->getAtivo();
			$endereco	= $alu->getEndereco();
			$numero		= $alu->getNumero();
			$complemento= $alu->getComplemento();
			$bairro		= $alu->getBairro();
			$cidade		= $alu->getCidade();
			$estado		= $alu->getEstado();
			$cep		= $alu->getCep();
			
		}else{
			array_push($msg_erro,"Aluno não encontrado!");
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

if (strlen($aluno)==0){
	array_push($msg_ok,"Cadastre um novo Aluno!");
	array_push($msg_ok,"Preencha com os dados abaixo e clique em 'Gravar'.");
	array_push($msg_ok,"Importante: selecione os Cursos e Disciplinas que este aluno está Matriculado");
}

$model->assign_vars(array(	'ALUNO'			=>	$aluno,
							'NOME'			=>	$nome,
							'RA'			=>	$ra,
							'EMAIL'			=>	$email,
							'SENHA'			=>	$senha,
							'ATIVO'			=>	($ativo!=0 OR  strlen($ativo)==0)?"checked":"",
							'INATIVO'		=>	($ativo==0 AND strlen($ativo)>0 )?"checked":"",
							'ENDERECO'		=>	$endereco,
							'NUMERO'		=>	$numero,
							'COMPLEMENTO'	=>	$complemento,
							'BAIRRO'		=>	$bairro,
							'CIDADE'		=>	$cidade,
							'ESTADO'		=>	$estado,
							'CEP'			=>	$cep,
							'BTN_NOME'		=>  (strlen($aluno)>0)?"Confirmar Alterações":"Gravar"
));	

$qtde_item = 20;
if (is_object($alu)){
	for ($i=0; $i < $alu->getQtdeDisciplina(); $i++){
		$model->assign_block_vars('disciplina',array('DISCIPLINA'		=>	$alu->getDisciplina($i)->getId(),
													'DISCIPLINA_NOME'	=>	$alu->getDisciplina($i)->getNome(),
													'CURSO_NOME'		=>	$alu->getDisciplina($i)->getCurso()->getNome(),
													'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
													'I'					=>	$i
													));
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
