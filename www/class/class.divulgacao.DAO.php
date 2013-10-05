<?

require_once("class.DAO.php");

class DivulgacaoDAO extends DAO {

	public function enviarEmailDAO($divulgacao){

		$banco = $this->getBancoDados(); 

		$query = "	SELECT tbl_divulgacao.divulgacao, tbl_divulgacao.professor, tbl_divulgacao.aluno, tbl_divulgacao.nome, tbl_divulgacao.email
					FROM tbl_divulgacao
					WHERE ultimo_email IS NULL
					ORDER BY tbl_divulgacao.ultimo_email ASC 
					LIMIT ".$divulgacao->getQtdeEmail();
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			$sessionFacade = new SessionFacade($banco); 

			while($linha = $banco->fetchArray($retorno)) { 

				$rand = new UniqueRand();

				if (getRealIpAddr() != '127.0.0.1'){
					/* Envio de E-Mail para Avisar Divulgacao */
					$mail             = new PHPMailer();
					$body             = $mail->getFile('../emails/divulgacao.html');

					$blablabla1 = "";
					$blablabla2 = "";
					$blablabla3 = "";

					$newcode = "";
					$newcode_length = 0;
					$codelenght = 300;
					while($newcode_length < $codelenght) {
						$x=1;
						$y=3;
						$part = rand($x,$y);
						if($part==1){$a=48;$b=57;}  // Numbers
						if($part==2){$a=65;$b=90;}  // UpperCase
						if($part==3){$a=97;$b=122;} // LowerCase
						$code_part=chr(rand($a,$b));
						$newcode_length = $newcode_length + 1;
						$newcode = $newcode.$code_part;
					}
					$blablabla1 = $newcode;

					$newcode = "";
					$newcode_length = 0;
					$codelenght = 300;
					while($newcode_length < $codelenght) {
						$x=1;
						$y=3;
						$part = rand($x,$y);
						if($part==1){$a=48;$b=57;}  // Numbers
						if($part==2){$a=65;$b=90;}  // UpperCase
						if($part==3){$a=97;$b=122;} // LowerCase
						$code_part=chr(rand($a,$b));
						$newcode_length = $newcode_length + 1;
						$newcode = $newcode.$code_part;
					}
					$blablabla2 = $newcode;

					$newcode = "";
					$newcode_length = 0;
					$codelenght = 300;
					while($newcode_length < $codelenght) {
						$x=1;
						$y=3;
						$part = rand($x,$y);
						if($part==1){$a=48;$b=57;}  // Numbers
						if($part==2){$a=65;$b=90;}  // UpperCase
						if($part==3){$a=97;$b=122;} // LowerCase
						$code_part=chr(rand($a,$b));
						$newcode_length = $newcode_length + 1;
						$newcode = $newcode.$code_part;
					}
					$blablabla3 = $newcode;

					$variaveis = array("{NOME}","{EMAIL}","{BLABLABLA1}","{BLABLABLA2}","{BLABLABLA3}");
					$valores   = array( $linha["nome"], $linha["email"] ,$blablabla1,$blablabla2,$blablabla3);
					$body      = str_replace($variaveis, $valores, $body);

					$mail->From       = "testenetweb@gmail.com";
					$mail->FromName   = "Fábio Nowaki";
					$mail->Subject    = "Sistema de Provas OnLine GRATUÍTO!";
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
					$mail->MsgHTML($body);
					$mail->AddAddress($linha["email"], $linha["nome"]);
					#$mail->AddAddress('testenetweb@gmail.com', 'TesteNet Web');
					$mail->Send();
				}

				sleep(10);

				$query = "UPDATE tbl_divulgacao SET ultimo_email = NOW() WHERE divulgacao = ".$linha["divulgacao"];
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar Divulgacao. ($query) "); 
				}
			}

		} else {
			throw new Exception("Erro ao recuperar Relatório de Acessos ($query)"); 
		}
	}


	public function divulgacaoPesquisaDAO($divulgacao){

		$banco = $this->getBancoDados(); 

		$query = "	SELECT tbl_divulgacao.divulgacao, tbl_divulgacao.professor, tbl_divulgacao.aluno, tbl_divulgacao.nome, tbl_divulgacao.email, tbl_professor.senha, tbl_professor.email AS email2
					FROM tbl_divulgacao
					LEFT JOIN tbl_professor USING(professor)
					WHERE tbl_divulgacao.acesso IS NOT NULL
					ORDER BY tbl_divulgacao.ultimo_email ASC 
					LIMIT ".$divulgacao->getQtdeEmail();
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			$sessionFacade = new SessionFacade($banco); 

			while($linha = $banco->fetchArray($retorno)) { 

				$rand = new UniqueRand();

				$email = $linha['email'];

				if (strlen($linha['email2'])>0){
					$email = $linha['email2'];
				}

				if (getRealIpAddr() != '127.0.0.1'){
					/* Envio de E-Mail para Avisar Divulgacao */
					$mail             = new PHPMailer();
					$body             = $mail->getFile('../emails/divulgacao_pesquisa.html');

					$variaveis = array("{NOME}","{EMAIL}","{KEY}","{P}","{A}");
					$valores   = array( $linha["nome"], $email ,md5($linha['professor'].$email),$linha['professor'],'');
					$body      = str_replace($variaveis, $valores, $body);

					$mail->From       = "testenetweb@gmail.com";
					$mail->FromName   = "Fábio Nowaki";
					$mail->Subject    = "Sistema de Provas OnLine GRATUÍTO!";
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
					$mail->MsgHTML($body);
					$mail->AddAddress($linha["email"], $linha["nome"]);
					#$mail->AddAddress('testenetweb@gmail.com', 'TesteNet Web');
					$mail->Send();
				}
			}

		} else {
			throw new Exception("Erro ao recuperar Disvulgacao de Pesquisa ($query)"); 
		}
	}
	
	public function divulgacaoAcessoDAO($email){

		$banco = $this->getBancoDados(); 

		$query = "UPDATE tbl_divulgacao SET acesso = NOW() WHERE email = '".$email."'";
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {
			$sessionFacade = new SessionFacade($banco); 
			while($linha = $banco->fetchArray($retorno)) { 
				if (getRealIpAddr() != '127.0.0.1'){
					/* Envio de E-Mail para Avisar Divulgacao */
					$mail             = new PHPMailer();
					$body             = "Professor $email acessou o Sistema!";
					$mail->From       = "testenetweb@gmail.com";
					$mail->FromName   = "TesteNet";
					$mail->Subject    = "Acesso!";
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
					$mail->MsgHTML($body);
					$mail->AddAddress('testenetweb@gmail.com', 'TesteNet Web');
					$mail->Send();
				}
			}
		}
	}
	
	public function recuperarDivulgacaDAO($divulgacao){

		$banco = $this->getBancoDados(); 

		$query = "	SELECT tbl_divulgacao.aluno, tbl_divulgacao.professor, tbl_divulgacao.nome, tbl_divulgacao.email
					FROM tbl_divulgacao
					ORDER BY tbl_divulgacao.ultimo_email ASC ";
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			$sessionFacade = new SessionFacade($banco); 
			$resultado     = array();

			while($linha = $banco->fetchArray($retorno)) { 

				if (strlen($linha["aluno"])>0){
					$obj_aluno     = $sessionFacade->recuperarAluno($linha["aluno"]);
				}

				$obj_prova     = $sessionFacade->recuperarProva($linha["prova"]);
				$prova         = $obj_prova->getId();
				$data          = $obj_prova->getDataInicio();
				$disciplina    = $obj_prova->getDisciplina()->getNome();
				$curso         = $obj_prova->getDisciplina()->getCurso()->getNome();

				$obj_prov_resp = $sessionFacade->recuperarProvaRespondida($prova,$aluno);
			}

			return $resultado; 
		} else {
			throw new Exception("Erro ao recuperar Relatório de Acessos ($query)"); 
		}
	}

	public function recuperarNomePesquisaDAO($email){

		$banco = $this->getBancoDados(); 

		$query = "	SELECT tbl_divulgacao.aluno, tbl_divulgacao.professor, tbl_divulgacao.nome, tbl_divulgacao.email
					FROM tbl_divulgacao
					WHERE tbl_divulgacao.email = '".$email."'
					ORDER BY tbl_divulgacao.ultimo_email ASC ";
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			$sessionFacade = new SessionFacade($banco); 
			$resultado     = "";

			while($linha = $banco->fetchArray($retorno)) { 
				$resultado = $linha["nome"];
			}

			return $resultado; 
		} else {
			throw new Exception("Erro ao recuperar Relatório de Acessos ($query)"); 
		}
	}


}
?>