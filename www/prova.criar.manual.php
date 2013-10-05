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

##############################################################################
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$prova				= addslashes(trim($_POST['prova']));
	$titulo_prova		= addslashes(trim($_POST['titulo_prova']));
	$curso				= addslashes(trim($_POST['curso']));
	$disciplina			= addslashes(trim($_POST['disciplina']));
	$numero_perguntas	= addslashes(trim($_POST['numero_perguntas']));
	$data_inicio		= addslashes(trim($_POST['data_inicio']));
	$data_termino		= addslashes(trim($_POST['data_termino']));
	$dificuldade		= addslashes(trim($_POST['dificuldade']));

	$qtde_perguntas		= addslashes(trim($_POST['qtde_perguntas']));
	$qtde_pergunta_prova = 0;

	try {
		$banco->iniciarTransacao();

		$prov = new Prova();
		$prov->setId($prova);
		$prov->setTitulo($titulo_prova);
		$prov->setNumeroPerguntas($numero_perguntas);
		$prov->setData(date("d/m/Y H:i:s"));
		$prov->setDataInicio($data_inicio);
		$prov->setDataTermino($data_termino);
		$prov->setDificuldade($dificuldade);
		$prov->setLiberada($data_inicio);

		if (strlen($disciplina)>0){
			$disc = $sessionFacade->recuperarDisciplina($disciplina); 
			$prov->setDisciplina($disc);
		}

		$prof = $sessionFacade->recuperarProfessor($_login_professor); 
		$prov->setProfessor($prof);

		/* Perguntas */
		for ($i=0; $i<$qtde_perguntas+10;$i++){
			$prova_pergunta = addslashes(trim($_POST['prova_pergunta_'.$i]));
			$pergunta       = addslashes(trim($_POST['pergunta_'.$i]));
			$peso           = addslashes(trim($_POST['peso_'.$i]));

			if (strlen($prova_pergunta)>0){
				$perg = NULL;
				$perg = $sessionFacade->recuperarProvaPergunta($prova_pergunta); 
				if ( is_object($perg)){
					$perg->setPeso($peso);
					$prov->addPergunta($perg);
					$qtde_pergunta_prova++;
				}
			}elseif (strlen($pergunta)>0){
				$perg = NULL;
				$perg = $sessionFacade->recuperarPergunta($pergunta); 
				if ( is_object($perg)){
					$perg_aux = new ProvaPergunta(); 
					$perg_aux->setTopico($perg->getTopico());
					$perg_aux->setTipoPergunta($perg->getTipoPergunta());
					$perg_aux->setTitulo($perg->getTitulo());
					$perg_aux->setDificuldade($perg->getDificuldade());
					$perg_aux->setFonte($perg->getFonte());
					$perg_aux->setPeso($peso);
					$perg_aux->setPerguntaOrigem($perg->getId());
					for ($j=0;$j<$perg->getQtdeResposta();$j++){
						$perg->getResposta($j)->setId(NULL);
						if (is_object($perg->getResposta($j)->getRespostaFilho())) {
							$perg->getResposta($j)->getRespostaFilho()->setId(NULL);
						}
						$perg_aux->addResposta($perg->getResposta($j));

						/*

											$resposta_filho = new Resposta();
											$resposta_filho->setId($resposta);
											#$resposta_filho->setPergunta($perg);
											$resposta_filho->setRespostaTexto($resposta_texto_filho);
											$resposta_filho->setRespostaCorreta($resposta_correta);
											$resposta_filho->setRespostaFilho($resposta_correta);

											$perg->addResposta($resposta_filho);
						*/

					}
					$prov->addPergunta($perg_aux);
					$qtde_pergunta_prova++;
				}
			}
		}

		$prov->setNumeroPerguntas($qtde_pergunta_prova);

		$sessionFacade->gravarProva($prov);
		$sessionFacade->distruiProvaAluno($prov);
		$sessionFacade->gravaDadosProvaPerguntas($prov); 
		$sessionFacade->distruiProvaAluno($prov);
		#throw new Exception('Teste.');
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: prova.criar.manual.php?prova=".$prov->getId()."&msg_codigo=1");
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
$titulo     = "Cadastro de Prova";
$sub_titulo = "Prova: Cadastrar";

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
	
if (isset($_GET['prova']) AND strlen(trim($_GET['prova']))>0){

	$prova = trim($_GET['prova']);

	try {
		$prov = $sessionFacade->recuperarProva($prova); 

		if ( is_object($prov)){
			$prova				= $prov->getId();
			$titulo_prova		= $prov->getTitulo();
			$disciplina			= $prov->getDisciplina()->getId();
			$curso				= $prov->getDisciplina()->getCurso()->getId();
			$professor			= $prov->getProfessor()->getId();
			$numero_perguntas	= $prov->getNumeroPerguntas();
			$data				= $prov->getData();
			$data_inicio		= $prov->getDataInicio();
			$data_termino		= $prov->getDataTermino();
			$dificuldade		= $prov->getDificuldade();
		}else{
			throw new Exception('Prova não encontrada!!!');
			#array_push($msg_erro,"Prova não encontrada!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}


/*         PROFESSOR, CURSO, DISCIPLINAS         */

try {
	$professores = $sessionFacade->recuperarProfessorTodosDAO();
	$cursos      = $sessionFacade->recuperarCursoTodosDAO();
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE'		=>	$dificuldade,
							'DISCIPLINA'		=>	optionDisciplina($disciplinas,$disciplina),
							'CURSO'				=>	optionCurso($cursos,$curso),
							'PROFESSOR'			=>	optionProfessor($professores,$professor),
							'BTN_NOME'			=>  (strlen($prova)>0)?"Confirmar Alterações":"Agendar Prova"
));	

if (is_object($prov)){
	for ($i=0;$i<$prov->getQtdePerguntas();$i++){
		$model->assign_block_vars('pergunta', array('I'				=>	$i,
													'NUMERO'		=>	$i+1,
													'PROVA_PERGUNTA'=>	$prov->getPergunta($i)->getId(),
													'PERGUNTA'		=>	$prov->getPergunta($i)->getPerguntaOrigem(),
													'TITULO_PERGUNTA'=>	$prov->getPergunta($i)->getTituloReduzido(40),
													'TOPICO'		=>	$prov->getPergunta($i)->getTopico()->getDescricao(),
													'TIPO'			=>	$prov->getPergunta($i)->getTipoPergunta()->getDescricao(),
													'TIPO_IMAGEM'	=>	$prov->getPergunta($i)->getTipoPergunta()->getImagem(),
													'PESO'			=>	$prov->getPergunta($i)->getPeso(),
													'DIFICULDADE'	=>	$prov->getPergunta($i)->getDificuldade('texto'),
													'DIFICULDADE'	=>	$prov->getPergunta($i)->getDificuldade('texto'),
													'CLASSE'		=>	$i%2==0?"class='odd'":""
													));
	}
	$model->assign_vars(array( 'QTDE_PERGUNTAS' => $prov->getQtdePerguntas()));
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>