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
$titulo="Detalhes do Animal";
$sub_titulo="Detalhes do Animal";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array('cadastro.animal.ver' => 'cadastro.animal.ver.htm'));

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	


	
	$msg_erro="";
	$msg="";

if (isset($_GET['animal']) && strlen($_GET['animal'])>0){	

	$animal		=	$_GET['animal'];
	$txtfazenda			=	1;	

	$query = "SELECT * FROM tbl_animal WHERE codigo = '$codigo_animal' AND fazenda = $txtfazenda LIMIT 1";
	$rSet = $db->Query($query);
}

if (isset($msg) && strlen($msg)>0){
	$model->assign_vars(array('MSG' => $msg));	
}

	$row = $db->FetchArray($rSet);
	$model->assign_vars(array(		'txtdataentrada'	=>	$txtentrada,
									'txtnumero'			=>	$txtnumero,
									'txtnascimento'		=>	$txtnascimento,
									'txtapelido'		=>	$txtapelido,
									'txtpai'			=>	$txtpai,
									'txtmae'			=>	$txtmae,
									'txtsexo'			=>	($txtsexo=="m")?'checked="checked"':'',
									'txtcrias'			=>	$txtcriasOption,
									'txtraca'			=>	$txtracaOption,									
									'txttipo'			=>	$txttipoOption,
									'txtproprietario'	=>	$txtproprietarioOption,
									'txtcompra'			=>	$txtcompra,
									'txtpvenda'			=>	$txtpvenda,
									'txtdatapvenda'		=>	$txtdatapvenda,									
									'txtobs'			=>	$txtobs							
									));	
				
$model->pparse('cadastro.animal.ver');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
