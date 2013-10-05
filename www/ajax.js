var req; 

function loadXMLDocStar(url) 

{ 
    req = null; 
    // Procura por um objeto nativo (Mozilla/Safari) 
    if (window.XMLHttpRequest) { 
        req = new XMLHttpRequest(); 
        req.onreadystatechange = processReqChange; 
        req.open("POST", url, true); 
        req.send(null); 
		return 0;
    // Procura por uma versão ActiveX (IE) 
    } else if (window.ActiveXObject) { 
        req = new ActiveXObject("Microsoft.XMLHTTP"); 
        if (req) { 
            req.onreadystatechange = processReqChange; 
            req.open("POST", url, true); 
            req.send(); 
			return 0;
        } 
    } 
} 

function processReqChange() 
{ 
    // apenas quando o estado for "completado" 
    if (req.readyState == 4) { 
        // apenas se o servidor retornar "OK" 
        if (req.status == 200) { 
            // procura pela div id="news" e insere o conteudo 
            // retornado nela, como texto HTML 
			// document.getElementById('news').innerHTML = req.responseText; 
//			alert("Marcados adicionado ao animal:\n"); 

        } else { 
            alert("Houve um problema ao adicionar o marcador:\n" + req.statusText); 

        } 
    } 
} 
