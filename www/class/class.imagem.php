<?

class Imagem {

	private $imagem				= "";
	private $pergunta			= "";
	private $descricao			= "";
	private $path				= "";
	private $thumb				= "";

	public $Ximagem				= "";
	public $Xpergunta			= "";
	public $Xdescricao			= "";
	public $Xpath				= "";
	public $Xthumb				= "";

	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_instituicao	= "";
	public $_login_professor	= "";
	public $_login_aluno		= "";

	function Imagem(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->imagem;
	}

	function setId($imagem){
		$this->imagem = $imagem;
	}

	function getPergunta(){
		return $this->pergunta;
	}

	function setPergunta($pergunta){
		$this->pergunta = $pergunta;
	}
	
	function getDescricao(){
		return $this->descricao;
	}

	function setDescricao($descricao){
		$this->descricao = $descricao;
	}	

	function getPath(){
		return $this->path;
	}

	function setPath($path){
		$this->path = $path;
	}

	function getThumb(){
		return $this->thumb;
	}

	function setThumb($thumb){
		$this->thumb = $thumb;
	}

}
?>