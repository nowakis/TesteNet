<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Vacina {

	private $vacina				= "";
	private $fazenda			= "";
	private $nome				= "";
	private $descricao			= "";
	private $custo				= "";
	private $ativo				= "";
	
	public $Xvacina				= "";
	public $Xfazenda			= "";
	public $Xnome				= "";
	public $Xcusto				= "";
	public $Xativo				= "";
	
	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_fazenda		= "";

	function Vacina(){
		global $_login_fazenda;
		$this->_login_fazenda	= $_login_fazenda;
	}

	function getId(){
		return $this->vacina;
	}

	function setId($vacina){
		$this->vacina = $vacina;
	}

	function getFazenda(){
		return $this->fazenda;
	}

	function setFazenda($fazenda){
		$this->fazenda = $fazenda;
	}

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
	}
	
	function getDescricao(){
		return $this->descricao;
	}

	function setDescricao($descricao){
		$this->descricao = $descricao;
	}
		
	function getCusto(){
		return $this->custo;
	}

	function setCusto($custo){
		$this->custo = $custo;
	}
		
	function getAtivo(){
		return $this->ativo;
	}

	function setAtivo($ativo){
		$this->ativo = $ativo;
	}
}
?>
