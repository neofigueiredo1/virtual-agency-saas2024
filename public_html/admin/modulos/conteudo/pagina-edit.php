<?php
    $pagId  = isset($_GET['pg_id'])   && is_numeric($_GET['pg_id'])   ? (int)$_GET['pg_id']   : 0 ;
    $enviar = isset($_POST['enviar']) && is_numeric($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;
    if ($enviar === 1){
        $directIn->paginaUpdate();
    }
    //Pasta de armazenamento
    $LocalModuloImgDir  = $directIn->pasta_modulo_images;


    // VERIFICANDO A PERMISSÃO
    if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-2') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-3'))
    {
        Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este recurso!', 1, '/admin/');
    }
?>

<ol class="breadcrumb">
    <li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Páginas</a></li>
    <li>Editar página</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina">Lista de páginas</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add">Criar nova página</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu">Menus</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add">Criar novo menu</a>
</div>

<hr>

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>


<?php
    $listSelected = $directIn->paginaListSelected($pagId);
    if(is_array($listSelected) && count($listSelected) > 0){
        foreach($listSelected as $arrayList){ ?>
            <form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" enctype="multipart/form-data" >
                <input type="hidden" name="enviar" value="1">
                <input type="hidden" name="pg_id" id="pg_id" value="<?php echo $pagId; ?>" >
                <input type="hidden" name="titulo_old" value="<?php echo $arrayList['titulo']; ?>">
                <input type="hidden" name="url_pagina_old" value="<?php echo $arrayList['url_pagina']; ?>">
                <input type="hidden" name="status_old" value="<?php echo $arrayList['status']; ?>">
                <input type="hidden" name="mae_old" value="<?php echo $arrayList["pagina_mae"]; ?>">
                <input type="hidden" name="enviar" value="1">

                <table class="table table_form" border="0">
                    <tr>
                        <th class="middle bg">Situação</th>
                        <td width="30%" class="middle">
                            <label class="radio-inline">
                              <input type="radio" id="" name="status" value="1" <?php echo ($arrayList['status'] == 1) ? 'checked' : ""; ?> >On-line
                            </label>
                            <label class="radio-inline">
                              <input type="radio" id="" name="status" value="0" <?php echo ($arrayList['status'] == 0) ? 'checked' : ""; ?>> Off-line
                            </label>
                        </td>
                        <th class="middle bg">Índice</th>
                        <td width="30%">
                            <input type="text" name="indice" id="indice" value="<?php echo $arrayList['indice']; ?>" class="form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td class="top bg">
                            <a class="btn btn-default btn-larg-todo" href="javascript:void(0);" onclick="$('.caixa_itens_avancados').stop().slideToggle('slow');">Itens Avançados</a>
                        </td>
                        <td colspan="3" style="" class="">
                            <span class="caixa_itens_avancados middle desc_itens_avanc"  style=" display: <?php echo ($arrayList["pagina_mae"] != "0" || $arrayList["link_externo"] != "" || $arrayList["extra"] != "") ? "none" : "block"; ?>;">Configuração de página mãe, link externo, Url amigável etc.</span>
                            <div class="clearfix"></div>
                            <div class="caixa_itens_avancados config_avanc_box" style=" display: <?php echo ($arrayList["pagina_mae"] != "0" || $arrayList["link_externo"] != "" || $arrayList["extra"] != "") ? "block" : "none"; ?>;">
                                <table class="table table_form itens_avancados" border="0">
                                    <tr>
                                        <td width="50%">
                                            Página mãe
                                            <select name="pagina_mae" id="pagina_mae" class="form-control">
                                                <option value="0">Não possui página mãe</option>
                                                <?php
                                                    $list = $directIn->paginaList();
                                                    if(isset($list) && $list !== false){
                                                        foreach ($list as $maeArrayList){
                                                            ?>
                                                                <option <?php if ($maeArrayList["pagina_idx"] == $arrayList["pagina_mae"] ) { echo "selected"; } ?> value="<?php echo $maeArrayList["pagina_idx"]; ?>">
                                                                    <?php echo $maeArrayList["titulo"]; ?>
                                                                </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                            </select>
                                        </td>
                                        <td width="50%">
                                            URL amigável
                                            <input type="text" name="url_pagina" id="url_pagina" class="form-control" onkeyup="testExistsUrlRewrite()" value="<?php echo $arrayList['url_rewrite'] ?>">
                                            <div class="clearfix"></div>
                                            <div class="alert alert-danger" id="erro-url" style="display:none; margin-top: 5px; margin-bottom: 0px; padding: 7px;">Esta url já existe</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Link externo
                                            <input type="text" name="link_externo" id="link_externo" class="form-control" value="<?php echo $arrayList['link_externo'] ?>" />
                                        </td>
                                        <td>
                                            Alvo
                                            <select name="alvo_link" id="alvo_link" class="form-control">
                                                <option value="0"       <?php if($arrayList['alvo_link'] == 0){ echo "selected";} ?>>Configuração padrão</option>
                                                <option value="_self"   <?php if($arrayList['alvo_link'] == "_self"){ echo "selected";} ?>>Mesma janela</option>
                                                <option value="_blank"  <?php if($arrayList['alvo_link'] == "_blank"){ echo "selected";} ?>>Nova janela</option>
                                                <option value="_parent" <?php if($arrayList['alvo_link'] == "_parent"){ echo "selected";} ?>>Janela pai</option>
                                                <option value="_top"    <?php if($arrayList['alvo_link'] == "_top"){ echo "selected";} ?>>Janela superior</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            Códigos extras
                                            <textarea name="extra" id="extra" rows="5" class="form-control"><?php echo $arrayList['extra'] ?></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>


                    <?php
                        if ($arrayList["titulo_seo"] != "" || $arrayList["palavra_chave"] != "" || $arrayList["descricao"] != ""){
                            echo "
                                <style>
                                    .desc_itens_avanc2{ display: none; }
                                    .config_avanc_box2{ display: block !important; }
                                </style>
                            ";
                        }
                    ?>


                    <tr>
                        <td class="top bg">
                            <a class="btn btn-default btn-larg-todo"  href="javascript:void(0);" onclick="$('.caixa_itens_avancados2').stop().slideToggle('slow');">Cofigurações de SEO</a>
                        </td>
                        <td colspan="3" style="" class="">
                            <span class="caixa_itens_avancados2 middle desc_itens_avanc desc_itens_avanc2" style="display:block;">Configuração do título da página, descrição e palavras-chave.</span>
                            <div class="clearfix"></div>
                            <div class="caixa_itens_avancados2 config_avanc_box2" style="display:none;">
                                <table class="table table_form itens_avancados" border="0">
                                    <tr>
                                  <td>
                                    Título da página
                                    <input type="text" class="form-control" name="titulo_seo" id="titulo_seo" value="<?php echo $arrayList['titulo_seo'] ?>" />
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    Palavras-chave
                                    <textarea name="palavra_chave" class="form-control" rows="3" id="palavra_chave"><?php echo $arrayList['palavra_chave'] ?></textarea>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="3">
                                    Descrição
                                    <textarea name="descricao" class="form-control" rows="4" id="descricao"><?php echo $arrayList['descricao'] ?></textarea>
                                  </td>
                                </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th width="20%" class="middle bg">Título*</th>
                        <td colspan="3">
                            <input type="text" name="titulo" id="titulo" class="form-control" data-required="true" onkeyup="testExistsTitleRewrite()" value="<?php echo $arrayList['titulo'] ?>">
                        </td>
                    </tr>

                    <tr>
                        <th class="middle bg top">Conteúdo</th>
                        <td colspan="3">
                            <textarea class="ckeditor" name="conteudo" id="conteudo" style="height:505px;"><?php echo $arrayList['conteudo'] ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="3" class="right">
                            <input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
                            <input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
                        </td>
                    </tr>

               </table>

            </form>
        <?php

        }
    }else{
        echo "
                <div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Nenhum registro encontrado.
             </div>";
        Sis::redirect("?mod=" . $mod . "&pag=" . $pag, 2);
    }
?>

<script type="text/javascript">

    function testExistsTitleRewrite()
    {
      var titulo    = $("#titulo").val();
      var url       = "modulos/conteudo/conteudo-exe.php?exe=10&id=<?php echo $pagId; ?>&titulo="+titulo;
      var obj_ajax  = http_request();

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
        var url_pagina    = $("#url_pagina").val();
        var url           = "modulos/conteudo/conteudo-exe.php?exe=11&edit=<?php echo $pagId; ?>&url="+url_pagina;
        var obj_ajax      = http_request();
        if (url_pagina != ""){
        obj_ajax.open("GET",url,true);
        obj_ajax.onreadystatechange = function(){
            if(obj_ajax.readyState == 4 && obj_ajax.status == 200){
                var resposta = obj_ajax.responseText;
                if(resposta!="ok"){
                    var bk_old = $("#url_pagina").val();
                    $('input[name=url_pagina]').addClass(resposta);
                    $("#erro-url").slideDown('fast', function(){
                        setTimeout(function(){
                            $("#url_pagina").val(bk_old+"-i");
                            $("#erro-url").slideUp('fast');
                        },2000);
                    });
                    $('input[name=url_pagina]').delay(2000).removeClass(resposta);
                }
            }
          }
          obj_ajax.send(null);
      }
   }

</script>