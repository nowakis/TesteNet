<?

class Relatorio {

	private $instituicao		= "";
	private $professor			= "";
	private $aluno				= "";
	private $data_inicial		= "";
	private $data_final			= "";
	private $agrupar_por_data	= "";
	private $relatorio			= "";

	public $Xinstituicao		= "";
	public $Xprofessor			= "";
	public $Xaluno				= "";
	public $Xdata_inicial		= "";
	public $Xdata_final			= "";
	public $Xagrupar_por_data	= "";
	public $Xrelatorio			= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Relatorio(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
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

	function getDataInicial(){
		return $this->data_inicial;
	}

	function setDataInicial($data_inicial){
		$this->data_inicial = $data_inicial;
	}

	function getDataFinal(){
		return $this->data_final;
	}

	function setDataFinal($data_final){
		$this->data_final = $data_final;
	}

	function getRelatorio(){
		return $this->relatorio;
	}

	function setRelatorio($relatorio){
		$this->relatorio = $relatorio;
	}

	function getAgruparPorData(){
		return $this->agrupar_por_data;
	}

	function setAgruparPorData($agrupar_por_data){
		$this->agrupar_por_data = $agrupar_por_data;
	}

	function gerarRelatorio($sessionFacade){
		if ($this->getRelatorio()=='frequencia'){
			return $sessionFacade->recuperarRelatorioAcesso($this);
		}
		if ($this->getRelatorio()=='prova'){
			return $sessionFacade->recuperarRelatorioProva($this);
		}
	}
}
?>