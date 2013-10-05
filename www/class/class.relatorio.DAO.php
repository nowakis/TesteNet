<?

require_once("class.DAO.php");

class RelatorioDAO extends DAO {

	public function recuperarRelatorioAcessoDAO($relatorio){

		$banco = $this->getBancoDados(); 

		if (strlen($relatorio->getDataInicial())>0 AND strlen($relatorio->getDataFinal())>0){
			$data_inicial= ConverteData($relatorio->getDataInicial(),"'");
			$data_final  = ConverteData($relatorio->getDataFinal(),"'");
			$sql_data = " AND data BETWEEN $data_inicial AND $data_final ";
		}

		if ($relatorio->getAgruparPorData()=='t'){
			$sql_group_data  = " DATE_FORMAT(data , '%d/%m/%Y') , " ;
			$sql_coluna_data = " DATE_FORMAT(data , '%d/%m/%Y') AS data, " ;
		}

		$query = "	SELECT professor, aluno, $sql_coluna_data count( log_acesso ) AS cont
					FROM tbl_log_acesso
					WHERE instituicao = $this->_login_instituicao
					AND aluno IS NOT NULL
					AND programa LIKE '%/www/index.php'
					$sql_data
					GROUP BY professor, $sql_group_data aluno
					ORDER BY $sql_group_data count( log_acesso ) ";
#echo nl2br($query);
		$banco = $this->getBancoDados(); 
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			$sessionFacade = new SessionFacade($banco); 
			$resultado     = array();

			while($linha = $banco->fetchArray($retorno)) { 
				$obj_aluno = $sessionFacade->recuperarAluno($linha["aluno"]);
				$aluno = $obj_aluno->getId();
				$nome  = $obj_aluno->getNome();
				$ra    = $obj_aluno->getRa();
				$count  = $linha["cont"];
				if (strlen($sql_group_data)>0){
					$data  = $linha["data"];
				}else{
					$data  = "";
				}
				array_push($resultado,array($aluno,$ra,$nome,$data,$count));
			}

			return $resultado; 
		} else {
			throw new Exception("Erro ao recuperar Relatório de Acessos ($query)"); 
		}
	}
	
	public function recuperarRelatorioProvaDAO($relatorio){

		$banco = $this->getBancoDados(); 

		if (strlen($relatorio->getDataInicial())>0 AND strlen($relatorio->getDataFinal())>0){
			$data_inicial= ConverteData($relatorio->getDataInicial(),"'");
			$data_final  = ConverteData($relatorio->getDataFinal(),"'");
			$sql_data = " AND data BETWEEN $data_inicial AND $data_final ";
		}

		$query = "	SELECT tbl_prova.prova, tbl_prova_aluno.aluno
					FROM tbl_prova
					JOIN tbl_disciplina  USING (disciplina)
					JOIN tbl_prova_aluno USING (prova)
					WHERE tbl_disciplina.instituicao = $this->_login_instituicao
					$sql_data
					ORDER BY tbl_prova.data_inicio
					";
		$banco = $this->getBancoDados(); 
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			$sessionFacade = new SessionFacade($banco); 
			$resultado     = array();

			while($linha = $banco->fetchArray($retorno)) { 

				$obj_aluno     = $sessionFacade->recuperarAluno($linha["aluno"]);
				$aluno         = $obj_aluno->getId();
				$nome          = $obj_aluno->getNome();
				$ra            = $obj_aluno->getRa();

				$obj_prova     = $sessionFacade->recuperarProva($linha["prova"]);
				$prova         = $obj_prova->getId();
				$data          = $obj_prova->getDataInicio();
				$disciplina    = $obj_prova->getDisciplina()->getNome();
				$curso         = $obj_prova->getDisciplina()->getCurso()->getNome();

				$obj_prov_resp = $sessionFacade->recuperarProvaRespondida($prova,$aluno);
				
				$data_termino  = $obj_prov_resp->getDataTermino();
				$nota          = $obj_prov_resp->getNota();
				$nota_liberada = $obj_prov_resp->getNotaLiberada();

				if (strlen($nota)==0){
					$nota = "<b style='color:#EE9611; font-size:12px'> - </b>";
				}elseif ($nota>6){
					$nota = "<b style='color:#0000FF; font-size:12px'>".$nota."</b>";
				}else{
					$nota = "<b style='color:#FF0000; font-size:12px'>".$nota."</b>";
				}

				$status = "<b style='color:#FF0000; font-size:12px'>Não Resolvida</b>";
				if (strlen($data_termino)>0){
					$status = "<b style='color:#009900; font-size:12px'>Concluída</b>";
				}

				array_push($resultado,array($aluno,$ra,$nome,$data_termino,$prova,$curso,$disciplina,$nota,$status));
			}

			return $resultado; 
		} else {
			throw new Exception("Erro ao recuperar Relatório de Acessos ($query)"); 
		}
	}
}
?>