function checkCadAdmin(thisForm) {
	"use strict";
	var nome, email, login, senha, senha_confirm, nivel, set_validade, validade, send, errorContent;

	nome          = replaceAll(thisForm.nome.value, " ", "");
	email         = replaceAll(thisForm.email.value, " ", "");
	login         = replaceAll(thisForm.login.value, " ", "");
	senha         = replaceAll(thisForm.senha.value, " ", "");
	senha_confirm = replaceAll(thisForm.senha_confirm.value, " ", "");
	nivel         = $(thisForm.nivel).is(':checked');
	set_validade  = $(thisForm.set_validade).is(':checked');
	validade      = replaceAll(thisForm.validade.value, " ", "");

	send          = new Boolean("true");
	errorContent  = "";

	$(thisForm).find("label").stop().removeClass("error");
	$(thisForm).find("input").stop().removeClass("error");

	if (nome==="") {
		$(thisForm).find("[for=nome]").addClass("error");
		$(thisForm.nome).addClass("error");
		errorContent += "- Campo requerido [ NOME ]. <br>";
		send = false;
	};

	if (email==="") {
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo requerido [ EMAIL ]. <br>";
		send = false;
	}else if(!is_valid_email(email)){
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo inválido [ EMAIL ]. <br>";
		send = false;
	}

	if (login==="") {
		$(thisForm).find("[for=login]").addClass("error");
		$(thisForm.login).addClass("error");
		errorContent += "- Campo requerido [ USUÁRIO ]. <br>";
		send = false;
	}else if(login.length < 6){
		$(thisForm).find("[for=login]").addClass("error");
		$(thisForm.login).addClass("error");
		errorContent += "- O Campo [ USUÁRIO ] deve conter no mínimo 6 caracteres. <br>";
		send = false;
	}else if (!(check_esp_char(login))) {
		$(thisForm).find("[for=login]").addClass("error");
		$(thisForm.login).addClass("error");
		errorContent += "- O Campo [ USUÁRIO ] não deve conter caracteres especiais. <br>";
		send = false;
	};

	if (senha==="") {
		$(thisForm).find("[for=senha]").addClass("error");
		$(thisForm.senha).addClass("error");
		errorContent += "- Campo requerido [ SENHA ]. <br>";
		send = false;
	}else if(senha.length < 6){
		$(thisForm).find("[for=senha]").addClass("error");
		$(thisForm.senha).addClass("error");
		errorContent += "- O Campo [ SENHA ] deve conter no mínimo 6 caracteres. <br>";
		send = false;
	} else if(senha!==senha_confirm){
		$(thisForm).find("[for=senha]").addClass("error");
		$(thisForm.senha).addClass("error");
		$(thisForm).find("[for=senha_confirm]").addClass("error");
		$(thisForm.senha_confirm).addClass("error");
		errorContent += "- Campo inválido [ CONFIRMAR SENHA ]. <br>";
		send = false;
	}

	if (senha_confirm==="") {
		$(thisForm).find("[for=senha_confirm]").addClass("error");
		$(thisForm.senha_confirm).addClass("error");
		errorContent += "- Campo requerido [ CONFIRMAR SENHA ]. <br>";
		send = false;
	}

	if (!nivel) {
		$(thisForm).find("[for=nivel-superadmin]").addClass("error");
		$(thisForm).find("[for=nivel-admin]").addClass("error");
		$(thisForm).find("[for=nivel-user]").addClass("error");
		$(thisForm.nivel).addClass("error");
		errorContent += "- Campo requerido [ NÍVEL DE PERMISÃO ]. <br>";
		send = false;
	}
	if (set_validade) {
		if (validade==="") {
			$(thisForm).find("[for=set_validade]").addClass("error");
			$(thisForm.validade).addClass("error");
			errorContent += "- Campo requerido [ DATA DE VALIDADE ]. <br>";
			send = false;
		}
	}

	if(send) {
		thisForm.submit();
	}else{
		$('#myModal .modal-body').html(errorContent);
		$('#myModal .modal-title').html("Foram detectados os seguintes erros:")
		$('#myModal').modal();
		$('#myModal').on('hidden.bs.modal', function()
		{
			$('[data-loading-text]').button('reset');
		});
	}

}


function checkEditAdmin(thisForm) {
	"use strict";
	var nome, email, login, set_pass, senha, senha_confirm, nivel, set_validade, validade, send, errorContent;

	nome          = replaceAll(thisForm.nome.value, " ", "");
	email         = replaceAll(thisForm.email.value, " ", "");
	login         = replaceAll(thisForm.login.value, " ", "");
	senha         = replaceAll(thisForm.senha.value, " ", "");
	senha_confirm = replaceAll(thisForm.senha_confirm.value, " ", "");
	nivel         = $(thisForm.nivel).is(':checked');
	set_validade  = $(thisForm.set_validade).is(':checked');
	set_pass      = $(thisForm.set_pass).is(':checked');
	validade      = replaceAll(thisForm.validade.value, " ", "");

	send          = new Boolean("true");
	errorContent  = "";

	$(thisForm).find("label").stop().removeClass("error");
	$(thisForm).find("input").stop().removeClass("error");

	if (nome==="") {
		$(thisForm).find("[for=nome]").addClass("error");
		$(thisForm.nome).addClass("error");
		errorContent += "- Campo requerido [ NOME ]. <br>";
		send = false;
	};

	if (email==="") {
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo requerido [ EMAIL ]. <br>";
		send = false;
	}else if(!is_valid_email(email)){
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo inválido [ EMAIL ]. <br>";
		send = false;
	}

	if (login==="") {
		$(thisForm).find("[for=login]").addClass("error");
		$(thisForm.login).addClass("error");
		errorContent += "- Campo requerido [ USUÁRIO ]. <br>";
		send = false;
	};

	if (set_pass) {
		if (senha==="") {
			$(thisForm).find("[for=senha]").addClass("error");
			$(thisForm.senha).addClass("error");
			errorContent += "- Campo requerido [ SENHA ]. <br>";
			send = false;
		}else if(senha.length < 6){
			$(thisForm).find("[for=senha]").addClass("error");
			$(thisForm.senha).addClass("error");
			errorContent += "- O Campo [ SENHA ] deve conter no mínimo 6 caracteres. <br>";
			send = false;
		} else if(senha!==senha_confirm){
			$(thisForm).find("[for=senha]").addClass("error");
			$(thisForm.senha).addClass("error");
			$(thisForm).find("[for=senha_confirm]").addClass("error");
			$(thisForm.senha_confirm).addClass("error");
			errorContent += "- Campo inválido [ CONFIRMAR SENHA ]. <br>";
			send = false;
		}
		if (senha_confirm==="") {
			$(thisForm).find("[for=senha_confirm]").addClass("error");
			$(thisForm.senha_confirm).addClass("error");
			errorContent += "- Campo requerido [ CONFIRMAR SENHA ]. <br>";
			send = false;
		}
	}

	if (!nivel) {
		$(thisForm).find("[for=nivel-admin]").addClass("error");
		$(thisForm).find("[for=nivel-user]").addClass("error");
		$(thisForm.nivel).addClass("error");
		errorContent += "- Campo requerido [ NÍVEL DE PERMISÃO ]. <br>";
		send = false;
	}
	if (set_validade) {
		if (validade==="") {
			$(thisForm).find("[for=set_validade]").addClass("error");
			$(thisForm.validade).addClass("error");
			errorContent += "- Campo requerido [ DATA DE VALIDADE ]. <br>";
			send = false;
		}
	}

	if(send) {
		thisForm.submit();
	}else{
		$('#myModal .modal-body').html(errorContent);
		$('#myModal .modal-title').html("Foram detectados os seguintes erros:")
		$('#myModal').modal();
		$('#myModal').on('hidden.bs.modal', function()
		{
			$('[data-loading-text]').button('reset');
		});
	}

}

function checkEditMeusDados(thisForm) {
	"use strict";
	var nome, email, login, nivel, send, errorContent;

	nome          = replaceAll(thisForm.nome.value, " ", "");
	email         = replaceAll(thisForm.email.value, " ", "");
	login         = replaceAll(thisForm.login.value, " ", "");

	send          = new Boolean("true");
	errorContent  = "";

	$(thisForm).find("label").stop().removeClass("error");
	$(thisForm).find("input").stop().removeClass("error");

	if (email==="") {
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo requerido [ EMAIL ]. <br>";
		send = false;
	}else if(!is_valid_email(email)){
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo inválido [ EMAIL ]. <br>";
		send = false;
	}

	if(send) {
		thisForm.submit();
	}else{
		$('#myModal .modal-body').html(errorContent);
		$('#myModal .modal-title').html("Foram detectados os seguintes erros:")
		$('#myModal').modal();
		$('#myModal').on('hidden.bs.modal', function()
		{
			$('[data-loading-text]').button('reset');
		});
	}
}

function checkEditPass(thisForm) {
	"use strict";
	var old_senha, senha, senha_confirm, send, errorContent;

	old_senha     = replaceAll(thisForm.old_senha.value, " ", "");
	senha         = replaceAll(thisForm.senha.value, " ", "");
	senha_confirm = replaceAll(thisForm.senha_confirm.value, " ", "");

	send          = new Boolean("true");
	errorContent  = "";

	$(thisForm).find("label").stop().removeClass("error");
	$(thisForm).find("input").stop().removeClass("error");


	if (old_senha==="") {
		$(thisForm).find("[for=old_senha]").addClass("error");
		$(thisForm.old_senha).addClass("error");
		errorContent += "- Campo requerido [ SENHA ANTERIOR ]. <br>";
		send = false;
	}
	if (senha==="") {
		$(thisForm).find("[for=senha]").addClass("error");
		$(thisForm.senha).addClass("error");
		errorContent += "- Campo requerido [ SENHA ]. <br>";
		send = false;
	}else if(senha.length < 6){
		$(thisForm).find("[for=senha]").addClass("error");
		$(thisForm.senha).addClass("error");
		errorContent += "- O Campo [ SENHA ] deve conter no mínimo 6 caracteres. <br>";
		send = false;
	} else if(senha!==senha_confirm){
		$(thisForm).find("[for=senha]").addClass("error");
		$(thisForm.senha).addClass("error");
		$(thisForm).find("[for=senha_confirm]").addClass("error");
		$(thisForm.senha_confirm).addClass("error");
		errorContent += "- Campo [ CONFIRMAR SENHA ] não pode ser diferente do campo [ SENHA ]. <br>";
		send = false;
	}

	if (senha_confirm==="") {
		$(thisForm).find("[for=senha_confirm]").addClass("error");
		$(thisForm.senha_confirm).addClass("error");
		errorContent += "- Campo requerido [ CONFIRMAR SENHA ]. <br>";
		send = false;
	}

	if(send) {
		thisForm.submit();
	}else{
		$('#myModal .modal-body').html(errorContent);
		$('#myModal .modal-title').html("Foram detectados os seguintes erros:")
		$('#myModal').modal();
		$('#myModal').on('hidden.bs.modal', function()
		{
			$('[data-loading-text]').button('reset');
		});
	}

}