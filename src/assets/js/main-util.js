var Util = {
	isNumber: function (n) { return !isNaN(parseFloat(n)) && isFinite(n); },

	/* Função para validação de e-mail */
	isEmail: function (mail) {
		var ret = false;
		if (typeof (mail) != "undefined") {
			mail = mail.match(/(\w+)@(.+)\.(\w+)$/);
			if (mail != null) {
				if ((mail[3].length == 2) || (mail[3].length == 3))
					ret = true;
			}
		}
		return ret;
	},

	isJson: function (jsonString) {
		if (/^[\],:{}\s]*$/.test(jsonString.replace(/\\["\\\/bfnrtu]/g, '@').
			replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
			replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
			//the json is ok
			return true;
		} else {
			//the json is not ok
			return false;
		}
	},

	isDate: function (data) {

		var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
		var date = data;
		var ardt = new Array;
		ardt = date.split("/");
		let erro = false;

		if (date.search(ExpReg) == -1) {
			erro = true;
		}
		else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30))
			erro = true;
		else if (ardt[1] == 2) {
			if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
				erro = true;
			if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
				erro = true;
		}
		return !erro;
	},

	getLabelByInputId: function (elId) {
		var labels = document.getElementsByTagName('label');
		for (var i = 0; i < labels.length; i++) {
			if (labels[i].htmlFor == elId) {
				return labels[i];
			}
		}
	},

	/**
	 * Substitui todas as ocorrências da string de procura com a string de substituição
	 * @param  {mixed} a  - string search
	 * @param  {mixed} b  - string replace
	 * @param  {mixed} d  - string subject
	 * @return {mixed}  string com os valores modificados.
	 */
	replaceAll: function (de, para, str) {
		return str.replace(new RegExp(de, 'g'), para);
	},

	/**
	 * verifica se elemento suporta determinado atributo - elmSupportAttr(element, attribute)
	 * @param element, attribute
	 * @return boolean
	 */
	elmSupportAttr: function (a, b) { var c = document.createElement(a); return b in c ? !0 : !1 },

	checkFormRequire: function (theForm, theMessageArea, callback) {
		callback = callback || false;

		var elements = theForm.elements,
			element,
			required,
			label,
			send = true;
		$(theMessageArea).slideUp();
		$(theMessageArea).html("Preencha os dados corretamente para continuar.");

		for (var i = elements.length - 1; i >= 0; i--) {
			element = elements[i];
			required = element.getAttribute('data-required');
			var elementValue = element.value;
			elementValue = elementValue.trim();

			jQuery(element).removeClass('error');
			jQuery(Util.getLabelByInputId(element.name)).removeClass('error');

			if (required === 'true') {
				if (elementValue == '') {
					jQuery(element).addClass('error');
					jQuery(Util.getLabelByInputId(element.name)).addClass('error');
					send = false;
				} else {
					if (element.type == 'email') {
						if (!Util.isEmail(elementValue)) {
							jQuery(element).addClass('error');
							jQuery(Util.getLabelByInputId(element.name)).addClass('error');
							send = false;
						} else {
							jQuery(element).removeClass('error');
							jQuery(Util.getLabelByInputId(element.name)).removeClass('error');
						}
					} else {
						jQuery(element).removeClass('error');
						jQuery(Util.getLabelByInputId(element.name)).removeClass('error');
					}
				}
			} else if (element.type == 'email' && elementValue != '') {
				if (!Util.isEmail(elementValue)) {
					jQuery(element).addClass('error');
					jQuery(Util.getLabelByInputId(element.name)).addClass('error');
					send = false;
				} else {
					jQuery(element).removeClass('error');
					jQuery(Util.getLabelByInputId(element.name)).removeClass('error');
				}
			}
		}

		if (send) {
			if (callback !== false) {
				return callback(theForm, theMessageArea);
			} else {
				theForm.submit();
			}
		} else {
			if (!window.location.hash) {
				$('html, body').animate({ scrollTop: ($(theForm).offset().top - 150) }, 'slow');
			}
			$(theMessageArea).stop().slideDown('fast', function () {
				$('[data-loading-text]').button('reset');
				$(theMessageArea).css('height','auto');
				// setTimeout(function () {
				// 	$(theMessageArea).slideUp('fast');
				// }, 3500);
			});
		}

	},
	numberToReal: function (numero) {
		var numero = numero.toFixed(2).split('.');
		numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
		return numero.join(',');
	},
	formatMoney: function (n, c, d, t) {
		var c = isNaN(c = Math.abs(c)) ? 2 : c,
			d = d == undefined ? "." : d,
			t = t == undefined ? "," : t,
			s = n < 0 ? "-" : "",
			i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
			j = (j = i.length) > 3 ? j % 3 : 0;

		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	}
};

function sendCadastro(form) {
	element = form.elements;

	nome = element.c_nome.value;
	email = element.c_email.value;
	telefone = element.c_telefone.value;
	assunto = element.c_assunto.value;
	mensagem = element.c_mensagem.value;

	cData = { nome: nome, email: email, telefone: telefone, assunto: assunto, mensagem: mensagem };

	$('#contato_form_preloader').slideDown("fast", function (event) {

		$.ajax({
			url: "/site/direct-includes/site-contato-exe.php",
			type: "POST",
			data: cData,
			success: function (data, textStatus, jqXHR) {
				if (data == "ok") {
					$('#contato_form_preloader').slideUp("fast");
					form.reset();
					$('#contato_form_sucesso').slideDown("fast", function (event) {
						setTimeout(function () {
							$('#contato_form_sucesso').slideUp("fast");
						}, 4000);
					});
				} else {
					console.log("002 - Falha!" + textStatus);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$('#contato_form_preloader').slideUp("fast");
				console.log("003 - Falha!" + textStatus);
			}
		}); //end ajax
	});
}

function sendContato(form,msg_area) {

	// let element = form.elements;

	// let nome = element.c_nome.value,
	// 	email = element.c_email.value,
	// 	telefone = element.c_telefone.value,
	// 	mensagem = element.c_mensagem.value;

	let formData = $(form).serialize(); //{ sac: 1, nome: nome, email: email, telefone: telefone, mensagem: mensagem };

	console.log(formData);

	$(form).find('.contato_form_body').slideUp('fast');
	$(form).find(".contato_form_preloader").slideDown("fast", function (event) {

		$.ajax({
			url: "/site/direct-includes/site-contato-ajax-controller.php",
			type: "POST",
			data: formData,
			success: function (data, textStatus, jqXHR) {

				$(form).find(".contato_form_preloader").slideUp("fast");
				if (data.trim() == "ok") {
					form.reset();
					$(form).find(".contato_form_sucesso").slideDown("fast", function (event) {
						setTimeout(function () {
							$(form).find(".contato_form_sucesso").slideUp("fast");
							$(form).find('.contato_form_body').slideDown('fast');
						}, 4000);
					});
				} else {
					console.log("002 - Falha!" + textStatus);
					$(form).find('.contato_form_body').slideDown('fast');
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$(form).find(".contato_form_preloader").slideUp("fast");
				$(form).find('.contato_form_body').slideDown('fast');
				console.log("003 - Falha!" + textStatus);
			}
		}); //end ajax
	});
}
