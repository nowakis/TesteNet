<?

require_once("class.DAO.php");

class LogDAO extends DAO {

	public function gravarLog($tipo,$usuario,$instituicao){

		$banco = $this->getBancoDados(); 

		if ($tipo == 'ALUNO'){
			$query = "INSERT INTO tbl_log_acesso (instituicao, aluno,ip,programa) VALUES (".$instituicao.",".$usuario.",'".getRealIpAddr()."','".$_SERVER["PHP_SELF"]."') ";
			if(!$banco->updateSQL($query)) {
				#throw new Exception("ERRO AO LOGAR"); 
			}
		}
		if ($tipo == 'PROFESSOR'){
			$query = "INSERT INTO tbl_log_acesso (instituicao, professor,ip,programa) VALUES (".$instituicao.",".$usuario.",'".getRealIpAddr()."','".$_SERVER["PHP_SELF"]."') ";
			if(!$banco->updateSQL($query)) {
				#throw new Exception("ERRO AO LOGAR"); 
			}
		}
	}
}
?>