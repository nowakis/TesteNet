<?

require_once("class.DAO.php");

class TipoCriacaoDAO extends DAO {

	public function gravaDadosTipoCriacao(TipoCriacao $tipo_criacao){

		if (strlen($tipo_criacao->getId())>0){
				$query = "UPDATE tbl_tipo_criacao SET
							descricao         = $tipo_criacao->Xdescricao,
							codigo            = $tipo_criacao->Xcodigo,
							ativo             = $tipo_criacao->Xativo
						WHERE tipo_criacao    = ".$tipo_criacao->getId()."
						AND   fazenda = $tipo_criacao->_login_fazenda ";
		}else{
				$query = "INSERT INTO tbl_tipo_criacao (
								fazenda,
								descricao,
								codigo,
								ativo
						) VALUES (
								$tipo_criacao->_login_fazenda,
								$tipo_criacao->Xdescricao,
								$tipo_criacao->Xcodigo,
								$tipo_criacao->Xativo
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir TIPO CRIAÇÃO. ($query) "); 
		}
	}

	public function recuperarTipoCriacao($id_tipo_criacao){

		$query ="SELECT tbl_tipo_criacao.tipo_criacao                         AS tipo_criacao,
						tbl_tipo_criacao.fazenda                              AS fazenda,
						tbl_tipo_criacao.descricao                            AS descricao,
						tbl_tipo_criacao.codigo                               AS codigo,
						tbl_tipo_criacao.ativo                                AS ativo
				FROM tbl_tipo_criacao
				WHERE tbl_tipo_criacao.fazenda       = $this->_login_fazenda
				AND   tbl_tipo_criacao.tipo_criacao  = $id_tipo_criacao ";

		$banco = $this->getBancoDados(); 
		$tipo_criacao = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma tipo de criação encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$tipo_criacao = new TipoCriacao(); 
				$tipo_criacao->setId($linha['tipo_criacao']);
				$tipo_criacao->setFazenda($linha["fazenda"]);
				$tipo_criacao->setDescricao($linha["descricao"]);
				$tipo_criacao->setCodigo($linha["codigo"]);
				$tipo_criacao->setAtivo($linha["ativo"]);
			}
			return $tipo_criacao; 
		} else {
			throw new Exception("Erro ao recuperar TipoCriacao ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tipo_criacao
				FROM tbl_tipo_criacao 
				WHERE ativo
				ORDER BY tipo_criacao ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$tipo_criacao = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma tipo de criação encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$tipo_criacoes[$i++] = $this->recuperarTipoCriacao($linha["tipo_criacao"]);
			}
			return $tipo_criacoes;
		}else {
			throw new Exception("Erro em query da recupeção de todas"); 
		}
	}
}
?>