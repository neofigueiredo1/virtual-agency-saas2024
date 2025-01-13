$(document).ready(function(){
	if($("#n_publicacoes").length > 0) {
		$("#n_publicacoes").mask("9?999");
	}

	$(".module_backup legend").click(function(){
		$(this).parent().find(".list-backups").slideToggle('fast');
	})
});
