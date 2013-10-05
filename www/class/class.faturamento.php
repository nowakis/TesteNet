<?
require_once('banco.inc.php');
include_once "funcoes.php";

class Faturamento {

	private $faturamento    = "";
	private $fazenda        = "";
	private $proprietario   = "";
	private $fornecedor     = "";
	private $nota_fiscal    = "";
	private $serie          = "";
	private $emissao        = "";
	private $saida          = "";
	private $conferida      = "";
	private $cancelada      = "";
	private $exportado      = "";
	private $transportadora = "";
	private $frete          = "";
	private $cfop           = "";
	private $natureza       = "";
	private $total_nota     = "";
	private $base_icms      = "";
	private $base_ipi       = "";
	private	$valor_icms     = "";
	private	$valor_ipi      = "";
	private	$observacao     = "";
	private $faturamento_item = "";
	
	public $Xfaturamento    = "";
	public $Xfazenda        = "";
	public $Xproprietario   = "";
	public $Xfornecedor     = "";
	public $Xnota_fiscal    = "";
	public $Xserie          = "";
	public $Xemissao        = "";
	public $Xsaida          = "";
	public $Xconferida      = "";
	public $Xcancelada      = "";
	public $Xexportado      = "";
	public $Xtransportadora = "";
	public $Xfrete          = "";
	public $Xcfop           = "";
	public $Xnatureza       = "";
	public $Xtotal_nota     = "";
	public $Xbase_icms      = "";
	public $Xbase_ipi       = "";
	public $Xvalor_icms     = "";
	public $Xvalor_ipi      = "";
	public $Xobservacao     = "";
	public $Xfaturamento_item= "";

	private $itens			= array();

	private $msg_erro		= array();
	private $msg_ok			= array();
	private $msg			= "";

	public $_login_fazenda			= "";
	public $_login_proprietario		= "";

	function Faturamento(){
		global $_login_fazenda;
		global $_login_proprietario;
		$this->_login_fazenda		= $_login_fazenda;
		$this->_login_proprietario	= $_login_proprietario;
	}

	function getId(){
		return $this->faturamento;
	}

	function setId($faturamento){
		$this->faturamento = $faturamento;
	}

	function getProprietario(){
		return $this->proprietario;
	}

	function setProprietario($proprietario){
		$this->proprietario = $proprietario;
	}

	function getFornecedor(){
		return $this->fornecedor;
	}

	function setFornecedor($fornecedor){
		$this->fornecedor = $fornecedor;
	}

	function getNotaFiscal(){
		return $this->nota_fiscal;
	}

	function setNotaFiscal($nota_fiscal){
		$this->nota_fiscal = $nota_fiscal;
	}

	function getSerie(){
		return $this->serie;
	}

	function setSerie($serie){
		$this->serie = $serie;
	}

	function getEmissao(){
		return $this->emissao;
	}

	function setEmissao($emissao){
		$this->emissao = $emissao;
	}

	function getSaida(){
		return $this->saida;
	}

	function setSaida($saida){
		$this->saida = $saida;
	}

	function getConferida(){
		return $this->conferida;
	}

	function setConferida($conferida){
		$this->conferida = $conferida;
	}

	function getCancelada(){
		return $this->cancelada;
	}

	function setCancelada($cancelada){
		$this->cancelada = $cancelada;
	}

	function getExportado(){
		return $this->exportado;
	}

	function setExportado($exportado){
		$this->exportado= $exportado;
	}

	function getTransportadora(){
		return $this->transportadora;
	}

	function setTransportadora($transportadora){
		$this->transportadora= $transportadora;
	}

	function getFrete(){
		return $this->frete;
	}

	function setFrete($frete){
		$this->frete = $frete;
	}

	function getCfop(){
		return $this->cfop;
	}

	function setCfop($cfop){
		$this->cfop = $cfop;
	}

	function getNatureza(){
		return $this->natureza;
	}

	function setNatureza($natureza){
		$this->natureza = $natureza;
	}

	function getTotalNota(){
		return $this->total_nota;
	}

	function setTotalNota($total_nota){
		$this->total_nota = $total_nota;
	}

	function getBaseIcms(){
		return $this->base_icms;
	}

	function setBaseIcms($base_icms){
		$this->base_icms = $base_icms;
	}

	function getBaseIpi(){
		return $this->base_ipi;
	}

	function setBaseIpi($base_ipi){
		$this->base_ipi = $base_ipi;
	}

	function getValorIcms(){
		return $this->valor_icms;
	}

	function setValorIcms($valor_icms){
		$this->valor_icms = $valor_icms;
	}

	function getValorIpi(){
		return $this->valor_ipi;
	}

	function setValorIpi($valor_ipi){
		$this->valor_ipi = $valor_ipi;
	}

	function getObservacao(){
		return $this->observacao;
	}

	function setObservacao($observacao){
		$this->observacao = $observacao;
	}

	function getFaturamentoItem(){
		return $this->faturamento_item;
	}

	function setFaturamentoItem($faturamento_item){
		$this->faturamento_item = $faturamento_item;
	}

	function addItem($faturamento_item){
		array_push($this->itens,$faturamento_item);
	}

	function removeItem($faturamento_item){
		#array_push($this->itens,$faturamento_item);
	}

	function qtdeItem(){
		return sizeof($this->itens);
	}

	function getItem($index){
		return $this->itens[$index];
	}

	function gravarConferencia(){
		$this->setConferida(date("d/m/Y H:i:s"));
	}
}
?>
