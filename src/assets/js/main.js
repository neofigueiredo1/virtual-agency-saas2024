// Scroll Nav

function sectionScroll(index) {
  var targetOffset = $(index).offset().top;
  var variacao = 30;
  $('html, body').animate({
    scrollTop: targetOffset - variacao
  }, 3000);
}


$(document).ready(function(){

    // Não permite copia do site
        // $('body').bind('cut copy', function(e) {
        //     e.preventDefault();
        // });
        // $("body").on("contextmenu", function(e) {
        //   return false;
        // });


    /*
    * Replace all SVG images with inline SVG
    */
    $('img.svg').each(function () {
        var $img = $(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');

        $.get(imgURL, function (data) {
          // Get the SVG tag, ignore the rest
          var $svg = $(data).find('svg');

          // Add replaced image's ID to the new SVG
          if (typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
          }
          // Add replaced image's classes to the new SVG
          if (typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', imgClass + ' replaced-svg');
          }

          // Remove any invalid XML tags as per http://validator.w3.org
          $svg = $svg.removeAttr('xmlns:a');

          // Replace image with new SVG
          $img.replaceWith($svg);

        }, 'xml');

    });


    //Carousel Depoimentos Home
    $('.s_banner_capa_topo').owlCarousel({
        margin: 0,
        nav: true,
        autoplay: false,
        dots: true,
        items: 1
    });

     //Carousel Depoimentos Home
    $('.s_a_vencer_items').owlCarousel({
        margin: 0,
        nav: true,
        autoplay: false,
        dots: true,
        items: 1
    });

    


    //Carousel Depoimentos Home
    $('.depoimentos_lista').owlCarousel({
        margin: 20,
        autoplay: false,
        nav: true,
        dots: true,
        items: 3,
        responsive : {
            0 : {
                items: 1,
                nav: false,
                dots: true,
            },
            600 : {
                items: 2,
                nav: false,
                dots: true,
            },
            1000: {
                items: 3
            }
        }
    });

    //Carousel Depoimentos Home
    $('.noticias_lista_carousel').owlCarousel({
        margin: 20,
        autoplay: false,
        nav: true,
        dots: true,
        items: 3,
        responsive : {
            0 : {
                items: 1,
                nav: false,
                dots: true,
            },
            600 : {
                items: 2,
                nav: false,
                dots: true,
            },
            1000: {
                items: 3
            }
        }
    });

    $('.s_publicaçoes_relacionadas_carousel').owlCarousel({
        margin: 20,
        nav: true,
        autoplay: false,
        dots: true,
        items: 3,
        responsive : {
            0 : {
                items: 1
            },
            600 : {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });


    // Scroll Menu Header
    $('a.btn-simulacao').on('click', function (e) {
      var href = ($(this).attr('href')).split('#');
      if (href.length == 2) {
        var id = '#' + href[1];
        if ($(id).length > 0) {
          e.preventDefault();
          sectionScroll(id);
        }
      }
    });


    //Mascaras para os campos
    $('.mask_date').mask('00/00/0000');
    $('.mask_cep').mask('00000-000');
    $('.mask_phone_with_ddd').mask('(00) 00000-0000');
    $('.mask_cpf').mask('000.000.000-00');
    $('.mask_cnpj').mask('00.000.000/0000-00');

    var SPMaskBehavior = function (val) {
      return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    spOptions = {
      onKeyPress: function(val, e, field, options) {
          field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    $('.mask_money').mask('000.000.000.000.000,00', {reverse: true});

    $('.mask_spcelphones').mask(SPMaskBehavior, spOptions);

    var CpfCnpjMaskBehavior = function(val) {
       
        return val.replace(/\D/g, '').length <= 11 ? '000.000.000-0099' : '00.000.000/0000-00';
    },
    cpfCnpjOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(CpfCnpjMaskBehavior.apply({}, arguments), options);
          }
      };
    $(".mask_cpfcnpj").mask(CpfCnpjMaskBehavior, cpfCnpjOptions);


    //Bootstrap Tootip
    $(function () {
      $('[data-toggle="popover"]').popover();
    })

});

$('#button_notificar').on('click', function(){

    $('.button_block_notificar').slideToggle('fast');

});