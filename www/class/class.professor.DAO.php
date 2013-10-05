<?

require_once("class.DAO.php");

class ProfessorDAO extends DAO {

	public function gravaDadosProfessor(Professor $professor){

		$banco = $this->getBancoDados(); 

		$query = " SELECT count(*) AS cont
					FROM tbl_professor
					WHERE upper(login) = upper(".$professor->Xlogin.") ";
		if (strlen($professor->getId())>0){
			$query .= " AND   professor      <> ".$professor->getId();
		}

		$retorno = $banco->executaSQL($query); 
		$linha = $banco->fetchArray($retorno); 
		if ($linha['cont']>0){
			throw new Exception("O login escolhido ".$professor->Xlogin." j&aacute; existe. Tente outro diferente."); 
		}

		if (strlen($professor->getId())>0) {
			$query = " UPDATE tbl_professor SET
							nome        = $professor->Xnome,
							email       = $professor->Xemail,
							login       = $professor->Xlogin,
							senha       = $professor->Xsenha,
							ativo       = $professor->Xativo,
							endereco    = $professor->Xendereco,
							numero      = $professor->Xnumero,
							complemento = $professor->Xcomplemento,
							bairro      = $professor->Xbairro,
							cidade      = $professor->Xcidade,
							estado      = $professor->Xestado,
							cep         = $professor->Xcep,
							pais        = $professor->Xpais
						WHERE professor    = ".$professor->getId();
		}else{
			$query = "INSERT INTO tbl_professor (
							nome,
							email,
							login,
							senha,
							ativo,
							nivel_ensino,
							area_atuacao,
							endereco,
							numero,
							complemento,
							bairro,
							cidade,
							estado,
							cep,
							pais
					) VALUES (
							$professor->Xnome,
							$professor->Xemail,
							$professor->Xlogin,
							$professor->Xsenha,
							$professor->Xativo,
							$professor->Xnivel_ensino,
							$professor->Xarea_atuacao,
							$professor->Xendereco,
							$professor->Xnumero,
							$professor->Xcomplemento,
							$professor->Xbairro,
							$professor->Xcidade,
							$professor->Xestado,
							$professor->Xcep,
							$professor->Xpais
						)";
		}

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Professor. ($query) "); 
		}

		if (strlen($professor->getId())==0){
			$professor->setId($banco->insert_id());
			$query = "INSERT INTO tbl_instituicao_professor (
							instituicao,
							professor
					) VALUES (
							".$professor->_login_instituicao.",
							".$professor->getId()."
					)";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro inserir relacionmaneto entre professor / disciplina ($query) "); 
			}
		}
	}

	public function recuperarProfessor($id_professor){

	$query ="SELECT tbl_professor.professor                   AS professor,
						tbl_instituicao_professor.instituicao AS instituicao,
						tbl_professor.nome                    AS nome,
						tbl_professor.email                   AS email,
						tbl_professor.login                   AS login,
						tbl_professor.senha                   AS senha,
						tbl_professor.ativo                   AS ativo,
						tbl_professor.nivel_ensino            AS nivel_ensino,
						tbl_professor.area_atuacao            AS area_atuacao,
						tbl_professor.endereco                AS endereco,
						tbl_professor.numero                  AS numero,
						tbl_professor.complemento             AS complemento,
						tbl_professor.bairro                  AS bairro,
						tbl_professor.cidade                  AS cidade,
						tbl_professor.estado                  AS estado,
						tbl_professor.cep                     AS cep,
						tbl_professor.pais                    AS pais
				FROM tbl_professor
				JOIN tbl_instituicao_professor ON tbl_instituicao_professor.professor = tbl_professor.professor
				WHERE tbl_professor.professor                 = $id_professor ";
				/*AND   tbl_instituicao_professor.instituicao   = $this->_login_instituicao*/

		$banco = $this->getBancoDados(); 
		$professor = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma professor encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade   = new SessionFacade($banco); 
				$obj_instituicao = $sessionFacade->recuperarInstituicao($linha["instituicao"]);

				$professor = new Professor(); 
				$professor->setId($linha['professor']);
				$professor->setInstituicao($obj_instituicao);
				$professor->setNome($linha["nome"]);
				$professor->setEmail($linha["email"]);
				$professor->setLogin($linha["login"]);
				$professor->setSenha($linha["senha"]);
				$professor->setAtivo($linha["ativo"]);
				$professor->setNivelEnsino($linha["nivel_ensino"]);
				$professor->setAreaAtuacao($linha["area_atuacao"]);
				$professor->setEndereco($linha["endereco"]);
				$professor->setNumero($linha["numero"]);
				$professor->setComplemento($linha["complemento"]);
				$professor->setBairro($linha["bairro"]);
				$professor->setCidade($linha["cidade"]);
				$professor->setEstado($linha["estado"]);
				$professor->setCep($linha["cep"]);
				$professor->setPais($linha["pais"]);
			}
			return $professor; 
		} else {
			throw new Exception("Erro ao recuperar Professor ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tbl_professor.professor
				FROM tbl_professor 
				JOIN tbl_instituicao_professor ON tbl_instituicao_professor.professor = tbl_professor.professor
				WHERE tbl_instituicao_professor.instituicao = $this->_login_instituicao
				ORDER BY tbl_professor.nome ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$professor = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma professor encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$professors[$i++] = $this->recuperarProfessor($linha["professor"]);
			}
			return $professors;
		} else {
			throw new Exception("Erro em query da recupeção de todas"); 
		}
	}
}
?>