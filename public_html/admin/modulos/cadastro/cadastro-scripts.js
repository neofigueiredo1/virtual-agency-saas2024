function checkCadastro(thisForm) {
	var nome_completo, email, definir, senha, senha_confirm, send, errorContent;

	nome_completo = replaceAll(thisForm.nome_completo.value, " ", "");
	email = replaceAll(thisForm.email.value, " ", "");
	senha = replaceAll(thisForm.senha.value, " ", "");
	senha_confirm = replaceAll(thisForm.senha_confirm.value, " ", "");

	send = true;
	errorContent = "<p>Foram detectados os seguintes erros:</p>";

	$(thisForm).find("label").stop().removeClass("error");
	$(thisForm).find("input").stop().removeClass("error");

	if (email === "") {
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo requerido [ EMAIL ]. <br>";
		send = false;
	} else if (!is_valid_email(email)) {
		$(thisForm).find("[for=email]").addClass("error");
		$(thisForm.email).addClass("error");
		errorContent += "- Campo inválido [ EMAIL ]. <br>";
		send = false;
	}
	if (thisForm.definir.checked === true) {
		if (senha === "") {
			$(thisForm).find("[for=senha]").addClass("error");
			$(thisForm.senha).addClass("error");
			errorContent += "- Campo requerido [ INSIRA UMA SENHA ]. <br>";
			send = false;
		}
		if (senha_confirm === "") {
			$(thisForm).find("[for=senha_confirm]").addClass("error");
			$(thisForm.senha_confirm).addClass("error");
			errorContent += "- Campo requerido [ REPITA A SENHA ]. <br>";
			send = false;
		}
		if (senha_confirm != senha) {
			$(thisForm).find("[for=senha]").addClass("error");
			$(thisForm.senha).addClass("error");
			$(thisForm).find("[for=senha_confirm]").addClass("error");
			$(thisForm.senha_confirm).addClass("error");
			errorContent += "- Campo requerido [ REPITA A SENHA CORRETAMENTE ]. <br>";
			send = false;
		}
	}

	if (send) {
		thisForm.submit();
	} else {
		$('#myModal .modal-body').html(errorContent);
		$('#myModal').modal();
		$('#myModal').on('hidden.bs.modal', function()
		{
			$('[data-loading-text]').button('reset');
		});
	}

}
$(document).ready(function(){
	$("#data_nasc").mask("99/99/9999");
	$('.telefone').focusout(function(){
      var phone, element;
      element = $(this);
      element.unmask();
      phone = element.val().replace(/\D/g, '');
      if(phone.length > 10) {
         element.mask("(99) 99999-9999");
      } else {
         element.mask("(99) 9999-99999");
      }
   }).trigger('focusout');
	// $("#c_senha").click(function() {
	// 	$("#senha").toggle("slow");
	// });
});
