<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10010-2') && !Sis::checkPerm('10010-3'))
{
    Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}

//Envio dos dados do formulário
$exe = isset($_POST['exe']) ? $_POST['exe'] : "";
if(!is_numeric($exe)){ $exe=0; }
if($exe==1)
{
    $directIn->theInsert();
}


?>

<ul class="breadcrumb">
    <li><a href="?mod=<?php echo $mod; ?>&pag=depoimento">Depoimentos</a></li>
    <li class="active" >Adicionar novo depoimento</li>
</ul>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=depoimento">Depoimentos</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=depoimento&act=add" disabled="disabled" >Criar novo depoimento</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;" >
    <i class="fa fa-exclamation-triangle"></i> &nbsp;&nbsp; Preencha todos os campos corretamente!
</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" enctype="multipart/form-data"  >
    <input type="hidden" name="exe" value="1">
    <table class="table table_form">
        <tr>
            <th width="20%" class="middle bg">Situação</th>
            <td width="80%" colspan="3">
                <label class="radio-inline"><input type="radio" name="status" value="1" id="status1" checked > On-line</label>
                <label class="radio-inline"><input type="radio" name="status" value="0" id="status0" > Off-line</label>
            </td>
        </tr>
        <tr>
            <th class="middle bg">Nome</th>
            <td colspan="3"><input type="text" class="form-control" data-required="true" name="nome" id="nome"></td>
        </tr>
        
        <tr>
            <th class="top bg" valign="top">Descrição</th>
            <td colspan="3"><textarea name="descricao" id="descricao" class="form-control" rows="8" ></textarea></td>
        </tr>
        <tr>
            <th class="top bg">Imagem</th>
            <td colspan="3" >
                Dimensões: <br />300px X 300px (Largura X Altura) <br /><br />
                <input type="file" name="imagem" id="imagem" title="Selecionar imagem" />
            </td>
        </tr>
        <tr>
            <td colspan="4" class="right" >
                <input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
                <input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
            </td>
        </tr>
    </table>
</form>