<?

require_once("class.DAO.php");

class ProvaCorrecaoDAO extends DAO {

	public function gravaDadosProvaRespondidaDAO(ProvaRespondida $prova_respondida){

		$banco = $this->getBancoDados(); 

		$query = "SELECT tbl_prova_aluno.prova
					FROM tbl_prova_aluno 
					WHERE prova = ".$prova_respondida->getProva()->getId()."
					AND   aluno = ".$prova_respondida->getAluno()->getId();
		$retorno = $banco->executaSQL($query); 
		if ($banco->numRows($retorno) == 0){
			$query = "INSERT INTO tbl_prova_aluno (
							prova,
							aluno,
							data_inicio,
							data_termino
					) VALUES (
							".$prova_respondida->Xprova.",
							".$prova_respondida->Xaluno.",
							".$prova_respondida->Xdata_inicio.",
							".$prova_respondida->Xdata_termino."
						)";
		}else{
			$query = " UPDATE tbl_prova_aluno SET
								data_inicio  = $prova_respondida->Xdata_inicio,
								data_termino = $prova_respondida->Xdata_termino
						WHERE prova = ".$prova_respondida->getProva()->getId()."
						AND   aluno = ".$prova_respondida->getAluno()->getId();
		}
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir PROVA RESPONDIDA. (SQL: $query) "); 
		}
	} 

	public function gravaDadosProvaPerguntaRespondidaDAO(ProvaRespondida $prova) {

		$banco = $this->getBancoDados();

		$query = "SELECT tbl_prova_aluno.data_inicio 
					FROM tbl_prova_aluno 
					JOIN tbl_prova USING(prova)
					WHERE prova = ".$prova->getProva()->getId()." 
					AND tbl_prova.liberada > tbl_prova.data_inicio 
					AND tbl_prova_aluno.data_inicio IS NOT NULL";
		$retorno = $banco->executaSQL($query); 
		if ($banco->numRows($retorno) > 0){
			throw new Exception("Prova não pode ser alterada pois a mesma já foi liberado e/ou alunos já resolveu."); 
		}

		$query = "	DELETE FROM tbl_prova_aluno_resposta 
					WHERE prova_aluno_pergunta IN (
						SELECT prova_aluno_pergunta
						FROM tbl_prova_aluno_pergunta 
						WHERE prova = ".$prova->getProva()->getId()." 
						AND   aluno = ".$prova->getAluno()->getId()."
					)";
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir APAGAR RESPOSTA DA PROVA. ($query) "); 
		}
		
		$query = "	DELETE FROM tbl_prova_aluno_pergunta
					WHERE prova = ".$prova->getProva()->getId()." 
					AND   aluno = ".$prova->getAluno()->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir APAGAR RESPOSTA DA PROVA. ($query) "); 
		}

		for ($i=0; $i<$prova->getQtdePerguntasRespondida();$i++){

			#print "<hr>LINHA: $i<br>";

			$aux_prova_pergunta = $prova->getPerguntaRespondida($i);

			$prova_aluno_pergunta = $aux_prova_pergunta[0];
			$prova_pergunta       = $aux_prova_pergunta[1];
			$valor_corrigido      = $aux_prova_pergunta[2];

			if (strlen($valor_corrigido)==0){
				$valor_corrigido = " NULL ";
			}

			$query = " INSERT INTO tbl_prova_aluno_pergunta (
							prova,
							aluno,
							prova_pergunta,
							valor_corrigido
						)VALUES(
							".$prova->getProva()->getId().",
							".$prova->getAluno()->getId().",
							".$prova_pergunta.",
							".$valor_corrigido."
						)";
			#echo nl2br($query);
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir APAGAR PERGUNTA DA PROVA. ($query) "); 
			}

			$prova_aluno_pergunta = $banco->insert_id();
			$respotas_as_perguntas = $prova->getRespostasPergunta($prova_pergunta);

			#print "Qtde Perguntas: ".count($respotas_as_perguntas)." <br>";
			#print "Id Pergunta: $prova_pergunta <br>";

			for ($j=0; $j<count($respotas_as_perguntas);$j++){

				$aux_prova_resposta = $respotas_as_perguntas[$j];

				$prova_aluno_resposta   = $aux_prova_resposta[0];
				$prova_pergunta         = $aux_prova_resposta[1];
				$prova_resposta         = $aux_prova_resposta[2];
				$resposta_texto         = $aux_prova_resposta[3];
				$resposta_correta       = $aux_prova_resposta[4];
				$resposta_valor         = $aux_prova_resposta[5];

				if (strlen($resposta_valor)==0){
					$resposta_valor = " NULL ";
				}

				$query = " INSERT INTO tbl_prova_aluno_resposta (
								prova_aluno_pergunta,
								prova_resposta,
								resposta_texto,
								resposta_correta,
								valor
							)VALUES(
								".$prova_aluno_pergunta.",
								".$prova_resposta.",
								'".$resposta_texto."',
								'".$resposta_correta."',
								".$resposta_valor."
							)";
				#echo nl2br($query);
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir APAGAR PERGUNTA DA PROVA. ($query) "); 
				}
			}
		}

		$query = "SELECT count(*) AS qtde_perguntas_sem_nota
					FROM tbl_prova_aluno 
					JOIN tbl_prova_aluno_pergunta USING(prova)
					WHERE tbl_prova_aluno.prova = ".$prova->getProva()->getId()." 
					AND   tbl_prova_aluno.aluno = ".$prova->getAluno()->getId()."
					AND   tbl_prova_aluno_pergunta.valor_corrigido IS NULL";
		$retorno = $banco->executaSQL($query); 
		if ($banco->numRows($retorno) > 0){
			$linha = $banco->fetchArray($retorno);
			if ($linha["qtde_perguntas_sem_nota"] == 0){
				$prova_respondida->setNotaLiberada(date('d/m/Y H:i'));
			}
		}
	}

	public function gravaProvaCorrecaoDAO(ProvaRespondida $prova_respondida){

		$banco = $this->getBancoDados();
		$prova = $prova_respondida->getProva();

		$query = "SELECT tbl_prova_aluno.prova
					FROM tbl_prova_aluno 
					WHERE prova = ".$prova->getId()."
					AND   aluno = ".$prova->getId();
		$retorno = $banco->executaSQL($query); 
		if ($banco->numRows($retorno) == 0){
			throw new Exception("Erro ao atualizar nota da prova (SQL: $query) "); 
		}

		$query = " UPDATE tbl_prova_aluno SET
							nota          = ".$prova_respondida->getNota().",
							nota_liberada = ".$prova_respondida->getNotaLiberada()."
					WHERE prova = ".$prova->getId()."
					AND   aluno = ".$prova->getId();
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar nota PROVA RESPONDIDA. (SQL: $query) "); 
		}

		if (strlen($prova_respondida->getNotaLiberada())>0){
			if (getRealIpAddr() != '127.0.0.1' or 1==1){
				$mail             = new PHPMailer();

				$body             = $mail->getFile('emails/prova_corrigida_aluno.html');
				$variaveis = array("{ALUNO}","{PROFESSOR}","{NOME_INSTITUICAO}","{PROVA_TITULO}",'{PROVA_INICIO}','{PROVA_TERMINO}','{PROVA_NOTA}', "{LOGIN}", "{SENHA}");
				$valores   = array(	$nome_aluno, 
									$prova->getProfessor()->getNome(),
									$prova->getDisciplina()->getInstituicao()->getNome(),
									$prova->getTitulo(),
									$prova->getDataInicio(),
									$prova->getDataTermino(),
									$prova_respondida->getNota(),
									$ra_aluno, 
									$senha_aluno);
				$body      = str_replace($variaveis, $valores, $body);
				$mail->From       = "testenetweb@gmail.com";
				$mail->FromName   = "TesteNet";
				$mail->Subject    = "TesteNet - Nova Prova!";
				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
				$mail->MsgHTML($body);
				$mail->AddAddress($email_aluno, $nome_aluno);
				$mail->AddAddress('testenetweb@gmail.com', 'Suporte TesteNet');
				$mail->Send();
			}
		}
	}


	public function recuperarProvaCorrecaoDAO($id_prova){

		$banco				= $this->getBancoDados();
		$sessionFacade		= new SessionFacade($banco);

		$prova				= $sessionFacade->recuperarProva($id_prova);

		if ($prova == NULL){
			throw new Exception("Prova não encontrada."); 
		}

		$prova_correcao = new ProvaCorrecao();
		$prova_correcao->setProva($prova);

		$query ="SELECT tbl_prova_aluno.aluno
				FROM tbl_prova_aluno
				JOIN tbl_prova USING(prova)
				WHERE tbl_prova_aluno.prova = ".$prova->getId();
		$retorno = $banco->executaSQL($query); 

		if($retorno == NULL) {
			throw new Exception("Erro ao recuperar Prova do Aluno (SQL: $query)"); 
		}

		while($linha = $banco->fetchArray($retorno)) {

			$obj_prova_resp	= $sessionFacade->recuperarProvaRespondida($prova->getId(), $linha["aluno"]);

			if ($obj_prova_resp != NULL){
				$prova_correcao->addProvaRespondida($obj_prova_resp);
			}
		}
		return $prova_correcao; 
	}
	
	public function provaCorrecaoGravarPerguntaNotaDAO($prov_correcao,$prova_pergunta,$prova_aluno,$pergunta_nota){

		$banco				= $this->getBancoDados();

		if ($prov_correcao == NULL){
			throw new Exception("Prova não encontrada."); 
		}

		$prova = $prov_correcao->getProva();

		$query="SELECT tbl_prova_aluno.aluno
				FROM tbl_prova_aluno
				JOIN tbl_prova USING(prova)
				WHERE tbl_prova_aluno.prova = ".$prova->getId()."
				ANd   tbl_prova_aluno.aluno = ".$prova_aluno."
				";
		$retorno = $banco->executaSQL($query); 
#echo nl2br($query)."<hr>";
		if($retorno == NULL or $banco->numRows($retorno) == 0) {
			throw new Exception("Erro ao recuperar Prova do Aluno (SQL: $query)"); 
		}

		$query="SELECT tbl_prova_aluno_pergunta.prova_aluno_pergunta
				FROM tbl_prova_aluno_pergunta
				JOIN tbl_prova_aluno ON tbl_prova_aluno.prova = tbl_prova_aluno_pergunta.prova AND tbl_prova_aluno.aluno = tbl_prova_aluno_pergunta.aluno
				WHERE tbl_prova_aluno.prova    = ".$prova->getId()."
				AND   tbl_prova_aluno.aluno    = ".$prova_aluno."
				AND   tbl_prova_aluno_pergunta.prova_pergunta = ".$prova_pergunta."
				";
		$retorno = $banco->executaSQL($query); 
#echo nl2br($query)."<hr>";
		if($retorno == NULL or $banco->numRows($retorno) == 0) {
			throw new Exception("Erro ao recuperar Prova do Aluno (SQL: $query)"); 
		}

		while($linha = $banco->fetchArray($retorno)) {

			$prova_aluno_pergunta = $linha["prova_aluno_pergunta"];

			$query ="UPDATE tbl_prova_aluno_pergunta SET
							valor_corrigido    = ".$pergunta_nota."
					WHERE prova_aluno_pergunta = ".$prova_aluno_pergunta;
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar nota da pegunta. ($query) "); 
			}
#echo nl2br($query)."<hr>";
			$query ="UPDATE tbl_prova_aluno_resposta SET valor = ".$pergunta_nota."
					WHERE prova_aluno_pergunta = ".$prova_aluno_pergunta;
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar nota da resposta. ($query) "); 
			}
#echo nl2br($query)."<hr>";
			$query ="UPDATE tbl_prova_aluno SET nota = (
						SELECT ROUND(SUM(valor_corrigido),2)
						FROM  tbl_prova_aluno_pergunta
						WHERE tbl_prova_aluno_pergunta.aluno = ".$prova_aluno."
						AND   tbl_prova_aluno_pergunta.prova = ".$prova->getId()."
					)
					WHERE tbl_prova_aluno.prova    = ".$prova->getId()."
					AND   tbl_prova_aluno.aluno    = ".$prova_aluno;
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar nota da resposta. ($query) "); 
			}
#echo nl2br($query)."<hr>";

			$query = "SELECT count(*) AS qtde_perguntas_sem_nota
						FROM tbl_prova_aluno 
						JOIN tbl_prova_aluno_pergunta USING(prova)
						WHERE tbl_prova_aluno.prova = ".$prova->getId()." 
						AND   tbl_prova_aluno.aluno = ".$prova_aluno."
						AND   tbl_prova_aluno_pergunta.valor_corrigido IS NULL";
			$retorno = $banco->executaSQL($query); 
			if($retorno == NULL or $banco->numRows($retorno) == 0) {
				throw new Exception("Erro ao verificar correção do Aluno (SQL: $query)"); 
			}
			if ($banco->numRows($retorno) > 0){
				$linha = $banco->fetchArray($retorno);
				if ($linha["qtde_perguntas_sem_nota"] == 0){
					$query ="UPDATE tbl_prova_aluno SET nota_liberada = CURRENT_TIMESTAMP
							WHERE tbl_prova_aluno.prova    = ".$prova->getId()."
							AND   tbl_prova_aluno.aluno    = ".$prova_aluno;
					if(!$banco->updateSQL($query)) {
						throw new Exception("Erro ao atualizar nota da resposta. ($query) "); 
					}
				}
			}
		}
	}
}
?>