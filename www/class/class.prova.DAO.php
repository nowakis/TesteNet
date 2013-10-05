<?

require_once("class.DAO.php");

class ProvaDAO extends DAO {

	public function gravaDadosProva(Prova $prova){

		$banco = $this->getBancoDados(); 

		if (strlen($prova->getId())>0){

			$query = "SELECT tbl_prova_aluno.data_inicio 
						FROM tbl_prova_aluno 
						JOIN tbl_prova USING(prova)
						WHERE prova = ".$prova->getId()." 
						AND  tbl_prova_aluno.data_termino IS NOT NULL ";
			$retorno = $banco->executaSQL($query); 
			if($retorno == NULL) {
				throw new Exception("Erro ao verificar se a prova já foi resolvido por Alunos"); 
			}

			if ($banco->numRows($retorno) > 0){
				throw new Exception("Prova não pode ser alterada pois a mesma já foi resolvida por aluno."); 
			}

			$query = "SELECT tbl_prova_aluno.data_inicio 
						FROM tbl_prova_aluno 
						JOIN tbl_prova USING(prova)
						WHERE prova = ".$prova->getId()." 
						AND (tbl_prova.liberada > tbl_prova.data_inicio OR tbl_prova_aluno.data_inicio IS NOT NULL )";
			$retorno = $banco->executaSQL($query); 
			if($retorno == NULL) {
				throw new Exception("Erro ao verificar se a resoluição da prova já foi iniciada por Aluno"); 
			}

			if ($banco->numRows($retorno) > 0){
				throw new Exception("Prova não pode ser alterada pois a mesma já foi liberado e/ou alunos já iniciaram a resolução."); 
			}
		}

		if (strlen($prova->getId())>0){
			$query = " UPDATE tbl_prova SET
							titulo           = $prova->Xtitulo,
							disciplina       = $prova->Xdisciplina,
							professor        = $prova->Xprofessor,
							numero_perguntas = $prova->Xnumero_perguntas,
							data             = $prova->Xdata,
							data_inicio      = $prova->Xdata_inicio,
							data_termino     = $prova->Xdata_termino,
							dificuldade      = $prova->Xdificuldade,
							liberada         = $prova->Xliberada
						WHERE prova = ".$prova->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PERGUNTA. ($query) "); 
			}
		}else{
			$query = "INSERT INTO tbl_prova (
							titulo,
							disciplina,
							professor,
							numero_perguntas,
							data,
							data_inicio,
							data_termino,
							dificuldade,
							liberada
					) VALUES (
							$prova->Xtitulo,
							$prova->Xdisciplina,
							$prova->Xprofessor,
							$prova->Xnumero_perguntas,
							$prova->Xdata,
							$prova->Xdata_inicio,
							$prova->Xdata_termino,
							$prova->Xdificuldade,
							$prova->Xliberada
						)";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PROVA. ($query) "); 
			}
			$prova->setId($banco->insert_id());
		}
	}
	
	public function gravaDadosProvaTopico(Prova $prova){

		$banco = $this->getBancoDados(); 

		$query = " DELETE FROM tbl_prova_topico WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir APAGAR TOPICO DA PROVA. ($query) "); 
		}

		$retorno = $banco->executaSQL("CREATE TEMPORARY TABLE todos_topicos (prova INT, topico INT) ENGINE = MEMORY"); 

		for ($i=0; $i<$prova->getQtdeTopico();$i++){
			$query = "INSERT INTO todos_topicos (
							prova,
							topico
					) VALUES (
							".$prova->getId().",
							".$prova->getTopico($i)->getId()."
						)";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir TOPICO NA PROVA. ($query) "); 
			}
		}

		$query = "INSERT INTO tbl_prova_topico ( prova, topico) SELECT DISTINCT prova, topico FROM todos_topicos";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir TOPICO NA PROVA. ($query) "); 
		}
	}

	public function gravaDadosProvaPergunta(Prova $prova){

		$banco = $this->getBancoDados(); 

		$query = " DELETE FROM tbl_prova_resposta WHERE prova_pergunta IN (SELECT prova_pergunta FROM tbl_prova_pergunta WHERE prova = ".$prova->getId()." AND  tbl_prova_pergunta.prova_pergunta NOT IN (".$prova->perguntasNaoExcluidas.") )";
		#echo nl2br($query);
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir APAGAR RESPOSTA DA PROVA. ($query) "); 
		}
		
		$query = " DELETE FROM tbl_prova_pergunta_imagem WHERE prova_pergunta IN (SELECT prova_pergunta FROM tbl_prova_pergunta WHERE prova = ".$prova->getId()." AND  tbl_prova_pergunta.prova_pergunta NOT IN (".$prova->perguntasNaoExcluidas.") )";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir APAGAR IMAGEM DA PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_pergunta WHERE prova = ".$prova->getId(). " AND  prova_pergunta NOT IN (".$prova->perguntasNaoExcluidas.") ";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir APAGAR PERGUNTA DA PROVA. ($query) "); 
		}

		$sessionFacade	= new SessionFacade($banco); 

		for ($i=0; $i<$prova->getQtdePerguntas();$i++){
			/* PERGUNTA */
			//$prova->getPergunta($i)->setId(NULL);
			$prova->getPergunta($i)->setProvaId($prova->getId());
			$obj_perg = $sessionFacade->gravarProvaPergunta($prova->getPergunta($i));
		}

		$query = "  SELECT SUM(peso) AS soma
					FROM tbl_prova_pergunta
					WHERE prova = ".$prova->getId();
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {
			$linha = $banco->fetchArray($retorno);
			if ($linha["soma"] > 10 ){
				throw new Exception("Soma dos pesos das peguntas não pode ser superior a 10.",0);
			}
		}else{
			throw new Exception("Erro ao verificar somatória de valores de perguntas.",0);
		}

	}

	public function apagarPerguntas(Prova $prova){

		$banco = $this->getBancoDados(); 

		if (strlen($prova->getId())>0){
			$query = " DELETE FROM tbl_prova_pergunta WHERE prova = ".$prova->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / apagar PERGUNTA X PROVA. ($query) "); 
			}
		}
	}

	public function recuperarProva($id_prova){

		$query ="SELECT tbl_prova.prova            AS prova,
						tbl_prova.titulo           AS titulo,
						tbl_prova.disciplina       AS disciplina,
						tbl_prova.professor        AS professor,
						tbl_prova.numero_perguntas AS numero_perguntas,
						DATE_FORMAT(tbl_prova.data , '%d/%m/%Y %H:%i') AS data,
						DATE_FORMAT(tbl_prova.data_inicio , '%d/%m/%Y %H:%i') AS data_inicio,
						DATE_FORMAT(tbl_prova.data_termino , '%d/%m/%Y %H:%i') AS data_termino,
						dificuldade              AS dificuldade,
						DATE_FORMAT(tbl_prova.liberada , '%d/%m/%Y %H:%i') AS liberada,
						CASE WHEN CURRENT_TIMESTAMP - tbl_prova.liberada > 0 THEN '' ELSE 'Prova só pode ser aberta após o horário de início.' END AS prova_nao_liberada
				FROM tbl_prova
				WHERE tbl_prova.prova = $id_prova ";

		$banco = $this->getBancoDados(); 
		$prova = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma prova encontrada.",0);
			}

			while($linha = $banco->fetchArray($retorno)) {

				$sessionFacade	= new SessionFacade($banco); 
				$obj_disciplina	= $sessionFacade->recuperarDisciplina($linha["disciplina"]);
				$obj_professor	= $sessionFacade->recuperarProfessor($linha["professor"]);

				$prova = new Prova(); 
				$prova->setId($linha['prova']);
				$prova->setTitulo($linha["titulo"]);
				$prova->setDisciplina($obj_disciplina);
				$prova->setProfessor($obj_professor);
				$prova->setNumeroPerguntas($linha["numero_perguntas"]);
				$prova->setData($linha["data"]);
				$prova->setDataInicio($linha["data_inicio"]);
				$prova->setDataTermino($linha["data_termino"]);
				$prova->setDificuldade($linha["dificuldade"]);
				$prova->setLiberada($linha["liberada"]);

				$prova->setProvaNaoLiberada($linha["prova_nao_liberada"]);

				if (strlen($linha["liberada"])==0){
					$prova->setProvaNaoLiberada('O professor ainda não liberou esta prova. Por favor, aguarde.');
				}

				$query ="SELECT tbl_prova_pergunta.prova_pergunta AS pergunta
						FROM tbl_prova
						JOIN tbl_prova_pergunta USING(prova)
						WHERE tbl_prova.prova = $id_prova";
				$retorno_item = $banco->executaSQL($query); 
				if($retorno_item != NULL) {
					while($linha_item = $banco->fetchArray($retorno_item)) {
						$obj_pergunta = $sessionFacade->recuperarProvaPergunta($linha_item["pergunta"]);
						$prova->addPergunta($obj_pergunta);
					}
				}
				
				$query="SELECT tbl_prova_topico.topico
						FROM   tbl_prova_topico
						WHERE  tbl_prova_topico.prova = $id_prova";
				$retorno_item = $banco->executaSQL($query); 
				if($retorno_item != NULL) {
					while($linha_item = $banco->fetchArray($retorno_item)) { 
						$obj_topico = $sessionFacade->recuperarTopico($linha_item["topico"]);
						$prova->addTopico($obj_topico);
					}
				}
			}
			return $prova; 
		} else {
			throw new Exception("Erro ao recuperar Prova ($query)"); 
		}
	}

	public function distruiProvaAluno(Prova $prova){

		$banco			= $this->getBancoDados(); 
		$sessionFacade	= new SessionFacade($banco); 

		$query = " DELETE FROM tbl_prova_aluno_email
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}
		
		$query = " DELETE FROM tbl_prova_aluno_resposta
					WHERE prova_aluno_pergunta IN (
							SELECT prova_aluno_pergunta
							FROM tbl_prova_aluno_pergunta
							WHERE tbl_prova_aluno_pergunta.prova = ".$prova->getId()."
					)";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_aluno_pergunta
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_aluno
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) ".$banco->mysql_error()); 
		}

		/* Apaga se a prova já foi gravado uma vez (para nao duplicar) */
		$sql = "DELETE FROM tbl_prova_aluno WHERE prova = ".$prova->getId(); 
		$retorno = $banco->executaSQL($sql);

		$sql = "INSERT INTO tbl_prova_aluno (prova,aluno) 
				SELECT prova, aluno 
				FROM tbl_prova 
				JOIN tbl_disciplina_aluno USING(disciplina) 
				WHERE tbl_prova.prova = ".$prova->getId(); 
		$retorno = $banco->executaSQL($sql);
		if($retorno == NULL) {
			throw new Exception("Erro em query de gerar prova / aluno (SQL: $sql)"); 
		}
		$query="SELECT  tbl_aluno.aluno,
						tbl_aluno.nome,
						tbl_aluno.email,
						tbl_aluno.ra,
						tbl_aluno.senha
				FROM   tbl_prova_aluno
				JOIN   tbl_aluno USING(aluno)
				WHERE  tbl_prova_aluno.prova = ".$prova->getId();
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Não consta nenhum aluno para esta prova. Verifique o cadastro dos alunos e se existem alunos cadastrados para este curso/disciplina.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$nome_aluno  = $linha["nome"];
				$email_aluno = $linha["email"];
				$ra_aluno    = $linha["ra"];
				$senha_aluno = $linha["senha"];

				if (getRealIpAddr() != '127.0.0.1' AND 1==2){
					$mail             = new PHPMailer();

					$body             = $mail->getFile('emails/prova_aluno.html');

					$variaveis = array("{ALUNO}","{PROFESSOR}","{PROVA_TITULO}",'{PROVA_INICIO}','{PROVA_TERMINO}', "{LOGIN}", "{SENHA}","{KEY}","{P}","{A}");
					$valores   = array(	$nome_aluno, 
										$prova->getProfessor()->getNome(),
										$prova->getTitulo(),
										$prova->getDataInicio(),
										$prova->getDataTermino(),
										$email_aluno, 
										$senha_aluno,
										md5($linha['aluno'].$linha['email']),
										'',
										$linha['aluno']
										);
					$body      = str_replace($variaveis, $valores, $body);

					$mail->From       = "testenetweb@gmail.com";
					$mail->FromName   = "TesteNet";
					$mail->Subject    = "TesteNet - Nova Prova!";
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
					$mail->MsgHTML($body);
					$mail->AddAddress($email_aluno, $nome_aluno);
					$mail->AddBCC('testenetweb@gmail.com', 'Suporte TesteNet');
					$mail->Send();
				}
			}
		}
	}

	public function selecionaPerguntas(Prova $prova){

		$banco			= $this->getBancoDados(); 
		$sessionFacade	= new SessionFacade($banco); 

/*		$ppt = $prova->getNumeroPerguntas() / $prova->getQtdeTopico();
		$ppt = number_format($ppt,0);
		if  ($ppt < 1 ){
			$ppt = 1;
		}
		$topicos = array();
		$lista_topicos = array();
		for ($i=0; $i<$prova->getQtdeTopico();$i++){
			array_push($lista_topicos,$prova->getTopico($i)->getId());
		}
		$topicos = $sessionFacade->recuperarPerguntaTopicoOrdenadoDAO($lista_topicos);

		for ($i=0; $i<count($topicos);$i++){
			echo "<br>Tópico: ".$topicos[$i][0]->getDescricao()." / Qtde Perguntas: ".$topicos[$i][1];
			$perguntas = $sessionFacade->recuperarPerguntaTopicoDAO($prova->getTopico($i), $prova->getDificuldade());
			for ($j=0; $j<count($perguntas);$j++){
				$prova->addPergunta($perguntas[$j]);
				echo "<br>Pergunta: ".$perguntas[$j]->getTitulo();
			}
		}
*/

		$qtde_por_vez			= 1;
		$sem_pergunta			= 1;
		$perguntas_inseridas	= array();
		$x_qtde_perguntas		= $prova->getNumeroPerguntas();
		$qtde_perguntas			= $prova->getNumeroPerguntas();

		while ($qtde_perguntas > 0 AND $sem_pergunta > 0){
			$sem_pergunta = 0;
			for ($i=0; $i<$prova->getQtdeTopico();$i++){
				#echo "<br>Tópico: ".$prova->getTopico($i)->getDescricao();
				$perguntas = $sessionFacade->recuperarPerguntaTopicoDAO($prova->getTopico($i), $prova->getDificuldade(), $qtde_por_vez, $perguntas_inseridas);

				for ($j=0; $j<count($perguntas); $j++ ) {
					$perg = $perguntas[$j];

					if ($qtde_perguntas>0){
						$perg_aux = new ProvaPergunta(); 
						$perg_aux->setTopico($perg->getTopico());
						$perg_aux->setTipoPergunta($perg->getTipoPergunta());
						$perg_aux->setTitulo($perg->getTitulo());
						$perg_aux->setDificuldade($perg->getDificuldade());
						$perg_aux->setFonte($perg->getFonte());
						$perg_aux->setPeso( round(10 / $x_qtde_perguntas, 2));
						$perg_aux->setPerguntaOrigem($perg->getId());

	#echo "Qtde: ".$perg->getQtdeResposta();
						for ($w=0;$w<$perg->getQtdeResposta();$w++){
							$perg->getResposta($w)->setId(NULL);
							if (is_object($perg->getResposta($w)->getRespostaFilho())) {
								$perg->getResposta($w)->getRespostaFilho()->setId(NULL);
							}
							$perg_aux->addResposta($perg->getResposta($w));
						}

						$prova->addPergunta($perg_aux);
						array_push($perguntas_inseridas,$perguntas[$j]->getId());
						$qtde_perguntas--;
						$sem_pergunta++;
					}
					#echo "<br>Pergunta: ".$perguntas[0]->getTitulo();
					#echo "<hr>";
				}
			}
		}
	}

	public function recuperarTodos($filtro) {

		$banco = $this->getBancoDados(); 

		$filtro_sql = "";

		$sql = "";

		/* No caso de PROFESSOR estar logado */
		if (strlen($this->_login_professor)>0){

			if ($filtro == 'agendada'){
				$filtro_sql = " AND tbl_prova.data_termino > CURRENT_TIMESTAMP";
			}
			if ($filtro == 'realizada'){
				$filtro_sql = " AND tbl_prova.data_termino < CURRENT_TIMESTAMP";
			}
			if ($filtro == 'correcao'){
				$filtro_sql = " AND (
									SELECT count(*)
									FROM  tbl_prova_aluno_pergunta
									JOIN  tbl_prova_aluno  ON tbl_prova_aluno.prova = tbl_prova_aluno_pergunta.prova AND tbl_prova_aluno.aluno = tbl_prova_aluno_pergunta.aluno
									WHERE tbl_prova_aluno_pergunta.prova = tbl_prova.prova
									AND   tbl_prova_aluno_pergunta.valor_corrigido IS NULL
									AND   tbl_prova.data_termino IS NOT NULL
								) > 0";
			}
			if ($filtro == 'criada'){
				$filtro_sql = " AND tbl_prova.data_inicio - CURRENT_TIMESTAMP < 3 ";
			}

			$sql = "SELECT DISTINCT prova
					FROM tbl_prova 
					JOIN tbl_disciplina USING(disciplina)
					WHERE tbl_disciplina.instituicao = $this->_login_instituicao
					AND   tbl_prova.professor        = $this->_login_professor
					".$filtro_sql."
					ORDER BY tbl_prova.data_inicio ASC"; 
		}

		/* No caso de ALUNO estar logado */
		if (strlen($this->_login_aluno)>0){

			if ($filtro == 'agendada'){
				$filtro_sql  = " AND tbl_prova.data_termino > CURRENT_TIMESTAMP ";
				$filtro_sql .= " AND tbl_prova_aluno.data_termino IS NULL  ";
			}
			if ($filtro == 'realizada'){
				$filtro_sql = " AND (tbl_prova.data_termino < CURRENT_TIMESTAMP OR tbl_prova_aluno.data_termino IS NOT NULL )  ";
			}
			if ($filtro == 'correcao'){
				$filtro_sql = " AND (
									SELECT count(*)
									FROM  tbl_prova_aluno_pergunta
									JOIN  tbl_prova_aluno  ON tbl_prova_aluno.prova = tbl_prova_aluno_pergunta.prova AND tbl_prova_aluno.aluno = tbl_prova_aluno_pergunta.aluno
									WHERE tbl_prova_aluno_pergunta.prova = tbl_prova.prova
									AND   tbl_prova_aluno_pergunta.valor_corrigido IS NULL
									AND   tbl_prova.data_termino IS NOT NULL
								) > 0";
			}
			$sql = "SELECT DISTINCT prova
					FROM tbl_prova 
					JOIN tbl_disciplina  USING(disciplina)
					JOIN tbl_prova_aluno USING(prova)
					WHERE tbl_disciplina.instituicao = $this->_login_instituicao
					AND   tbl_prova_aluno.aluno      = $this->_login_aluno
					".$filtro_sql."
					ORDER BY tbl_prova.data_inicio ASC"; 
		}
		#echo nl2br($sql);

		/* No caso de nao tiver ningm logado - ROTINA AUTOMATICA */
		if (strlen($sql)==0){
			#throw new Exception("Erro inesperado.Tente novamente. Se o erro persistir, entre em contato com seu supervisor.",0);
			$sql = "SELECT DISTINCT prova
					FROM tbl_prova 
					JOIN tbl_disciplina USING(disciplina)
					WHERE 1=1
					".$filtro_sql."
					ORDER BY tbl_prova.data_inicio ASC"; 
		}

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$prova = NULL;
			$provas = array();
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma prova cadastrada.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				array_push($provas,$this->recuperarProva($linha["prova"]));
			}
			return $provas;
		} else {
			throw new Exception("Erro em query da recupeção de todas"); 
		}
	}
	
	public function recuperarQtde($filtro) {

		$banco = $this->getBancoDados(); 

		$filtro_sql = "";

		if ($filtro == 'agendada'){
			$filtro_sql = " AND tbl_prova.data_termino > CURRENT_TIMESTAMP";
		}
		if ($filtro == 'realizada'){
			$filtro_sql = " AND tbl_prova.data_termino < CURRENT_TIMESTAMP";
		}
		if ($filtro == 'correcao'){
			$filtro_sql = " AND (
								SELECT count(*)
								FROM  tbl_prova_aluno_pergunta
								JOIN  tbl_prova_aluno  ON tbl_prova_aluno.prova = tbl_prova_aluno_pergunta.prova AND tbl_prova_aluno.aluno = tbl_prova_aluno_pergunta.aluno
								WHERE tbl_prova_aluno_pergunta.prova = tbl_prova.prova
								AND   tbl_prova_aluno_pergunta.valor_corrigido IS NULL
								AND   tbl_prova.data_termino IS NOT NULL
							) > 0";
		}

		$sql = "";

		/* No caso de PROFESSOR estar logado */
		if (strlen($this->_login_professor)>0){
			$sql = "SELECT COUNT(prova) AS qtde
					FROM tbl_prova 
					JOIN tbl_disciplina USING(disciplina)
					WHERE tbl_disciplina.instituicao = $this->_login_instituicao
					AND   tbl_prova.professor        = $this->_login_professor
					".$filtro_sql."
					ORDER BY tbl_prova.data_inicio ASC"; 
		}

		/* No caso de ALUNO estar logado */
		if (strlen($this->_login_aluno)>0){

			if ($filtro == 'agendada'){
				$filtro_sql =  " AND tbl_prova.data_termino > CURRENT_TIMESTAMP ";
				$filtro_sql .= " AND tbl_prova_aluno.data_termino IS NULL ";
			}

			$sql = "SELECT COUNT(prova) AS qtde
					FROM tbl_prova 
					JOIN tbl_disciplina  USING(disciplina)
					JOIN tbl_prova_aluno USING(prova)
					WHERE tbl_disciplina.instituicao = $this->_login_instituicao
					AND   tbl_prova_aluno.aluno      = $this->_login_aluno
					".$filtro_sql."
					ORDER BY tbl_prova.data_inicio ASC"; 
		}

		if (strlen($sql)==0){
			throw new Exception("Erro inesperado.Tente novamente. Se o erro persistir, entre em contato com seu supervisor.",0);
		}

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			return $linha['qtde'];
		} else {
			throw new Exception("Erro em query da recupeção da qtde de aluno"); 
		}
	}


	public function excluirProvaDAO(Prova $prova){

		$banco = $this->getBancoDados(); 

		$sql = "SELECT count(*) AS qtde
				FROM tbl_prova 
				JOIN tbl_disciplina USING(disciplina)
				WHERE tbl_disciplina.instituicao = ".$this->_login_instituicao."
				AND   tbl_prova.prova            = ".$prova->getId(); 
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			if ($linha['qtde'] == 0){
				throw new Exception("Prova não encontrada!"); 
			}
		} else {
			throw new Exception("Erro ao excluir a prova. ($sql)"); 
		}

		$query = " DELETE FROM tbl_prova_aluno_email
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_aluno_resposta
					WHERE prova_aluno_pergunta IN (
							SELECT prova_aluno_pergunta
							FROM tbl_prova_aluno_pergunta
							WHERE tbl_prova_aluno_pergunta.prova = ".$prova->getId()."
					)";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_aluno_pergunta
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_aluno
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) ".$banco->mysql_error()); 
		}

		$query = " DELETE FROM tbl_prova_resposta
					WHERE prova_pergunta IN (
							SELECT prova_pergunta
							FROM tbl_prova_pergunta
							WHERE prova = ".$prova->getId()."
					)";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova_pergunta_imagem 
					WHERE prova_pergunta IN (
						SELECT prova_pergunta 
						FROM tbl_prova_pergunta 
						WHERE prova = ".$prova->getId()."
					)";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}
		
		$query = " DELETE FROM tbl_prova_pergunta
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) ".$banco->mysql_error()); 
		}

		$query = " DELETE FROM tbl_prova_topico
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) "); 
		}

		$query = " DELETE FROM tbl_prova
					WHERE prova = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao excluir PROVA. ($query) 2".$banco->mysql_error()); 
		}
	}


	public function enviaEmailProvaAlunoDAO($filtro = ''){

		$banco			= $this->getBancoDados(); 
		$sessionFacade	= new SessionFacade($banco); 

		/* INSERI REGISTRO PARA AS PROVAS QUE AINDA NAO TEM */
		$query = " INSERT INTO tbl_prova_aluno_email (prova,aluno)
					SELECT tbl_prova_aluno.prova, tbl_prova_aluno.aluno
					FROM tbl_prova
					JOIN tbl_disciplina  USING(disciplina)
					JOIN tbl_prova_aluno ON tbl_prova_aluno.prova = tbl_prova.prova
					WHERE (tbl_prova_aluno.prova,tbl_prova_aluno.aluno) NOT IN (
						SELECT tbl_prova_aluno.prova,tbl_prova_aluno.aluno
						FROM tbl_prova_aluno
						JOIN tbl_prova_aluno_email ON tbl_prova_aluno_email.aluno = tbl_prova_aluno.aluno AND tbl_prova_aluno_email.prova = tbl_prova_aluno.prova
					);";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro inserir registro de email da prova ($query) ".$banco->mysql_error()); 
		}

		$filtro_sql   = " AND   DATEDIFF(tbl_prova.data_inicio,CURRENT_DATE) BETWEEN 0 and 4 ";
		$filtro_sql_2 = "";

		if ($filtro == 'corrigida'){
			$filtro_sql = " AND tbl_prova.prova IN (
									SELECT tbl_prova_aluno.prova
									FROM  tbl_prova_aluno_pergunta
									JOIN  tbl_prova_aluno  ON tbl_prova_aluno.prova = tbl_prova_aluno_pergunta.prova AND tbl_prova_aluno.aluno = tbl_prova_aluno_pergunta.aluno
									WHERE tbl_prova_aluno_pergunta.prova = tbl_prova.prova
									AND   tbl_prova_aluno_pergunta.valor_corrigido IS NULL
									AND   DATEDIFF(tbl_prova_aluno.nota_liberada,CURRENT_DATE) BETWEEN 0 and 1
							) > 0";
			$filtro_sql_2 = $filtro_sql;
		}

		$query ="SELECT tbl_prova.prova                  AS prova,
						tbl_prova.titulo                 AS titulo,
						tbl_disciplina.nome              AS disciplina,
						tbl_professor.nome               AS professor,
						DATE_FORMAT(tbl_prova.data , '%d/%m/%Y %H:%i') AS data,
						DATE_FORMAT(tbl_prova.data_inicio , '%d/%m/%Y %H:%i') AS data_inicio,
						DATE_FORMAT(tbl_prova.data_termino , '%d/%m/%Y %H:%i') AS data_termino,
						DATE_FORMAT(tbl_prova.liberada , '%d/%m/%Y %H:%i') AS liberada
				FROM tbl_prova
				JOIN tbl_disciplina USING(disciplina)
				LEFT JOIN tbl_professor  ON tbl_professor.professor = tbl_prova.professor
				WHERE tbl_disciplina.instituicao = $this->_login_instituicao
				$filtro_sql
				";
		$retorno = $banco->executaSQL($query);
		if($retorno != NULL) {
			while($linha = $banco->fetchArray($retorno)) {
				$prova         = $linha['prova'];
				$titulo        = $linha['titulo'];
				$disciplina    = $linha['disciplina'];
				$professor     = $linha['professor'];
				$data          = $linha['data'];
				$data_inicio   = $linha['data_inicio'];
				$data_termino  = $linha['data_termino'];
				$liberada      = $linha['liberada'];

				$sql = "SELECT tbl_prova_aluno.aluno AS aluno,
								tbl_aluno.nome,
								tbl_aluno.email,
								tbl_aluno.email AS login,
								tbl_aluno.senha
						FROM tbl_prova 
						JOIN tbl_prova_aluno            USING (prova)
						JOIN tbl_aluno                  ON tbl_aluno.aluno       = tbl_prova_aluno.aluno
						LEFT JOIN tbl_prova_aluno_email ON tbl_prova_aluno.prova = tbl_prova_aluno_email.prova AND tbl_prova_aluno.aluno = tbl_prova_aluno_email.aluno
						WHERE tbl_prova.prova = ".$prova."
						AND   tbl_prova_aluno_email.agendada IS NOT TRUE 
						$filtro_sql_2
						"; 
				$retorno_aluno = $banco->executaSQL($sql);
				if($retorno_aluno != NULL) {
					while($linha = $banco->fetchArray($retorno_aluno)) {

						if (getRealIpAddr() != '127.0.0.1'){
							$mail             = new PHPMailer();
							$body             = $mail->getFile('../emails/prova_aluno.html');

							$variaveis = array("{ALUNO}","{PROFESSOR}","{PROVA_TITULO}",'{PROVA_INICIO}','{PROVA_TERMINO}', "{LOGIN}", "{SENHA}","{KEY}","{P}","{A}");
							$valores   = array(	$linha['nome'], 
												$disciplina,
												$titulo,
												$data_inicio,
												$data_termino,
												$linha['login'], 
												$linha['senha'],
												md5($linha['aluno'].$linha['login']),
												'',
												$linha['aluno']
												);
							$body      = str_replace($variaveis, $valores, $body);

							$mail->From       = "testenetweb@gmail.com";
							$mail->FromName   = "TesteNet";
							$mail->Subject    = "TesteNet - Nova Prova!";
							$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
							$mail->MsgHTML($body);
							$mail->AddAddress($linha['email'], $linha['nome']);
							$mail->AddBCC('testenetweb@gmail.com', 'Suporte TesteNet');
							$mail->Send();
						}

						/* atualiza envio do email */
						$query = " UPDATE tbl_prova_aluno_email SET
											data     = CURRENT_TIMESTAMP,
											agendada = TRUE
									WHERE prova = ".$prova."
									AND   aluno = ".$linha['aluno']."
									";
						if(!$banco->updateSQL($query)) {
							#throw new Exception("Erro atualizar registro de email da prova ($query) ".$banco->mysql_error()); 
						}
					}
				} else {
					throw new Exception("Erro em query da recupeção do envio de email das provas para os Alunos. (QUERY: $sql)"); 
				}
			}

		}

	}
}
?>