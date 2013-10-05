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

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Cadastro de Lotes";
$sub_titulo="Cadastro: Lote";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array('cadastro.lote' => 'cadastro.lote.htm'));

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

	
##############################################################################
##############                       INICIO                   	##############
##############################################################################	
	
	$msg_erro="";
	$msg="";

	$txtlote			=	"";
	$txtnome			=	"";	
	$txtfazenda			=	"";		
	$txtinicio			=	"";
	$txtfim				=	"";
	$txtcategoria		=	"";
	$txtativo			=	"";	
	$txtobservacao		=	"";			
	
##############################################################################
##############                       EXCLUIR                   	##############
##############################################################################	

	if (isset($_GET['excluir']) && strlen($_GET['excluir'])>0){
		$codi=$_GET['excluir'];
		$query = "DELETE FROM tbl_lote WHERE lote=$codi AND fazenda=$login_fazenda";		
		$rSet = $db->Query($query);
	}
	
##############################################################################
##############                       ALTERAR                   	##############
##############################################################################	

	if (isset($_GET['alterar']) && strlen($_GET['alterar'])>0){
		$codi=$_GET['alterar'];
		$query = "SELECT * FROM tbl_lote WHERE lote=$codi";		
		$rSet = $db->Query($query);
	    $linha = $db->FetchArray($rSet);
		
		$txtlote			=	$linha['lote'];
		$txtnome			=	$linha['nome'];	
		$txtfazenda			=	$linha['fazenda'];		
		$txtinicio			=	$linha['data_inicio'];
		$txtfim				=	$linha['data_fim'];
		$txtcategoria		=	$linha['categoria'];
		$txtativo			=	$linha['ativo'];	
		$txtobservacao		=	$linha['obs'];				
	}
	
	
##############################################################################
##############                     CADASTRAR                   	##############
##############################################################################	

if (isset($_POST['Cadastrar_x'])){	

	$txtlote			=	addslashes(trim($_POST['txtlote']));

	$txtnome			=	addslashes(trim($_POST['txtnome']));
	$msg_erro.=(strlen($txtnome)==0)?'<br>Informe o nome do lote!':'';	

	$txtfazenda			=	$login_fazenda;		
	
	if (strlen(trim($_POST['txtinicio']))>0){
		$txtinicio			=	"'".@converte_data(addslashes(trim($_POST['txtinicio'])))."'";
	//	$msg_erro.=(strlen($txtfim)==0)?'<br>Informe a data de início do lote!':'';	
	}
	else {
		$txtinicio="NULL";
	}
		
	if (strlen(trim($_POST['txtfim']))>0){
		$txtfim			=	"'".@converte_data(addslashes(trim($_POST['txtfim'])))."'";
	//	$msg_erro.=(strlen($txtfim)==0)?'<br>Informe a data de início do lote!':'';	
	}
	else {
		$txtfim="NULL";
	}
	
	$txtcategoria			=	addslashes(trim($_POST['txtcategoria']));
	$msg_erro.=(strlen($txtcategoria)==0)?'<br>Informe a categoria do lote!':'';	

	$txtativo			=	addslashes(trim($_POST['txtativo']));
	$msg_erro.=(strlen($txtativo)==0)?'<br>Informe se o lote está ativo ou inativo!':'';	

	$txtobservacao			=	addslashes(trim($_POST['txtobservacao']));
//	$msg_erro.=(strlen($txtobservacao)==0)?'<br>Informe as observações do lote!':'';	

	if (strlen($msg_erro)==0){
			if (strlen($txtlote)>0){
				$query = "UPDATE tbl_lote SET nome='$txtnome',
												fazenda=$txtfazenda,
												data_inicio='$txtinicio',
												data_fim='$txtfim',
												categoria='$txtcategoria',
												ativo='$txtativo',
												observacao='$txtobservacao'
							WHERE lote=$txtlote";
				$rSet = $db->Query($query);
				$msg = "<br><div id='msg_sucesso'>Informações do Lote alterado com sucesso!</div>";			
			}
			else{
				$query = "INSERT INTO tbl_lote 			  (nome,fazenda,data_inicio,data_fim,categoria,ativo,obs)
							values 	('$txtnome',$login_fazenda,$txtinicio,$txtfim,'$txtcategoria','$txtativo','$txtobservacao')";
				$rSet = $db->Query($query);
				$msg = "<br><div id='msg_sucesso'>Lote cadastrado com sucesso!</div>";			
			
			}
			$msg_erro .= $db->MyError();

			$txtlote			=	"";
			$txtnome			=	"";	
			$txtfazenda			=	"";		
			$txtinicio			=	"";
			$txtfim				=	"";
			$txtcategoria		=	"";
			$txtativo			=	"";	
			$txtobservacao		=	"";		
	
	}
	if (strlen($msg_erro)>0){
			$msg = "<br><div id='msg_erro'><b>Ocorreu o seguinte erro:</b><br> $msg_erro</div>";			
	}
}

if (isset($msg) && strlen($msg)>0){
	$model->assign_vars(array('MSG' => $msg));	
}
	
	$model->assign_vars(array(		'LOTE'				=>	$txtlote,
									'NOME'				=>	$txtnome,
									'INICIO'			=>	$txtinicio,
									'FIM'				=>	$txtfim,
									'CATEGORIA'			=>	$txtcategoria,
									'ATIVO'				=>	$txtativo,
									'OBSERVACAO'		=>	$txtobservacao
									));	

##############################################################################
##############            LISTAGEM DOS LOTES                   	##############
##############################################################################										



	$query = "SELECT 	tbl_lote.lote AS lote_lote,
						tbl_lote.nome AS lote_nome,
						DATE_FORMAT(tbl_lote.data_inicio , '%d/%m/%Y') AS lote_data_inicio,						
						DATE_FORMAT(tbl_lote.data_fim , '%d/%m/%Y') AS lote_data_fim,						
						tbl_lote.categoria as lote_categoria,
						tbl_lote.ativo AS lote_ativo,
						tbl_lote.obs AS lote_obs,
						count(tbl_animal_lote.animal) AS contador
				FROM	tbl_lote
				LEFT JOIN	tbl_animal_lote ON tbl_animal_lote.lote = tbl_lote.lote
				WHERE	fazenda=$login_fazenda
				GROUP BY	tbl_lote.lote,
							tbl_lote.nome,
							tbl_lote.data_inicio,
							tbl_lote.data_fim,
							tbl_lote.categoria,
							tbl_lote.ativo,
							tbl_lote.obs
				";
				
							
	$rSet = $db->Query($query);
	$count=0;	
	while ($linha = $db->FetchArray($rSet)){
		  $model->assign_block_vars('lote', array( 'LOTE'			=>	$linha['lote_lote'],
													'NOME'		=>	$linha['lote_nome'],		  
													'INICIO'		=>	($linha['lote_data_inicio']=="00/00/0000")?"-":$linha['lote_data_inicio'],		  																
													'FIM'			=>	($linha['lote_data_fim']=="00/00/0000")?"-":$linha['lote_data_fim'],		  																
													'CATEGORIA'		=>	$linha['lote_categoria'],
													'ATIVO'			=>	$linha['lote_ativo']==1?"ATIVO":"INATIVO",
													'OBSERVACAO'	=>	$linha['lote_obs'],						
													'ANIMAIS'		=>	($linha['contador']==0)?"":$linha['contador'],																										
													'CLASSE'		=>  ($count%2==0)?"classe_1":"classe_2",
													'COR'			=>  ($count++%2==0)?"#FFFFFF":"#F5F8FB"		
													));			  
	}
	if ($count==0){
		$model->assign_block_vars('naoecnontrado', array('MSG'	=>	'Nenhum lote cadastrado!'));
	}	
		  									
				
$model->pparse('cadastro.lote');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
