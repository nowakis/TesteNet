<?

require_once("class.DAO.php");

class RacaDAO extends DAO {

	public function gravaDadosRaca(Raca $raca){

		if (strlen($raca->getId())>0){
				$query = "UPDATE tbl_raca SET
							codigo            = $raca->Xcodigo,
							nome              = $raca->Xnome,
							descricao         = $raca->Xdescricao,
							data              = $raca->Xdata,
							observacao        = $raca->Xobservacao
						WHERE raca    = ".$raca->getId()."
						AND   fazenda = $raca->_login_fazenda ";
		}else{
				$query = "INSERT INTO tbl_raca (
								fazenda,
								codigo,
								nome,
								descricao,
								data,
								observacao
						) VALUES (
								$raca->_login_fazenda,
								$raca->Xcodigo,
								$raca->Xnome,
								$raca->Xdescricao,
								$raca->Xdata,
								$raca->Xobservacao
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir RACA. ($query) "); 
		}
	}

	public function recuperarRaca($id_raca){

		$query ="SELECT tbl_raca.raca                                 AS raca,
						tbl_raca.fazenda                              AS fazenda,
						tbl_raca.codigo                               AS codigo,
						tbl_raca.nome                                 AS nome,
						tbl_raca.descricao                            AS descricao,
						DATE_FORMAT(tbl_raca.data , '%d/%m/%Y')       AS data,
						tbl_raca.ativo                                AS ativo,
						tbl_raca.observacao                           AS observacao
				FROM tbl_raca
				WHERE tbl_raca.fazenda       = $this->_login_fazenda
				AND   tbl_raca.raca          = $id_raca  ";

		$banco = $this->getBancoDados(); 
		$raca = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma raзa encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$raca = new Raca(); 
				$raca->setId($linha['raca']);
				$raca->setFazenda($linha["fazenda"]);
				$raca->setCodigo($linha["codigo"]);
				$raca->setNome($linha["nome"]);
				$raca->setDescricao($linha["descricao"]);
				$raca->setData($linha["data"]);
				$raca->setAtivo($linha["ativo"]);
				$raca->setObservacao($linha["observacao"]);
			}
			return $raca; 
		} else {
			throw new Exception("Erro ao recuperar Raзa ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT raca
				FROM tbl_raca 
				WHERE fazenda = $this->_login_fazenda
				ORDER BY nome DESC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$raca = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma raзa encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$racas[$i++] = $this->recuperarRaca($linha["raca"]);
			}
			return $racas;
		} else {
			throw new Exception("Erro em query da recupeзгo de todas"); 
		}
	}
}
?>