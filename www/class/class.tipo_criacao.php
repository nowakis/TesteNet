<?
require_once('banco.inc.php');
include_once "funcoes.php";

class TipoCriacao {

	private $tipo_criacao		= "";
	private $fazenda			= "";
	private $descricao			= "";
	private $codigo				= "";
	private $ativo				= "";
	
	public $Xtipo_criacao		= "";
	public $Xfazenda			= "";
	public $Xdescricao			= "";
	public $Xcodigo				= "";
	public $Xativo				= "";
	
	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_descricao	= "";

	function TipoCriacao(){
		global $_login_descricao;
		global $_login_fazenda;
		$this->_login_descricao	= $_login_descricao;
		$this->_login_fazenda	= $_login_fazenda;
	}

	function getId(){
		return $this->tipo_criacao;
	}

	function setId($tipo_criacao){
		$this->tipo_criacao = $tipo_criacao;
	}

	function getFazenda(){
		return $this->fazenda;
	}

	function setFazenda($fazenda){
		$this->fazenda = $fazenda;
	}

	function getDescricao(){
		return $this->descricao;
	}

	function setDescricao($descricao){
		$this->descricao = $descricao;
	}
	
	function getCodigo(){
		return $this->codigo;
	}

	function setCodigo($codigo){
		$this->codigo = $codigo;
	}
		
	function getAtivo(){
		return $this->ativo;
	}

	function setAtivo($ativo){
		$this->ativo = $ativo;
	}
}
?>
