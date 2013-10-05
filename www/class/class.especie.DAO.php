<?

require_once("class.DAO.php");

class EspecieDAO extends DAO {

	public function gravaDadosEspecie(Especie $especie){

		if (strlen($especie->getId())>0){
				$query = "UPDATE tbl_especie SET
							nome              = $especie->Xnome,
							descricao         = $especie->Xdescricao
						WHERE especie    = ".$especie->getId()."
						AND   fazenda = $especie->_login_fazenda ";
		}else{
				$query = "INSERT INTO tbl_especie (
								fazenda,
								nome,
								descricao
						) VALUES (
								$especie->_login_fazenda,
								$especie->Xnome,
								$especie->Xdescricao
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir ESPECIE. ($query) "); 
		}
	}

	public function recuperarEspecie($id_especie){

		$query ="SELECT tbl_especie.especie                      AS especie,
						tbl_especie.fazenda                      AS fazenda,
						tbl_especie.nome                         AS nome,
						tbl_especie.descricao                    AS descricao
				FROM tbl_especie
				WHERE tbl_especie.fazenda   = $this->_login_fazenda
				AND   tbl_especie.especie   = $id_especie ";

		$banco = $this->getBancoDados(); 
		$especie = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma especie encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$especie = new Especie(); 
				$especie->setId($linha['especie']);
				$especie->setFazenda($linha["fazenda"]);
				$especie->setNome($linha["nome"]);
				$especie->setDescricao($linha["descricao"]);
			}
			return $especie; 
		} else {
			throw new Exception("Erro ao recuperar Especie ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT especie
				FROM tbl_especie 
				WHERE fazenda = $this->_login_fazenda
				ORDER BY especie ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$especie = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma especie encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$especies[$i++] = $this->recuperarEspecie($linha["especie"]);
			}
			return $especies;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas"); 
		}
	}
}
?>