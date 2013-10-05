<?

require_once("class.DAO.php");

class ComunicadoDAO extends DAO {

	public function gravaDadosComunicado(Comunicado $comunicado){

		$banco = $this->getBancoDados(); 

		if (strlen($comunicado->getId())>0){
			$query = " UPDATE tbl_comunicado SET
								instituicao     = $comunicado->Xinstituicao,
								curso           = $comunicado->Xcurso,
								professor       = $comunicado->Xprofessor,
								titulo          = $comunicado->Xtitulo,
								data            = $comunicado->Xdata,
								comentario      = $comunicado->Xcomentario,
								obrigatorio     = $comunicado->Xobrigatorio
						WHERE comunicado = ".$comunicado->getId();
		}else{
			$query = "INSERT INTO tbl_comunicado (
								instituicao    ,
								curso          ,
								professor      ,
								titulo         ,
								data           ,
								comentario     ,
								obrigatorio    
						) VALUES (
								$comunicado->Xinstituicao, 
								$comunicado->Xcurso,       
								$comunicado->Xprofessor,   
								$comunicado->Xtitulo,      
								$comunicado->Xdata,        
								$comunicado->Xcomentario,  
								$comunicado->Xobrigatorio  
						)";
		}
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Comunicado. (SQL: $query ) "); 
		}

		if (strlen($comunicado->getId())==0){
			$comunicado->setId($banco->insert_id());
		}

		if (is_object($comunicado->getCurso())){
			$query = "	INSERT INTO tbl_comunicado_aluno (comunicado,aluno)
						SELECT DISTINCT 
								".$comunicado->getId().",
								tbl_curso_aluno.aluno
						FROM tbl_curso_aluno
						JOIN tbl_comunicado USING(curso)
						WHERE tbl_curso_aluno.aluno NOT IN (
							SELECT aluno 
							FROM tbl_comunicado_aluno
							WHERE comunicado = ".$comunicado->getId()."
						)
						AND tbl_curso_aluno.curso = ".$comunicado->getCurso()->getId();
		}else{
			$query = "	INSERT INTO tbl_comunicado_aluno (comunicado,aluno)
						SELECT DISTINCT 
								".$comunicado->getId().",
								tbl_aluno.aluno
						FROM tbl_aluno
						WHERE tbl_aluno.instituicao = ".$comunicado->_login_instituicao."
						AND tbl_aluno.aluno NOT IN (
							SELECT aluno 
							FROM tbl_comunicado_aluno
							WHERE comunicado = ".$comunicado->getId()."
						)";
		}

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Comunicado. (SQL: $query ) "); 
		}

		$query = "	SELECT 	tbl_comunicado.instituicao,
							tbl_comunicado.professor,
							tbl_curso_aluno.aluno
					FROM tbl_curso_aluno
					JOIN tbl_comunicado USING(curso)
					LEFT JOIN tbl_professor ON tbl_professor.professor = tbl_comunicado.professor
					WHERE tbl_comunicado.comunicado = ".$comunicado->getId();
		$banco = $this->getBancoDados(); 
		$comunicado = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade		= new SessionFacade($banco); 
				$obj_instituicao	= $sessionFacade->recuperarInstituicao($linha["instituicao"]);
				$obj_professor		= $sessionFacade->recuperarProfessor($linha["professor"]);
				$obj_aluno			= $sessionFacade->recuperarAluno($linha["aluno"]);

				/* Envio de Email para Notificação */
				if (getRealIpAddr() != '127.0.0.1'){
					$mail             = new PHPMailer();

					$body             = $mail->getFile('emails/comunicado.html');
					$variaveis = array("{ALUNO}","{PROFESSOR}","{NOME_INSTITUICAO}","{LOGIN}", "{SENHA}","{KEY}","{P}","{A}");
					$valores   = array(	$obj_aluno->getNome(), 
										$obj_professor->getNome(),
										$obj_instituicao->getNome(),
										$obj_aluno->getEmail(), 
										$obj_aluno->getSenha(),
										md5($obj_aluno->getId().$obj_aluno->getEmail()),
										'',
										$obj_aluno->getId()
						);
					$body      = str_replace($variaveis, $valores, $body);
					$mail->From       = "testenetweb@gmail.com";
					$mail->FromName   = "TesteNet";
					$mail->Subject    = "TesteNet - Novo Comunicado!";
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
					$mail->MsgHTML($body);
					$mail->AddAddress($obj_aluno->getEmail(), $obj_aluno->getNome());
					$mail->AddBCC('testenetweb@gmail.com', 'Suporte TesteNet');
					$mail->Send();
				}
			}
		}

	}

	public function recuperarComunicado($id_comunicado){

		$query ="SELECT tbl_comunicado.comunicado        AS comunicado,
						tbl_comunicado.instituicao       AS instituicao,
						tbl_comunicado.curso             AS curso,
						tbl_comunicado.professor         AS professor,
						tbl_comunicado.titulo            AS titulo,
						DATE_FORMAT(tbl_comunicado.data , '%d/%m/%Y %H:%i') AS data,
						tbl_comunicado.comentario        AS comentario,
						tbl_comunicado.obrigatorio       AS obrigatorio
				FROM tbl_comunicado
				WHERE tbl_comunicado.comunicado = $id_comunicado ";

		$banco = $this->getBancoDados(); 
		$comunicado = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma comunicado encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade		= new SessionFacade($banco); 
				$obj_instituicao	= $sessionFacade->recuperarInstituicao($linha["instituicao"]);
				$obj_curso			= $sessionFacade->recuperarCurso($linha["curso"]);
				$obj_professor		= $sessionFacade->recuperarProfessor($linha["professor"]);

				$comunicado = new Comunicado(); 
				$comunicado->setId($linha['comunicado']);
				$comunicado->setInstituicao($obj_instituicao);
				$comunicado->setCurso($obj_curso);
				$comunicado->setProfessor($obj_professor);
				$comunicado->setTitulo($linha["titulo"]);
				$comunicado->setData($linha["data"]);
				$comunicado->setComentario($linha["comentario"]);
				$comunicado->setObrigatorio($linha["obrigatorio"]);
			}
			return $comunicado; 
		} else {
			throw new Exception("Erro ao recuperar Comunicado ($query)"); 
		}
	}


	public function confirmarLeitura(Comunicado $comunicado, Aluno $aluno){

		$banco = $this->getBancoDados(); 

		$query = " UPDATE tbl_comunicado_aluno SET
						data_leitura = CURRENT_TIMESTAMP
					WHERE comunicado = ".$comunicado->getId()."
					AND   aluno      = ".$aluno->getId()."
					AND   data_leitura IS NULL";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao confirmar leitura do Comunicado. (SQL: $query ) "); 
		}
	}

	public function recuperarTodos($filtro = '') {

		$banco = $this->getBancoDados(); 

		$filtro_sql = "";

		if ($filtro == 'novos'){
			$filtro_sql = " AND tbl_comunicado_aluno.data_leitura IS NULL";
		}

		if ($filtro == 'obrigatorio'){
			$filtro_sql  = " AND tbl_comunicado.obrigatorio IS TRUE ";
			$filtro_sql .= " AND tbl_comunicado_aluno.data_leitura IS NULL ";
		}

		/* No caso de PROFESSOR estar logado */
		if (strlen($this->_login_professor)>0){
			$sql = "SELECT DISTINCT comunicado
					FROM tbl_comunicado 
					WHERE tbl_comunicado.instituicao = $this->_login_instituicao
					ORDER BY tbl_comunicado.data ASC"; 
		}

		/* No caso de ALUNO estar logado */
		if (strlen($this->_login_aluno)>0){
			$sql = "SELECT DISTINCT comunicado
					FROM tbl_comunicado
					JOIN tbl_comunicado_aluno  USING(comunicado)
					WHERE tbl_comunicado.instituicao = ".$this->_login_instituicao."
					AND   tbl_comunicado_aluno.aluno = ".$this->_login_aluno."
					".$filtro_sql."
					ORDER BY tbl_comunicado.data ASC"; 
		}
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$comunicados = array();
			$i = 0;
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma comunicado encontrado.",0);
			}
			while($linha = mysql_fetch_array($retorno)) {
				array_push($comunicados,$this->recuperarComunicado($linha["comunicado"]));
			}
			return $comunicados;
		} else {
			return NULL;
		}
	}
}
?>