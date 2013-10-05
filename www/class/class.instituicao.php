<?

class Instituicao {

	private $instituicao		= "";
	private $nome				= "";
	private $unificado			= "";
	private $endereco			= "";
	private $numero				= "";
	private $complemento		= "";
	private $bairro				= "";
	private $cidade				= "";
	private $estado				= "";
	private $cep				= "";
	private $pais				= "";

	public $Xinstituicao		= "";
	public $Xnome				= "";
	public $Xunificado			= "";
	public $Xendereco			= "";
	public $Xnumero				= "";
	public $Xcomplemento		= "";
	public $Xbairro				= "";
	public $Xcidade				= "";
	public $Xestado				= "";
	public $Xcep				= "";
	public $Xpais				= "";
	
	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_instituicao	= "";
	public $_login_professor	= "";
	public $_login_aluno		= "";

	function Instituicao(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->instituicao;
	}

	function setId($instituicao){
		$this->instituicao = $instituicao;
	}

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
	}
	
	function getUnificado(){
		return $this->unificado;
	}

	function setUnificado($unificado){
		$this->unificado = $unificado;
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
}
?>