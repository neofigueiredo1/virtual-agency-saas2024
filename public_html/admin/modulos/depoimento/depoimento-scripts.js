// function salva_ordem_galeria()
// {
//     var ordem = $("#imagens-lista").sortable("toArray");
//     var tbl=5; //Midias
//     var url = "modulos/galeria/galeria-exe.php?exe=1&ordem="+ordem+"&tbl="+tbl;
//     var obj_ajax = http_request();
//         obj_ajax.open("GET",url,true);
//         obj_ajax.onreadystatechange = function(){
//             if(obj_ajax.readyState == 4){
//                 if(obj_ajax.status == 200){
//                     var resposta = obj_ajax.responseText;
//                     if(resposta!="ok"){ alert(resposta); }
//                 }
//             }
//         }
//         obj_ajax.send(null);
// }
// $("#imagens-lista").sortable({ axis: "y", stop:salva_ordem_galeria });


function salva_ordem()
{
    var ordem = $("#imagens-lista").sortable("toArray");
    var tbl=5; //Midias
    var url = "modulos/depoimento/depoimento-exe.php?exe=1&ordem="+ordem+"&tbl="+tbl;
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
    var tbl=5; //Midias
    var url = "modulos/depoimento/depoimento-exe.php?exe=2&tbl="+tbl+"&id="+id;
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