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

/*
$teste = "o pedreiro trrabalha bem";
$teste2 = "o pedereiro tabalha bem";
echo "<br>Teste1= ".get_lcs(str_limpo($teste),str_limpo($teste2));
echo "<br>Teste2= "._similar(str_limpo($teste),str_limpo($teste2));
exit;
*/

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
##############               CORREÇÃO DA PROVA                  ##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$prova				= addslashes(trim($_POST['prova']));

	try {
		$banco->iniciarTransacao();

		$prov_correcao = $sessionFacade->recuperarProvaCorrecao($prova);
		$prov          = $prov_correcao->getProva();

		if (!is_object($prov)){
			throw new Exception("Prova não encontrada!");
		}

		$lista_prova_pergunta  = $_POST['prova_pergunta'];

		/* Perguntas / Respostas */
		for ($i=0; $i<count($lista_prova_pergunta);$i++){

			$prova_pergunta = $lista_prova_pergunta[$i];

			if (strlen($prova_pergunta)==0){
				continue;
			}

			$prova_aluno    = $_POST['prova_aluno_'.$prova_pergunta];

			#print_r($prova_aluno);

			/* Notas Pergunta*/
			for ($j=0; $j<count($prova_aluno);$j++){

				$aluno = $prova_aluno[$j];

				#print_r($aluno);

				if (strlen($aluno)==0){
					continue;
				}

				$pergunta_nota  = addslashes(trim($_POST['pergunta_nota_'.$prova_pergunta.'_'.$aluno]));
				
				if (strlen($prova_aluno)==0){
					$pergunta_nota = "0";
				}

				$sessionFacade->provaCorrecaoGravarPerguntaNota($prov_correcao,$prova_pergunta,$aluno,$pergunta_nota); 
			}
		}

		#throw new Exception("Teste Correção");
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: prova.correcao.php?prova=".$prov->getId()."&msg_codigo=1");
		exit;

	} catch(Exception $e) { 
		$banco->desfazerTransacao();
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "prova";
$titulo     = "Prova";
$sub_titulo = "Prova";

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

if (isset($_GET['prova']) AND strlen($_GET['prova'])>0){
	$prova = trim($_GET['prova']);
}

if (isset($_POST['prova']) AND strlen($_POST['prova'])>0){
	$prova = trim($_POST['prova']);
}

if (strlen(trim($prova))>0){

	try {
		$prov_correcao = $sessionFacade->recuperarProvaCorrecao($prova);
		$prov          = $prov_correcao->getProva();

		if ( is_object($prov)){
			$prova				= $prov->getId();
			$titulo_prova		= $prov->getTitulo();
			$disciplina			= $prov->getDisciplina()->getNome();
			$curso				= $prov->getDisciplina()->getCurso()->getNome();
			$professor			= $prov->getProfessor()->getNome();
			#$numero_perguntas	= $prov->getNumeroPerguntas();
			$data				= $prov->getData();
			$data_inicio		= $prov->getDataInicio();
			$data_termino		= $prov->getDataTermino();
			#$dificuldade		= $prov->getDificuldade();
		}else{
			throw new Exception('Prova não encontrada!');
			#array_push($msg_erro,"Prova não encontrada!");
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

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE'		=>	$dificuldade,
							'DISCIPLINA'		=>	$disciplina,
							'CURSO'				=>	$curso,
							'PROFESSOR'			=>	$professor
));	

$qtde_perguntas = 0;

if (is_object($prov)){

	for ($i=0; $i< $prov->getQtdePerguntas(); $i++ ){

		$perg = $prov->getPergunta($i);

		$pergunta = $prov_correcao->getPerguntaCorrecao($perg);

		if ($pergunta === false){
			continue;
		}

		$qtde_perguntas++;

		$model->assign_block_vars('pergunta', array('I'					=>	$i+1,
													'PROVA_PERGUNTA'	=>	$perg->getId(),
													'TITULO_PERGUNTA'	=>	$perg->getTitulo(),
													'TOPICO'			=>	$perg->getTopico()->getDescricao(),
													'TIPO'				=>	$perg->getTipoPergunta()->getDescricao(),
													'DIFICULDADE'		=>	$perg->getDificuldade('texto'),
													'PESO'				=>	$perg->getPeso(),
													'CLASSE'			=>	$i%2==0?"class='odd'":""
													));

		/* DISSERTATIVA */
		if ($perg->getTipoPergunta()->getId()=="1"){
			$model->assign_block_vars('pergunta.resposta',array('RESPOSTA_TEXTO'	=>	$perg->getResposta(0)->getRespostaTexto() ));
		}

		for ($j=0;$j<$prov_correcao->getQtdeProvaRespondida();$j++) {

			$prov_resp = $prov_correcao->getProvaRespondida($j);

			/* Só faz a correção para alunos que terminaram a prova */
			if (strlen($prov_resp->getDataTermino())==0){
				continue;
			}

			$resposta  = $prov_resp->getRespostasPerguntaItem( $perg->getId(), $perg->getResposta(0)->getId() );

			/* Se já tiver nota, nao corrigi de novo */
			if (strlen($resposta[5])>0){
				continue;
			}

			$porcetangem_acerto = _similar(str_limpo( $perg->getResposta(0)->getRespostaTexto() ),str_limpo( $resposta[3] ));

			/* DISSERTATIVA */
			if ($perg->getTipoPergunta()->getId()=="1"){
				$model->assign_block_vars('pergunta.dissertativa',array('J'					=>	$j,
																		'ALUNO'				=>	$prov_resp->getAluno()->getId(),
																		'RA'				=>	$prov_resp->getAluno()->getRa(),
																		'NOME_ALUNO'		=>	$prov_resp->getAluno()->getNome(),
																		'PROVA_PERGUNTA'	=>	$perg->getId(),
																		'PROVA_RESPOSTA'	=>	$resposta[3],
																		'PORCENTAGEM_ACERTO'=>	round($porcetangem_acerto),
																		'PESO'				=>	$perg->getPeso()
																		));
			}
		}
		break;
	}
}

if ($qtde_perguntas == 0){
	$model->assign_block_vars('sem_perguntas', array('PROVA' => $prov->getId()));
}else{
	$model->assign_block_vars('com_perguntas', array('PROVA' => $prov->getId()));
}



fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	
/*
$array1 = array("color" => "red", "note" => "pink",  "pano" => "verde", "balde" => "azul");
$array2 = array( "pc" => "prata", "color" => "roxo", "pano" => "preto", "balde" => "amarelo");
$result = array_merge($array1, $array2);
print_r($result);
*/

/*
Array ( 
[color] => green 
[0] => 2 
[1] => 4 
[2] => a 
[3] => b 
[shape] => trapezoid 
[4] => 4 
) 
*/
include "rodape.php";


?>