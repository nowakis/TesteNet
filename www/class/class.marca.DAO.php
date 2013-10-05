<?
include_once "class.SessionFacade.php";
require_once("class.DAO.php");

class MarcaDAO extends DAO {

	public function gravaDadosMarca(Marca $marca){

		if (strlen($marca->getId())>0){
				$query = "UPDATE tbl_marca SET
							codigo            = $marca->Xcodigo,
							proprietario      = $marca->Xproprietario,
							ativo             = $marca->Xativo,
							observacao        = $marca->Xobservacao
						WHERE marca    = ".$marca->getId();
		}else{
				$query = "INSERT INTO tbl_marca (
								codigo,
								proprietario,
								ativo,
								observacao
						) VALUES (
								$marca->Xcodigo,
								$marca->Xproprietario,
								$marca->Xativo,
								$marca->Xobservacao
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir MARCA. ($query) "); 
		}
	}

	public function recuperarMarca($id_marca){

		$query ="SELECT tbl_marca.marca                                AS marca,
						tbl_marca.codigo                               AS codigo,
						tbl_marca.proprietario                         AS proprietario,
						tbl_marca.ativo                                AS ativo,
						tbl_marca.observacao                           AS observacao
				FROM tbl_marca
				WHERE tbl_marca.marca = $id_marca ";

		$banco = $this->getBancoDados(); 
		$marca = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma marca encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade = new SessionFacade($banco); 
				$obj_proprietario = $sessionFacade->recuperarProprietario($linha["proprietario"]);

				$marca = new Marca(); 
				$marca->setId($linha['marca']);
				$marca->setCodigo($linha["codigo"]);
				$marca->setProprietario($obj_proprietario);
				$marca->setAtivo($linha["ativo"]);
				$marca->setObservacao($linha["observacao"]);
			}
			return $marca; 
		} else {
			throw new Exception("Erro ao recuperar Marca ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tbl_marca.marca
				FROM tbl_marca 
				JOIN tbl_proprietario         ON tbl_proprietario.proprietario         = tbl_marca.proprietario
				JOIN tbl_proprietario_fazenda ON tbl_proprietario_fazenda.proprietario = tbl_proprietario.proprietario
				WHERE tbl_proprietario_fazenda.fazenda = $this->_login_fazenda
				ORDER BY tbl_marca.marca ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$marca = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma marca encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$marcas[$i++] = $this->recuperarMarca($linha["marca"]);
			}
			return $marcas;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas marcas"); 
		}
	}
}
?>