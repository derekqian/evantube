// ==========================================================================
// @function		Complete AHAH function
// @author		Daniele Florio
// @site		www.gizax.it
// @version		1.1.3 experimental

// @thanksTo		Andrea Paiola,Walter Wlodarski,Scott Chapman

// @updated 1.1.3 ( execJS function ) @thanks to Giovanni Zona

// (c) 2006 Daniele Florio <daniele@gizax.it>

// ==========================================================================

/* USAGE:

1) Posting data to form:
<form id="myform" action="javascript:ahahscript.likeSubmit('helloworld.php', 'post', 'myform', 'mytarget');">

									    ('comments_ajax.php', 'commentajax', '', 'GET', '', this)
2) Getting simple url

<a href="#" onclick="javascript:ahahscript.ahah('test.htm', 'mytaget', '', 'GET', '', this);">click me</a>

*/

var ahahscript = {

	//loading : 'loading...',
	loading : "<br /><center><img src=javascripts/ajax-loader.gif </center>",

	ahah : function (url, target, delay, method, parameters) {

	  if ( ( method == undefined ) || ( method == "GET" ) || ( method == "get" ) ){

			this.creaDIV(target, this.loading);

			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}
			if (req) {
				req.onreadystatechange = function() {
					ahahscript.ahahDone(url, target, delay, method, parameters);
				};
				req.open(method, url, true);
				req.send("");
			}
		}
		if ( (method == "POST") || (method == "post") ){

			this.creaDIV(target, this.loading);

			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}
			if (req) {
				req.onreadystatechange = function() {
					ahahscript.ahahDone(url, target, delay, method, parameters);
				};
				req.open(method, url, true);
				req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				req.send(parameters);
			 }
		}
	},

	creaDIV : function (target, html){
		if (document.body.innerHTML) {
			document.getElementById(target).innerHTML = html;
	   	}
	   	else if (document.getElementById){
			var element = document.getElementById(target);
			var range = document.createRange();
			range.selectNodeContents(element);
			range.deleteContents();
			element.appendChild(range.createContextualFragment(html));
	   }
	},

	execJS : function (node) {

		var st = node.getElementsByTagName('SCRIPT');
		var strExec;

		var bSaf = (navigator.userAgent.indexOf('Safari') != -1);
		var bOpera = (navigator.userAgent.indexOf('Opera') != -1);
		var bMoz = (navigator.appName == 'Netscape');

		for(var i=0;i<st.length; i++) {
			if (bSaf) {
			  strExec = st[i].innerHTML;
			}
			else if (bOpera) {
			  strExec = st[i].text;
			}
			else if (bMoz) {
			  strExec = st[i].textContent;
			}
			else {
			  strExec = st[i].text;
			}
			try {
			  eval(strExec);
			} catch(e) {
			  alert(e);
			}
		}

	},

	ahahDone : function (url, target, delay, method, parameters) {
		if (req.readyState == 4) {
			element = document.getElementById(target);
			if (req.status == 200) {

				//this.creaDIV(target, req.responseText);
				output = req.responseText;
				document.getElementById(target).innerHTML = output;
				var j = document.createElement("div");
				j.innerHTML = "_" + output + "_";
				this.execJS(j);

			}
			else {
				this.creaDIV(target, "ahah error:\n"+req.statusText);
			}
		}
	},

	/*

	@@ parameters :
	fileName	= name of your cgi or other
	method		= GET or POST, default is GET
	formName	= name of your form
	dynamicTarget	= name of your dynamic Target DIV or other

	@@ usage :


	*/

	likeSubmit : function ( file, method, formName, target ) {

		var the_form = document.getElementById(formName);
		var num = the_form.elements.length;
		var url = "";
		var radio_buttons = new Array();
		var nome_buttons = new Array();
		var check_buttons = new Array();
		var nome_buttons = new Array();


		// submit radio values
		var j = 0;
		var a = 0;
		for(var i=0; i<the_form.length; i++){
			var temp = the_form.elements[i].type;
			if ( (temp == "radio") && ( the_form.elements[i].checked) ) {
				nome_buttons[a] = the_form.elements[i].name;
				radio_buttons[j] = the_form.elements[i].value;
				j++;
				a++;
			}
		}
		for(var k = 0; k < radio_buttons.length; k++) {
			url += nome_buttons[k] + "=" + radio_buttons[k] + "&";
		}

		// submit checkbox values
		var j = 0;
		var a = 0;
		for(var i=0; i<the_form.length; i++){
			var temp = the_form.elements[i].type;
			if ( (temp == "checkbox") && ( the_form.elements[i].checked) ) {
				nome_buttons[a] = the_form.elements[i].name;
				check_buttons[j] = the_form.elements[i].value;
				j++;
				a++;
			}
		}
		for(var k = 0; k < check_buttons.length; k++) {
			url += nome_buttons[k] + "=" + check_buttons[k] + "&";
		}

		// submit all kind of input
		for (var i = 0; i < num; i++){
			var chiave = the_form.elements[i].name;
			var valore = the_form.elements[i].value;
			var tipo = the_form.elements[i].type;

			if ( (tipo == "submit") || (tipo == "radio") || (tipo == "checkbox") ){}
			else {
				url += chiave + "=" + valore + "&";
			}
		}

		var parameters = url;
		url = file + "?" + url;

		if (method == undefined) {
			method = "GET";
		}
		if (method == "GET") {
			this.ahah(url, target, '', method, '');
		}
		else {
			this.ahah(file, target, '', method, parameters);
		}
	}

};

