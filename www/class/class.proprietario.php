<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Proprietario {

	private $proprietario		= "";
	private $nome				= "";
	private $cpf				= "";
	private $rg					= "";
	private $endereco			= "";
	private $numero				= "";
	private $complemento		= "";
	private $bairro				= "";
	private $cidade				= "";
	private $estado				= "";
	private $cep				= "";
	private $pais				= "";
	private $email				= "";
	private $login				= "";
	private $senha				= "";
	private $observacao			= "";
	private $ativo				= "";
	
	public $Xproprietario		= "";
	public $Xnome				= "";
	public $Xcpf				= "";
	public $Xrg					= "";
	public $Xendereco			= "";
	public $Xnumero				= "";
	public $Xcomplemento		= "";
	public $Xbairro				= "";
	public $Xcidade				= "";
	public $Xestado				= "";
	public $Xcep				= "";
	public $Xpais				= "";
	public $Xemail				= "";
	public $Xlogin				= "";
	public $Xsenha				= "";
	public $Xobservacao			= "";
	public $Xativo				= "";
	
	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_proprietario		= "";

	function Proprietario(){
		global $_login_proprietario;
		$this->_login_proprietario	= $_login_proprietario;
	}

	function getId(){
		return $this->proprietario;
	}

	function setId($proprietario){
		$this->proprietario = $proprietario;
	}

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
	}

	function getCpf(){
		return $this->cpf;
	}

	function setCpf($cpf){
		$this->cpf = $cpf;
	}

	function getRg(){
		return $this->rg;
	}

	function setRg($rg){
		$this->rg = $rg;
	}

	function getEndereco(){
		return $this->endereco;
	}

	function setEndereco($endereco){
		$this->endereco = $endereco;
	}

	function getNumero(){
		return $this->numero;
	}

	function setNumero($numero){
		$this->numero = $numero;
	}

	function getComplemento(){
		return $this->complemento;
	}

	function setComplemento($complemento){
		$this->complemento = $complemento;
	}

	function getBairro(){
		return $this->bairro;
	}

	function setBairro($bairro){
		$this->bairro = $bairro;
	}

	function getCidade(){
		return $this->cidade;
	}

	function setCidade($cidade){
		$this->cidade = $cidade;
	}

	function getEstado(){
		return $this->estado;
	}

	function setEstado($estado){
		$this->estado= $estado;
	}

	function getCep(){
		return $this->cep;
	}

	function setCep($cep){
		$this->cep= $cep;
	}

	function getPais(){
		return $this->pais;
	}

	function setPais($pais){
		$this->pais = $pais;
	}

	function getEmail(){
		return $this->email;
	}

	function setEmail($email){
		$this->email = $email;
	}

	function getLogin(){
		return $this->login;
	}

	function setLogin($login){
		$this->login = $login;
	}

	function getSenha(){
		return $this->senha;
	}

	function setSenha($senha){
		$this->senha = $senha;
	}

	function getObservacao(){
		return $this->observacao;
	}

	function setObservacao($observacao){
		$this->observacao = $observacao;
	}

	function getAtivo(){
		return $this->ativo;
	}

	function setAtivo($ativo){
		$this->ativo = $ativo;
	}
}
?>
