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

header("Content-Type: text/html; charset=ISO-8859-1",true);

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";

if (isset($_POST['disciplina'])){
	if (strlen($_POST['disciplina'])>0){
		$topicos = $sessionFacade->recuperarTopicoTodosDAO($_POST['disciplina']);
		if (count($topicos)>0){
			echo optionTopico($topicos,"-1");
		}else{
			echo "<option value=''>Nenhum cadastrado</option>";
		}
	}else{
		echo "<option value=''>Selecione a disciplina</option>";
	}
}


?>
