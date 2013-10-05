<?

require_once('banco.config.php');
require_once('class/class.banco.php');

global $banco;
	try {
		$banco = new BancodeDados();
		$banco->conecta();
		$sessionFacade = new SessionFacade($banco); 
	} catch(Exception $e) { 
		header("location: /testenet/");
		exit;
	}
?>