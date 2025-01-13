var tbl = (typeof tbl !== 'undefined') ? tbl : 0;
function salva_ordem()
{
    var ordem = $("#imagens-lista").sortable("toArray");
    //var tbl=5; //Midias
    var url = "modulos/faq/faq-exe.php?exe=1&ordem="+ordem+"&tbl="+tbl;
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