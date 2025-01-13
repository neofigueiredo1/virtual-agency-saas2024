//Checagem de dados enviados do formulário de cadastro de tipos de banners
function checkCadTipoBanner(thisForm) {
    "use strict";
    var nome, send, errorContent;

    nome          = replaceAll(thisForm.nome.value, " ", "");

    send          = new Boolean("true");
    errorContent  = "<p>Foram detectados os seguintes erros:</p>"

    $(thisForm).find("label").stop().removeClass("error");
    $(thisForm).find("input").stop().removeClass("error");

    if (nome==="") {
        $(thisForm).find("[for=nome]").addClass("error");
        $(thisForm.nome).addClass("error");
        errorContent += "- Campo requerido [ NOME ]. <br>";
        send = false;
    };

    if(send) {
        thisForm.submit();
    }else{
        $('#myModal .modal-body').html(errorContent);
        $('#myModal').modal();
        $('#myModal').on('hide', function(){
            setTimeout(function(){
                $('[data-loading-text]').button('reset');
            },500);
        });                
    }
}

//Checagem de dados enviados do formulário de cadastro de banners
$("#form-cad-banner").submit(function (event) {
	"use strict";

	var nome, arquivo, send, errorContent;
	nome    = replaceAll($(this.nome).val(), " ", "");
	arquivo = replaceAll($(this.arquivo).val(), " ", "");

	send          = new Boolean("true");
	errorContent  = "<p>Foram detectados os seguintes erros:</p>"

	$(this).find("label").stop().removeClass("error");
	$(this).find("input").stop().removeClass("error");

	if (nome==="") {
		$(this).find("[for=nome]").addClass("error");
		$(this.nome).addClass("error");
		errorContent += "- Campo requerido [ NOME ]. <br>";
		send = false;
	};
        
        /*
	if (arquivo==="") {
		$(this).find("[for=arquivo]").addClass("error");
		$(this.arquivo).addClass("error");
		errorContent += "- Campo requerido [ ARQUIVO ]. <br>";
		send = false;
	};
        */

	if(send) {
		return true;
	}else{
		$('#myModal .modal-body').html(errorContent);
		$('#myModal').modal();
		$('#myModal').on('hide', function(){
                    setTimeout(function(){
                        $('[data-loading-text]').button('reset');
                    },500);
                });                
		return false;
	}

});
