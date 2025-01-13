/*jslint browser: true*/
/*global $,console*/

// Avoid 'console' errors in browsers that lack a console.
(function(){ "use strict"; var a,b,d,c,e;b=function(){};d="assert clear count debug dir dirxml error exception group groupCollapsed groupEnd info log markTimeline profile profileEnd table time timeEnd timeStamp trace warn".split(" ");c=d.length;for(e=window.console=window.console||{};c--;)a=d[c],e[a]||(e[a]=b)})();

// Função Para Replace!
// return STRING
function replaceAll(a, b, d) { "use strict"; for(var c = a.indexOf(b); - 1 < c;) a = a.replace(b, d), c = a.indexOf(b); return a }

// validação de email
// return BOOLEAN
function is_valid_email(a) { "use strict"; var b = !1; if("undefined" != typeof a && (a = a.match(/(\w+)@(.+)\.(\w+)$/), null != a && (2 == a[3].length || 3 == a[3].length))) b = !0; return b }

// validar formulário de login

$("#form-login").submit(function (event) {
	"use strict";

	var nomeCmd, senhaCmd, nomeVal, senhaVal, send;
	nomeVal  = replaceAll($(this.nome).val(), " ", "");
	senhaVal = replaceAll($(this.senha).val(), " ", "");
	nomeCmd  = $(this).find("#nome-wrap");
	senhaCmd = $(this).find("#senha-wrap");
	send     = new Boolean("true");

	$(this).find("div").stop().removeClass("danger");
	$(this).find(".help").stop().fadeOut();

	if(nomeVal === "") {
		nomeCmd.stop().addClass("danger");
		nomeCmd.find(".help").stop().html("Preencha o campo abaixo.");
		nomeCmd.find(".help").stop().fadeIn();
		nomeCmd.find("input").focus();
		send = false;
	}

	if(senhaVal === "") {
		senhaCmd.stop().addClass("danger");
		senhaCmd.find(".help").stop().html("Preencha o campo abaixo.");
		senhaCmd.find(".help").stop().fadeIn();
		senhaCmd.find("input").focus();
		send = false;
	}

	return send;

});

// validar formulário de recuperação de senha
$("#form-user-lost, #form-pass-lost").submit(function (event) {

	"use strict";
	var emailCmd, emailVal, send;
	emailVal = replaceAll($(this.email).val(), " ", "");
	emailCmd = $(this).find("#email-wrap");
	send     = true;

	$(this).find("div").stop().removeClass("danger");
	$(this).find(".help").stop().fadeOut();

	if(emailVal === "") {
		emailCmd.stop().addClass("danger");
		emailCmd.find(".help").stop().html("Preencha o campo abaixo.");
		emailCmd.find(".help").stop().fadeIn();
		emailCmd.find("input").focus();
		send = false;
	} else if(!is_valid_email(emailVal)) {
		emailCmd.stop().addClass("danger");
		emailCmd.find(".help").stop().html("Informe um e-mail válido.");
		emailCmd.find(".help").stop().fadeIn();
		emailCmd.find("input").focus();
		send = false;
	}

	return send;

});

jQuery(document).ready(function($) {
	// trocar campo senha do login
	$("#show-pass").click(function(event) {
		if($(this).is(":checked")) {
			$('#senha').css('display', 'none');
			$('#senha_t').css('display', 'block');
			$("#senha_t").focus();
		} else {
			$('#senha').css('display', 'block');
			$('#senha_t').css('display', 'none');
			$("#senha").focus();
		}
	});

	// toggle teclado virtual
	$("#get-tcl-virtual").click(function(event) {
		if($("#tcl-virtual").css("display") != "block") {
			$("#form-login").animate({
				marginLeft: '-=25px'
			}, 200, function() {
				$("#tcl-virtual").fadeIn();
			});
		} else {
			$("#tcl-virtual").fadeOut(200, function() {
				$("#form-login").animate({
					marginLeft: '+=25px'
				}, 200);
			});
		}
	});

	// show get user form
	$("#user-lost").click(function(event) {
		$("#tcl-virtual").fadeOut(200);
		$("#form-login").fadeOut(200, function() {
			$("#form-user-lost").fadeIn(200);
			$("#form-user-lost").fadeIn(200);
		});
	});

	// show get pass form
	$("#pass-lost").click(function (event) {
		$("#tcl-virtual").fadeOut(200);
		$("#form-login").fadeOut(200, function() {
			$("#form-pass-lost").fadeIn(200);
		});
	});

	// iguala campos senha
	$("#senha").keyup(function() {
		$("#senha_t").val($(this).val());
	});
	$("#senha_t").keyup(function() {
		$("#senha").val($(this).val());
	});

});

$(".lost_voltar").click(function (event) {
	$("form").each(function (event) {
		$(this).fadeOut('fast', function() {
			setTimeout(function() {$("#form-login").fadeIn();},300);
		});
	});
});


$(".form-new-pass").submit(function (event) {
	"use strict";

	var senhaRecuCmd, senhaCmd, senhaRecuVal, senhaVal, send;
	senhaVal = replaceAll($(this.senha).val(), " ", "");
	console.log(senhaVal);
	senhaRecuVal  = replaceAll($(this.recu_senha).val(), " ", "");
	console.log(senhaRecuVal);
	senhaCmd = $(this).find("#senha-wrap");
	senhaRecuCmd  = $(this).find("#recu-senha-wrap");
	send     = new Boolean("true");

	$(this).find("div").stop().removeClass("danger");
	$(this).find(".help").stop().fadeOut();

	if(senhaRecuVal === "") {
		senhaRecuCmd.stop().addClass("danger");
		senhaRecuCmd.find(".help").stop().html("Preencha o campo abaixo.");
		senhaRecuCmd.find(".help").stop().fadeIn();
		senhaRecuCmd.find("input").focus();
		send = false;
	}

	if(senhaVal === "") {
		senhaCmd.stop().addClass("danger");
		senhaCmd.find(".help").stop().html("Preencha o campo abaixo.");
		senhaCmd.find(".help").stop().fadeIn();
		senhaCmd.find("input").focus();
		send = false;
	}

	if(senhaVal !== senhaRecuVal){
		senhaRecuCmd.stop().addClass("danger");
		senhaRecuCmd.find(".help").stop().html("Este campo deve ser igual à senha.");
		senhaRecuCmd.find(".help").stop().fadeIn();
		senhaRecuCmd.find("input").focus();
		send = false;
	}

	return send;

});

// ajustar estilo da página
function adjustStyle() { }

// window.onload
function onLoadWn() {
	"use strict";
	$("#nome").focus();
}

// window.onresize
function onResizeWn(){ }