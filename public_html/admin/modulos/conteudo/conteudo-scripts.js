jQuery("pagina-in").on('click', function (event) {

});

jQuery(window).on('load',function($) {

	jQuery('#paginas-out, #paginas-in').sortable({
		connectWith: "ul",
		refreshPositions: true,
		opacity: 0.8,
		scroll:true,
		placeholder: 'placeholder',
		tolerance: 'pointer',
		update: function( event, ui ) {
			var paginas = "";
			jQuery("#paginas-in").find("li").each(function (event) {
				if ( event!==0 ) { paginas += ","; }
				paginas += jQuery(this).attr("pagina-id");
			});
			jQuery("#paginas").val(paginas);
	}}).disableSelection();

	/*Para a lista de paginas*/
	if (jQuery('ol.sortable').length>0)
	{
		jQuery('ol.sortable').nestedSortable({
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div',
			maxLevels: 2,
			stop: function(){ _timeout = setTimeout(function(){  atualizaLista(jQuery('ol.sortable').nestedSortable('serialize')); },1000); },

			isTree: true,
			expandOnHover: 700,
			startCollapsed: false
		});
		jQuery('.disclose').on('click', function() {
			jQuery(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
		})
	}

});


	/*Para a lista de paginas*/
	var _timeout=null;
	var updating=false;

	function atualizaLista(serialized)
	{
		clearTimeout(_timeout);
		if(!updating)
		{
			updating=true;
			serialized = replaceAll(serialized,"&","@@");
			var urlForGet = "/admin/modulos/conteudo/conteudo-exe.php?exe=42&lserial="+serialized;
			$.ajax({
			  url: urlForGet
			}).done(function(data){
				updating=false;
			});
		}else{ _timeout = setTimeout(function(){ atualizaLista(serialized); },3000) }
	}

	function m_t_edit(obj){
		$(obj).parent().parent().find('.titulo').slideUp('fast');
		$(obj).parent().parent().find('.titulo_edit').slideDown('fast');
	}

	function m_t_save(obg){

		var texto = $(obg).parent().parent().find('.form-control').val();
		if(texto!=""){
			$(obg).parent().parent().find('.titulo span').html(texto);
		}else{
			$(obg).parent().parent().find('.titulo span').html($(obg).parent().parent().find('.titulo_original').val());
		}
		$(obg).parent().parent().find('.titulo').slideDown('fast');
		$(obg).parent().parent().find('.titulo_edit').slideUp('fast');
	}

	function testExistsTitleRewrite()
   {

      var titulo 	= $("#titulo").val();
      var url 		= "modulos/conteudo/conteudo-exe.php?exe=10&titulo="+titulo;
      var obj_ajax = http_request();
      obj_ajax.open("GET",url,true);
      obj_ajax.onreadystatechange = function(){
         if(obj_ajax.readyState == 4 && obj_ajax.status == 200){
            var resposta = obj_ajax.responseText;
            if(resposta=="ok"){ alert(resposta); }else{
            	$('input[name=url_pagina]').val(resposta);
            }
         }
      }
      obj_ajax.send(null);
   }

   function testExistsUrlRewrite()
   {
      var url       = $("#url_pagina").val();
      var url       = "modulos/conteudo/conteudo-exe.php?exe=11&url="+url;
      var obj_ajax  = http_request();

      obj_ajax.open("GET",url,true);
      obj_ajax.onreadystatechange = function(){
         if(obj_ajax.readyState == 4 && obj_ajax.status == 200){
            var resposta = obj_ajax.responseText;
            if(resposta!="ok"){
                $('input[name=url_pagina]').addClass(resposta);
                $('#erro-url').slideDown();
                $("#erro-url").delay(3000).slideUp('fast');
                $('input[name=url_pagina]').delay(2000).removeClass(resposta);
            }
         }
      }
      obj_ajax.send(null);
   }

