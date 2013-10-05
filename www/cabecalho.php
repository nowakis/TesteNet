<?php
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	date_default_timezone_set("America/Sao_Paulo");
	include_once "class/class.Template.inc.php";
	include_once "funcoes.php";

	$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

	if (strpos($_nome_programa,'comunicado') === false) {
		if (strlen($_login_aluno)>0){
			$comunicados = $sessionFacade->recuperarComunicadoTodosDAO('obrigatorio');
			if (count($comunicados)>0){
				header("Location: comunicado.lista.aluno.php?filtro=obrigatorio");
				exit;
			}
		}
	}


	include_once "class/Easy_Mail.class.php";


	include_once "class/class.Erro.php"; 
		
	$theme = ".";
	$model = new Template($theme);
	$model->set_filenames(array('cabecalho' => 'cabecalho.htm'));

	$model->assign_block_vars('sub_titulo', array());  
	if (isset($layout)){
		switch ($layout){
				case "inicio": 
						$model->assign_vars(array('SUB_MENU_ATIVO' => "1"));		
//						$model->assign_vars(array('MENU_1' => "current"));								
//						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  
						break;
				case "cadastro": 
						$model->assign_vars(array('SUB_MENU_ATIVO' => "2"));		
//						$model->assign_vars(array('MENU_1' => "current"));								
//						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  
						break;						
				case "movimento": 
						$model->assign_vars(array('SUB_MENU_ATIVO' => "3"));		
//						$model->assign_vars(array('MENU_1' => "current"));								
//						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  				
						break;
				case "gerencia": 
						$model->assign_vars(array('SUB_MENU_ATIVO' => "4"));		
//						$model->assign_vars(array('MENU_1' => "current"));								
//						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  					
						break;
				case "relatorio": 
						$model->assign_vars(array('SUB_MENU_ATIVO' => "5"));		
//						$model->assign_vars(array('MENU_1' => "current"));								
//						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  					
						break;
				case "financeiro": 
						$model->assign_vars(array('SUB_MENU_ATIVO' => "6"));		
//						$model->assign_vars(array('MENU_1' => "current"));								
//						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  					
						break;
				default: $model->assign_vars(array('SUB_MENU_ATIVO' => "0"));		
		}
		
	}

	$model->assign_block_vars('menu', array());  

	if (strlen($_login_professor)>0){
		$model->assign_block_vars('menu.menu_professor', array());
	}

	if (strlen($_login_aluno)>0){
		$model->assign_block_vars('menu.menu_aluno', array());
	}

	$model->assign_vars(array('TITULO'		=> $titulo));
	$model->assign_vars(array('SUBTITULO'	=> $sub_titulo));
	$model->assign_vars(array('LOGIN_NOME'	=> $_login_nome));
	$model->assign_vars(array('LOGIN_EMAIL'	=> $_login_email));
	$model->assign_vars(array('DATA'		=> $_login_data_logado));
	$model->assign_vars(array('INSTITUICAO'	=> $_login_instituicao));
	
	try {
		$instituicoes = $sessionFacade->recurarInstituicaoTodosDAO();
		if (count($instituicoes)==1){
			 $model->assign_vars(array('INSTITUICAO_NOME' => $instituicoes[0]->getNome()));
		}else{
			$model->assign_block_vars('nome_instituicao', array());
			for($i= 0; $i < count($instituicoes); $i++) { 
				$model->assign_block_vars('nome_instituicao.instituicao', array(	'INSTITUICAO'	=>	$instituicoes[$i]->getId(),
																					'NOME'			=>	$instituicoes[$i]->getNome(),
																					'SELECTED'		=>	($instituicoes[$i]->getId() == $_login_instituicao)?"SELECTED":""
																				));
			}
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}

	$model->pparse('cabecalho');
	
?>