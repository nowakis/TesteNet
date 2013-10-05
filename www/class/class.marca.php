<?
require_once('banco.inc.php');
include_once "funcoes.php";

/** 
 * Manipuladora de Marcas
 * 
 * Características: 
 * - Seta a Marca
 * - Proprietario
 * 
 * @since 01/10/2008
 * @author Fabio Nowaki<fabio.nowaki@gmail.com> 
 */ 

class Marca {

	/** 
	 * ID da Marca
	 * @var integer $marca
	 */
	private $marca				= "";
	private $codigo				= "";
	private $proprietario		= "";
	private $ativo				= "";
	private $observacao			= "";
	
	public $Xmarca				= "";
	public $Xcodigo				= "";
	public $Xproprietario		= "";
	public $Xativo				= "";
	public $Xobservacao			= "";
	
	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_proprietario		= "";

	function Marca(){
		global $_login_proprietario;
		$this->_login_proprietario	= $_login_proprietario;
	}

	function getId(){
		return $this->marca;
	}

	function setId($marca){
		$this->marca = $marca;
	}

	function getProprietario(){
		return $this->proprietario;
	}

	function setProprietario($proprietario){
		$this->proprietario = $proprietario;
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

	function getObservacao(){
		return $this->observacao;
	}

	function setObservacao($observacao){
		$this->observacao = $observacao;
	}

}
?>
