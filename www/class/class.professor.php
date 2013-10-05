<?

class Professor {

	private $professor			= "";
	private $instituicao		= "";
	private $nome				= "";
	private $login				= "";
	private $senha				= "";
	private $email				= "";
	private $ativo				= "";
	private $area				= "";
	private $endereco			= "";
	private $numero				= "";
	private $complemento		= "";
	private $bairro				= "";
	private $cidade				= "";
	private $estado				= "";
	private $cep				= "";
	private $pais				= "";

	public $Xprofessor			= "";
	public $Xinstituicao		= "";
	public $Xnome				= "";
	public $Xlogin				= "";
	public $Xsenha				= "";
	public $Xemail				= "";
	public $Xativo				= "";
	public $Xarea				= "";
	public $Xendereco			= "";
	public $Xnumero				= "";
	public $Xcomplemento		= "";
	public $Xbairro				= "";
	public $Xcidade				= "";
	public $Xestado				= "";
	public $Xcep				= "";
	public $Xpais				= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Professor(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->professor;
	}

	function setId($professor){
		$this->professor = $professor;
	}

	function getInstituicao(){
		return $this->instituicao;
	}

	function setInstituicao($instituicao){
		$this->instituicao = $instituicao;
	}

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
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

	function getEmail(){
		return $this->email;
	}

	function setEmail($email){
		$this->email = $email;
	}
	
	function getAtivo(){
		return $this->ativo;
	}

	function setAtivo($ativo){
		$this->ativo = $ativo;
	}
	
	function getNivelEnsino(){
		return $this->nivel_ensino;
	}

	function SetNivelEnsino($nivel_ensino){
		$this->nivel_ensino = $nivel_ensino;
	}

	function getAreaAtuacao(){
		return $this->area_atuacao;
	}

	function setAreaAtuacao($area_atuacao){
		$this->area_atuacao = $area_atuacao;
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