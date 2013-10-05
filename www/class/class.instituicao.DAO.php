<?

require_once("class.DAO.php");

class InstituicaoDAO extends DAO {

	public function gravaDadosInstituicao(Instituicao $instituicao){

		if (strlen($instituicao->getId())>0){
			$query = " UPDATE tbl_instituicao SET
						nome        = $instituicao->Xnome,
						endereco    = $instituicao->Xendereco,
						numero      = $instituicao->Xnumero,
						complemento = $instituicao->Xcomplemento,
						bairro      = $instituicao->Xbairro,
						cidade      = $instituicao->Xcidade,
						estado      = $instituicao->Xestado,
						cep         = $instituicao->Xcep,
						pais        = $instituicao->Xpais
						WHERE instituicao    = ".$instituicao->getId();
		}else{
			$query = "INSERT INTO tbl_instituicao (
							nome,
							unificado,
							endereco,
							numero,
							complemento,
							bairro,
							cidade,
							estado,
							cep,
							pais
					) VALUES (
							$instituicao->Xnome,
							$instituicao->Xunificado,
							$instituicao->Xendereco,
							$instituicao->Xnumero,
							$instituicao->Xcomplemento,
							$instituicao->Xbairro,
							$instituicao->Xcidade,
							$instituicao->Xestado,
							$instituicao->Xcep,
							$instituicao->Xpais 
						)";
		}
		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Instituicao. ($query) "); 
		}
		
		if (strlen($instituicao->getId())==0){

			$instituicao->setId($banco->insert_id());

			global $_login_unificado;
			global $_login_professor;

			if ($_login_unificado=='1' and strlen($_login_professor)>0){
				$query = "INSERT INTO tbl_instituicao_professor (
								instituicao,
								professor
						) VALUES (
								".$instituicao->getId().",
								".$_login_professor."
						)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro inserir relacionamento entre professor / disciplina ($query) "); 
				}
			}
		}
	}

	public function recuperarInstituicao($id_instituicao){

		$query ="SELECT tbl_instituicao.instituicao          AS instituicao,
						tbl_instituicao.nome                 AS nome,
						tbl_instituicao.unificado            AS unificado,
						tbl_instituicao.endereco             AS endereco,
						tbl_instituicao.numero               AS numero,
						tbl_instituicao.complemento          AS complemento,
						tbl_instituicao.bairro               AS bairro,
						tbl_instituicao.cidade               AS cidade,
						tbl_instituicao.estado               AS estado,
						tbl_instituicao.cep                  AS cep,
						tbl_instituicao.pais                 AS pais
				FROM tbl_instituicao
				WHERE tbl_instituicao.instituicao     = $id_instituicao ";

		$banco = $this->getBancoDados(); 
		$instituicao = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma instituicao encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$instituicao = new Instituicao(); 
				$instituicao->setId($linha['instituicao']);
				$instituicao->setNome($linha["nome"]);
				$instituicao->setUnificado($linha["unificado"]);
				$instituicao->setEndereco($linha["endereco"]);
				$instituicao->setNumero($linha["numero"]);
				$instituicao->setComplemento($linha["complemento"]);
				$instituicao->setBairro($linha["bairro"]);
				$instituicao->setCidade($linha["cidade"]);
				$instituicao->setEstado($linha["estado"]);
				$instituicao->setCep($linha["cep"]);
				$instituicao->setPais($linha["pais"]);
			}
			return $instituicao; 
		} else {
			throw new Exception("Erro ao recuperar Instituicao ($query)"); 
		}
	}

	public function recuperarTodos() {

		global $_login_unificado;
		global $_login_professor;
		global $_login_aluno;

		$banco = $this->getBancoDados(); 

		$sql = "SELECT instituicao
				FROM tbl_instituicao 
				ORDER BY nome ASC"; 

		if ($_login_unificado=="1"){
			$sql = "SELECT tbl_instituicao.instituicao
					FROM tbl_instituicao
					JOIN tbl_instituicao_professor ON tbl_instituicao_professor.instituicao = tbl_instituicao.instituicao
					WHERE tbl_instituicao_professor.professor = ".$_login_professor."
					ORDER BY tbl_instituicao.nome ASC "; 
		}

		if (strlen($_login_aluno)>0){
			$sql = "SELECT tbl_aluno.instituicao
					FROM tbl_aluno
					WHERE tbl_aluno.aluno = ".$_login_aluno; 
		}

		#echo nl2br($sql);
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$instituicao = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma instituicao encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$instituicaos[$i++] = $this->recuperarInstituicao($linha["instituicao"]);
			}
			return $instituicaos;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas"); 
		}
	}
}
?>