<?
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/html; charset=iso-8859-1",true);

require_once "../funcoes.php";
require_once "../class/class.SessionFacade.php";
require_once "../banco.con.php";

echo "Envio de Email Pesquisa \n\n";

echo "Inicio: ".date("d/m/Y H:i");

$pesquisa = new Divulgacao();
$pesquisa->setQtdeEmail(30);
$resultado = $pesquisa->divulgacaoPesquisa($sessionFacade);

echo "\n";

echo "Término: ".date("d/m/Y H:i");

?>