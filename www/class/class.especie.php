<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Especie {

	private $especie			= "";
	private $fazenda			= "";
	private $nome				= "";
	private $descricao			= "";
	
	public $Xespecie			= "";
	public $Xfazenda			= "";
	public $Xnome				= "";
	public $Xdescricao			= "";

	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_proprietario	= "";
	public $_login_fazenda		= "";

	function Especie(){
		global $_login_proprietario;
		global $_login_fazenda;
		$this->_login_proprietario	= $_login_proprietario;
		$this->_login_fazenda		= $_login_fazenda;
	}

	function getId(){
		return $this->especie;
	}

	function setId($especie){
		$this->especie = $especie;
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

}
?>
