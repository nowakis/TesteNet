<?

require_once("class.DAO.php");

class AlunoDAO extends DAO {

	public function gravaDadosAluno(Aluno $aluno){

		$banco = $this->getBancoDados(); 

		$query = " SELECT count(*) AS cont
					FROM tbl_aluno
					WHERE 1=1 /*instituicao = $aluno->_login_instituicao */
					AND   email       = $aluno->Xemail 
					AND   senha       = $aluno->Xsenha ";
		if (strlen($aluno->getId())>0){
			$query .= " AND   aluno      <> ".$aluno->getId();
		}

		$retorno = $banco->executaSQL($query); 

		if ($banco->numRows($retorno) == 0){
			throw new Exception("Erro inesperado ao salvar informações do aluno. Tente novamente. Se o erro persistir, entre em contato com o seu supervisor.",0);
		}

		$linha = $banco->fetchArray($retorno); 
		if ($linha['cont']>0){
			throw new Exception("Já existe outro aluno com este Email. Não é possível duplicidade. Operação não foi concluída",0);
		}

		if (strlen($aluno->getId())>0){
			$query = " UPDATE tbl_aluno SET
							nome        = $aluno->Xnome,
							ra          = $aluno->Xra,
							email       = $aluno->Xemail,
							senha       = $aluno->Xsenha,
							ativo       = $aluno->Xativo,
							endereco    = $aluno->Xendereco,
							numero      = $aluno->Xnumero,
							complemento = $aluno->Xcomplemento,
							bairro      = $aluno->Xbairro,
							cidade      = $aluno->Xcidade,
							estado      = $aluno->Xestado,
							cep         = $aluno->Xcep,
							pais        = $aluno->Xpais
						WHERE aluno    = ".$aluno->getId()."
						AND   instituicao = $aluno->_login_instituicao ";
		}else{
			$query = "INSERT INTO tbl_aluno (
							instituicao,
							nome,
							ra,
							email,
							senha,
							ativo,
							endereco,
							numero,
							complemento,
							bairro,
							cidade,
							estado,
							cep,
							pais
					) VALUES (
							$aluno->_login_instituicao,
							$aluno->Xnome,
							$aluno->Xra,
							$aluno->Xemail,
							$aluno->Xsenha,
							$aluno->Xativo,
							$aluno->Xendereco,
							$aluno->Xnumero,
							$aluno->Xcomplemento,
							$aluno->Xbairro,
							$aluno->Xcidade,
							$aluno->Xestado,
							$aluno->Xcep,
							$aluno->Xpais
						)";
		}

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir ALUNO. ($query) "); 
		}
		if (strlen($aluno->getId())==0){
			$aluno->setId($banco->insert_id());

			/* Envio de E-Mail para Avisar Aluno */
			$mail             = new PHPMailer();
			$body             = $mail->getFile('emails/cadastro_aluno.html');

			global $_login_instituicao_nome;

			$variaveis = array("{ALUNO}","{LOGIN}", "{SENHA}", "{NOME_INSTITUICAO}");
			$valores   = array(	$aluno->getNome(), 
								$aluno->getEmail(),
								$aluno->getSenha(),
								$_login_instituicao_nome
							);
			$body      = str_replace($variaveis, $valores, $body);

			$mail->From       = "testenetweb@gmail.com";
			$mail->FromName   = "TesteNet";
			$mail->Subject    = "TesteNet - Seja Bem Vindo!";
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
			$mail->MsgHTML($body);
			$mail->AddAddress($aluno->getEmail(), $aluno->getNome());
			$mail->AddBCC('testenetweb@gmail.com', 'Suporte TesteNet');
			$mail->Send();
		}
	}

	public function gravarNovaSenhaAluno(Aluno $aluno){

		$banco = $this->getBancoDados(); 

		$query = " SELECT count(*) AS cont
					FROM tbl_aluno
					WHERE 1=1 /*instituicao = $aluno->_login_instituicao */
					AND   email       = $aluno->Xemail 
					AND   senha       = $aluno->Xsenha 
					AND   aluno      <> ".$aluno->getId();
		$retorno = $banco->executaSQL($query); 

		if ($banco->numRows($retorno) == 0){
			throw new Exception("Erro inesperado ao salvar informações do aluno. Tente novamente. Se o erro persistir, entre em contato com o seu supervisor.",0);
		}

		$linha = $banco->fetchArray($retorno); 
		if ($linha['cont']>0){
			throw new Exception("Já existe outro aluno com este Email. Não é possível duplicidade. Operação não foi concluída",0);
		}

		if (strlen($aluno->getId())>0){
			$query = " UPDATE tbl_aluno SET
							senha       = $aluno->Xsenha
						WHERE aluno = ".$aluno->getId()."
						AND   instituicao = $aluno->_login_instituicao ";
		}

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar senha ALUNO. (SQL: $query) "); 
		}

		if (strlen($aluno->getId())==0){
			$aluno->setId($banco->insert_id());
		}
	}

	public function recuperarAluno($id_aluno){

		$query ="SELECT tbl_aluno.aluno                AS aluno,
						tbl_aluno.instituicao          AS instituicao,
						tbl_aluno.nome                 AS nome,
						tbl_aluno.ra                   AS ra,
						tbl_aluno.email                AS email,
						tbl_aluno.senha                AS senha,
						tbl_aluno.ativo                AS ativo,
						tbl_aluno.endereco             AS endereco,
						tbl_aluno.numero               AS numero,
						tbl_aluno.complemento          AS complemento,
						tbl_aluno.bairro               AS bairro,
						tbl_aluno.cidade               AS cidade,
						tbl_aluno.estado               AS estado,
						tbl_aluno.cep                  AS cep,
						tbl_aluno.pais                 AS pais
				FROM tbl_aluno
				WHERE tbl_aluno.instituicao   = $this->_login_instituicao
				AND   tbl_aluno.aluno         = $id_aluno ";

		$banco = $this->getBancoDados(); 
		$aluno = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma aluno encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$aluno = new Aluno(); 
				$aluno->setId($linha['aluno']);
				$aluno->setInstituicao($linha["instituicao"]);
				$aluno->setNome($linha["nome"]);
				$aluno->setRa($linha["ra"]);
				$aluno->setEmail($linha["email"]);
				$aluno->setSenha($linha["senha"]);
				$aluno->setAtivo($linha["ativo"]);
				$aluno->setEndereco($linha["endereco"]);
				$aluno->setNumero($linha["numero"]);
				$aluno->setComplemento($linha["complemento"]);
				$aluno->setBairro($linha["bairro"]);
				$aluno->setCidade($linha["cidade"]);
				$aluno->setEstado($linha["estado"]);
				$aluno->setCep($linha["cep"]);
				$aluno->setPais($linha["pais"]);
			}
			return $aluno; 
		} else {
			throw new Exception("Erro ao recuperar Aluno ($query)"); 
		}
	}

	public function recuperarTodos($obrigatorio) {

		$sql = "SELECT aluno
				FROM tbl_aluno 
				WHERE instituicao = $this->_login_instituicao
				ORDER BY nome ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$aluno = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0 and $obrigatorio != 'opcional'){
				throw new Exception("Nenhuma aluno encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$alunos[$i++] = $this->recuperarAluno($linha["aluno"]);
			}
			return $alunos;
		} else {
			throw new Exception("Erro em query da recupeção de todas"); 
		}
	}

	public function recuperarQtde() {

		$sql = "SELECT count(*) AS qtde
				FROM tbl_aluno 
				WHERE instituicao = ".$this->_login_instituicao; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			return $linha['qtde'];
		} else {
			throw new Exception("Erro em query da recupeção da qtde de alunos.");
		}
	}

	/* ALUNO X CURSO */

	public function recuperarTodosAlunoCurso(Curso $curso){

		$banco = $this->getBancoDados(); 

		$query="SELECT DISTINCT tbl_disciplina_aluno.aluno AS aluno
				FROM tbl_disciplina_aluno
				JOIN tbl_disciplina USING(disciplina)
				WHERE tbl_disciplina.curso = ".$curso->getId();
		$retorno = $banco->executaSQL($query);
		if($retorno != NULL) {
			$alunos = NULL;
			$i = "0";
			while($linha = mysql_fetch_array($retorno)) {
				$alunos[$i] = $this->recuperarAluno($linha["aluno"]);
				$alunos[$i] = $this->recuperarDadosAlunoDisciplina($alunos[$i]);
				$i++;
			}
			return $alunos;
		} else {
			throw new Exception("Erro em query ($query)"); 
		}
	}
	
	/* ALUNO X DISCIPLINA */

	public function recuperarDadosAlunoDisciplina(Aluno $aluno){

		$query="SELECT tbl_disciplina_aluno.disciplina
				FROM tbl_disciplina_aluno
				WHERE tbl_disciplina_aluno.aluno = ".$aluno->getId();

		$banco = $this->getBancoDados(); 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {
			while($linha = $banco->fetchArray($retorno)) { 
				
				$disciplina		= $linha['disciplina'];
				$sessionFacade	= new SessionFacade($banco); 
				$disc			= $sessionFacade->recuperarDisciplina($disciplina); 
				if ( is_object($disc)){
					$aluno->addDisciplina($disc);
				}
			}
		}
		return $aluno; 
	}

	public function gravaDadosAlunoDisciplina(Aluno $aluno){

		$banco = $this->getBancoDados(); 
		
		for ($i=0;$i<$aluno->getQtdeDisciplina();$i++){
			$aluno_id      = $aluno->getId();
			$disciplina_id = $aluno->getDisciplina($i)->getId();
			$curso_id      = $aluno->getDisciplina($i)->getCurso()->getId();

			if (strlen($disciplina_id)>0){
				$query="SELECT count(*) AS qtde
						FROM tbl_disciplina_aluno
						WHERE tbl_disciplina_aluno.aluno      = $aluno_id
						AND   tbl_disciplina_aluno.disciplina = $disciplina_id";
				$retorno = $banco->executaSQL($query); 
				$linha = $banco->fetchArray($retorno);
				if ($linha['qtde']==0){
					$query = "INSERT INTO tbl_disciplina_aluno (
									aluno,
									disciplina
								) VALUES (
									$aluno_id,
									$disciplina_id
								)";
					if(!$banco->updateSQL($query)) {
						throw new Exception("Erro ao inserir DISCIPLINA ALUNO. ($query) "); 
					}
				}

				$query="SELECT count(*) AS qtde
						FROM tbl_curso_aluno
						WHERE tbl_curso_aluno.aluno = $aluno_id
						AND   tbl_curso_aluno.curso = $curso_id";
				$retorno = $banco->executaSQL($query); 
				$linha = $banco->fetchArray($retorno);
				if ($linha['qtde']==0){
					$query = "INSERT INTO tbl_curso_aluno (
									curso,
									aluno
								) VALUES (
									$curso_id,
									$aluno_id
								)";
					if(!$banco->updateSQL($query)) {
						throw new Exception("Erro ao inserir CURSO ALUNO. ($query) "); 
					}
				}
			}
		}
	}

	public function apagarDisciplinas(Aluno $aluno){

		$banco = $this->getBancoDados(); 

		if (strlen($aluno->getId())>0){
			$query = "DELETE FROM tbl_disciplina_aluno WHERE aluno = ".$aluno->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao apagar Disciplinas do Aluno. ($query) "); 
			}
		}
	}



	public function excluirAlunoDAO(Aluno $aluno){

		$banco = $this->getBancoDados(); 

		$sql = "SELECT count(*) AS qtde
				FROM tbl_prova_aluno
				WHERE aluno = ".$aluno->getId(); 
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			if ($linha['qtde'] > 0){
				throw new Exception("Já foi aplicado uma prova para ".$aluno->getNome().". Exclusão não concluída!"); 
			}
		} else {
			throw new Exception("Erro ao excluir o aluno. ($sql)"); 
		}

		$query = " DELETE FROM tbl_aluno
					WHERE aluno = ".$aluno->getId();
		if(!$banco->updateSQL($query)) {
			$erro = $banco->mysql_error();
			if (strpos($erro,"foreign key constraint fails")) {
				throw new Exception("Erro ao excluir. Aluno já foi relacionado para prova."); 
			}else{
				throw new Exception("Erro ao excluir Aluno. (".$banco->mysql_error().")"); 
			}
		}
	}
}
?>