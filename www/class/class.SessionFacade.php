<?
require_once("class.pergunta.php");
require_once("class.pergunta.DAO.php");
require_once("class.resposta.php");
require_once("class.resposta.DAO.php");
require_once("class.imagem.php");
require_once("class.imagem.DAO.php");
require_once("class.instituicao.php");
require_once("class.instituicao.DAO.php");
require_once("class.professor.php");
require_once("class.professor.DAO.php");
require_once("class.aluno.php");
require_once("class.aluno.DAO.php");
require_once("class.tipo_pergunta.php");
require_once("class.tipo_pergunta.DAO.php");
require_once("class.topico.php");
require_once("class.topico.DAO.php");
require_once("class.curso.php");
require_once("class.curso.DAO.php");
require_once("class.disciplina.php");
require_once("class.disciplina.DAO.php");
require_once("class.prova.php");
require_once("class.prova.DAO.php");
require_once("class.prova_pergunta.php");
require_once("class.prova_pergunta.DAO.php");
require_once("class.prova_resposta.DAO.php");
require_once("class.prova_imagem.DAO.php");
require_once("class.prova_respondida.php");
require_once("class.prova_respondida.DAO.php");
require_once("class.prova_correcao.php");
require_once("class.prova_correcao.DAO.php");
require_once("class.comunicado.php");
require_once("class.comunicado.DAO.php");
require_once("class.pesquisa.php");
require_once("class.pesquisa.DAO.php");
require_once("class.relatorio.php");
require_once("class.relatorio.DAO.php");
require_once("class.divulgacao.php");
require_once("class.divulgacao.DAO.php");

require_once("class.log.DAO.php");
require_once("class.unique_rand.php");
require_once("class.phpmailer.php");


class SessionFacade {

	public $banco; 

	public function SessionFacade(BancodeDados $banco) { 
		$this->banco = $banco; 
	}


#####################################################################################################
#########################                  LOG               ########################################
#####################################################################################################

	function logAcessoAluno(Instituicao $instituicao,Aluno $aluno){
		$logDAO = new LogDAO(); 
		$logDAO->setBancoDados($this->banco); 
		$logDAO->gravarLog('ALUNO',$aluno->getId(),$instituicao->getId()); 
	}
	
	function logAcessoProfessor(Instituicao $instituicao,Professor $professor){
		$logDAO = new LogDAO(); 
		$logDAO->setBancoDados($this->banco); 
		$logDAO->gravarLog('PROFESSOR',$professor->getId(),$instituicao->getId()); 
	}

#####################################################################################################
#########################               INSTITUICAO          ########################################
#####################################################################################################


	function gravarInstituicao(Instituicao $instituicao){

		if (strlen($instituicao->getNome())==0){
			throw new Exception('Informe o nome da instituição!');
		}else{
			$instituicao->Xnome = "'".$instituicao->getNome()."'";
		}
		
		if (strlen($instituicao->getUnificado())==0){
			$instituicao->Xunificado = "0";
		}else{
			$instituicao->Xunificado = $instituicao->getUnificado();
		}

		if (strlen($instituicao->getEndereco())==0){
			$instituicao->Xendereco = " NULL ";
		}else{
			$instituicao->Xendereco = "'".$instituicao->getEndereco()."'";
		}

		if (strlen($instituicao->getNumero())==0){
			$instituicao->Xnumero = " NULL ";
		}else{
			$instituicao->Xnumero = "'".$instituicao->getNumero()."'";
		}

		if (strlen($instituicao->getComplemento())==0){
			$instituicao->Xcomplemento = " NULL ";
		}else{
			$instituicao->Xcomplemento = "'".$instituicao->getComplemento()."'";
		}

		if (strlen($instituicao->getBairro())==0){
			$instituicao->Xbairro = " NULL ";
		}else{
			$instituicao->Xbairro = "'".$instituicao->getBairro()."'";
		}

		if (strlen($instituicao->getCidade())==0){
			$instituicao->Xcidade = " NULL ";
		}else{
			$instituicao->Xcidade = "'".$instituicao->getCidade()."'";
		}

		if (strlen($instituicao->getEstado())==0){
			$instituicao->Xestado = " NULL ";
		}else{
			$instituicao->Xestado = "'".$instituicao->getEstado()."'";
		}

		if (strlen($instituicao->getCep())==0){
			$instituicao->Xcep = " NULL ";
		}else{
			$instituicao->Xcep = "'".$instituicao->getCep()."'";
		}

		if (strlen($instituicao->getPais())==0){
			$instituicao->Xpais = "'BR'";
		}else{
			$instituicao->Xpais = "'".$instituicao->getPais()."'";
		}

		$instituicaoDAO = new InstituicaoDAO(); 
		$instituicaoDAO->setBancoDados($this->banco); 
		$instituicaoDAO->gravaDadosInstituicao($instituicao); 
	}

	public function recuperarInstituicao($instituicao){
		if (strlen($instituicao)>0){
			$InstituicaoDAO = new InstituicaoDAO();
			$InstituicaoDAO->setBancoDados($this->banco); 
			return $InstituicaoDAO->recuperarInstituicao($instituicao); 
		}else{
			return null;
		}
	}

	public function recurarInstituicaoTodosDAO(){
		$instituicaoDAO = new InstituicaoDAO();
		$instituicaoDAO->setBancoDados($this->banco); 
		return $instituicaoDAO->recuperarTodos();
	}


#####################################################################################################
#########################                 CURSO              ########################################
#####################################################################################################


	function gravarCurso(Curso $curso){

		if (strlen($curso->getNome())==0){
			throw new Exception('Informe o nome do curso!');
		}else{
			$curso->Xnome = "'".$curso->getNome()."'";
		}

		if (!is_object($curso->getInstituicao())){
			throw new Exception('Instituição é obrigatória');
		}else{
			$curso->Xinstituicao = $curso->getInstituicao()->getId();
		}

		$cursoDAO = new CursoDAO(); 
		$cursoDAO->setBancoDados($this->banco); 
		$cursoDAO->gravaDadosCurso($curso); 
	}

	public function recuperarCurso($curso){
		if (strlen($curso)>0){
			$CursoDAO = new CursoDAO();
			$CursoDAO->setBancoDados($this->banco); 
			return $CursoDAO->recuperarCurso($curso); 
		}else{
			return null;
		}
	}

	public function recuperarCursoTodosDAO($obrigatorio = 'obrigatorio'){
		$cursoDAO = new CursoDAO();
		$cursoDAO->setBancoDados($this->banco); 
		return $cursoDAO->recuperarTodos($obrigatorio);
	}
	
	public function recuperarQtdeCursoDAO(){
		$cursoDAO = new CursoDAO();
		$cursoDAO->setBancoDados($this->banco); 
		return $cursoDAO->recuperarQtde();
	}
	
	public function excluirCurso(Curso $curso){
		$cursoDAO = new CursoDAO();
		$cursoDAO->setBancoDados($this->banco); 
		$cursoDAO->excluirCursoDAO($curso);
	}

#####################################################################################################
#########################              DISCIPLINA            ########################################
#####################################################################################################


	function gravarDisciplina(Disciplina $disciplina){

		if (!is_object($disciplina->getInstituicao())){
			throw new Exception('Informe a instituição!');
		}else{
			$disciplina->Xinstituicao = $disciplina->getInstituicao()->getId();
		}

		if (!is_object($disciplina->getProfessor())){
			#throw new Exception('Informe o professor!');
			$disciplina->Xprofessor = " NULL ";
		}else{
			$disciplina->Xprofessor = $disciplina->getProfessor()->getId();
		}

		if (!is_object($disciplina->getCurso())){
			throw new Exception('Informe o curso!');
		}else{
			$disciplina->Xcurso = $disciplina->getCurso()->getId();
		}

		if (strlen($disciplina->getNome())==0){
			throw new Exception('Informe o nome da disciplina!');
		}else{
			$disciplina->Xnome = "'".$disciplina->getNome()."'";
		}

		$disciplinaDAO = new DisciplinaDAO(); 
		$disciplinaDAO->setBancoDados($this->banco); 
		$disciplinaDAO->gravaDadosDisciplina($disciplina); 
		$disciplinaDAO->apagaDadosDisciplinaTopico($disciplina); 

		for ($i=0;$i<$disciplina->getQtdeTopico();$i++){
			$disciplina->getTopico($i)->setDisciplina($disciplina);
			$this->gravarTopico($disciplina->getTopico($i)); 
		}
	}

	function gravarDisciplinaAluno(Disciplina $disciplina){

		$alunos = $this->recuperarAlunoTodosCursoDAO($disciplina->getCurso());

		for ($i=0;$i<count($alunos);$i++){
			$alunos[$i]->addDisciplina($disciplina);
			$this->gravarAlunoDisciplina($alunos[$i]);
		}
	}

	public function recuperarDisciplina($disciplina){
		if (strlen($disciplina)>0){
			$disciplinaDAO = new DisciplinaDAO();
			$disciplinaDAO->setBancoDados($this->banco); 
			return $disciplinaDAO->recuperarDisciplina($disciplina); 
		}else{
			return null;
		}
	}

	public function recuperarDisciplinaTodosDAO($curso = '',$obrigatorio = 'obrigatorio'){
		$DisciplinaDAO = new DisciplinaDAO();
		$DisciplinaDAO->setBancoDados($this->banco); 
		return $DisciplinaDAO->recuperarTodos($curso,$obrigatorio);
	}

	public function recuperarDisciplinaQtdeTodosDAO(){
		$DisciplinaDAO = new DisciplinaDAO();
		$DisciplinaDAO->setBancoDados($this->banco); 
		return $DisciplinaDAO->recuperarQtde();
	}

	public function excluirDisciplina(Disciplina $disciplina){
		$disciplinaDAO = new DisciplinaDAO();
		$disciplinaDAO->setBancoDados($this->banco); 
		$disciplinaDAO->excluirDisciplinaDAO($disciplina);
	}
	


#####################################################################################################
#########################                  TOPICO            ########################################
#####################################################################################################


	function gravarTopico(Topico $topico){

		if (strlen($topico->getDescricao())==0){
			throw new Exception('Informe a descrição do tópico!');
		}else{
			$topico->Xdescricao = "'".$topico->getDescricao()."'";
		}

		if (!is_object($topico->getDisciplina())){
			$topico->Xdisciplina = " NULL ";
		}else{
			$topico->Xdisciplina = $topico->getDisciplina()->getId();
		}

		$topicoDAO = new TopicoDAO(); 
		$topicoDAO->setBancoDados($this->banco); 
		$topicoDAO->gravaDadosTopico($topico); 
	}


	public function recuperarTopico($topico){
		if (strlen($topico)>0){
			$topicoDAO = new TopicoDAO();
			$topicoDAO->setBancoDados($this->banco); 
			return $topicoDAO->recuperarTopico($topico); 
		}else{
			return null;
		}
	}

	public function recuperarTopicoTodosDAO($disciplina_id = ''){
		$TopicoDAO = new TopicoDAO();
		$TopicoDAO->setBancoDados($this->banco); 
		return $TopicoDAO->recuperarTodos($disciplina_id);
	}



#####################################################################################################
#########################             TIPO PERGUNTA          ########################################
#####################################################################################################


	function gravarTipoPergunta(TipoPergunta $tipo_pergunta){

		if (strlen($tipo_pergunta->getDescricao())==0){
			throw new Exception('Informe a descricao!');
		}else{
			$tipo_pergunta->Xdescricao = $tipo_pergunta->getDescricao();
		}

		if (strlen($tipo_pergunta->getQtdeRespostas())==0){
			$tipo_pergunta->Xdescricao = 1;
		}else{
			$tipo_pergunta->Xdescricao = $tipo_pergunta->getDescricao();
		}

		if (strlen($tipo_pergunta->getImagem())==0){
			$tipo_pergunta->Ximagem = " NULL ";
		}else{
			$tipo_pergunta->Ximagem = $tipo_pergunta->getImagem();
		}

		$tipoPerguntaDAO = new TipoPerguntaDAO(); 
		$tipoPerguntaDAO->setBancoDados($this->banco); 
		$tipoPerguntaDAO->gravaDadosTipoPergunta($tipo_pergunta); 
	}

	public function recuperarTipoPergunta($tipo_pergunta){
		if (strlen($tipo_pergunta)>0){
			$tipoPerguntaDAO = new TipoPerguntaDAO();
			$tipoPerguntaDAO->setBancoDados($this->banco); 
			return $tipoPerguntaDAO->recuperarTipoPergunta($tipo_pergunta); 
		}else{
			return null;
		}
	}

	public function recuperarTipoPerguntaTodosDAO(){
		$tipoPerguntaDAO = new TipoPerguntaDAO();
		$tipoPerguntaDAO->setBancoDados($this->banco); 
		return $tipoPerguntaDAO->recuperarTodos();
	}


#####################################################################################################
#########################              PROFESSOR             ########################################
#####################################################################################################


	function gravarProfessor(Professor $professor){

		#if (!is_object($professor->getInstituicao())){
		#	throw new Exception('Informe a instituição!');
		#}else{
		#	$professor->Xinstituicao = $professor->getInstituicao()->getId();
		#}

		if (strlen($professor->getNome())==0){
			throw new Exception('Informe o nome do professor!');
		}else{
			$professor->Xnome = "'".$professor->getNome()."'";
		}

		if (strlen($professor->getLogin())==0){
			throw new Exception('Informe o login do professor!');
		}else{
			$professor->Xlogin = "'".$professor->getLogin()."'";
		}

		if (strlen($professor->getSenha())==0){
			throw new Exception('Informe a senha!');
		}else{
			$professor->Xsenha = "'".$professor->getSenha()."'";
		}

		if (strlen($professor->getEmail())==0){
			throw new Exception('Informe o email do professor!');
		}else{
			$professor->Xemail = "'".$professor->getEmail()."'";
		}

		if (strlen($professor->getAtivo())==0){
			$professor->Xativo = 0;
		}else{
			$professor->Xativo = $professor->getAtivo();
		}

		if (strlen($professor->getNivelEnsino())==0){
			$professor->Xnivel_ensino = " NULL ";
		}else{
			$professor->Xnivel_ensino = "'".$professor->getNivelEnsino()."'";
		}

		if (strlen($professor->getAreaAtuacao())==0){
			$professor->Xarea_atuacao = " NULL ";
		}else{
			$professor->Xarea_atuacao = "'".$professor->getAreaAtuacao()."'";
		}

		if (strlen($professor->getEndereco())==0){
			$professor->Xendereco = " NULL ";
		}else{
			$professor->Xendereco = "'".$professor->getEndereco()."'";
		}

		if (strlen($professor->getNumero())==0){
			$professor->Xnumero = " NULL ";
		}else{
			$professor->Xnumero = "'".$professor->getNumero()."'";
		}

		if (strlen($professor->getComplemento())==0){
			$professor->Xcomplemento = " NULL ";
		}else{
			$professor->Xcomplemento = "'".$professor->getComplemento()."'";
		}

		if (strlen($professor->getBairro())==0){
			$professor->Xbairro = " NULL ";
		}else{
			$professor->Xbairro = "'".$professor->getBairro()."'";
		}

		if (strlen($professor->getCidade())==0){
			$professor->Xcidade = " NULL ";
		}else{
			$professor->Xcidade = "'".$professor->getCidade()."'";
		}

		if (strlen($professor->getEstado())==0){
			$professor->Xestado = " NULL ";
		}else{
			$professor->Xestado = "'".$professor->getEstado()."'";
		}

		if (strlen($professor->getCep())==0){
			$professor->Xcep = " NULL ";
		}else{
			$professor->Xcep = "'".$professor->getCep()."'";
		}

		if (strlen($professor->getPais())==0){
			$professor->Xpais = "'BR'";
		}else{
			$professor->Xpais = "'".$professor->getPais()."'";
		}

		$professorDAO = new ProfessorDAO(); 
		$professorDAO->setBancoDados($this->banco); 
		$professorDAO->gravaDadosProfessor($professor); 
	}


	public function recuperarProfessor($professor){
		if (strlen($professor)>0){
			$professorDAO = new ProfessorDAO();
			$professorDAO->setBancoDados($this->banco); 
			return $professorDAO->recuperarProfessor($professor); 
		}else{
			return null;
		}
	}

	public function recuperarProfessorTodosDAO(){
		$ProfessorDAO = new ProfessorDAO();
		$ProfessorDAO->setBancoDados($this->banco); 
		return $ProfessorDAO->recuperarTodos();
	}


#####################################################################################################
#########################                ALUNO               ########################################
#####################################################################################################


	function gravarAluno(Aluno $aluno){

		#if (!is_object($aluno->getInstituicao())){
		#	throw new Exception('Informe a instituição!');
		#}else{
		#	$aluno->Xinstituicao = $aluno->getInstituicao()->getId();
		#}

		if (strlen($aluno->getNome())==0){
			throw new Exception('Informe o nome do aluno!');
		}else{
			$aluno->Xnome = "'".$aluno->getNome()."'";
		}

		if (strlen($aluno->getRa())==0){
			throw new Exception('Informe o RA do aluno!');
		}else{
			$aluno->Xra = "'".$aluno->getRa()."'";
		}

		if (strlen($aluno->getEmail())==0){
			throw new Exception('Informe o email do aluno!');
		}else{
			$aluno->Xemail = "'".$aluno->getEmail()."'";
		}

		if (strlen($aluno->getSenha())==0){
			throw new Exception('Informe a senha do aluno!');
		}else{
			$aluno->Xsenha = "'".$aluno->getSenha()."'";
		}
		
		if (strlen($aluno->getAtivo())==0){
			$aluno->Xativo = " 0 ";
		}else{
			$aluno->Xativo = $aluno->getAtivo();
		}

		if (strlen($aluno->getEndereco())==0){
			$aluno->Xendereco = " NULL ";
		}else{
			$aluno->Xendereco = "'".$aluno->getEndereco()."'";
		}

		if (strlen($aluno->getNumero())==0){
			$aluno->Xnumero = " NULL ";
		}else{
			$aluno->Xnumero = "'".$aluno->getNumero()."'";
		}

		if (strlen($aluno->getComplemento())==0){
			$aluno->Xcomplemento = " NULL ";
		}else{
			$aluno->Xcomplemento = "'".$aluno->getComplemento()."'";
		}

		if (strlen($aluno->getBairro())==0){
			$aluno->Xbairro = " NULL ";
		}else{
			$aluno->Xbairro = "'".$aluno->getBairro()."'";
		}

		if (strlen($aluno->getCidade())==0){
			$aluno->Xcidade = " NULL ";
		}else{
			$aluno->Xcidade = "'".$aluno->getCidade()."'";
		}

		if (strlen($aluno->getEstado())==0){
			$aluno->Xestado = " NULL ";
		}else{
			$aluno->Xestado = "'".$aluno->getEstado()."'";
		}

		if (strlen($aluno->getCep())==0){
			$aluno->Xcep = " NULL ";
		}else{
			$aluno->Xcep = "'".$aluno->getCep()."'";
		}

		if (strlen($aluno->getPais())==0){
			$aluno->Xpais = "'BR'";
		}else{
			$aluno->Xpais = "'".$aluno->getPais()."'";
		}

		$alunoDAO = new AlunoDAO(); 
		$alunoDAO->setBancoDados($this->banco); 
		$alunoDAO->gravaDadosAluno($aluno); 
	}

	function gravarNovaSenhaAluno(Aluno $aluno, $senha_atual, $nova_senha){

		if (strlen($nova_senha)==0) {
			throw new Exception('Digite a senha.');
		}

		if (strlen($nova_senha) < 4 ) {
			throw new Exception('A senha deve ter no mínimo 4 caracteres.');
		}

		if ($aluno->getSenha() <> $senha_atual) {
			throw new Exception('Senha atual não confere!');
		}

		$aluno->setSenha($nova_senha);
		$aluno->Xsenha = "'".$aluno->getSenha()."'";


		$alunoDAO = new AlunoDAO(); 
		$alunoDAO->setBancoDados($this->banco); 
		$alunoDAO->gravarNovaSenhaAluno($aluno); 
	}

	


	public function recuperarAluno($aluno){
		if (strlen($aluno)>0){
			$alunoDAO = new AlunoDAO();
			$alunoDAO->setBancoDados($this->banco); 
			return $alunoDAO->recuperarAluno($aluno); 
		}else{
			return null;
		}
	}

	public function recuperarAlunoTodosDAO($obrigatorio = 'obrigatorio'){
		$AlunoDAO = new AlunoDAO();
		$AlunoDAO->setBancoDados($this->banco); 
		return $AlunoDAO->recuperarTodos($obrigatorio);
	}

	public function recuperarQtdeAlunosDAO(){
		$AlunoDAO = new AlunoDAO();
		$AlunoDAO->setBancoDados($this->banco); 
		return $AlunoDAO->recuperarQtde();
	}

	/* ALUNO X CURSO */
	function recuperarAlunoTodosCursoDAO(Curso $curso){
		$alunoDAO = new AlunoDAO(); 
		$alunoDAO->setBancoDados($this->banco); 
		return $alunoDAO->recuperarTodosAlunoCurso($curso); 
	}

	/* ALUNO X DISCIPLINA */
	function recuperarAlunoDisciplina(Aluno $aluno){
		$alunoDAO = new AlunoDAO(); 
		$alunoDAO->setBancoDados($this->banco); 
		return $alunoDAO->recuperarDadosAlunoDisciplina($aluno); 
	}

	function gravarAlunoDisciplina(Aluno $aluno){
		$alunoDAO = new AlunoDAO(); 
		$alunoDAO->setBancoDados($this->banco); 
		$alunoDAO->apagarDisciplinas($aluno); 
		$alunoDAO->gravaDadosAlunoDisciplina($aluno); 
	}

	public function excluirAluno(Aluno $aluno){
		$alunoDAO = new AlunoDAO();
		$alunoDAO->setBancoDados($this->banco); 
		$alunoDAO->excluirAlunoDAO($aluno);
	}

#####################################################################################################
#########################               COMUNICADO           ########################################
#####################################################################################################


	function gravarComunicado(Comunicado $comunicado){

		if (!is_object($comunicado->getInstituicao())){
			throw new Exception('Instituição é obrigatória');
		}else{
			$comunicado->Xinstituicao = $comunicado->getInstituicao()->getId();
		}
		
		if (!is_object($comunicado->getCurso())){
			$comunicado->Xcurso = " NULL ";
		}else{
			$comunicado->Xcurso = $comunicado->getCurso()->getId();
		}
				
		if (!is_object($comunicado->getProfessor())){
			$comunicado->Xprofessor = " NULL ";
		}else{
			$comunicado->Xprofessor = $comunicado->getProfessor()->getId();
		}

		if (strlen($comunicado->getTitulo())==0){
			throw new Exception('Informe o título do comunicado!');
		}else{
			$comunicado->Xtitulo = "'".$comunicado->getTitulo()."'";
		}

		if (strlen($comunicado->getData())==0){
			throw new Exception('Informe a data do comunicado!');
		}else{
			$comunicado->Xdata = ConverteData($comunicado->getData(),"'");
		}

		if (strlen($comunicado->getComentario())==0){
			throw new Exception('Informe o conteúdo do comunicado');
		}else{
			$comunicado->Xcomentario = "'".$comunicado->getComentario()."'";
		}
		
		if (strlen($comunicado->getObrigatorio())==0){
			$comunicado->Xobrigatorio = " 0 ";
		}else{
			$comunicado->Xobrigatorio = "'".$comunicado->getObrigatorio()."'";
		}

		$comunicadoDAO = new ComunicadoDAO(); 
		$comunicadoDAO->setBancoDados($this->banco); 
		$comunicadoDAO->gravaDadosComunicado($comunicado); 
	}

	public function recuperarComunicado($comunicado){
		if (strlen($comunicado)>0){
			$ComunicadoDAO = new ComunicadoDAO();
			$ComunicadoDAO->setBancoDados($this->banco); 
			return $ComunicadoDAO->recuperarComunicado($comunicado); 
		}else{
			return null;
		}
	}

	public function confirmarLeituraComunicado($comunicado, $aluno){
		if (is_object($comunicado) AND is_object($aluno)){
			$ComunicadoDAO = new ComunicadoDAO();
			$ComunicadoDAO->setBancoDados($this->banco); 
			return $ComunicadoDAO->confirmarLeitura($comunicado, $aluno); 
		}else{
			return null;
		}
	}
	
	public function recuperarComunicadoTodosDAO($filtro = ''){
		$comunicadoDAO = new ComunicadoDAO();
		$comunicadoDAO->setBancoDados($this->banco); 
		return $comunicadoDAO->recuperarTodos($filtro);
	}

	public function excluirComunicado(Comunicado $comunicado){
		$comunicadoDAO = new ComunicadoDAO();
		$comunicadoDAO->setBancoDados($this->banco); 
		$comunicadoDAO->excluirComunicadoDAO($comunicado);
	}

#####################################################################################################
#########################              PERGUNTA            ##########################################
#####################################################################################################

	function gravarPergunta(Pergunta $pergunta){

		if (strlen($pergunta->getTitulo())==0){
			throw new Exception('Informe o título da pergunta!');
		}else{
			$pergunta->Xtitulo = "'".$pergunta->getTitulo()."'";
		}

		if (!is_object($pergunta->getTipoPergunta())){
			throw new Exception('Informe o tipo da pergunta!');
		}else{
			$pergunta->Xtipo_pergunta = $pergunta->getTipoPergunta()->getId();
		}

		if (!is_object($pergunta->getTopico())){
			throw new Exception('Informe o tópico da pergunta!');
		}else{
			$pergunta->Xtopico = $pergunta->getTopico()->getId();
		}

		if (strlen($pergunta->getDificuldade())==0){
			throw new Exception('Selecione a dificuldade da perguta!');
		}else{
			$pergunta->Xdificuldade = $pergunta->getDificuldade();
		}

		if (strlen($pergunta->getFonte())==0){
			$pergunta->Xfonte = " NULL ";
		}else{
			$pergunta->Xfonte = "'".$pergunta->getFonte()."'";
		}

		if (strlen($pergunta->getAtiva())==0){
			$pergunta->Xativa = 0;
		}else{
			$pergunta->Xativa = $pergunta->getAtiva();
		}

		$perguntaDAO = new PerguntaDAO(); 
		$perguntaDAO->setBancoDados($this->banco); 
		$perguntaDAO->gravaDadosPergunta($pergunta); 
		$perguntaDAO->apagarRespostas($pergunta); 
#echo nl2br($pergunta->getQtdeResposta());
#echo "<br>";
		for ($i=0;$i<$pergunta->getQtdeResposta();$i++){
			$pergunta->getResposta($i)->setPergunta($pergunta->getId());
			$this->gravarResposta($pergunta->getResposta($i)); 
		}
	}

	public function recuperarPergunta($pergunta){
		if (strlen($pergunta)>0){
			$perguntaDAO = new PerguntaDAO();
			$perguntaDAO->setBancoDados($this->banco); 
			return $perguntaDAO->recuperarPergunta($pergunta);
		}else{
			return null;
		}
	}

	public function recuperarPerguntaTodosDAO($obrigatorio = 'obrigatorio'){
		$PerguntaDAO = new PerguntaDAO();
		$PerguntaDAO->setBancoDados($this->banco); 
		return $PerguntaDAO->recuperarTodos($obrigatorio);
	}
	
	public function recuperarPerguntaQtdeTodosDAO(){
		$PerguntaDAO = new PerguntaDAO();
		$PerguntaDAO->setBancoDados($this->banco); 
		return $PerguntaDAO->recuperarQtde();
	}

	public function recuperarPerguntaDisciplinaTodosDAO(Disciplina $disciplina){
		$PerguntaDAO = new PerguntaDAO();
		$PerguntaDAO->setBancoDados($this->banco); 
		return $PerguntaDAO->recuperarTodosDisciplina($disciplina);
	}

	public function recuperarPerguntaTopicoDAO(Topico $topico, $dificuldade, $qtde_perguntas = 1, $perguntas_inseridas = array()){
		$PerguntaDAO = new PerguntaDAO();
		$PerguntaDAO->setBancoDados($this->banco); 
		return $PerguntaDAO->recuperarTodosPorTopico($topico->getId(), $dificuldade, $qtde_perguntas, $perguntas_inseridas);
	}

	public function excluirPergunta(Pergunta $pergunta){
		$perguntaDAO = new PerguntaDAO();
		$perguntaDAO->setBancoDados($this->banco); 
		$perguntaDAO->excluirPerguntaDAO($pergunta);
	}

#####################################################################################################
#########################              RESPOTA             ##########################################
#####################################################################################################

	function gravarResposta(Resposta $resposta){

		if (strlen($resposta->getPergunta())==0){
			throw new Exception('Informe a pergunta!');
		}else{
			$resposta->Xpergunta = $resposta->getPergunta();
		}
		
		if (strlen($resposta->getRespostaCorreta())==0){
			$resposta->Xresposta_correta = " NULL ";
		}else{
			$resposta->Xresposta_correta = "'".$resposta->getRespostaCorreta()."'";
		}		

		if (strlen($resposta->getRespostaTexto())==0){
			$resposta->Xresposta_texto = " NULL ";
		}else{
			$resposta->Xresposta_texto = "'".$resposta->getRespostaTexto()."'";
		}

		if (!is_object($resposta->getRespostaFilho())){
			$resposta->Xresposta_filho = " NULL ";
		}else{
			$resposta->Xresposta_filho = $resposta->getRespostaFilho()->getId();
		}

		$respostaDAO = new RespostaDAO(); 
		$respostaDAO->setBancoDados($this->banco); 
		$respostaDAO->gravaDadosResposta($resposta); 
	}

	public function recuperarResposta($resposta){
		if (strlen($resposta)>0){
			$respostaDAO = new RespostaDAO();
			$respostaDAO->setBancoDados($this->banco); 
			return $respostaDAO->recuperarResposta($resposta);
		}else{
			return null;
		}
	}

	public function recuperarRespostaTodosDAO(){
		$RespostaDAO = new RespostaDAO();
		$RespostaDAO->setBancoDados($this->banco); 
		return $RespostaDAO->recuperarTodos();
	}


#####################################################################################################
#########################          PERGUNTA IMAGEM         ##########################################
#####################################################################################################

	function gravarImagem(Imagem $imagem){

		if (strlen($imagem->getPergunta())==0){
			throw new Exception('Informe a pergunta!');
		}else{
			$imagem->Xpergunta = $imagem->getPergunta();
		}

		if (strlen($imagem->getDescricao())==0){
			#throw new Exception('Informe a descricao da imagem!');
			$imagem->Xdescricao = " NULL ";
		}else{
			$imagem->Xdescricao = "'".$imagem->getDescricao()."'";
		}

		if (strlen($imagem->getPath())==0){
			throw new Exception('Selecione a imagem!');
		}else{
			$imagem->Xpath = "'".$imagem->getPath()."'";
		}
		
		if (strlen($imagem->getThumb())==0){
			$imagem->Xthumb = " NULL ";
		}else{
			$imagem->Xthumb = "'".$imagem->getThumb()."'";
		}

		$imagemDAO = new ImagemDAO(); 
		$imagemDAO->setBancoDados($this->banco); 
		$imagemDAO->gravaDadosImagem($imagem); 
	}

	public function recuperarImagem($imagem){
		if (strlen($imagem)>0){
			$imagemDAO = new ImagemDAO();
			$imagemDAO->setBancoDados($this->banco); 
			return $imagemDAO->recuperarImagem($imagem);
		}else{
			return null;
		}
	}

	public function recuperarImagemTodosDAO(){
		$ImagemDAO = new ImagemDAO();
		$ImagemDAO->setBancoDados($this->banco); 
		return $ImagemDAO->recuperarTodos();
	}

	public function recuperarImagemPerguntaTodosDAO(Pergunta $pergunta){
		$ImagemDAO = new ImagemDAO();
		$ImagemDAO->setBancoDados($this->banco); 
		return $ImagemDAO->recuperarTodosPergunta($pergunta);
	}


#####################################################################################################
#########################                 PROVA              ########################################
#####################################################################################################


	function gravarProva(Prova $prova){

		if (strlen($prova->getTitulo())==0){
			throw new Exception('Informe o título da prova!');
		}else{
			$prova->Xtitulo = "'".$prova->getTitulo()."'";
		}

		if (!is_object($prova->getDisciplina())){
			throw new Exception('A disciplina é obrigatória');
		}else{
			$prova->Xdisciplina = $prova->getDisciplina()->getId();
		}

		if (!is_object($prova->getProfessor())){
			throw new Exception('Selecione o professor');
		}else{
			$prova->Xprofessor = $prova->getProfessor()->getId();
		}

		if (strlen($prova->getNumeroPerguntas())==0 or $prova->getNumeroPerguntas() == 0){
			#$prova->Xnumero_perguntas = " NULL ";
			throw new Exception('Informe a quantidade de perguntas para a aprova!');
		}else{
			$prova->Xnumero_perguntas = $prova->getNumeroPerguntas();
		}

		if (strlen($prova->getData())==0){
			throw new Exception('Informe a data da prova!');
		}else{
			$prova->Xdata = ConverteData($prova->getData(),"'");
		}

		if (strlen($prova->getDataInicio())==0){
			throw new Exception('Informe a data de início da prova!');
		}else{
			$prova->Xdata_inicio = ConverteData($prova->getDataInicio(),"'");
		}

		if (strlen($prova->getDataTermino())==0){
			throw new Exception('Informe a data de término da prova!');
		}else{
			$prova->Xdata_termino = ConverteData($prova->getDataTermino(),"'");
		}

		if (strlen($prova->getDificuldade())==0){
			$prova->Xdificuldade = " NULL ";
		}else{
			$prova->Xdificuldade = $prova->getDificuldade();
		}

		if (strlen($prova->getLiberada())==0){
			$prova->Xliberada = " NULL ";
		}else{
			$prova->Xliberada = ConverteData($prova->getLiberada(),"'");
		}

		$provaDAO = new ProvaDAO(); 
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->gravaDadosProva($prova); 
	}


	function gravaDadosProvaPerguntas(Prova $prova){

		if ($prova->getQtdePerguntas()==0){
			throw new Exception('Nenhuma pergunta selecionada');
		}

		$provaDAO = new ProvaDAO(); 
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->gravaDadosProvaPergunta($prova); 
	}

	function gravaDadosProvaTopico(Prova $prova){

		if ($prova->getQtdeTopico()==0){
			throw new Exception('Informe no mínimo 1 tópico');
		}

		$provaDAO = new ProvaDAO(); 
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->gravaDadosProvaTopico($prova); 
	}

	function selecionaPerguntas(Prova $prova){

		if ($prova->getQtdeTopico()==0){
			throw new Exception('Para criar uma prova, é precisa selecionar pelo menos 1 tópico!');
		}

		if ($prova->getNumeroPerguntas()==0){
			throw new Exception('Informe o número de perguntas!');
		}else{
			$prova->Xnumero_perguntas = $prova->getNumeroPerguntas();
		}

		if (strlen($prova->getDificuldade())==0){
			throw new Exception('Informe o nível de dificuldade da prova!');
		}else{
			$prova->Xdificuldade = $prova->getDificuldade();
		}

		$provaDAO = new ProvaDAO(); 
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->selecionaPerguntas($prova); 
	}


	function distruiProvaAluno(Prova $prova){

		$provaDAO = new ProvaDAO(); 
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->distruiProvaAluno($prova); 
	}

	public function recuperarProva($prova){
		if (strlen($prova)>0){
			$provaDAO = new ProvaDAO();
			$provaDAO->setBancoDados($this->banco); 
			return $provaDAO->recuperarProva($prova); 
		}else{
			return null;
		}
	}

	public function recuperarProvaTodosDAO($filtro = ''){
		$provaDAO = new ProvaDAO();
		$provaDAO->setBancoDados($this->banco); 
		return $provaDAO->recuperarTodos($filtro);
	}
	
	public function recuperarProvaQtdeDAO($filtro = ''){
		$provaDAO = new ProvaDAO();
		$provaDAO->setBancoDados($this->banco); 
		return $provaDAO->recuperarQtde($filtro);
	}

	
	public function excluirProva(Prova $prova){
		$provaDAO = new ProvaDAO();
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->excluirProvaDAO($prova);
	}

	public function enviaEmailProvaAluno($filtro = ''){
		$provaDAO = new ProvaDAO();
		$provaDAO->setBancoDados($this->banco); 
		$provaDAO->enviaEmailProvaAlunoDAO($filtro);
	}




#####################################################################################################
#########################          PROVA PERGUNTA          ##########################################
#####################################################################################################

	function gravarProvaPergunta(ProvaPergunta $prova_pergunta){

		if (strlen($prova_pergunta->getTitulo())==0){
			throw new Exception('Informe o título da pergunta!');
		}else{
			$prova_pergunta->Xtitulo = "'".$prova_pergunta->getTitulo()."'";
		}

		if (!is_object($prova_pergunta->getTipoPergunta())){
			throw new Exception('Informe o tipo da pergunta!');
		}else{
			$prova_pergunta->Xtipo_pergunta = $prova_pergunta->getTipoPergunta()->getId();
		}

		if (!is_object($prova_pergunta->getTopico())){
			throw new Exception('Informe o tópico da pergunta!');
		}else{
			$prova_pergunta->Xtopico = $prova_pergunta->getTopico()->getId();
		}

		if (strlen($prova_pergunta->getDificuldade())==0){
			throw new Exception('Selecione a dificuldade da perguta!');
		}else{
			$prova_pergunta->Xdificuldade = $prova_pergunta->getDificuldade();
		}

		if (strlen($prova_pergunta->getFonte())==0){
			$prova_pergunta->Xfonte = " NULL ";
		}else{
			$prova_pergunta->Xfonte = "'".$prova_pergunta->getFonte()."'";
		}
		
		if (strlen($prova_pergunta->getPeso())==0){
			$prova_pergunta->Xpeso = " NULL ";
		}else{
			$prova_pergunta->setPeso( str_replace(",",".",$prova_pergunta->getPeso()) );
			$prova_pergunta->Xpeso = $prova_pergunta->getPeso();
		}

		if (strlen($prova_pergunta->getPerguntaOrigem())==0){
			throw new Exception('Qual origem desta pergunta?');
		}else{
			$prova_pergunta->Xpergunta_origem = $prova_pergunta->getPerguntaOrigem();
		}
		
		if (strlen($prova_pergunta->getProvaId())==0){
			throw new Exception('Problema ao identificar a oritem da prova. Tente novamente.');
		}else{
			$prova_pergunta->Xprova_id = $prova_pergunta->getProvaId();
		}

		$provaPerguntaDAO = new ProvaPerguntaDAO(); 
		$provaPerguntaDAO->setBancoDados($this->banco); 
		$provaPerguntaDAO->gravaDadosProvaPergunta($prova_pergunta); 

#echo "<br><br>Pergunta: (".$prova_pergunta->getPerguntaOrigem().")";
#echo "<br>Qtde Respostas: ".$prova_pergunta->getQtdeResposta();
/*
if ( $prova_pergunta->getPerguntaOrigem() == '3'){
	echo "<br><br>";
	print_r( $prova_pergunta->getResposta(0) );
	echo "<br><br>";
	print_r( $prova_pergunta->getResposta(1) );
	echo "<br><br>";
	print_r( $prova_pergunta->getResposta(2) );
	echo "<br><br>";
	print_r( $prova_pergunta->getResposta(3) );
	echo "<br><br>";
	print_r( $prova_pergunta->getResposta(4) );
	echo "<br><br>";
}
*/
		for ($i=0;$i<$prova_pergunta->getQtdeResposta();$i++){
			$prova_pergunta->getResposta($i)->setPergunta($prova_pergunta->getId());

			if (is_object($prova_pergunta->getResposta($i)->getRespostaFilho())) {
				$resposta_filho = new Resposta();
				$resposta_filho->setId($prova_pergunta->getResposta($i)->getRespostaFilho()->getId());
				$resposta_filho->setPergunta($prova_pergunta->getId());
				$resposta_filho->setRespostaTexto($prova_pergunta->getResposta($i)->getRespostaFilho()->getRespostaTexto());
				$resposta_filho->setRespostaCorreta($prova_pergunta->getResposta($i)->getRespostaFilho()->getRespostaCorreta());
				#$resposta_filho->setRespostaFilho($prova_pergunta->getResposta($i)->getRespostaFilho()->getId());
				$resposta_filho = $this->gravarProvaResposta($resposta_filho); 
				$prova_pergunta->getResposta($i)->setRespostaFilho($resposta_filho);
			}
			$this->gravarProvaResposta($prova_pergunta->getResposta($i)); 
		}
		$this->atualizarOrdemResposta($prova_pergunta);
	}

	public function atualizarOrdemResposta($prova_pergunta){
		$provaPerguntaDAO = new ProvaPerguntaDAO();
		$provaPerguntaDAO->setBancoDados($this->banco); 
		$provaPerguntaDAO->atualizarOrdemRespostaDAO($prova_pergunta);
	}

	public function recuperarProvaPergunta($prova_pergunta){
		if (strlen($prova_pergunta)>0){
			$provaPerguntaDAO = new ProvaPerguntaDAO();
			$provaPerguntaDAO->setBancoDados($this->banco); 
			return $provaPerguntaDAO->recuperarProvaPergunta($prova_pergunta);
		}else{
			return null;
		}
	}

	public function recuperarProvaPerguntaTodosDAO($obrigatorio = 'obrigatorio'){
		$provaPerguntaDAO = new ProvaPerguntaDAO();
		$provaPerguntaDAO->setBancoDados($this->banco); 
		return $provaPerguntaDAO->recuperarTodos($obrigatorio);
	}

#####################################################################################################
#########################           PROVA RESPOTA          ##########################################
#####################################################################################################

	function gravarProvaResposta(Resposta $resposta){

		if (strlen($resposta->getPergunta())==0){
			throw new Exception('Informe a pergunta!');
		}else{
			$resposta->Xpergunta = $resposta->getPergunta();
		}
		
		if (strlen($resposta->getRespostaCorreta())==0){
			$resposta->Xresposta_correta = " NULL ";
		}else{
			$resposta->Xresposta_correta = "'".$resposta->getRespostaCorreta()."'";
		}		

		if (strlen($resposta->getRespostaTexto())==0){
			$resposta->Xresposta_texto = " NULL ";
		}else{
			$resposta->Xresposta_texto = "'".$resposta->getRespostaTexto()."'";
		}

		if (!is_object($resposta->getRespostaFilho())){
			$resposta->Xresposta_filho = " NULL ";
		}else{
			$resposta->Xresposta_filho = $resposta->getRespostaFilho()->getId();
		}

		$provaRespostaDAO = new ProvaRespostaDAO(); 
		$provaRespostaDAO->setBancoDados($this->banco); 
		return $provaRespostaDAO->gravaDadosProvaResposta($resposta); 
	}

	public function recuperarProvaResposta($resposta){
		if (strlen($resposta)>0){
			$provaRespostaDAO = new ProvaRespostaDAO();
			$provaRespostaDAO->setBancoDados($this->banco); 
			return $provaRespostaDAO->recuperarProvaResposta($resposta);
		}else{
			return null;
		}
	}

	public function recuperarProvaRespostaTodosDAO(){
		$provaRespostaDAO = new ProvaRespostaDAO();
		$provaRespostaDAO->setBancoDados($this->banco); 
		return $provaRespostaDAO->recuperarTodos();
	}


#####################################################################################################
#########################       PROVA PERGUNTA IMAGEM      ##########################################
#####################################################################################################

	function gravarProvaImagem(Imagem $imagem){

		if (strlen($imagem->getPergunta())==0){
			throw new Exception('Informe a pergunta!');
		}else{
			$imagem->Xpergunta = $imagem->getPergunta();
		}

		if (strlen($imagem->getDescricao())==0){
			#throw new Exception('Informe a descricao da imagem!');
			$imagem->Xdescricao = " NULL ";
		}else{
			$imagem->Xdescricao = "'".$imagem->getDescricao()."'";
		}

		if (strlen($imagem->getPath())==0){
			throw new Exception('Selecione a imagem!');
		}else{
			$imagem->Xpath = "'".$imagem->getPath()."'";
		}
		
		if (strlen($imagem->getThumb())==0){
			$imagem->Xthumb = " NULL ";
		}else{
			$imagem->Xthumb = "'".$imagem->getThumb()."'";
		}

		$provaImagemDAO = new ProvaImagemDAO(); 
		$provaImagemDAO->setBancoDados($this->banco); 
		$provaImagemDAO->gravaDadosImagem($imagem); 
	}

	public function recuperarProvaImagem($imagem){
		if (strlen($imagem)>0){
			$provaImagemDAO = new ProvaImagemDAO();
			$provaImagemDAO->setBancoDados($this->banco); 
			return $provaImagemDAO->recuperarImagem($imagem);
		}else{
			return null;
		}
	}

	public function recuperarProvaImagemTodosDAO(){
		$provaImagemDAO = new ProvaImagemDAO();
		$provaImagemDAO->setBancoDados($this->banco); 
		return $provaImagemDAO->recuperarTodos();
	}

	public function recuperarProvaImagemPerguntaTodosDAO(Pergunta $prova_pergunta){
		$provaImagemDAO = new ProvaImagemDAO();
		$provaImagemDAO->setBancoDados($this->banco); 
		return $provaImagemDAO->recuperarTodosPergunta($prova_pergunta);
	}


#####################################################################################################
#########################         PROVA ALUNO RESPOTA       #########################################
#####################################################################################################


	public function recuperarProvaRespondida($prova,$aluno){
		if (strlen($prova)>0){
			$provaRespondidaDAO = new ProvaRespondidaDAO();
			$provaRespondidaDAO->setBancoDados($this->banco); 
			return $provaRespondidaDAO->recuperarProvaRespondidaDAO($prova,$aluno); 
		}else{
			return null;
		}
	}

	function gravaDadosProvaRespondida(ProvaRespondida $prova){

		if (strlen($prova->getDataInicio())==0){
			$prova->Xdata_inicio = ' NULL ';
		}else{
			$prova->Xdata_inicio = ConverteData($prova->getDataInicio(),"'");
		}

		if (strlen($prova->getDataTermino())==0){
			$prova->Xdata_termino = ' CURRENT_TIMESTAMP ';
		}else{
			$prova->Xdata_termino = ConverteData($prova->getDataTermino(),"'");
		}

		if (strlen($prova->getNota())==0){
			$prova->Xnota = " NULL ";
		}else{
			$prova->Xnota = $prova->getNota();
		}

		$prova->Xaluno = $prova->getAluno()->getId();

		$provaRespondidaDAO = new ProvaRespondidaDAO(); 
		$provaRespondidaDAO->setBancoDados($this->banco); 
		$provaRespondidaDAO->gravaDadosProvaRespondidaDAO($prova); 
	}

	function gravaDadosProvaRespondidaDataInicio(ProvaRespondida $prova){

		if (strlen($prova->getDataInicio())==0){
			$prova->Xdata_inicio = ' NULL ';
		}else{
			$prova->Xdata_inicio = ConverteData($prova->getDataInicio(),"'");
		}

		$prova->Xaluno = $prova->getAluno()->getId();

		$provaRespondidaDAO = new ProvaRespondidaDAO(); 
		$provaRespondidaDAO->setBancoDados($this->banco); 
		$provaRespondidaDAO->gravaDadosProvaRespondidaDataInicioDAO($prova); 
	}

	function gravaDadosProvaPerguntaRespondida(ProvaRespondida $prova){
		$provaRespondidaDAO = new ProvaRespondidaDAO(); 
		$provaRespondidaDAO->setBancoDados($this->banco); 
		$provaRespondidaDAO->gravaDadosProvaPerguntaRespondidaDAO($prova); 
	}

	function provaCorrigir(ProvaRespondida $prova){
		$provaRespondidaDAO = new ProvaRespondidaDAO(); 
		$provaRespondidaDAO->setBancoDados($this->banco); 
		$provaRespondidaDAO->provaCorrigirDAO($prova); 
	}

	function gravaProvaCorrigir(ProvaRespondida $prova){
#var_dump($prova);
#echo "Nota::::::::::::::::(".$prova->getNota()."):::";
		if (strlen($prova->getNota())==0) {
			$prova->Xnota = ' NULL ';
		}else{
			$prova->Xnota = $prova->getNota();
		}

		if (strlen($prova->getNotaLiberada())==0){
			$prova->Xnota_liberada = ' NULL ';
		}else{
			$prova->Xnota_liberada = ConverteData($prova->getNotaLiberada(),"'");
		}

		$provaRespondidaDAO = new ProvaRespondidaDAO(); 
		$provaRespondidaDAO->setBancoDados($this->banco); 
		$provaRespondidaDAO->gravaProvaCorrigirDAO($prova); 
	}

#####################################################################################################
#########################        PROVA ALUNO CORRECAO       #########################################
#####################################################################################################

	function recuperarProvaCorrecao($prova){
		if (strlen($prova)>0){
			$provaCorrecaoDAO = new ProvaCorrecaoDAO(); 
			$provaCorrecaoDAO->setBancoDados($this->banco); 
			return $provaCorrecaoDAO->recuperarProvaCorrecaoDAO($prova); 
		}else{
			return null;
		}
	}

	function provaCorrecaoGravarPerguntaNota($prov_correcao,$prova_pergunta,$prova_aluno,$pergunta_nota){
		$provaCorrecaoDAO = new ProvaCorrecaoDAO(); 
		$provaCorrecaoDAO->setBancoDados($this->banco); 
		$provaCorrecaoDAO->provaCorrecaoGravarPerguntaNotaDAO($prov_correcao,$prova_pergunta,$prova_aluno,$pergunta_nota);
	}

	
#####################################################################################################
#########################               PESQUISA            #########################################
#####################################################################################################

	function gravarPesquisa($pesquisa){

		
		if (is_object($pesquisa->getProfessor())) {
			$pesquisa->Xprofessor   = $pesquisa->getProfessor()->getId();
			$pesquisa->Xinstituicao = $pesquisa->getProfessor()->getInstituicao()->getId();
		}else{
			$pesquisa->Xprofessor   = ' null ';
			$pesquisa->Xinstituicao = ' null ';
		}

		if (is_object($pesquisa->getAluno())) {
			$pesquisa->Xaluno = $pesquisa->getAluno()->getId();
		}else{
			$pesquisa->Xaluno = ' null ';
		}

		if (strlen($pesquisa->getData())==0){
			$pesquisa->Xdata = ' CURRENT_TIMESTAMP ';
		}else{
			$pesquisa->Xdata = ConverteData($pesquisa->getData(),"'");
		}

		$PesquisaDAO = new PesquisaDAO(); 
		$PesquisaDAO->setBancoDados($this->banco); 
		$PesquisaDAO->gravarPesquisaDAO($pesquisa); 
	}


	public function recuperarPesquisa($pesquisa){
		if (strlen($pesquisa)>0){
			$pesquisaDAO = new PesquisaDAO();
			$pesquisaDAO->setBancoDados($this->banco); 
			return $pesquisaDAO->recuperarPesquisa($pesquisa); 
		}else{
			return null;
		}
	}

	public function recuperarPesquisaTodos($filtro = null){
		$pesquisaDAO = new PesquisaDAO();
		$pesquisaDAO->setBancoDados($this->banco); 
		return $pesquisaDAO->recuperarPesquisaTodosDAO($filtro);
	}
	
	public function recuperarFazerPesquisa($filtro = null){
		$pesquisaDAO = new PesquisaDAO();
		$pesquisaDAO->setBancoDados($this->banco); 
		return $pesquisaDAO->recuperarFazerPesquisaDAO($filtro);
	}
	
#####################################################################################################
#########################               RELATORIO           #########################################
#####################################################################################################


	function recuperarRelatorioAcesso($relatorio){
		$relatorioDAO = new RelatorioDAO(); 
		$relatorioDAO->setBancoDados($this->banco); 
		return $relatorioDAO->recuperarRelatorioAcessoDAO($relatorio); 
	}

	function recuperarRelatorioProva($relatorio){
		$relatorioDAO = new RelatorioDAO(); 
		$relatorioDAO->setBancoDados($this->banco); 
		return $relatorioDAO->recuperarRelatorioProvaDAO($relatorio); 
	}	

#####################################################################################################
#########################               DIVULGACAO          #########################################
#####################################################################################################


	function enviarEmail($divulgacao){
		$divulgacaoDAO = new DivulgacaoDAO(); 
		$divulgacaoDAO->setBancoDados($this->banco); 
		return $divulgacaoDAO->enviarEmailDAO($divulgacao); 
	}

	function divulgacaoAcesso($email){
		$divulgacaoDAO = new DivulgacaoDAO(); 
		$divulgacaoDAO->setBancoDados($this->banco); 
		return $divulgacaoDAO->divulgacaoAcessoDAO($email); 
	}

	function divulgacaoPesquisa($email){
		$divulgacaoDAO = new DivulgacaoDAO(); 
		$divulgacaoDAO->setBancoDados($this->banco); 
		return $divulgacaoDAO->divulgacaoPesquisaDAO($email); 
	}

	function recuperarNomePesquisa($email){
		$divulgacaoDAO = new DivulgacaoDAO(); 
		$divulgacaoDAO->setBancoDados($this->banco); 
		return $divulgacaoDAO->recuperarNomePesquisaDAO($email); 
	}

}
?>