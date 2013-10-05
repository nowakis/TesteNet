<?

require_once("class.DAO.php");

class ProvaRespondidaDAO extends DAO {

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
	public function gravaDadosProvaRespondidaDataInicioDAO(ProvaRespondida $prova_respondida){

		$banco = $this->getBancoDados(); 

		$query = "SELECT tbl_prova_aluno.prova
					FROM tbl_prova_aluno 
					WHERE prova = ".$prova_respondida->getProva()->getId()."
					AND   aluno = ".$prova_respondida->getAluno()->getId();
		$retorno = $banco->executaSQL($query); 
		if ($banco->numRows($retorno) > 0){
			$query = " UPDATE tbl_prova_aluno SET
								data_inicio  = $prova_respondida->Xdata_inicio
						WHERE prova = ".$prova_respondida->getProva()->getId()."
						AND   aluno = ".$prova_respondida->getAluno()->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar data de início da prova. (SQL: $query) "); 
			}
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
				#echo "<br>".nl2br($query);
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
			#echo "1)".nl2br($query);
			if ($linha["qtde_perguntas_sem_nota"] == 0){
				#echo "11)".nl2br($query);
				$prova->setNotaLiberada(date('d/m/Y H:i'));
				#echo "2)".nl2br($query);
			}
		}
		#echo "2)".nl2br($query);
	}

	public function provaCorrigirDAO(ProvaRespondida $prova) {
		$banco = $this->getBancoDados(); 
		$sessionFacade		= new SessionFacade($banco);

		$prov = $prova->getProva();
		$nota = 0;

		#echo "<br>Corrigindo....".$prov->getQtdePerguntas();

		for ($i=0; $i < $prov->getQtdePerguntas(); $i++ ){
			$prova_pergunta = $prov->getPergunta($i);

			$peso = $prova_pergunta->getPeso();

			#echo "<br>Peso: $peso....";

			/* DISSERTATIVA */
			if ($prova_pergunta->getTipoPergunta()->getId() == "1"){
				/* Dissertativa nao faz correção */
			}

			/* MULTIPLA ESCOLHA */
			if ($prova_pergunta->getTipoPergunta()->getId() == "2"){
				$valor_corrigido_pergunta = 0;

				for ($j=0; $j<$prova_pergunta->getQtdeResposta(); $j++){
					$resposta = $prova_pergunta->getResposta($j);

					$respondidas = $prova->getRespostasPerguntaItem($prova_pergunta->getId(), $resposta->getId());

					if ($respondidas != null){
						if ($resposta->getRespostaCorreta() == "1"){
							$valor_corrigido_pergunta = 1 * $peso;
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 1 , $valor_corrigido_pergunta);
						}else{
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 0, 0);
						}
						#echo "Achou!!! = $valor_corrigido_pergunta = ";
					}#else echo " - Nao achou. ";
				}
				#echo "22222 ".$prova_pergunta->getId()."=".$valor_corrigido_pergunta."=";
				$prova->setValorCorrigidoPergunta( $prova_pergunta->getId(), $valor_corrigido_pergunta );
			}

			/* VERDADEIRO OU FALSO */
			if ($prova_pergunta->getTipoPergunta()->getId() == "3"){
				$valor_corrigido_pergunta = 0;

				for ($j=0; $j<$prova_pergunta->getQtdeResposta(); $j++){
					$resposta = $prova_pergunta->getResposta($j);

					$respondidas = $prova->getRespostasPerguntaItem($prova_pergunta->getId(), $resposta->getId());
					#echo "<br>";
					#print_r($respondidas);

					if ($respondidas != null){

						#echo "Correto? ( ".$resposta->getRespostaCorreta()." ) -(".strlen($respondidas[3]).") OR (".$resposta->getRespostaCorreta().") - (".strlen($respondidas[3]).")";

						if (($resposta->getRespostaCorreta() == "1" AND strlen($respondidas[3])>0) OR ($resposta->getRespostaCorreta() == "0" AND strlen($respondidas[3])==0)){
							$valor_corrigido_pergunta += (1 / $prova_pergunta->getQtdeResposta())*$peso;
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 1 , (1 / $prova_pergunta->getQtdeResposta())*$peso );
							#echo " -> CORRETO!!!!!! <br>";
						}else{
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 0, 0 );
							#echo " -> ERRADO! <br>";
						}
					}
				}
				$prova->setValorCorrigidoPergunta( $prova_pergunta->getId(), $valor_corrigido_pergunta );
			}
			/* COMPLETE */
			if ($prova_pergunta->getTipoPergunta()->getId() == "4"){
				$valor_corrigido_pergunta = 0;

				for ($j=0; $j<$prova_pergunta->getQtdeResposta(); $j++){
					$resposta = $prova_pergunta->getResposta($j);

					$respondidas = $prova->getRespostasPerguntaItem($prova_pergunta->getId(), $resposta->getId());
					#echo "<br>";
					#print_r($respondidas);
					#echo "<br>";

					if ($respondidas != null){
						#echo "Resposta Filho".$resposta->getRespostaFilho()->getId();
						#echo "<br>";
						$resposta_do_item  = $resposta->getRespostaFilho()->getRespostaTexto();
						$resposta_do_aluno = $respondidas[3];

						/* Faz a verificação da resposta do aluno. Faz a comparação do que o aluno fez com a resposta do professor. Tolerância de 5% */
						if (str_limpo($resposta_do_item) == str_limpo($resposta_do_aluno) or _similar(str_limpo($resposta_do_item),str_limpo($resposta_do_aluno))>95) {
							$valor_corrigido_pergunta += (1 / $prova_pergunta->getQtdeResposta())*$peso;
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 1 , (1 / $prova_pergunta->getQtdeResposta())*$peso );
							#echo " -> CORRETO!!!!!! <br>";
						}else{
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 0, 0 );
							#echo " -> ERRADO! <br>";
						}

						/*
						if (($resposta->getRespostaCorreta() == "1" AND strlen($respondidas[3])>0) OR ($resposta->getRespostaCorreta() == "0" AND strlen($respondidas[3])==0)){
							$valor_corrigido_pergunta += (1 / $prova_pergunta->getQtdeResposta())*$peso;
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 1 , (1 / $prova_pergunta->getQtdeResposta())*$peso );
							#echo " -> CORRETO!!!!!! <br>";
						}else{
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 0, 0 );
							#echo " -> ERRADO! <br>";
						}
						*/
					}
				}
				$prova->setValorCorrigidoPergunta( $prova_pergunta->getId(), $valor_corrigido_pergunta );
			}
			/* LACUNA */
			if ($prova_pergunta->getTipoPergunta()->getId() == "5"){
				$valor_corrigido_pergunta = 0;

				for ($j=0; $j<$prova_pergunta->getQtdeResposta(); $j++){
					$resposta = $prova_pergunta->getResposta($j);

					$respondidas = $prova->getRespostasPerguntaItem($prova_pergunta->getId(), $resposta->getId());
					#echo "<br>";
					#print_r($respondidas);
					#echo "<br>";

					if ($respondidas != null){
						#echo "Resposta Filho".$resposta->getRespostaFilho()->getId();
						#echo "<br>";
						if ($resposta->getRespostaFilho()->getId() == $respondidas[3]){
							$valor_corrigido_pergunta += (1 / $prova_pergunta->getQtdeResposta())*$peso;
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 1 , (1 / $prova_pergunta->getQtdeResposta())*$peso );
							#echo " -> CORRETO!!!!!! <br>";
						}else{
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 0, 0 );
							#echo " -> ERRADO! <br>";
						}

						/*
						if (($resposta->getRespostaCorreta() == "1" AND strlen($respondidas[3])>0) OR ($resposta->getRespostaCorreta() == "0" AND strlen($respondidas[3])==0)){
							$valor_corrigido_pergunta += (1 / $prova_pergunta->getQtdeResposta())*$peso;
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 1 , (1 / $prova_pergunta->getQtdeResposta())*$peso );
							#echo " -> CORRETO!!!!!! <br>";
						}else{
							$prova->setValorCorrigidoResposta( $prova_pergunta->getId(), $resposta->getId(), 0, 0 );
							#echo " -> ERRADO! <br>";
						}
						*/
					}
				}
				$prova->setValorCorrigidoPergunta( $prova_pergunta->getId(), $valor_corrigido_pergunta );
			}
			#echo "<hr style='color:red'>->($valor_corrigido_pergunta)";	
			$nota += $valor_corrigido_pergunta;
		}
		#echo "Nota: Parcial($nota)";
		$prova->setNota($nota);
	}


	public function gravaProvaCorrigirDAO(ProvaRespondida $prova_respondida){

		$banco = $this->getBancoDados();

		$query = "SELECT tbl_prova_aluno.prova
					FROM tbl_prova_aluno 
					WHERE prova = ".$prova_respondida->getProva()->getId()."
					AND   aluno = ".$prova_respondida->getAluno()->getId();
		$retorno = $banco->executaSQL($query); 
		if ($banco->numRows($retorno) == 0){
			throw new Exception("Erro ao atualizar nota da prova (SQL: $query) "); 
		}

		$query = " UPDATE tbl_prova_aluno SET
							nota          = ".$prova_respondida->Xnota.",
							nota_liberada = ".$prova_respondida->Xnota_liberada."
					WHERE prova = ".$prova_respondida->getProva()->getId()."
					AND   aluno = ".$prova_respondida->getAluno()->getId();
		#echo nl2br($query);
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar nota PROVA RESPONDIDA. (SQL: $query) "); 
		}

		if (strlen($prova_respondida->getNotaLiberada())>0){
			if (getRealIpAddr() != '127.0.0.1' or 1==1){

				$prova = $prova_respondida->getProva();
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


	public function recuperarProvaRespondidaDAO($id_prova,$id_aluno){

		$banco				= $this->getBancoDados();
		$sessionFacade		= new SessionFacade($banco);
		$obj_prova			= $sessionFacade->recuperarProva($id_prova);
		$prova_respondida	= NULL;

		$query ="SELECT DATE_FORMAT(tbl_prova_aluno.data_inicio , '%d/%m/%Y %H:%i')  AS data_inicio,
						DATE_FORMAT(tbl_prova_aluno.data_termino , '%d/%m/%Y %H:%i') AS data_termino,
						tbl_prova_aluno.qtde_perguntas                               AS qtde_perguntas,
						tbl_prova_aluno.qtde_acertos                                 AS qtde_acertos,
						tbl_prova_aluno.nota                                         AS nota,
						tbl_prova_aluno.professor                                    AS professor,
						tbl_prova_aluno.nota_liberada                                AS nota_liberada
				FROM tbl_prova_aluno
				WHERE tbl_prova_aluno.prova = $id_prova 
				AND   tbl_prova_aluno.aluno = $id_aluno";

		$retorno = $banco->executaSQL($query); 
		if($retorno == NULL) {
			throw new Exception("Erro ao recuperar Prova do Aluno $id_aluno (SQL: $query)"); 
		}

		if ($banco->numRows($retorno) == 0){
			throw new Exception("Nenhuma prova encontrada.",0);
		}

		while($linha = $banco->fetchArray($retorno)) {

			$obj_professor	= $sessionFacade->recuperarProfessor($linha["professor"]);
			$obj_aluno		= $sessionFacade->recuperarAluno($id_aluno);

			$prova_respondida = new ProvaRespondida(); 
			$prova_respondida->setProva($obj_prova);
			$prova_respondida->setAluno($obj_aluno);
			$prova_respondida->setProfessor($obj_professor);
			$prova_respondida->setDataInicio($linha["data_inicio"]);
			$prova_respondida->setDataTermino($linha["data_termino"]);
			$prova_respondida->setQtdePerguntas($linha["qtde_perguntas"]);
			$prova_respondida->setQtdeAcertos($linha["qtde_acertos"]);
			$prova_respondida->setNota($linha["nota"]);
			$prova_respondida->setNotaLiberada($linha["nota_liberada"]);

			$query="SELECT	tbl_prova_aluno_pergunta.prova_aluno_pergunta AS prova_aluno_pergunta,
							tbl_prova_aluno_pergunta.prova_pergunta       AS prova_pergunta,
							tbl_prova_aluno_pergunta.valor_corrigido      AS valor_corrigido,
							tbl_prova_pergunta.tipo_pergunta              AS tipo_pergunta
					FROM tbl_prova_aluno_pergunta
					JOIN tbl_prova_pergunta USING(prova_pergunta)
					WHERE tbl_prova_aluno_pergunta.prova = ".$prova_respondida->getProva()->getId()."
					AND   tbl_prova_aluno_pergunta.aluno = ".$prova_respondida->getAluno()->getId();
			#echo nl2br($query);
			#echo "<hr>"; 
			$retorno_pergunta = $banco->executaSQL($query); 
			if($retorno_pergunta == NULL) {
				throw new Exception("Erro ao recuperar prova corrigida (SQL: $query)"); 
			}
			while($linha_pergunta = $banco->fetchArray($retorno_pergunta)) {

				$prova_respondida->addPerguntaRespondida(	$linha_pergunta["prova_aluno_pergunta"],
															$linha_pergunta["prova_pergunta"],
															$linha_pergunta["valor_corrigido"] );

				$query="SELECT	tbl_prova_aluno_resposta.prova_aluno_resposta AS prova_aluno_resposta,
								tbl_prova_aluno_resposta.prova_aluno_pergunta AS prova_aluno_pergunta,
								tbl_prova_aluno_resposta.prova_resposta       AS prova_resposta,
								tbl_prova_aluno_resposta.resposta_texto       AS resposta_texto,
								tbl_prova_aluno_pergunta.prova_pergunta       AS prova_pergunta,
								tbl_prova_aluno_resposta.resposta_correta     AS resposta_correta,
								tbl_prova_aluno_resposta.valor                AS valor
						FROM tbl_prova_aluno_resposta
						JOIN tbl_prova_aluno_pergunta ON tbl_prova_aluno_pergunta.prova_aluno_pergunta = tbl_prova_aluno_resposta.prova_aluno_pergunta 
						WHERE tbl_prova_aluno_resposta.prova_aluno_pergunta = ".$linha_pergunta["prova_aluno_pergunta"];
				#echo nl2br($query);
				#echo "<hr>";
				$retorno_item = $banco->executaSQL($query); 
				if($retorno_item == NULL) {
					throw new Exception("Erro ao recuperar prova corrigida (SQL: $query)"); 
				}
				while($linha_item = $banco->fetchArray($retorno_item)) {

					$prova_respondida->addResposta(	$linha_item["prova_aluno_resposta"],
													/*$linha_item["prova_aluno_pergunta"],*/
													$linha_item["prova_pergunta"],
													$linha_item["prova_resposta"],
													$linha_item["resposta_texto"],
													$linha_item["resposta_correta"],
													$linha_item["valor"] );
				}
			}
		}
		return $prova_respondida; 
	}

}
?>