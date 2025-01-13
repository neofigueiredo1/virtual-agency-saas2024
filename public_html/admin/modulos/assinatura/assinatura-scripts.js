var tbl = (typeof tbl !== 'undefined') ? tbl : 0;
function salva_ordem()
{
    var ordem = $("#imagens-lista").sortable("toArray");
    //var tbl=5; //Midias
    var url = "modulos/ecommerce/ecommerce-exe.php?exe=1&ordem="+ordem+"&tbl="+tbl;
    var obj_ajax = http_request();
        obj_ajax.open("GET",url,true);
        obj_ajax.onreadystatechange = function(){
            if(obj_ajax.readyState == 4){
                if(obj_ajax.status == 200){
                    var resposta = obj_ajax.responseText;
                    if(resposta!="ok"){ alert(resposta); }
                }
            }
        }
        obj_ajax.send(null);
}
$("#imagens-lista").sortable({ axis: "y",stop:salva_ordem });

function remove_imagem(id)
{
    //var tbl=5; //Midias
    var url = "modulos/ecommerce/ecommerce-exe.php?exe=2&tbl="+tbl+"&id="+id;
    var obj_ajax = http_request();
        obj_ajax.open("GET",url,true);
        obj_ajax.onreadystatechange = function(){
            if(obj_ajax.readyState == 4){
                if(obj_ajax.status == 200){
                    var resposta = obj_ajax.responseText;
                    if(resposta!="ok"){ alert(resposta); }else
                    {
                        $("#imagens-lista #"+id).fadeOut(1000,function(){ $("#imagens-lista #"+id).remove(); });
                        $("#img_lista_reposta").html("Imagem excluída com sucesso!");
                        $("#img_lista_reposta").slideDown();
                        $("#img_lista_reposta").delay(2000).slideUp();
                    }
                }
            }
        }
        obj_ajax.send(null);
}

function remove_selecionado()
{
    var selecionados = document.all.r_imagem;
    if(selecionados.length!=null){
        for(i=0;i<=selecionados.length;i++)
        {
            if(selecionados[i].checked){
                remove_imagem(selecionados[i].value);
            }
        }
    }else{
        if(selecionados.checked){
            remove_imagem(selecionados.value);
        }
    }

}

function prod_var_remove(elemento,pid,vid)
{
    $(elemento).parent().parent().slideUp('fast',function(){
        $.ajax({
            type:"POST",
            url:"/admin/modulos/ecommerce/produto-prodvar-form.php",
            data:{pid:pid,vid:vid,exe:23}
        }).done(function(data){
            $(elemento).parent().parent().parent().remove();
        });
    });
}

function prod_var_add()
{
    $.ajax({
        type:"POST",
        url:"/admin/modulos/ecommerce/produto-prodvar-form.php"
    }).done(function(data){
        $("#variacao_lista").append(data);
        $(".produto_variacao").slideDown('fast',function(){
            $('.produto_variacao .moneymask').mask('000.000.000.000.000,00', {reverse: true});
        });
    });
}

function prod_var_add_bat() {
    
    let quantidade = parseInt($('#batvat_quantidade').val());
    let produto_codigo = $('#codigo').val();
    let dataBase = {
        quantidade:((isNaN(quantidade)||quantidade<0)?1:quantidade),
        codigo:null,
        nome:null,
        dc_select:null,
    };
    
    let dataIdsPossibilities = [];
    let dataIdsPossibilitiesNames = [];
    
    let varDataIds = ($('#batvar_dc_ids').val()).split(",");
    let varDataIdsLength = varDataIds.length;
    let varDataIdsInit = false;
    
    console.log('varDataIdsLength: '+varDataIdsLength);

    for (var i=0; i < varDataIdsLength; i++) {
        
        console.log('varDataIdsInit:' + varDataIdsInit);

        $('.batvar_valor_'+varDataIds[i]+':checked').each(function(index){
            varDataIdsInit = true;
            var myId = '['+$(this).attr('data-valor-idx')+']';
            var myName = $(this).attr('data-nome');
            console.log('myName ('+i+') :' + myName);
            prod_var_add_bat_getRet(varDataIds,i+1,myId,myName,dataIdsPossibilities,dataIdsPossibilitiesNames);
        });

        console.log('varDataIdsInit:' + varDataIdsInit);

        if (varDataIdsInit){ break; }

    }

    

    // console.log(dataIdsPossibilities);
    // console.log(dataIdsPossibilitiesNames);

    for (var i=0; i < dataIdsPossibilities.length; i++) {

        var dataSend = dataBase;
            dataSend.codigo = (produto_codigo.trim()!="") ? produto_codigo + "-" + i.toString() : i.toString();
            dataSend.nome = dataIdsPossibilitiesNames[i];
            dataSend.dc_select = dataIdsPossibilities[i];

        console.log(dataSend);

        $.ajax({
            type:"POST",
            data:dataSend,
            url:"/admin/modulos/ecommerce/produto-prodvar-form.php"
        }).done(function(data){
            $("#variacao_lista").append(data);
            $(".produto_variacao").slideDown('fast',function(){
                $('.produto_variacao .moneymask').mask('000.000.000.000.000,00', {reverse: true});
            });
        });

    }
}

function prod_var_add_bat_getRet(array,indexNext,myId,myName,arrayPossibilities,arrayPossibilitiesNames){
    
    console.log('-----------------------');
    console.log('prod_var_add_bat_getRet');
    console.log('myName: ' + myName);
    console.log('indexNext: ' + indexNext);
    console.log('array.length: ' + array.length);


    if(indexNext<array.length){
        let myIdNext = '';
        let myNameNext = '';
        let hasNoNext = true;
        $('.batvar_valor_'+array[indexNext]+':checked').each(function(index){
            
            hasNoNext = false;

            console.log(':checked each ' + index);

            myIdNext = myId + '['+$(this).attr('data-valor-idx')+']';
            myNameNext = myName + ' - '+$(this).attr('data-nome');

            console.log('myIdNext:' + myIdNext)

            prod_var_add_bat_getRet(array,indexNext+1,myIdNext,myNameNext,arrayPossibilities,arrayPossibilitiesNames);

        });
        if (hasNoNext){
            myIdNext = myId;
            myNameNext = myName;
            prod_var_add_bat_getRet(array,indexNext+1,myIdNext,myNameNext,arrayPossibilities,arrayPossibilitiesNames);
        }
    }else{
        arrayPossibilities.push(myId);
        arrayPossibilitiesNames.push(myName);
    }
}


$( "#variacao_lista" ).sortable();


$(document).ready(function() {
    $('.moneymask').mask('000.000.000.000.000,00', {reverse: true});
});


var video_checking_data = false;
$(document).ready(function(){
    $('.video_url').keyup(function(e){
        var video_url = $(this).val();
        if(!video_checking_data){
            video_checking_data = true;
            var url = "modulos/ecommerce/ecommerce-exe.php?exe=3&url="+video_url;
            var obj_ajax = http_request();
                obj_ajax.open("GET",url,true);
                obj_ajax.onreadystatechange = function(){
                    if(obj_ajax.readyState == 4){
                        if(obj_ajax.status == 200){
                            $(".video_info").fadeOut('fast');
                            $(".video_image").html("");
                            $(".video_source").html("");
                            var resposta = obj_ajax.responseText;
                            var arr_dados = resposta.split("@@");
                            if(arr_dados.length==2){
                                $(".video_image").html("<img src='"+arr_dados[0]+"' />");
                                $(".video_source").html(arr_dados[1]);
                                $(".video_info").fadeIn('fast');
                            }
                            video_checking_data = false;
                        }
                    }
                }
                obj_ajax.send(null);
        }
    });


    var video_url = $('.video_url').val();
    var url = "modulos/ecommerce/ecommerce-exe.php?exe=3&url="+video_url;
    var obj_ajax = http_request();
        obj_ajax.open("GET",url,true);
        obj_ajax.onreadystatechange = function(){
            if(obj_ajax.readyState == 4){
                if(obj_ajax.status == 200){
                    $(".video_info").fadeOut('fast');
                    $(".video_image").html("");
                    $(".video_source").html("");
                    var resposta = obj_ajax.responseText;
                    var arr_dados = resposta.split("@@");
                    if(arr_dados.length==2){
                        $(".video_image").html("<img src='"+arr_dados[0]+"' />");
                        $(".video_source").html(arr_dados[1]);
                        $(".video_info").fadeIn('fast');
                    }
                }
            }
        }
        obj_ajax.send(null);
});