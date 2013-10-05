<?
require_once('banco.inc.php');
include_once "funcoes.php";

class FaturamentoItem {

	private $faturamento_item = "";
	private $faturamento      = "";
	private $especie          = "";
	private $raca             = "";
	private $qtde             = "";
	private $preco            = "";
	
	public $Xfaturamento_item = "";
	public $Xfaturamento      = "";
	public $Xespecie          = "";
	public $Xraca             = "";
	public $Xqtde             = "";
	public $Xpreco            = "";

	private $msg_erro		= array();
	private $msg_ok			= array();
	private $msg			= "";

	function FaturamentoItem(){
	}

	function getId(){
		return $this->faturamento_item;
	}

	function setId($faturamento_item){
		$this->faturamento_item = $faturamento_item;
	}

	function getFaturamento(){
		return $this->faturamento;
	}

	function setFaturamento($faturamento){
		$this->faturamento = $faturamento;
	}

	function getEspecie(){
		return $this->especie;
	}

	function setEspecie($especie){
		$this->especie = $especie;
	}

	function getRaca(){
		return $this->raca;
	}

	function setRaca($raca){
		$this->raca = $raca;
	}

	function getQtde(){
		return $this->qtde;
	}

	function setQtde($qtde){
		$this->qtde = $qtde;
	}

	function getPreco(){
		return $this->preco;
	}

	function setPreco($preco){
		$this->preco = $preco;
	}
}
?>
