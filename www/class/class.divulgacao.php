<?

class Divulgacao {

	private $professor			= "";
	private $aluno				= "";
	private $nome				= "";
	private $email				= "";
	private $ultimo_email		= "";
	private $qtde_email			= "";

	public $Xprofessor			= "";
	public $Xaluno				= "";
	public $Xnome				= "";
	public $Xemail				= "";
	public $Xultimo_email		= "";
	public $Xqtde_email			= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Divulgacao(){
		$this->setQtdeEmail(10);
	}

	function getProfessor(){
		return $this->professor;
	}

	function setProfessor($professor){
		$this->professor = $professor;
	}

	function getAluno(){
		return $this->aluno;
	}

	function setAluno($aluno){
		$this->aluno = $aluno;
	}

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
	}

	function getEmail(){
		return $this->email;
	}

	function setEmail($email){
		$this->email = $email;
	}

	function getUltimoEmail(){
		return $this->ultimo_email;
	}

	function setUltimoEmail($ultimo_email){
		$this->ultimo_email = $ultimo_email;
	}

	function enviarEmail($sessionFacade){
		return $sessionFacade->enviarEmail($this);
	}

	function divulgacaoPesquisa($sessionFacade){
		return $sessionFacade->divulgacaoPesquisa($this);
	}
	
	function getQtdeEmail(){
		return $this->qtde_email;
	}

	function setQtdeEmail($qtde_email){
		$this->qtde_email = $qtde_email;
	}
}
?>