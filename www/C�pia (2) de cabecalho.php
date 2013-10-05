<?php

	include_once "class.Template.inc.php";
	require_once('banco.inc.php');
	include_once "funcoes.php";
	

	$theme = ".";
	$model = new Template($theme);
	$model->set_filenames(array('cabecalho' => 'cabecalho.htm'));

			 

	$model->assign_vars(array('MENU_1' => "plain"));								
	$model->assign_vars(array('MENU_2' => "plain"));
	$model->assign_vars(array('MENU_3' => "plain"));
	$model->assign_vars(array('MENU_4' => "plain"));
	$model->assign_vars(array('MENU_5' => "plain"));
				
	$model->assign_block_vars('sub_titulo', array());  
	if (isset($layout)){
		switch ($layout){
				case "cadastro": 
						$model->assign_vars(array('IMG_CADASTRO' => "1"));		
						$model->assign_vars(array('MENU_1' => "current"));								
						$model->assign_block_vars('sub_titulo.sub_cadastro', array());  
						break;
				case "movimento": 
						$model->assign_vars(array('IMG_MOVIMENTO' => "1"));			
						$model->assign_vars(array('MENU_2' => "current"));														
						$model->assign_block_vars('sub_titulo.sub_movimento', array());  						
						break;
				case "gerencia": 
						$model->assign_vars(array('IMG_GERENCIA' => "1"));		
						$model->assign_vars(array('MENU_3' => "current"));														
						$model->assign_block_vars('sub_titulo.sub_gerencia', array());  						
						break;
				case "relatorio": 
						$model->assign_vars(array('IMG_RELATORIO' => "1"));		
						$model->assign_vars(array('MENU_4' => "current"));														
						$model->assign_block_vars('sub_titulo.sub_relatorio', array());  						
						break;
				case "financeiro": 
						$model->assign_vars(array('IMG_FINANCEIRO' => "1"));		
						$model->assign_vars(array('MENU_5' => "current"));														
						$model->assign_block_vars('sub_titulo.sub_financeiro', array());  						
						break;
		}
		
	}
				
	$model->assign_vars(array('TITULO'	 => $titulo));		
	$model->assign_vars(array('SUBTITULO'	 => $sub_titulo));			
	
	$model->assign_vars(array('LOGIN'	 => "Login: Fabio Nowaki"));		
	$model->assign_vars(array('DATA'	 => date("d \d\e M \d\e Y - H:i:s")));
	
	$model->pparse('cabecalho');
	
	if (isset($_SESSION['login_fazenda'])){
		$login_fazenda=$_SESSION['login_fazenda'];
	}

?>




