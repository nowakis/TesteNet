<?

require_once("class.DAO.php");

class CursoDAO extends DAO {

	public function gravaDadosCurso(Curso $curso){

		$banco = $this->getBancoDados(); 

		if (strlen($curso->getId())>0){
				$query = " UPDATE tbl_curso SET
							nome        = $curso->Xnome
							WHERE curso       = ".$curso->getId()."
							AND   instituicao = $curso->_login_instituicao ";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar CURSO. ($query) "); 
				}
		}else{
				$query = "INSERT INTO tbl_curso (
								instituicao,
								nome
						) VALUES (
								$curso->_login_instituicao,
								$curso->Xnome
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao inserir CURSO. ($query) "); 
				}
				$curso->setId($banco->insert_id());
		}
	}

	public function recuperarCurso($id_curso){

		$query ="SELECT tbl_curso.curso                AS curso,
						tbl_curso.instituicao          AS instituicao,
						tbl_curso.nome                 AS nome
				FROM tbl_curso
				WHERE tbl_curso.instituicao   = $this->_login_instituicao
				AND   tbl_curso.curso         = $id_curso ";

		$banco = $this->getBancoDados(); 
		$curso = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma curso encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade   = new SessionFacade($banco); 
				$obj_instituicao = $sessionFacade->recuperarInstituicao($linha["instituicao"]);

				$curso = new Curso(); 
				$curso->setId($linha['curso']);
				$curso->setInstituicao($obj_instituicao);
				$curso->setNome($linha["nome"]);
			}
			return $curso; 
		} else {
			throw new Exception("Erro ao recuperar Curso ($query)"); 
		}
	}

	public function recuperarTodos($obrigatorio) {

		$sql = "SELECT curso
				FROM tbl_curso 
				WHERE instituicao = $this->_login_instituicao
				ORDER BY nome ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$cursos = NULL;
			$i = 0;
			
			if ($banco->numRows($retorno) == 0 and $obrigatorio != 'opcional'){
				throw new Exception("Nenhuma curso encontrado. Щ necessсrio cadastrar para realizar esta operaчуo. Acesse o menu Instituiчуo / Curso",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$cursos[$i++] = $this->recuperarCurso($linha["curso"]);
			}
			return $cursos;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas"); 
		}
	}

	public function recuperarQtde() {

		$banco = $this->getBancoDados(); 

		$sql = "SELECT count(*) AS qtde
				FROM tbl_curso 
				WHERE instituicao = ".$this->_login_instituicao;
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			return $linha['qtde'];
		} else {
			throw new Exception("Erro em query da recupeчуo de qtde de cursos"); 
		}
	}


}
?>