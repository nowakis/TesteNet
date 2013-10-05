<?
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/html; charset=iso-8859-1",true);
require_once "www/funcoes.php";
require_once "www/class/class.SessionFacade.php";
require_once "www/banco.con.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TesteNet! Provas On-Line</title>
<link rel="stylesheet" type="text/css" href="estilo.css" media="screen" />
</head>

<body>

<?

if (isset($_GET['divulgacao']) AND strlen($_GET['divulgacao'])>0){

	if (isset($_GET['email']) AND strlen($_GET['email'])>0){
		$email = $_GET['email'];
		$sessionFacade->divulgacaoAcesso($email);
	}
}

if (isset($_GET['msg_erro']) AND strlen($_GET['msg_erro'])>0){
	$msg_erro = trim($_GET['msg_erro']);
	echo "<script language='JavaScript'>";
	if ($msg_erro=="404"){
		echo 'alert("Usuario inativo. \n\nEntre em contato com o supervisor de sua instituicao.");';
	}
	if ($msg_erro=='402'){
		echo 'alert("Login ou senha incorretos. Tente novamente. \n\nCaso tenha esquecido a senha, entre em contato com o supervisor de sua instituicao");';
	}
	if ($msg_erro=='501'){
		echo 'alert("Erro de autenticacao. Por motivos de segurança sera necessario logar novamente no sistema.");';
	}
	if ($msg_erro=='502'){
		echo 'alert("Erro de autenticacao. Por motivos de seguranca sera necessario logar novamente no sistema.");';
	}
	if ($msg_erro=='503'){
		echo 'alert("Seu dados foram atenticados, mas houve um erro no carregamento. Tente novamente. Se o problema persistir, contate o seu surpevisor.");';
	}
	echo "</script>";
}
?>


<div id="box">

<div id="head"></div>

<div id="content">

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td  width="10" valign="top" background="imagens/fundo_esquerda_aux.jpg">
			<img src="imagens/fundo_lado_inteira_esquerdo.jpg" />
		</td>
		<td valign="top" width="767">
			<img src="imagens/logo_fotos-07.jpg" USEMAP="#map1" border=0/>
				<MAP NAME="map1">
					<AREA HREF="index.php"    ALT="Início"      TITLE="Início"      SHAPE=RECT COORDS="22, 215,108,236">
					<AREA HREF="cadastro.php" ALT="Cadastre-se" TITLE="Cadastre-se" SHAPE=RECT COORDS="111,215,218,236">
					<AREA HREF="ajuda.php"    ALT="Ajuda"       TITLE="Ajuda"       SHAPE=RECT COORDS="198,215,275,236">
					<AREA HREF="ajuda.php"    ALT="Ajuda"       TITLE="Ajuda"       SHAPE=RECT COORDS="198,215,275,236">
					<AREA HREF="index.php"    ALT="Início"      TITLE="Início"      SHAPE=RECT COORDS="24,22,68,36">
					<AREA HREF="contato.php"  ALT="Contato"     TITLE="Contato"     SHAPE=RECT COORDS="90,24,150,36">
				</MAP>

			<div id='conteudo'>
				<div id='conteudo_direita'>
					<div id='conteudo_login'>
						<h1>Login</h1>
						<p>Digite abaixo o seu login e senha de acesso</p>

						<form method='POST' action='www/login.php' name='frm_login'>
							<p>Login:<br>
							<input type="text" name="login" value='<?=$_GET['login'];?>'>
							</p>
							<p>Senha:<br>
							<input type="password" name="senha">
							</p>
							<p><input type="submit" name='Acessar' value='Acessar'></p>
						</form>
						<p>Sem cadastro? <a href='cadastro.php'>Cadastre-se</a></p>
					</div>
				</div>
				<div id='conteudo_meio'>
					<h1>C&oacute;digo Aberto</h1>
					<h3>Por F&aacute;bio Nowaki em 28/10/2009</h3>
					<p>Foi disponibilizado o c&oacute;digo fonte do sistem TesteNet.</p>
					<p>Para mais informa&ccedil;&otilde;es e fazer o download do sistema, <a href='http://blog.nowakis.com/2009/07/19/testenet-sistema-de-provas-on-line/' target='_blank'>clique aqui</a></p>
					<hr>
					<h1>Sistema TesteNet</h1>
					<h3>Por F&aacute;bio Nowaki em 10/02/2009</h3>
					<p>Gerencie suas provas e testes de maneira f&aacute;cil e r&aacute;pido. O Sistema TesteNet &eacute; <b>GRATU&Iacute;TO</b> e est&aacute; dispon&iacute;vel para professores e institui&ccedil;&otilde;es.</p>
					<p>Confira as principais caracter&iacute;sticas do TesteNet:</p>
					<p>
					<ul>
						<li>Cadastro de Cursos, Disciplinas e T&oacute;picos</li>
						<li>Cadastro de Alunos completo</li>
						<li>Cadastro de Perguntas e Respostas</li>
						<li>5 Tipos de Perguntas: dissertativa, m&uacute;ltipla-escolha, verdadeiro ou falso, relacione e complete</li>
						<li>Agendamento de Provas: 2 maneiras f&aacute;ceis e r&aacute;pido</li>
						<li>Corre&ccedil;&atilde;o autom&aacute;tica das provas</li>
						<li>Envio do resultado para os Alunos por email, autom&aacute;tico!</li>
						<li>Gerenciamento de comunicados</li>
						<li>Relat&oacute;rios de provas e frequ&ecirc;ncias</li>
					</ul>
					</p>
					<p>Fa&ccedil;a seu cadastro! <a href='cadastro.php'>Clique aqui</a></p>
					<hr>
					<!--
					<h1>Liberado Vers&atilde;o de Testes do Sistema TesteNet</h1>
					<h3>Por F&aacute;bio Nowaki em 19/05/2008</h3>
					<p>Liberado Vers&atilde;o de Testes do Sistema TesteNet:</p>
					<p>
					<ul>
						<li>M&oacute;dulo  Professor liberado com as princiais funcionalidades</li>
						<li>Multi institui&ccedil;&atilde;o</li>
						<li>Multi professor</li>
					</ul>
					</p>
					<p>Em desenvolvimento final:</p>
					<ul>
						<li>M&oacute;dulo Institui&ccedil;&atilde;o</li>
						<li>M&oacute;dulo Aluno</li>
					</ul>
					<p>Fa&ccedil;a seu cadastro! <a href='cadastro.php'>Clique aqui</a></p>
					<hr>
					<h1>Lan&ccedil;ado Novo Visual TesteNet</h1>
					<h3>Por F&aacute;bio Nowaki em 16/05/2008</h3>
					<p>Lan&ccedil;ado um novo visual para o Sistema TesteNet. O objetivo &eacute; deixar o sistema mais amig&aacute;vel ao usu&aacute;rio.</p>
					<p> -->
					<br>
					<br>
					<br>
			</div>
		</td>
		<td  width="10" valign="top" background="imagens/fundo_direita_aux.jpg">
			<img src="imagens/fundo_lado_inteira_direita.jpg" />
		</td>
	</tr>
	<tr>
		<td colspan="3"><img src="imagens/fundo_tabela.jpg" /></td>
	</tr>
	</table>
</div>

<br clear="all" />
<br clear="all" />
</div>
		<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			try {
			var pageTracker = _gat._getTracker("UA-5848095-2");
			pageTracker._trackPageview();
			} catch(err) {}
		</script>
</body>
</html>
