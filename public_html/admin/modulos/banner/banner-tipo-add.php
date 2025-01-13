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
    $directIn->tipoInsert();
}
?>

<ul class="breadcrumb">
    <li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de Banner</a></li>
    <li class="active">Criar novo tipo</li>
</ul>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de banner</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add" disabled="disabled" >Criar novo tipo</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" >
    <input type="hidden" name="exe" value="1">
    <table class="table table_form">
        <tr>
            <th class="middle bg">Nome</th>
            <td colspan="3"><input type="text" class="form-control" data-required="true" name="nome" id="nome"></td>
        </tr>
        <tr>
            <th class="middle bg">Descrição</th>
            <td colspan="3"><textarea class="form-control" name="descricao_secao" id="descricao_secao" rows="5"></textarea>
        </tr>
        <tr>
            <th class="middle bg">
            Tipos de Banners
            <a class="fa fa-info-circle ctn-popover" data-content="<p>Tipos de banners disponíveis e seus respectivos tamanhos</p>" data-original-title="Tipos de Banners"></a>
            </th>
            <td colspan="3">
                <textarea name="subtipo_list_banner" id="subtipo_list_banner" rows="7" class="form-control" <?php echo ($_SESSION['usuario']["id"]!="2"&&$_SESSION['usuario']["nivel"]!="1")?"disabled":"";?>></textarea>
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
