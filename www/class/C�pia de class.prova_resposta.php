<?

class ProvaResposta {

	private $prova_resposta		= "";
	private $prova_pergunta		= "";
	private $resposta_texto		= "";
	private $resposta_correta	= "";
	private $resposta_filho		= "";

	public $Xprova_resposta		= "";
	public $Xprova_pergunta		= "";
	public $Xresposta_texto		= "";
	public $Xresposta_correta	= "";
	public $Xresposta_filho		= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function ProvaResposta(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->prova_resposta;
	}

	function setId($prova_resposta){
		$this->prova_resposta = $prova_resposta;
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

}
?>