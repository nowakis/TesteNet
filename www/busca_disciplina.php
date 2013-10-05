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

if (isset($_POST['curso'])){
	if (strlen($_POST['curso'])>0){
		$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO($_POST['curso'],'obrigatorio');
		echo optionDisciplina($disciplinas,"-1");
	}else{
		echo "<option value=''>Selecione o curso</option>";
	}
}


?>
