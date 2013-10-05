<?

class Pesquisa {

	private $pesquisa			= "";
	private $instituicao		= "";
	private $professor			= "";
	private $aluno				= "";
	private $data				= "";
	private $perguntas			= array();

	public $Xpesquisa			= "";
	public $Xinstituicao		= "";
	public $Xprofessor			= "";
	public $Xaluno				= "";
	public $Xdata				= "";
	public $Xperguntas			= array();

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Pesquisa(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->pesquisa;
	}

	function setId($pesquisa){
		$this->pesquisa = $pesquisa;
	}

	function getInstituicao(){
		return $this->instituicao;
	}

	function setInstituicao($instituicao){
		$this->instituicao = $instituicao;
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

	function getData(){
		return $this->data;
	}

	function setData($data){
		$this->data = $data;
	}

	/* PERGUNTAS */
	function addPergunta($pergunta){
		array_push($this->perguntas,$pergunta);
	}

	function getQtdePergunta(){
		return count($this->perguntas);
	}

	function getPergunta($index){
		return $this->perguntas[$index];
	}
}
?>