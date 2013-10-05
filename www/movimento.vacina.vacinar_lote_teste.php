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

$lista_ordem="";
$desc=" ASC";

$msg_erro="";
$msg="";

if (@$_GET['method']=='getXML'){
	header("Content-Type: text/xml",true);
	$query="SELECT vacina,nome
			FROM tbl_vacina";
	$rSet = $db->Query($query);
	
	$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
	$xml .= '<categories>';
	while ($linha = $db->FetchArray($rSet)){
		$xml .= '<category>';
		$xml .= '<id>'. $linha['vacina'] .'</id>';
		$xml .= '<fname>'. $linha['nome'] .'</fname>';
		$xml .= '</category>';
	}
	$xml .= '</categories>';
	echo $xml;
	exit;
}
		
##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

header("Content-Type: text/html;  charset=ISO-8859-1",true);

$layout="movimento";
$titulo="Vacinas";
$sub_titulo="Aplicar Vacina a Lotes";

include "cabecalho.php";

	
##############################################################################
##############                       AÇÕES                   	##############
##############################################################################	

if (isset($_POST['txtLote']) && strlen($_POST['txtLote'])>0){

	$txtLote	=trim($_POST['txtLote']);
	$txtVacinas	=trim($_POST['txtVacinas']);
	$txtobs		=trim($_POST['txtobs']);			
	$msg_erro	.=	(strlen(trim($_POST['txtdata']))!=10)?'<br>Data inválida!':'';
	$txtdata	=	@converte_data(trim($_POST['txtdata']));

	$rSet = $db->Query("BEGIN");								
	
	if (strlen($msg_erro)==0){
		$campo="";
		$valor_campo="";$cont=0;
			$query = "INSERT tbl_aplicacao_vacina (vacina,animal,data) VALUES ($txtVacina,$valor_campo,'$txtData')";
			$rSet = $db->Query($query);
			$msg_erro .= $db->MyError();
			if (strlen($msg_erro)>0){
				break;
				
			}

	}
	if (strlen($msg_erro)==0){
		$rSet = $db->Query("COMMIT");								
		$msg.="Vacinas aplicadas com sucesso!";
	}
	else {
		$rSet = $db->Query("ROLLBACK");								
	}

}
	
##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array('movimento.vacina.vacinar_lote_teste' => 'movimento.vacina.vacinar_lote_teste.htm'));



##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	

############### MSG

	if (strlen($msg_erro)>0)
		$model->assign_vars(array('MSG' => "<br><div id='msg_ok'><img src='imagens/forbidden.gif' align='absmiddle' style='padding-right:5px'/> $msg_erro</div>"));	

	if (strlen($msg)>0)
		$model->assign_vars(array('MSG' => $msg));			

############### MSG	


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	


############### CARREGA LOTES	- INICIO
	$query = "SELECT lote,nome,data_inicio,data_fim,obs,categoria
				FROM tbl_lote
				WHERE fazenda=$login_fazenda
				AND ativo=1
				AND (data_fim < NOW() OR data_fim ='0000-00-00')
				AND (data_inicio > NOW() OR data_inicio ='0000-00-00')
				";	
	$query = "SELECT lote,nome,data_inicio,data_fim,obs,categoria
				FROM tbl_lote
				WHERE fazenda=$login_fazenda
				AND ativo=1
				";	
			 			 
	$rSet = $db->Query($query);
	
	if (isset($msg) && strlen($msg)>0){
		$model->assign_vars(array('MSG' => "<br><div id='msg_ok'><img src='imagens/warning.gif' align='absmiddle' style='padding-right:5px'/>$msg</div>"));	
	}
	
	while ($linha = $db->FetchArray($rSet)){
		  $model->assign_block_vars('lote', array('LOTE'			=>	$linha['lote'],
													'NOME'		=>	$linha['nome'],		  
													'INICIO'		=>	$linha['data_inicio'],
													'FIM'			=>	$linha['data_fim'],
													'CATEGORIA'		=>	$linha['categoria'],
													'OBS'			=>	$linha['obs']
																));			  
	}
############### CARREGA LOTES - FIM
	
############### CARREGA VACINAS - INICIO
	$query = "SELECT vacina,nome
			 FROM tbl_vacina
				WHERE fazenda=$login_fazenda
				AND ativo=1";	
	$rSet = $db->Query($query);
	$vacinas="";
	while ($linha = $db->FetchArray($rSet)){
		  $model->assign_block_vars('vacina', array('VACINA'		=>	$linha['vacina'],
													'NOME'			=>	$linha['nome']
													));
	}
############### CARREGA VACINAS	- FIM


				
$model->pparse('movimento.vacina.vacinar_lote_teste');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
