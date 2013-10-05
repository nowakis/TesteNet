<?

class Aluno {

	private $aluno				= "";
	private $instituicao		= "";
	private $nome				= "";
	private $ra					= "";
	private $email				= "";
	private $senha				= "";
	private $ativo				= "";
	private $endereco			= "";
	private $numero				= "";
	private $complemento		= "";
	private $bairro				= "";
	private $cidade				= "";
	private $estado				= "";
	private $cep				= "";
	private $pais				= "";
	private $disciplinas			= array();

	public $Xaluno				= "";
	public $Xinstituicao		= "";
	public $Xnome				= "";
	public $Xra					= "";
	public $Xemail				= "";
	public $Xsenha				= "";
	public $Xativo				= "";
	public $Xendereco			= "";
	public $Xnumero				= "";
	public $Xcomplemento		= "";
	public $Xbairro				= "";
	public $Xcidade				= "";
	public $Xestado				= "";
	public $Xcep				= "";
	public $Xpais				= "";
	public $Xdisciplinas			= array();

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Aluno(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->aluno;
	}

	function setId($aluno){
		$this->aluno = $aluno;
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

	function getRa(){
		return $this->ra;
	}

	function setRa($ra){
		$this->ra = $ra;
	}

	function getEmail(){
		return $this->email;
	}

	function setEmail($email){
		$this->email = $email;
	}

	function getSenha(){
		return $this->senha;
	}

	function setSenha($senha){
		$this->senha = $senha;
	}

	function getAtivo(){
		return $this->ativo;
	}

	function setAtivo($ativo){
		$this->ativo = $ativo;
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

	/* ALUNO - DISCIPLINA */
	function addDisciplina($disciplina){
		array_push($this->disciplinas,$disciplina);
	}

	function getQtdeDisciplina(){
		return sizeof($this->disciplinas);
	}

	function getDisciplina($index){
		return $this->disciplinas[$index];
	}
}
?>