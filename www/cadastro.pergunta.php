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
require_once "class/class.upload.php";
require_once "banco.con.php";
require_once "autentica_usuario.php";


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
	
	$pergunta				= addslashes(trim($_POST['pergunta']));
	$curso					= addslashes(trim($_POST['curso']));
	$disciplina				= addslashes(trim($_POST['disciplina']));
	$topico					= addslashes(trim($_POST['topico']));
	$topico_descricao		= addslashes(trim($_POST['topico_descricao']));
	$titulo_pergunta		= addslashes(trim($_POST['titulo']));
	$tipo_pergunta			= addslashes(trim($_POST['tipo_pergunta']));
	$dificuldade			= addslashes(trim($_POST['dificuldade']));
	$fonte					= addslashes(trim($_POST['fonte']));
	$ativa					= addslashes(trim($_POST['ativa']));


	try {
		$banco->iniciarTransacao();

		$obj_tipo_pergunta = $sessionFacade->recuperarTipoPergunta($tipo_pergunta);
		$obj_topico        = $sessionFacade->recuperarTopico($topico);
		$obj_curso         = $sessionFacade->recuperarCurso($curso);
		$obj_disciplina    = $sessionFacade->recuperarDisciplina($disciplina);

		if (strlen($topico_descricao)>0){
			$obj_topico = new Topico();
			$obj_topico->setDisciplina($obj_disciplina);
			$obj_topico->setDescricao($topico_descricao);
			$sessionFacade->gravarTopico($obj_topico);
		}

		$perg = new Pergunta();
		$perg->setId($pergunta);
		$perg->setTopico($obj_topico);
		$perg->setTitulo($titulo_pergunta);
		$perg->setTipoPergunta($obj_tipo_pergunta);
		$perg->setDificuldade($dificuldade);
		$perg->setFonte($fonte);
		$perg->setAtiva($ativa);

		$qtde_respostas = 0;

		if (is_object($obj_tipo_pergunta)){
			$qtde_respostas = $obj_tipo_pergunta->getQtdeRespostas();
		}

		for ($i=0; $i<$qtde_respostas; $i++){

			/* Dissertativa */
			if ($tipo_pergunta=="1"){
				$resposta			= addslashes(trim($_POST['dissertativa_resposta_'.$i]));
				$resposta_texto		= addslashes(trim($_POST['dissertativa_resposta_texto_'.$i]));
				$resposta_correta	= "1";
				$resposta_filho		= "";
			}

			/* Multipla-Escolha */
			if ($tipo_pergunta=="2"){
				$resposta			= addslashes(trim($_POST['multipla_escolha_resposta_'.$i]));
				$resposta_texto		= addslashes(trim($_POST['multipla_escolha_resposta_texto_'.$i]));
				$resposta_correta	= addslashes(trim($_POST['multipla_escolha_resposta_correta']));
				if ($resposta_correta==$i){
					$resposta_correta = "1";
				}else{
					$resposta_correta = "0";
				}
				$resposta_filho		= "";
			}
			
			/* Verdadeiro-Falso */
			if ($tipo_pergunta=="3"){
				$resposta			= addslashes(trim($_POST['verdadeiro_falso_resposta_'.$i]));
				$resposta_texto		= addslashes(trim($_POST['verdadeiro_falso_resposta_texto_'.$i]));
				$resposta_correta	= addslashes(trim($_POST['verdadeiro_falso_resposta_correta_'.$i]));
				if (strlen($resposta_correta)>0){
					$resposta_correta = "1";
				}else{
					$resposta_correta = "0";
				}
				$resposta_filho		= "";
			}

			/* Complete */
			if ($tipo_pergunta=="4"){
				$resposta				= addslashes(trim($_POST['complete_resposta_'.$i]));
				$resposta_texto			= addslashes(trim($_POST['complete_resposta_texto_'.$i]));
				$resposta_texto_filho	= addslashes(trim($_POST['complete_resposta_texto_filho_'.$i]));

				#echo $resposta_texto."<br>";
				$resposta_correta		= "1";

				if (strlen($resposta_texto_filho)==0 OR strlen($resposta_texto)==0){
					continue;
				}

				$resposta_filho = new Resposta();
				$resposta_filho->setId($resposta);
				#$resposta_filho->setPergunta($perg);
				$resposta_filho->setRespostaTexto($resposta_texto_filho);
				$resposta_filho->setRespostaCorreta($resposta_correta);
				$resposta_filho->setRespostaFilho($resposta_correta);

				$perg->addResposta($resposta_filho);
			}

			/* Lacuna */
			if ($tipo_pergunta=="5"){
				$resposta				= addslashes(trim($_POST['lacuna_resposta_'.$i]));
				$resposta_texto			= addslashes(trim($_POST['lacuna_resposta_texto_'.$i]));
				$resposta_texto_filho	= addslashes(trim($_POST['lacuna_resposta_texto_filho_'.$i]));
				$resposta_correta		= "1";

				if (strlen($resposta_texto_filho)==0 OR strlen($resposta_texto)==0){
					continue;
				}

				$resposta_filho = new Resposta();
				$resposta_filho->setId($resposta);
				#$resposta_filho->setPergunta($perg);
				$resposta_filho->setRespostaTexto($resposta_texto_filho);
				$resposta_filho->setRespostaCorreta($resposta_correta);
				$resposta_filho->setRespostaFilho($resposta_correta);

				$perg->addResposta($resposta_filho);
			}

			if (strlen($resposta_texto)==0){
				continue;
			}

			$resItem = new Resposta();
			$resItem->setId($resposta);
			#$resItem->setPergunta($perg);
			$resItem->setRespostaTexto($resposta_texto);
			$resItem->setRespostaCorreta($resposta_correta);
			$resItem->setRespostaFilho($resposta_filho);

			$perg->addResposta($resItem);
		}
		$sessionFacade->gravarPergunta($perg);

		/* ---------- UPLOAD DAS IMAGENS ---------- */
		$files = array();
		if (isset($_FILES['imagens'])){
			foreach ($_FILES['imagens'] as $k => $l) {
				foreach ($l as $i => $v) {
					if (!array_key_exists($i, $files)) {
						$files[$i] = array();
					}
					$files[$i][$k] = $v;
				}
			}
		}

		foreach ($files as $file) {
			$handle = new Upload($file);
			if ($handle->uploaded) {

				$obj_imagem = new Imagem();
				$obj_imagem->setPergunta($perg->getId());
				$obj_imagem->setDescricao('');
				
				$path_imagem = "perguntas/imagens/";
				$path_thumb  = "perguntas/imagens/";

				#Resize
				$handle->image_resize            = true;
				$handle->image_ratio_y           = true;
				$handle->image_x                 = 640;
				$handle->file_name_body_add      = "_".$perg->getId();

				$handle->Process($path_imagem);
				if ($handle->processed) {
					$path_imagem = $handle->file_dst_pathname;
					$obj_imagem->setPath($path_imagem);
				} else {
					throw new Exception("Erro no UPLOAD da imagem: ".$handle->error,0);
				}

				#Thumb
				$handle->image_resize            = true;
				$handle->image_ratio_y           = true;
				$handle->image_x                 = 100;
				$handle->image_contrast          = 10;
				$handle->jpeg_quality            = 70;
				$handle->file_name_body_add      = "_".$perg->getId()."_thumb";

				$handle->Process($path_thumb);
				if ($handle->processed) {
					$path_thumb  = $handle->file_dst_pathname;
					$obj_imagem->setThumb($path_thumb);
					#$handle->file_dst_name
				} else {
					throw new Exception("Erro no UPLOAD da imagem Thumb: ".$handle->error,0);
				}

				$sessionFacade->gravarImagem($obj_imagem);
				$handle-> Clean();
			}
		}

		#throw new Exception("teste"); 

		$banco->efetivarTransacao();
		header("Location: ".$PHP_SELF."?pergunta=".$perg->getId()."&msg_codigo=1");
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
$titulo     = "Cadastro de Pergunta";
$sub_titulo = "Cadastro: Pergunta";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.pergunta' => 'cadastro.pergunta.htm'));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['pergunta']) AND strlen(trim($_GET['pergunta']))>0){

	$pergunta = trim($_GET['pergunta']);

	try {
		$perg = $sessionFacade->recuperarPergunta($pergunta); 

		if ( $perg->getId() > 0){
			$pergunta			=	$perg->getId();
			$titulo_pergunta	=	$perg->getTitulo();
			$topico				=	(is_object($perg->getTopico()))?$perg->getTopico()->getId():"";
			$disciplina			=	(is_object($perg->getTopico()))?is_object($perg->getTopico()->getDisciplina())?$perg->getTopico()->getDisciplina()->getId():"":"";
			$curso				=	(is_object($perg->getTopico()))?is_object($perg->getTopico()->getDisciplina())?$perg->getTopico()->getDisciplina()->getCurso()->getId():"":"";
			$tipo_pergunta		=	(is_object($perg->getTipoPergunta()))?$perg->getTipoPergunta()->getId():"";
			$dificuldade		=	$perg->getDificuldade();
			$fonte				=	$perg->getFonte();
			$ativa				=	$perg->getAtiva();
		}else{
			array_push($msg_erro,"Pergunta não encontrado!");
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

if (strlen($pergunta)==0){
	array_push($msg_ok,"Cadastre uma nova Pergunta!");
	array_push($msg_ok,"Preencha com os dados abaixo e clique em 'Gravar'.");
	array_push($msg_ok,"Importante: todos os campos relacionados a pergunta são obrigatórios!");
}

/*    CURSO - DISCIPLINA - TIPO PERGUNTA - TOPICOS         */
try {
	$cursos			= $sessionFacade->recuperarCursoTodosDAO();
	$disciplinas	= $sessionFacade->recuperarDisciplinaTodosDAO();
	$tipo_perguntas = $sessionFacade->recuperarTipoPerguntaTodosDAO();
	$topicos		= $sessionFacade->recuperarTopicoTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

$model->assign_vars(array(		'PERGUNTA'			=>	$pergunta,
								'CURSO'				=>	optionCurso($cursos,$curso),
								'DISCIPLINA'		=>	optionDisciplina($disciplinas,$disciplina),
								'TOPICO'			=>	optionTopico($topicos,$topico),
								'TITULO_PERGUNTA'	=>	$titulo_pergunta,
								'DIFICULDADE_1'		=>	($dificuldade==25)?' CHECKED ':'',
								'DIFICULDADE_2'		=>	($dificuldade==50)?' CHECKED ':'',
								'DIFICULDADE_3'		=>	($dificuldade==75)?' CHECKED ':'',
								'TIPO_PERGUNTA'		=>	optionTipoPergunta($tipo_perguntas,$tipo_pergunta),
								'FONTE'				=>	$fonte,
								'ATIVA'				=>	($ativa!=0 OR  strlen($ativa)==0)?"checked":"",
								'INATIVA'			=>	($ativa==0 AND strlen($ativa)>0 )?"checked":"",
								'BTN_NOME'			=>  (strlen($pergunta)>0)?"Confirmar Alterações":"Gravar"
								));	

/* DISSERTATIVA */
$qtde_item = 1;
$qtde_inicio = 0;
if (is_object($perg)){
	if (is_object($perg->getTipoPergunta())){
		if ($perg->getTipoPergunta()->getId()=="1"){
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$qtde_inicio = $i+1;
				$model->assign_block_vars('dissertativa',array('RESPOSTA'			=>	$perg->getResposta($i)->getId(),
																'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																'I'					=>	$i
																));
			}
		}
	}
}

for ($i=$qtde_inicio; $i<$qtde_item; $i++){
	$model->assign_block_vars('dissertativa',array('RESPOSTA'				=>	'',
													'DISSERTATIVA_RESPOSTA'	=>	'',
													'CLASSE'	=>  ($i%2==0)?"class='odd'":"",
													'I'			=>	$i
													));
}

/* MULTIPLA-ESCOLHA  */
$qtde_item = 10;
$qtde_inicio = 0;
if (is_object($perg)){
	if (is_object($perg->getTipoPergunta())){
		if ($perg->getTipoPergunta()->getId()=="2"){
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$qtde_inicio = $i+1;
				$model->assign_block_vars('multipla_escolha',array('RESPOSTA'			=>	$perg->getResposta($i)->getId(),
																	'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																	'CORRETA'			=>	$perg->getResposta($i)->getRespostaCorreta()=="1"?"CHECKED":"",
																	'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																	'I'					=>	$i+1,
																	'NUMERO'			=>	strtolower(chr($i+65))
																	));
			}
		}
	}
}

for ($i=$qtde_inicio; $i<$qtde_item; $i++){
				$model->assign_block_vars('multipla_escolha',array('RESPOSTA'			=>	'',
																	'RESPOSTA_TEXTO'	=>	'',
																	'CORRETA'			=>	'',
																	'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																	'I'					=>	$i+1,
																	'NUMERO'			=>	strtolower(chr($i+65))
																	));
}

/* VERDADEIRO-FALSO  */
$qtde_item = 10;
$qtde_inicio = 0;
if (is_object($perg)){
	if (is_object($perg->getTipoPergunta())){
		if ($perg->getTipoPergunta()->getId()=="3"){
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$qtde_inicio = $i+1;
				$model->assign_block_vars('verdadeiro_falso',array('RESPOSTA'			=>	$perg->getResposta($i)->getId(),
																	'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																	'CORRETA'			=>	$perg->getResposta($i)->getRespostaCorreta()=="1"?"CHECKED":"",
																	'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																	'I'					=>	$i+1,
																	'NUMERO'			=>	$i+1
																	));
			}
		}
	}
}

for ($i=$qtde_inicio; $i<$qtde_item; $i++){
				$model->assign_block_vars('verdadeiro_falso',array('RESPOSTA'			=>	'',
																	'RESPOSTA_TEXTO'	=>	'',
																	'CORRETA'			=>	'',
																	'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																	'I'					=>	$i+1,
																	'NUMERO'			=>	$i+1
																	));
}
	
/* LACUNA  */
$qtde_item = 10;
$qtde_inicio = 0;
if (is_object($perg)){
	if (is_object($perg->getTipoPergunta())){
		if ($perg->getTipoPergunta()->getId()=="5"){
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$qtde_inicio = $i+1;
				$model->assign_block_vars('lacuna',array('RESPOSTA'				=>	$perg->getResposta($i)->getId(),
														'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
														'RESPOSTA_TEXTO_FILHO'	=>	is_object($perg->getResposta($i)->getRespostaFilho())?$perg->getResposta($i)->getRespostaFilho()->getRespostaTexto():"",
														'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
														'I'						=>	$i+1,
														'NUMERO'				=>	strtolower(chr($i+65))
														));
			}
		}
	}
}

for ($i=$qtde_inicio; $i<$qtde_item; $i++){
				$model->assign_block_vars('lacuna',array('RESPOSTA'				=>	'',
														'RESPOSTA_TEXTO'		=>	'',
														'RESPOSTA_TEXTO_FILHO'	=>	'',
														'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
														'I'						=>	$i+1,
														'NUMERO'				=>	strtolower(chr($i+65))
														));
}


/* COMPLETE  */
$qtde_item = 10;
$qtde_inicio = 0;
if (is_object($perg)){
	if (is_object($perg->getTipoPergunta())){
		if ($perg->getTipoPergunta()->getId()=="4"){
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$qtde_inicio = $i+1;
				$model->assign_block_vars('complete',array(	'RESPOSTA'			=>	$perg->getResposta($i)->getId(),
															'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
															'RESPOSTA_TEXTO_FILHO'	=>	is_object($perg->getResposta($i)->getRespostaFilho())?$perg->getResposta($i)->getRespostaFilho()->getRespostaTexto():"",
															'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
															'I'						=>	$i+1,
															'NUMERO'				=>	$i+1
															));
			}
		}
	}
}

for ($i=$qtde_inicio; $i<$qtde_item; $i++){
				$model->assign_block_vars('complete',array('RESPOSTA'			=>	'',
															'RESPOSTA_TEXTO'	=>	'',
															'CORRETA'			=>	'',
															'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
															'I'					=>	$i+1,
															'NUMERO'			=>	$i+1
															));
}
fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->pparse('cadastro.pergunta');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
