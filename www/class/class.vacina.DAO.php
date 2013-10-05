<?

require_once("class.DAO.php");

class VacinaDAO extends DAO {

	public function gravaDadosVacina(Vacina $vacina){

		if (strlen($vacina->getId())>0){
				$query = "UPDATE tbl_vacina SET
							nome              = $vacina->Xnome,
							descricao         = $vacina->Xdescricao,
							custo             = $vacina->Xcusto,
							ativo             = $vacina->Xativo
						WHERE vacina  = ".$vacina->getId()."
						AND   fazenda = $vacina->_login_fazenda ";
		}else{
				$query = "INSERT INTO tbl_vacina (
								fazenda,
								nome,
								descricao,
								custo,
								ativo
						) VALUES (
								$vacina->_login_fazenda,
								$vacina->Xnome,
								$vacina->Xdescricao,
								$vacina->Xcusto,
								$vacina->Xativo
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir VACINA. ($query) "); 
		}
	}

	public function recuperarVacina($id_vacina){

		$query ="SELECT tbl_vacina.vacina                    AS vacina,
						tbl_vacina.fazenda                   AS fazenda,
						tbl_vacina.nome                      AS nome,
						tbl_vacina.descricao                 AS descricao,
						tbl_vacina.custo                     AS custo,
						tbl_vacina.ativo                     AS ativo
				FROM tbl_vacina
				WHERE tbl_vacina.fazenda   = $this->_login_fazenda
				AND   tbl_vacina.vacina    = $id_vacina ";

		$banco = $this->getBancoDados(); 
		$vacina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma vacina encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$vacina = new Vacina(); 
				$vacina->setId($linha['vacina']);
				$vacina->setFazenda($linha["fazenda"]);
				$vacina->setNome($linha["nome"]);
				$vacina->setDescricao($linha["descricao"]);
				$vacina->setCusto($linha["custo"]);
				$vacina->setAtivo($linha["ativo"]);
			}
			return $vacina; 
		} else {
			throw new Exception("Erro ao recuperar Vacina ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT vacina
				FROM tbl_vacina 
				WHERE fazenda = $this->_login_fazenda
				ORDER BY vacina ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$vacina = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma vacina encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$vacinas[$i++] = $this->recuperarVacina($linha["vacina"]);
			}
			return $vacinas;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas"); 
		}
	}
}
?>