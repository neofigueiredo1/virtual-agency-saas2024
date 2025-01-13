<?php
    // VERIFICANDO A PERMISSÃO
    if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-4'))
    {
        Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
    }
    //Envio dos dados do formulário
    $exe = isset($_POST['exe']) ? $_POST['exe'] : "";
    if(!is_numeric($exe)){ $exe=0; }
    if($exe==1)
    {
        $directIn->tipoUpdate();
    }
    ?>

    <ul class="breadcrumb">
        <li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de banner</a></li>
        <li class="active">Editar tipo</li>
    </ul>

    <div class="btn-group">
        <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de banner</a>
        <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=banner&act=tipo-add" >Criar novo tipo</a>
    </div>

    <hr />

    <div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

    <?php
    //Listagem do banner
    if (is_numeric($_GET['tid']) && $_GET['tid']!=0) {
        $lista = $directIn->tipoListSelected($_GET['tid']);
        if(is_array($lista) && count($lista)>0){
            //Efeitos de transição do banner
            foreach ($lista as $lista_arr){
                ?>

    <form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" >
        <input type="hidden" name="exe" value="1">
        <input type="hidden" name="tid" value="<?php echo $lista_arr['tipo_idx'] ?>">
        <table class="table table_form">
            <tr>
                <th class="middle bg">Nome</th>
                <td colspan="3"><input type="text" class="form-control" data-required="true" name="nome" id="nome" value="<?php echo $lista_arr['nome'] ?>"></td>
            </tr>
            <tr>
                <th class="middle bg">Descrição</th>
                <td colspan="3">
                    <textarea name="descricao_secao" id="descricao_secao" rows="5" class="form-control" ><?php echo $lista_arr['descricao_secao'] ?></textarea>
                </td>
            </tr>
            <tr>
                <th class="middle bg">
                Tipos de Banners
                <a class="fa fa-info-circle ctn-popover" data-content="<p>Tipos de banners disponíveis e seus respectivos tamanhos</p>" data-original-title="Tipos de Banners"></a>
                </th>
                <td colspan="3">
                    <textarea name="subtipo_list_banner" id="subtipo_list_banner" rows="7" class="form-control" <?php echo ($_SESSION['usuario']["id"]!="2"&&$_SESSION['usuario']["nivel"]!="1")?"disabled":"";?> ><?php echo $lista_arr['subtipo_list_banner'] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="right" >
                    <input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list' }">
                    <input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box',checkCadTipoBanner);">
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
                Registro inexistente.
            </div>";
            Sis::redirect("?mod=".$mod."&pag=".$pag."&act=tipo-list",3);
        }
    } else {
        echo "
            <div class='alert alert-warning'>
            <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
            Registro inexistente.
        </div>";
        Sis::redirect("?mod=".$mod."&pag=".$pag."&act=tipo-list",3);
    }
?>
