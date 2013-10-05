<?

class ProvaPergunta extends Pergunta {

	private $peso					= "";
	private $pergunta_origem		= "";
	private $prova_id				= "";

	public $Xpeso					= "";
	public $Xpergunta_origem		= "";
	public $Xprova_id				= "";

	function ProvaPergunta(){
	}

	function getPeso(){
		return $this->peso;
	}

	function setPeso($peso){
		$this->peso = $peso;
	}

	function getPerguntaOrigem(){
		return $this->pergunta_origem;
	}

	function setPerguntaOrigem($pergunta_origem){
		$this->pergunta_origem = $pergunta_origem;
	}
	
	function getProvaId(){
		return $this->prova_id;
	}

	function setProvaId($prova_id){
		$this->prova_id = $prova_id;
	}
	
	function getRespostaFilhoOrdem($index){
		return $this->getRespostaOrdem($index);
	}
}
?>