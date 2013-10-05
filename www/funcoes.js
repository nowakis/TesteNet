
function AtivarDesativaStar(dados,cod){
	var retorno = loadXMLDocStar('cadastro.animal.lista.php?star='+dados+'&codigo='+cod);

	if (retorno==0){	
		if (document.getElementById('image_star'+cod).alt== "1"){
				document.getElementById('image_star'+cod).alt = "0";
				document.getElementById('image_star'+cod).src = "imagens/velhos/star_0_2.gif";
				document.getElementById('image_star'+cod).title = "Marcar";
		}
		else{
			document.getElementById('image_star'+cod).alt = "1";
			document.getElementById('image_star'+cod).src = "imagens/velhos/star_1_2.gif";
			document.getElementById('image_star'+cod).title = "Desmarcar";
		}
	} else alert("Erro: não foi possível marcar este animal. Tente novamente.");
}

function perguntar(var1)
{
	if(confirm(var1)){
		return true
	}
	else return false;
}

function MostraEsconde(componente,cor,destino)
{
	if (document.getElementById)
	{
		// this is the way the standards work
		var style2 = document.getElementById(componente).style;
		var td = document.getElementById(destino);		
		if (style2.display=="block"){
			style2.display = "none";
			td.className=cor;
		}
		else {
			style2.display = "block";
			td.className='classe_3';
		}

	}
}
function MostraEsconde2(componente)
{
	if (document.getElementById)
	{
		// this is the way the standards work
		var style2 = document.getElementById(componente).style;
		if (style2.display=="block"){
			style2.display = "none";
		}
		else {
			style2.display = "block";
		}

	}
}

function servOC(i, href, nColor) {
  var trObj = (document.getElementById) ? document.getElementById('ihtr' + i) : eval("document.all['ihtr" + i + "']");
  var nameObj = (document.getElementById) ? document.getElementById('name' + i) : eval("document.all['name" + i + "']");
  var ifObj = (document.getElementById) ? document.getElementById('ihif' + i) : eval("document.all['ihif" + i + "']");
  if (trObj != null) {
    if (trObj.style.display=="none") {
      ifObj.style.height = "0px";
      trObj.style.display="";
      nameObj.style.background="#ECECD9";
      if (!ifObj.src) ifObj.src = href;
      smoothHeight('ihif' + i, 0, 210, 42, 'o');
    }
    else {
      nameObj.style.background=nColor;
      smoothHeight('ihif' + i, 210, 0, 42, 'ihtr' + i);
    }
  }
}


function createRequestObject(){
		var request_;
		var browser = navigator.appName;
		if(browser == "Microsoft Internet Explorer"){
			request_ = new ActiveXObject("Microsoft.XMLHTTP");
		}else{
			request_ = new XMLHttpRequest();
		}
		return request_;
}
		
var http = new Array();
	
function getInfo(animal,id){
	var curDateTime = new Date();
	var area = document.getElementById(id);	
	http[curDateTime] = createRequestObject();
	http[curDateTime].open('get', 'cadastro.animal.lista.php?detalhes='+animal);
	http[curDateTime].onreadystatechange = function(){
		if (http[curDateTime].readyState == 1) {
			area.innerHTML   = "<center><b style='font-weight:normal;font-size:10px;color:#666'>Aguarde....Carregando...<br></b><img src='imagens/loading2.gif'></center>";
		}		
		if (http[curDateTime].readyState == 4) {
			if (http[curDateTime].status == 200 || http[curDateTime].status == 304) {
				var response = http[curDateTime].responseText;
				area.innerHTML = response;
			}
		}
	}
	http[curDateTime].send(null);
}

function carregar_animais(lote,id){
	var curDateTime = new Date();
	var area = document.getElementById(id);	
	http[curDateTime] = createRequestObject();
	http[curDateTime].open('get', 'movimento.vacina.vacinar_lote.ajax.php?lote='+lote);
	http[curDateTime].onreadystatechange = function(){
		if (http[curDateTime].readyState == 1) {
			area.innerHTML   = "<center><b style='font-weight:normal;font-size:10px;color:#666'>Aguarde....Carregando...<br></b><img src='imagens/loading2.gif'></center>";
		}		
		if (http[curDateTime].readyState == 4) {
			if (http[curDateTime].status == 200 || http[curDateTime].status == 304) {
				var response = http[curDateTime].responseText;
				area.innerHTML = response;
			}
		}
	}
	http[curDateTime].send(null);
}
		

		
			
function go(){
	getInfo();
	window.setTimeout("go()", 2000);
}
			
			
// checkbox
function CheckBox_selecionarTudo(form2){ 
   for (i=0;i<form2.elements.length;i++){ 
      if(form2.elements[i].type == "checkbox") 
         if (form2.elements[i].checked==1) 
            form2.elements[i].checked=0;
		else
            form2.elements[i].checked=1;
   }
} 			

function criarLote()
  {
  var name=prompt("Digite o Nome do Lote","Lote")
  if (name!=null && name!="")
    {
	alert(name);
	}
  }

function pesquisa_animal (filtro, valor,cabecalho) {
    var url = "";
    if (filtro != "" && valor != "") {
        url = "movimento.vacina.vacinar_lote.ajax.php?"+filtro+"="+valor+"&cabecalho="+cabecalho;
        var janela = window.open(url,"janela","toolbar=no,location=no,status=no,scrollbars=yes,directories=no,width=770,height=500,top=18,left=0");
        janela.focus();
    }
}

function selecionarLinha(id,checke){
	var lin = document.getElementById(id);
	var com = document.getElementById(checke);	
	if ((com) && (lin)){
		if (com.checked==true){
			com.checked=false;
			lin.className = "odd";
		}
		else{
			com.checked=true;
			lin.className = "linhaSalto";
		}
	}
}


function abrePergunta(pergunta){

	if (!pergunta){
		alert('Selecione a pergunta!')
		return;
	}

	var url = "pergunta.press.php?pergunta="+pergunta;
	janelaPergunta = window.open(url, "janelaPergunta", "toolbar=no, location=no, status=yes, scrollbars=yes, directories=no,menubar=no, width=650, height=550, top=150, left=250");
}

function abreProvaPergunta(prova_pergunta){

	if (!prova_pergunta){
		alert('Selecione a pergunta!')
		return;
	}

	var url = "pergunta.press.php?prova_pergunta="+prova_pergunta;
	janelaProvaPergunta = window.open(url, "janelaProvaPergunta", "toolbar=no, location=no, status=yes, scrollbars=yes, directories=no,menubar=no, width=650, height=550, top=150, left=250");
}

function abreProva(prova){

	if (!prova){
		alert('Selecione a prova!')
		return;
	}

	var url = "prova.press.php?prova="+prova;
	janelaProva = window.open(url, "janelaProva", "toolbar=no, location=no, status=yes, scrollbars=yes, directories=no,menubar=no, width=650, height=550, top=100, left=200");
}

function abreComunicado(comunicado){

	if (!comunicado){
		alert('Selecione o comunicado!')
		return;
	}

	var url = "comunicado.press.php?comunicado="+comunicado;
	janelaComunicado = window.open(url, "janelaComunicado", "toolbar=no, location=no, status=yes, scrollbars=yes, directories=no,menubar=no, width=650, height=550, top=100, left=200");
}

function popupPergunta(){

	if (!$('#disciplina').val()){
		alert('Selecione o curso e a disciplina')
		return;
	}

	var url = "pergunta.adicionar.php?disciplina="+$('#disciplina').val();
	janela = window.open(url, "popupPergunta", "toolbar=no, location=no, status=yes, scrollbars=yes, directories=no,menubar=no, width=750, height=450, top=100, left=200");
}

function popupTopico(){
	var url = "disciplina.topico.adicionar.php";
	janela = window.open(url, "janela", "toolbar=no, location=no, status=yes, scrollbars=yes, directories=no,menubar=no, width=350, height=250, top=350, left=350");
}
