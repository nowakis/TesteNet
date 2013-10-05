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
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$prova				= addslashes(trim($_POST['prova']));
	$qtde_perguntas		= addslashes(trim($_POST['qtde_perguntas']));

	try {
		$banco->iniciarTransacao();

		$prov = $sessionFacade->recuperarProvaRespondida($prova,$_login_aluno); 

		if (!is_object($prov)){
			throw new Exception("Prova não encontrada!");
		}

		$prov->zerarPerguntasRespostas();

		/* Perguntas / Respostas */
		for ($i=0; $i<$qtde_perguntas+2;$i++){
			$prova_pergunta  = addslashes(trim($_POST['prova_pergunta_'.$i]));
			if (strlen($prova_pergunta)>0){
				$perg = $sessionFacade->recuperarProvaPergunta($prova_pergunta); 
				if ( is_object($perg)){
					#echo "<hr>";
					$prov->addPerguntaRespondida(	null,
													$prova_pergunta,
													null );

					switch ($perg->getTipoPergunta()->getId()){

						/* Dissertativa */
						case "1";	$prova_resposta    = addslashes(trim($_POST['dissertativa_resposta_'.$prova_pergunta]));
									$pergunta_resposta = addslashes(trim($_POST['dissertativa_pergunta_resposta_'.$prova_pergunta]));
									#echo "<br>Pergunta: $prova_pergunta -> Resposta: $prova_resposta ($pergunta_resposta)";
									$prov->addResposta(	null,
														$prova_pergunta,
														$pergunta_resposta,
														$prova_resposta,
														null,
														null
														 );
									break;

						/* Multipla Escolha */
						case "2";	$pergunta_resposta  = addslashes(trim($_POST['multipla_escolha_resposta_correta_'.$prova_pergunta]));
									#echo "<br>Pergunta: $prova_pergunta -> Resposta: $pergunta_resposta";
//									$prov->addResposta(null,$prova_pergunta, $pergunta_resposta, $pergunta_resposta);

									$prov->addResposta(	null,
														$prova_pergunta,
														$pergunta_resposta,
														$pergunta_resposta,
														null,
														null
														 );
									#echo "<br>Pergunta: $prova_pergunta -> Resposta: ($pergunta_resposta) \\";
									break;

						/* Verdadeiro ou Falso */
						case "3";	$pergunta_resposta = $_POST['verdadeiro_falso_resposta_correta_'.$prova_pergunta];
									$todas_respostas   = $_POST['verdadeiro_falso_resposta_correta_todas_'.$prova_pergunta];
									$todas_tmp         = array();

									#print_r($pergunta_resposta);
									#$pergunta_resposta = array_merge($todas_respostas, $pergunta_resposta);
									#print_r($todas_respostas);
									
									if (is_array($pergunta_resposta)){
										$todas_tmp = array_unique($todas_respostas + $pergunta_resposta);
									}

									/* SETA AS RESPOSTAS DO ALUNO */
									while (list ($key,$val) = @each ($todas_tmp)) {

										$reposta_assinalada = null;
										if (in_array($val,$pergunta_resposta)){
											$reposta_assinalada = $val;
										}

										//$prov->addResposta('',$prova_pergunta, $val,$val);
										$prov->addResposta(	null,
															$prova_pergunta,
															$val,
															$reposta_assinalada,
															null,
															null
															 );
										#echo "<br>Pergunta: $prova_pergunta -> Resposta: ($reposta_assinalada) \\";
									}
									#os demais sao respostas falsas
									break;

						/* Complete */
						case "4";	$pergunta_resposta = $_POST['complete_resposta_'.$prova_pergunta];
									while (list ($key,$val) = @each ($pergunta_resposta)) {
										if (strlen($val)>0){
											$reposta_filho = $_POST['complete_resposta_texto_filho_'.$val];

											$prov->addResposta(	null,
																$prova_pergunta,
																$val,
																$reposta_filho,
																null,
																null
																 );
/*
											echo nl2br("<br>(	null,
																$prova_pergunta,
																$val,
																$reposta_filho,
																null,
																null)");
*/
										}
									}
									break;

						/* Lacuna */

						case "5";	$pergunta_resposta = $_POST['lacuna_resposta_texto_'.$prova_pergunta];
									while (list ($key,$val) = @each ($pergunta_resposta)) {
										if (strlen($val)>0){
											$resposta_numero    = $_POST['lacuna_resposta_correta_'.$val];

											$reposta_assinalada = $_POST['lacuna_resposta_numero_'.$prova_pergunta.'_'.$resposta_numero];
											$reposta_filho      = $_POST['lacuna_resposta_filho_'.$val];
											$prov->addResposta(	null,
																$prova_pergunta,
																$reposta_assinalada,
																$reposta_filho,
																null,
																null
																 );
/*
											echo nl2br("<br> $resposta_numero
												(	null,
																$prova_pergunta,
																$reposta_assinalada,
																$reposta_filho,
																null,
																null
																 );

											");
*/
										}
									}
									break;
					}
				}else{
					throw new Exception("Pergunta nao encontrada!");
				}
			}
		}


		$sessionFacade->gravaDadosProvaRespondida($prov);
		#echo "<hr>";
		#print_r($prov);
		#echo "<hr>";
		#throw new Exception("Teste Correção");
		$sessionFacade->provaCorrigir($prov);
		#print_r($prov);
		#echo "<hr>";
		#throw new Exception("Teste Correção");
		$sessionFacade->gravaDadosProvaPerguntaRespondida($prov);
		#print_r($prov);
		#echo "<hr>";
		#echo "Nota: ->".$prov->Xnota."<-";
		#throw new Exception("Teste Correção");
		$sessionFacade->gravaProvaCorrigir($prov);
		#echo "Nota: ->".$prov->Xnota."<-";

		#throw new Exception("Teste Correção");

		#$banco->desfazerTransacao();
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: prova.finaliza.php?prova=".$prov->getProva()->getId()."&msg_codigo=1");
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
		$prov_resp = $sessionFacade->recuperarProvaRespondida($prova,$_login_aluno); 
		$prov      = $prov_resp->getProva();

		if ( is_object($prov)){
			if (strlen($prov->getProvaNaoLiberada())>0){
				$erro_tmp = $prov->getProvaNaoLiberada();
				unset($prov);
				echo "<script language='javascript'>alert('$erro_tmp'); history.go(-1);</script>";
				exit;
				#throw new Exception($erro_tmp);
			}

			if ($prov_resp->getProvaRespondida()) {
				unset($prov_resp);
				echo "<script language='javascript'> window.location = 'prova.finaliza.php?prova=".$prova."';</script>";
				exit;
				#throw new Exception($erro_tmp);
			}

			$prov_resp->setDataInicio(date('d/m/Y H:i:s'));
			$sessionFacade->gravaDadosProvaRespondidaDataInicio($prov_resp);

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

if (is_object($prov)){
	for ($j=0;$j<$prov->getQtdePerguntas();$j++){

		$perg = $prov->getPergunta($j);

		$model->assign_block_vars('pergunta', array('I'					=>	$j+1,
													'PROVA_PERGUNTA'	=>	$perg->getId(),
													'TITULO_PERGUNTA'	=>	$perg->getTitulo(),
													'TOPICO'			=>	$perg->getTopico()->getDescricao(),
													'TIPO'				=>	$perg->getTipoPergunta()->getDescricao(),
													'DIFICULDADE'		=>	$perg->getDificuldade('texto'),
													'PESO'				=>	$perg->getPeso(),
													'CLASSE'			=>	$j%2==0?"class='odd'":""
													));

		/* DISSERTATIVA */
		if ($perg->getTipoPergunta()->getId()=="1"){
			$model->assign_block_vars('pergunta.dissertativa',array('PROVA_RESPOSTA'	=>	$perg->getResposta(0)->getId(),
																	'PROVA_PERGUNTA'	=>	$perg->getId()
																	));
		}

		/* MULTIPLA-ESCOLHA  */
		if ($perg->getTipoPergunta()->getId()=="2"){
			$model->assign_block_vars('pergunta.multipla_escolha',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$model->assign_block_vars('pergunta.multipla_escolha.item',array('PROVA_PERGUNTA'	=>	$perg->getId(),
																				'PROVA_RESPOSTA'	=>	$perg->getResposta($i)->getId(),
																				'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																				'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																				'I'					=>	$i,
																				'NUMERO'			=>	strtolower(chr($i+65))
																				));
			}
		}

		/* VERDADEIRO-FALSO  */
		if ($perg->getTipoPergunta()->getId()=="3"){
			$model->assign_block_vars('pergunta.verdadeiro_falso',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$model->assign_block_vars('pergunta.verdadeiro_falso.item',array('PROVA_PERGUNTA'	=>	$perg->getId(),
																				'PROVA_RESPOSTA'	=>	$perg->getResposta($i)->getId(),
																				'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																				'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																				'I'					=>	$i,
																				'NUMERO'			=>	$i+1
																				));
			}
		}

		/* COMPLETE  */
		if ($perg->getTipoPergunta()->getId()=="4"){
			$model->assign_block_vars('pergunta.complete',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){
				$model->assign_block_vars('pergunta.complete.item',array('PROVA_PERGUNTA'	=>	$perg->getId(),
																		'PROVA_RESPOSTA'	=>	$perg->getResposta($i)->getId(),
																		'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																		'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																		'I'					=>	$i+1,
																		'NUMERO'			=>	$i+1
																		));
			}
		}
			
		/* LACUNA  */
		if ($perg->getTipoPergunta()->getId()=="5"){
			$model->assign_block_vars('pergunta.lacuna',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){

				$montaCombo = "<select name='lacuna_resposta_correta_".$perg->getResposta($i)->getId()."' style='width:50px'>";
				$montaCombo .= "<option value=''></option>";
				for ($k=0; $k<$perg->getQtdeResposta(); $k++){
					$montaCombo .= "<option value='".strtolower(chr($k+65))."'>".strtolower(chr($k+65))."</option>";
				}
				$montaCombo .= "</select>";

				$model->assign_block_vars('pergunta.lacuna.item',array('PROVA_PERGUNTA'			=>	$perg->getId(),
																		'PROVA_RESPOSTA'		=>	$perg->getResposta($i)->getId(),
																		'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
																		'RESPOSTA_TEXTO_FILHO'	=>	is_object($perg->getRespostaFilhoOrdem($i)->getRespostaFilho())?$perg->getRespostaFilhoOrdem($i)->getRespostaFilho()->getRespostaTexto():"",
																		'RESPOSTA_FILHO'		=>	is_object($perg->getRespostaFilhoOrdem($i)->getRespostaFilho())?$perg->getRespostaFilhoOrdem($i)->getRespostaFilho()->getId():"",
																		"PROVA_RESPOSTA_COMBO"	=> $montaCombo,
																		'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
																		'I'						=>	$i,
																		'NUMERO'				=>	strtolower(chr($i+65))
																		));
			}
		}


	}
	$model->assign_vars(array( 'QTDE_PERGUNTAS' => $prov->getQtdePerguntas()));
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