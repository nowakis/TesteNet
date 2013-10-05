var Ajax = new Object();

Ajax.Request = function(url,id, callbackMethod){
	if ( id == 0 )	{
		return;
	}
	Page.getPageCenterX();
	Ajax.request = Ajax.createRequestObject();
	Ajax.request.onreadystatechange = callbackMethod;
	Ajax.request.open("POST", url+id, true);
	Ajax.request.send(url);
}

Ajax.Response = function (){
	if(Ajax.CheckReadyState(Ajax.request))	{		
		document.getElementById('txtVacinas').length = 0;
		document.getElementById('txtVacinas').options[0] = new Option("Selecione a Vacina","0");
		
		var	response = Ajax.request.responseXML.documentElement;
		var _data = response.getElementsByTagName('category');
		if(_data.length == 0){
			document.getElementById('txtVacinas').options[0] = new Option("Nenhuma vacina disponível!",'0');	
		}
		var i
		for ( i = 0 ; i < _data.length ; i ++ )	{
			document.getElementById('txtVacinas').options[i+1] = new Option(response.getElementsByTagName('fname')[i].firstChild.data,response.getElementsByTagName('id')[i].firstChild.data);	
		}
	}
}

Ajax.createRequestObject = function(){
	var obj;
	if(window.XMLHttpRequest)	{
		obj = new XMLHttpRequest();
	}
	else if(window.ActiveXObject)	{
		obj = new ActiveXObject("MSXML2.XMLHTTP");
	}
	return obj;
}

Ajax.CheckReadyState = function(obj){
	if(obj.readyState < 4) {		
		document.getElementById('loading').style.top = (Page.top + Page.height/2)-100;
		document.getElementById('loading').style.left = Page.width/2-75;
		document.getElementById('loading').style.position = "absolute";
		document.getElementById('loading').innerHTML = "<table border=0 cellpadding=0 cellspacing=1 width=160 bgcolor=gray><tr><td align=center class=loaded height=45 bgcolor=#ffffff>Aguarde....Carregando...<br><img src='imagens/loading2.gif'/></td></tr></table>";  
	}
	//if(obj.readyState == 1) { document.getElementById('loading').innerHTML = "Loading..."; }
	//if(obj.readyState == 2) { document.getElementById('loading').innerHTML = "Loading..."; }
	//if(obj.readyState == 3) { document.getElementById('loading').innerHTML = "Loading..."; }	
	if(obj.readyState == 4)	{
		if(obj.status == 200){
			document.getElementById('loading').innerHTML = "<table border=0 cellpadding=0 cellspacing=1 width=160 bgcolor=gray><tr><td align=center class=loaded height=45 bgcolor=#ffffff>Informações carregadas com sucesso!</td></tr></table>";
			setTimeout('Page.loadOut()',2000);
			return true;
		}
		else{
			document.getElementById('loading').innerHTML = "HTTP " + obj.status;
		}
	}
}

var Page = new Object();
Page.width;
Page.height;
Page.top;

Page.loadOut = function (){
	document.getElementById('loading').innerHTML ='';	
}
Page.getPageCenterX = function (){
	var fWidth;
	var fHeight;		
	//For old IE browsers 
	if(document.all) { 
		fWidth = document.body.clientWidth; 
		fHeight = document.body.clientHeight; 
	} 
	//For DOM1 browsers 
	else if(document.getElementById &&!document.all){ 
			fWidth = innerWidth; 
			fHeight = innerHeight; 
		} 
		else if(document.getElementById) { 
				fWidth = innerWidth; 
				fHeight = innerHeight; 		
			} 
			//For Opera 
			else if (is.op) { 
					fWidth = innerWidth; 
					fHeight = innerHeight; 		
				} 
				//For old Netscape 
				else if (document.layers) { 
						fWidth = window.innerWidth; 
						fHeight = window.innerHeight; 		
					}
	Page.width = fWidth;
	Page.height = fHeight;
	Page.top = window.document.body.scrollTop;
}