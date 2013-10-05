<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Pelagem {

	private $pelagem			= "";
	private $fazenda			= "";
	private $especie			= "";
	private $descricao			= "";
	
	public $Xpelagem			= "";
	public $Xfazenda			= "";
	public $Xespecie			= "";
	public $Xdescricao			= "";

	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_proprietario	= "";
	public $_login_fazenda		= "";

	function Pelagem(){
		global $_login_proprietario;
		global $_login_fazenda;
		$this->_login_proprietario	= $_login_proprietario;
		$this->_login_fazenda		= $_login_fazenda;
	}

	function getId(){
		return $this->pelagem;
	}

	function setId($pelagem){
		$this->pelagem = $pelagem;
	}

	function getFazenda(){
		return $this->fazenda;
	}

	function setFazenda($fazenda){
		$this->fazenda = $fazenda;
	}
	
	function getEspecie(){
		return $this->especie;
	}

	function setEspecie($especie){
		$this->especie = $especie;
	}
		
	function getDescricao(){
		return $this->descricao;
	}

	function setDescricao($descricao){
		$this->descricao = $descricao;
	}

}
?>
