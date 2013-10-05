<?

require_once("class.DAO.php");

class PelagemDAO extends DAO {

	public function gravaDadosPelagem(Pelagem $pelagem){

		if (strlen($pelagem->getId())>0){
				$query = "UPDATE tbl_pelagem SET
							fazenda           = $pelagem->Xfazenda,
							especie           = $pelagem->Xespecie,
							descricao         = $pelagem->Xdescricao
						WHERE pelagem    = ".$pelagem->getId()."
						AND   fazenda = $pelagem->_login_fazenda ";
		}else{
				$query = "INSERT INTO tbl_pelagem (
								fazenda,
								especie,
								descricao
						) VALUES (
								$pelagem->_login_fazenda,
								$pelagem->Xespecie,
								$pelagem->Xdescricao
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir PELAGEM. ($query) "); 
		}
	}

	public function recuperarPelagem($id_pelagem){

		$query ="SELECT tbl_pelagem.pelagem                      AS pelagem,
						tbl_pelagem.fazenda                      AS fazenda,
						tbl_pelagem.especie                      AS especie,
						tbl_pelagem.descricao                    AS descricao
				FROM tbl_pelagem
				WHERE tbl_pelagem.fazenda   = $this->_login_fazenda
				AND   tbl_pelagem.pelagem   = $id_pelagem ";

		$banco = $this->getBancoDados(); 
		$pelagem = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma pelagem encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$pelagem = new Pelagem(); 
				$pelagem->setId($linha['pelagem']);
				$pelagem->setFazenda($linha["fazenda"]);
				$pelagem->setEspecie($linha["especie"]);
				$pelagem->setDescricao($linha["descricao"]);
			}
			return $pelagem; 
		} else {
			throw new Exception("Erro ao recuperar Pelagem ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT pelagem
				FROM tbl_pelagem 
				WHERE fazenda = $this->_login_fazenda
				ORDER BY pelagem ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$pelagem = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma pelagem encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$pelagems[$i++] = $this->recuperarPelagem($linha["pelagem"]);
			}
			return $pelagems;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas"); 
		}
	}
}
?>