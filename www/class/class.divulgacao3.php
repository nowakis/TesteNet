<?

class Divulgacao {

	private $divulgacao			= "";
	private $professor			= "";
	private $aluno				= "";
	private $nome				= ""
	private $email				= ""
	private $ultimo_email		= "";

	public $Xdivulgacao			= "";
	public $Xprofessor			= "";
	public $Xaluno				= "";
	public $Xnome				= "";
	public $Xemail				= "";
	public $Xultimo_email		= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Divulgacao(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->divulgacao;
	}

	function setId($divulgacao){
		$this->divulgacao = $divulgacao;
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

}
?>