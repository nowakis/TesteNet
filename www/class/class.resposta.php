<?

class Resposta {

	private $resposta				= "";
	private $pergunta				= "";
	private $resposta_texto			= "";
	private $resposta_correta		= "";
	private $resposta_filho			= "";
	private $ordem					= "";

	public $Xresposta				= "";
	public $Xpergunta				= "";
	public $Xresposta_texto			= "";
	public $Xresposta_correta		= "";
	public $Xresposta_filho			= "";
	public $xordem					= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Resposta(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->resposta;
	}

	function setId($resposta){
		$this->resposta = $resposta;
	}

	function getPergunta(){
		return $this->pergunta;
	}

	function setPergunta($pergunta){
		$this->pergunta = $pergunta;
	}
	
	function getRespostaCorreta(){
		return $this->resposta_correta;
	}

	function setRespostaCorreta($resposta_correta){
		$this->resposta_correta = $resposta_correta;
	}
		
	function getRespostaTexto(){
		return $this->resposta_texto;
	}

	function setRespostaTexto($resposta_texto){
		$this->resposta_texto = $resposta_texto;
	}		

	function getRespostaFilho(){
		return $this->resposta_filho;
	}

	function setRespostaFilho($resposta_filho){
		$this->resposta_filho = $resposta_filho;
	}

	function getOrdem(){
		return $this->ordem;
	}

	function setOrdem($ordem){
		$this->ordem = $ordem;
	}

}
?>