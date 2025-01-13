
$(window).on('load',function(){
	if ($('#faq-accordion').length>0)
	{
		$('#faq-accordion').accordion({collapsible: true});
		$('#faq-accordion').on( "click", function( event, ui ) {
			setTimeout(function(){
				var activeIndex = $("#faq-accordion" ).accordion( "option", "active" );
				$('html, body').animate({ scrollTop: ($('#faq-accordion .item-h').eq(activeIndex).offset().top - 80)}, 'slow');
			},500);
		} );
	};
});