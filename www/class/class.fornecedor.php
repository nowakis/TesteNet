<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Fornecedor extends Proprietario{
	
	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_proprietario		= "";

}
?>
