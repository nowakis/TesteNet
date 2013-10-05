<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<meta name="description" content="description"/>
<meta name="keywords" content="keywords"/> 
<meta name="author" content="author"/> 
<link rel="stylesheet" type="text/css" href="default.css"/>
<title>Nowakis Solutions</title>
<script language="javascript" type="text/javascript" defer='defer'>
	document.frm_login.login.focus();
</script>
</head>

<body>

<div class="main">

	<div class="main_left">
		
		<div class="header">
			<h1><a href="index.htm">TesteNet</a></h1>
		</div>

		<div class="link_menu">
			<a href="#" accesskey="1">TesteNet!</a>
		</div>

		<div class="content">
			
			<h1>Login</h1>
			<div class="descr">Acesso ao sistema TesteNet</div>

			<?
				if (isset($_GET["login"])) {
					$login = trim($_GET["login"]);
				}
				if (isset($_GET["msg_erro"])) {
					$msg_erro = trim($_GET["msg_erro"]);
					if ($msg_erro == "402"){
						echo "<p>Usuário ou senha incorretos.</p>";
					}elseif($msg_erro == "404"){
						echo "<p>Usuário inativo. Contate o Suporte</p>";
					}else{
						echo "<p>Erro desconhecido</p>";
					}
				}
			?>
			<p>Digite abaixo o seu login e senha de acesso</p>

			<form method='POST' action='/testenet/www/login.php' name='frm_login'>
			<p>Login:<br>
			<input type="text" name="login" value='<?=$login?>'>
			</p>
			<p>Senha:<br>
			<input type="password" name="senha">
			</p>

			<p><input type="submit" name='Acessar' value='Acessar'></p>
			</form>
					
		</div>

	</div>

	<div class="footer">
		<div class="left">&copy; <a href="index.html">Website</a> 2008. Fabio Nowaki <a href="http://fabio.nowakis.com">Fabio</a></div>
	</div>

</div>

</body>

</html>