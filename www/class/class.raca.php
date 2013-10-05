<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Raca {

	private $raca				= "";
	private $fazenda			= "";
	private $codigo				= "";
	private $nome				= "";
	private $descricao			= "";
	private $data				= "";
	private $ativo				= "";
	private $observacao			= "";
	
	public $Xraca				= "";
	public $Xfazenda			= "";
	public $Xcodigo				= "";
	public $Xnome				= "";
	public $Xdescricao			= "";
	public $Xdata				= "";
	public $Xativo				= "";
	public $Xobservacao			= "";
	
	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_proprietario	= "";
	public $_login_fazenda		= "";

	function Raca(){
		global $_login_proprietario;
		global $_login_fazenda;
		$this->_login_proprietario	= $_login_proprietario;
		$this->_login_fazenda		= $_login_fazenda;

		$this->setFazenda($_login_fazenda);
	}

	function getId(){
		return $this->raca;
	}

	function setId($raca){
		$this->raca = $raca;
	}

	function getFazenda(){
		return $this->fazenda;
	}

	function setFazenda($fazenda){
		$this->fazenda = $fazenda;
	}
	
	function getCodigo(){
		return $this->codigo;
	}

	function setCodigo($codigo){
		$this->codigo = $codigo;
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

	function getData(){
		return $this->data;
	}

	function setData($data){
		$this->data = $data;
	}

	function getAtivo(){
		return $this->ativo;
	}

	function setAtivo($ativo){
		$this->ativo = $ativo;
	}

	function getObservacao(){
		return $this->observacao;
	}

	function setObservacao($observacao){
		$this->observacao = $observacao;
	}

}
?>
