/*jslint browser: true*/
/*global $,console,jQuery*/

// Avoid 'console' errors in browsers that lack a console.
(function() {
	var a, b, d, c, e;
	b = function() {};
	d = "assert clear count debug dir dirxml error exception group groupCollapsed groupEnd info log markTimeline profile profileEnd table time timeEnd timeStamp trace warn".split(" ");
	c = d.length;
	for(e = window.console = window.console || {}; c--;) a = d[c], e[a] || (e[a] = b);
})();

/**
 * Substitui todas as ocorrências da string de procura com a string de substituição
 * @param  {mixed} a  - string search
 * @param  {mixed} b  - string replace
 * @param  {mixed} d  - string subject
 * @return {mixed}  string com os valores modificados.
 */
 function replaceAll(a, b, d) {
 	"use strict";
 	for(var c = a.indexOf(b); - 1 < c;) a = a.replace(b, d), c = a.indexOf(b);
 		return a;
 }

/**
 * Cria o objeto AJAX de acordo com o navegador.
 */
 function http_request(){
 	if(window.XMLHttpRequest){ var http = new XMLHttpRequest(); }else{ var http = new ActiveXObject("Microsoft.XMLHTTP"); }
 	return http;
 }


/**
 * validação de email
 * @param  {string} a - email a ser verificado
 * @return {boolen}
 */
 function is_valid_email(a) {
 	"use strict";
 	var b = !1;
 	if("undefined" != typeof a && (a = a.match(/(\w+)@(.+)\.(\w+)$/), null != a && (2 == a[3].length || 3 == a[3].length))) b = !0;
 	return b;
 }

// mostrar input para setar a data de validade
function show_set_validade(thisInput) {
	if($('#set_validade').is(':checked')) {
		$("#validade-wrap").fadeIn(400, function() {
			show_calendario();
		});
	} else {
		$("#validade-wrap").fadeOut();
	}
}

// mostrar calendário
function show_calendario() {
	$.datepicker.setDefaults( $.datepicker.regional[ "pt-BR" ] );
	$(".datepicker").datepicker();
	//console.log($(".datepicker"));
	$(".datepicker_limit").datepicker({ minDate: 1 });
	$(".datepicker_limit_2").datepicker({ minDate: 2 });
}


// live search
$.extend($.expr[":"], {
	"contains-ci": function(elem, i, match, array) {
		return(elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
});

function live_search() {
	if($(".input-search").length > 0) {
		$(".input-search").keyup(function() {
			$("tr").removeClass("striped-tb");
			if($(this).val() !== "") {
				$(".table-search tbody>tr").hide();
				$(".table-search td:contains-ci('" + $(this).val() + "')").parent("tr").show();
				$(".table-search tr:visible:even").addClass("striped-tb");
			} else {
				$(".table-search tbody>tr").show();
				$("tr:even").addClass("striped-tb");
			}
		});
	}
}


function fauxPlaceholder() {
	if(!Modernizr.placeholder) {
		$("input[placeholder]").each(function() {
			var $input = $(this);
			var placeholder = $input.attr('placeholder');
			$input.val(placeholder);
			$input.addClass("placeholder");
			$input.focus(function() {
				if($input.val() == placeholder) {
					$input.removeClass("placeholder");
					$input.val("");
				}
			});
			$input.blur(function() {
				if($input.val() === '') {
					$input.addClass("placeholder");
					$input.val(placeholder);
				}
			});
		});
	}
}


function format_input_file() {
	// $("input[type=file]").each(function (e) {
	// 	var $this = $(this);
	// 	var title = $this.attr('title');
	// 	$this.wrap('<div class="input_file">');
	// 	$this.parents('.input_file:eq(0)').append('<input type="button" value="' + title + '">');
	// 	// $this.parents('.input_file:eq(0)').width($this.parents('.input_file:eq(0)').children("[type=button]").width());
	// 	$this.change(function(e) {
	// 		var arquivo = replaceAll($this.val(), 'C:\\fakepath\\', '');
	// 		if($this.parents('.input_file:eq(0)').children(".input_file_value").length > 0) {
	// 			$this.parents('.input_file:eq(0)').children(".input_file_value").html(arquivo);
	// 		} else {
	// 			$this.parents('.input_file:eq(0)').append("<div class='input_file_value'>" + arquivo + "</div>");
	// 		}
	// 	});
	// });

	// $(".input_file input[type=button]").click(function (e) {
	// 	$(this).siblings("input[type=file]").click();
	// });
	var input, wrapper, fileInputs = document.getElementsByTagName('input');
	for (var i = 0; i < fileInputs.length; i++) {
		input = fileInputs[i];
		if (input.type === 'file') {
			// get the file-input element size
			inputWidth = input.clientWidth;
			inputHeight = input.clientHeight;

			// style file-input element
			input.style.opacity = 0;
			input.style.filter = 'alpha(opacity=0)';
			input.style.position = 'absolute';
			input.style.cursor = 'pointer';
			input.style.zIndex = '10';

			// wrap the file-input element
			wrapper = document.createElement('div');
			wrapper.appendChild(input.cloneNode(true));
			input.parentNode.replaceChild(wrapper, input);

			wrapper.style.overflow = 'hidden';
			wrapper.style.display = 'inline-block';
			wrapper.style.position = 'relative';
			wrapper.style.width = inputWidth + 'px';
			wrapper.style.height = inputHeight + 5 + 'px';

			// create the replace input elements
			var textInput = document.createElement('input');
				textInput.setAttribute('type', 'text');
				textInput.setAttribute('disabled', 'disabled');
				textInput.style.width = '60%';
			var btnInput = document.createElement('input');
				btnInput.setAttribute('type', 'button');
				btnInput.setAttribute('value', 'Escolher arquivo');
				btnInput.style.width = '38%';

			// write elements
			wrapper.appendChild(textInput);
			wrapper.appendChild(btnInput);

			wrapper.childNodes[0].onchange = function() {
				value = this.value;
				value = value.split('\\');
				value = value[(value.length-1)];
				this.parentNode.childNodes[1].value = value;
			};
		}
	}
}



function check_esp_char(stringTeste) {
	palavra = new RegExp('\^((?:[.]|[?]|[=]|[&]|[@]|[_]|[ ]|[-])|([0-9|a-zA-Z|.|_| |.|=|&|@|?|-]{0,100}))\$');
	if(!palavra.test(stringTeste)) {
		return false;
	} else {
		return true;
	}
}


function adjustStyle() {
	"use strict";
	var article_width = $(".main").width() - $(".main>aside").width() - ((5/100)*$(".main").width()) ;
	$(".main>article").width(article_width);
	var altura_total = document.body.scrollHeight;
	$(".main_nav").css("min-height",(altura_total-109-56));
}

function checkboxCmdToogle(check, cmd) {
	if($(check).is(":checked")) {
		cmd.slideDown();
	} else {
		cmd.slideUp();
	}
}


/*Cancela a edição no formulário e volta para a página indicada*/
function form_cancela(goTo)
{
	if(confirm("Ao cancelar todas as alterações realizadas não serão salvas, deseja cancelar?")){
		window.location.href=goTo;
	}else{
		return false;
	}
}


function get_param_value(url, name) {
	var urlparts = url.split('?');
	if (urlparts.length > 1) {
		var parameters = urlparts[1].split('&');
		for (var i = 0; i < parameters.length; i++) {
			var paramparts = parameters[i].split('=');
			if (paramparts.length > 1 && unescape(paramparts[0]) == name) {
				return unescape(paramparts[1]);
			}
		}
	}
	return null;
}


function embed_video(url,camada,largura,altura){

	if (url.search("vimeo") !== -1) {
		var urlEmb =  url.split("/");
		vidCodIndex = urlEmb.length - 1;
		url = "http://player.vimeo.com/video/"+urlEmb[vidCodIndex];
		$("#"+camada).html("<iframe src='"+url+"' width='"+largura+"' height='"+altura+"' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>");
	}

	if (url.search("dailymotion") !== -1) {
		var url_daily = url.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
		if (url_daily !== null) {
			if(url_daily[4] !== undefined) {
				url = 'http://www.dailymotion.com/swf/video/'+url_daily[4];
			}else{
				url = 'http://www.dailymotion.com/swf/video/'+url_daily[2];
			}
		}

		html =  '<object width="'+largura+'" height="'+altura+'">';
		html +=		'<param name="movie" value="'+url+'"></param>';
		html +=		'<param name="allowFullScreen" value="true"></param>';
		html +=		'<param name="allowScriptAccess" value="always"></param>';
		html +=		'<embed type="application/x-shockwave-flash" src="'+url+'" width="'+largura+'" height="'+altura+'" allowfullscreen="true" allowscriptaccess="always"></embed>';
		html += '</object>';

		$("#"+camada).html(html);
	}

	if (url.search("youtube") !== -1) {
		url = "http://www.youtube.com/embed/"+get_param_value(url, "v");
		$("#"+camada).html("<iframe src='"+url+"' width='"+largura+"' height='"+altura+"' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>");
	}
}

function show_file_image(input,camada) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		var mime = input.files[0].type;
		if (mime != 'image/jpeg' && mime != 'image/pjpeg' && mime != 'image/gif' && mime != 'image/png' && mime != 'image/bmp' && mime != 'image/x-windows-bmp' ) {
			alert("Arquivo inválido. \nSelecione um arquivo de imagem");
		}else{
			reader.onload = function (e) {
				$('#'+camada).attr('src', e.target.result);
			};
			reader.readAsDataURL(input.files[0]);
		}
	}
}

function get_video_thumb (url) {
	if (url.search("youtube.com") !== -1) {
		return 'http://img.youtube.com/vi/' + get_param_value(url, "v") + '/0.jpg';
	}
	if (url.search("vimeo.com") !== -1) {
		var urlEmb =  url.split("/");
		vidCodIndex = urlEmb.length - 1;
		var vimeoVideoID = urlEmb[vidCodIndex];
		$.getJSON('http://www.vimeo.com/api/v2/video/' + vimeoVideoID + '.json?callback=?', {format: "json"}, function (data) {
			return escape(data[0].thumbnail_large);
		});
	}
	if (url.search("dailymotion.com") !== -1) {
		return url.substr(0, 27) + 'thumbnail' + url.substr(26, url.length);
	}

}



function read_file_input(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            return e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}




// window.onload
//function onLoadWn() {
$(document).ready(function(){

	"use strict";

	//$(".date").mask("99/99/9999");

	$('.a_tooltip').tooltip();

	if($(".ctn-popover").length > 0) {
		$('.ctn-popover').popover({
			"trigger": "hover",
			"html": true
		});
	}

	if($(".striped").length > 0) {
		$(".striped tr:even, .striped li:even").addClass("striped-tb");
	}

	$('[data-loading-text]').on('click', function (event) {
		$(this).button('loading');
	});

	show_calendario();
	adjustStyle();
	// live_search();
	// format_input_file();

	// cria placeholder para IE
	if (!elmSupportAttr("input", "placeholder")) {
		Placeholder.init();
	}

	$('html, body').animate({ scrollTop: $(document.body).offset().top}, 'slow');

	// $("a[href=#]").click(function (event) { return false; });

	/*Ajusta a altura do menu*/


});
//}

// window.onresize
function onResizeWn() {
	"use strict";
	adjustStyle();
}

function confirmDelete(url){
	if (confirm('Você deseja excluir os dados ?')) {
		window.location = url;
	} else {
		return false;
	}
}

function confirmAction(msg,url){
	if (confirm(msg)){
		window.location = url;
	} else {
		return false;
	}
}

function onlyNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function isValidDate(s) {
	var bits = s.split('/');
	var y = bits[2],
		m = bits[1],
		d = bits[0];
	// Assume not leap year by default (note zero index for Jan)
	var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	// If evenly divisible by 4 and not evenly divisible by 100,
	// or is evenly divisible by 400, then a leap year
	if ((!(y % 4) && y % 100) || !(y % 400)) {
		daysInMonth[1] = 29;
	}
	return d <= daysInMonth[--m]
}

/**
 * verifica se elemento suporta determinado atributo - elmSupportAttr(element, attribute)
 * @param element, attribute
 * @return boolean
 */

function elmSupportAttr(a, b) {
	var c = document.createElement(a);
	return b in c ? !0 : !1;
}



function getBrowser() {
	if (window.XMLHttpRequest) {
		return "mozilla";
	} else if (window.ActiveXObject) {
		return "ie";
	}
}

function doXMLRequester() {
	if (getBrowser() == "ie") {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else if (getBrowser() == "mozilla") {
		return new XMLHttpRequest();
	}
}

function getLabelByInputId(elId) {
	var labels = document.getElementsByTagName('label');
	for (var i = 0; i < labels.length; i++) {
		if (labels[i].htmlFor == elId) {
			return labels[i];
		}
    }
}

function checkFormRequire(theForm,theMessageArea,callback) {
	callback = callback || false;

	var elements = theForm.elements,
		element,
		required,
		label,
		send = true;

	$(theMessageArea).slideUp();

	for (var i = elements.length - 1; i >= 0; i--) {
		element = elements[i];
		required = element.getAttribute('data-required');
		elementValue = element.value;
		if(elementValue.replaceAll != null){
			elementValue = elementValue.replaceAll(' ','');
		}

		if (required === 'true') {
			if (elementValue == '') {
				jQuery(element).addClass('error');
				jQuery(getLabelByInputId(element.name)).addClass('error');
				send = false;
			} else {
				if (element.type == 'email') {
					if (!is_valid_email(elementValue)) {
						jQuery(element).addClass('error');
						jQuery(getLabelByInputId(element.name)).addClass('error');
						send = false;
					} else {
						jQuery(element).removeClass('error');
						jQuery(getLabelByInputId(element.name)).removeClass('error');
					}
				} else {
					jQuery(element).removeClass('error');
					jQuery(getLabelByInputId(element.name)).removeClass('error');
				}
			}
		}else if (element.type == 'email' && elementValue != '') {
			if (!is_valid_email(elementValue)) {
				jQuery(element).addClass('error');
				jQuery(getLabelByInputId(element.name)).addClass('error');
				send = false;
			} else {
				jQuery(element).removeClass('error');
				jQuery(getLabelByInputId(element.name)).removeClass('error');
			}
		}
	}

	if (send) {
		if (callback !== false) {
			return callback(theForm);
		}else{
			theForm.submit();
		}
	} else {
		$('html, body').animate({ scrollTop: $(document.body).offset().top}, 'slow');
		$(theMessageArea).slideDown('fast',function(){
			$('[data-loading-text]').button('reset');
			setTimeout(function(){
				$(theMessageArea).slideUp('fast');
			},3000);
		});
	}

}



window.onresize = onResizeWn;


   function menuTransition(modCod){
   	$('.list-menu').css("background-color", "#eaeaea");
   	$('.seta-right'+modCod).parent().parent().css("background-color", "white");
   	$('.sub_nav').slideUp();
   	$('.fa-caret-down').hide();
   	$('.fa-caret-right').show();
   	$('.seta-right'+modCod).hide("fast", function(){
   		$('.seta-to-submenu'+modCod).show();
   	});
   }

   function showHideMenu(){

   }



function modulo_salva_ordem()
{
    var ordem = $("#gradesort").sortable("toArray");
    var tbl=5; //Midias
    var url = "library/ajax-directin.php?exe=1025&ordem="+ordem;
    var obj_ajax = http_request();
        obj_ajax.open("GET",url,true);
        obj_ajax.onreadystatechange = function(){
            if(obj_ajax.readyState == 4){
                if(obj_ajax.status == 200){
                    var resposta = obj_ajax.responseText;
                    if(resposta!="ok"){ console.log(resposta); }
                }
            }
        }
        obj_ajax.send(null);
}