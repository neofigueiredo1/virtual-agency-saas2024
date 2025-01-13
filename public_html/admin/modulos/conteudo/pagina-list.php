<ol class="breadcrumb">
    <li>Páginas</li>
</ol>

    <?php
        // CRIANDO A NAVEGAÇÃO DE ACORDO COM A PERMISSÃO
        if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-1') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-2')
             && !Sis::checkPerm($directIn->MODULO_CODIGO.'-3')  && !Sis::checkPerm($directIn->MODULO_CODIGO.'-4')){
            Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este módulo!', 1, '/admin/');
        }else{
            if (Sis::checkPerm($directIn->MODULO_CODIGO.'-1') || Sis::checkPerm($directIn->MODULO_CODIGO.'-2'))
            {
                ?>
                <div class="btn-group">
                    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina" disabled="disabled">Lista de páginas</a>
                    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add">Criar nova página</a>
                    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu">Menus</a>
                    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add">Criar novo menu</a>
                </div>
                <?php
            }else{
                if (Sis::checkPerm($directIn->MODULO_CODIGO.'-3')) {
                    ?>
                    <div class="btn-group">
                        <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina" disabled="disabled">Lista de páginas</a>
                        <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add">Criar nova página</a>
                    </div>
                    <?php
                }
                if (Sis::checkPerm($directIn->MODULO_CODIGO.'-4')) {
                    Sis::redirect('?mod=conteudo&pag=menu');
                }
            }
        }
    ?>

<hr />
<?php
    $list = $directIn->paginaList();
    if(is_array($list) && count($list) > 0){
?>
        <div class="panel panel-default">
          <!-- Default panel contents -->
            <div class="panel-heading">Lista de páginas do site</div>

            <?php
                echo "
                    <table class='table table-hover table-striped table_list'>
                        <thead>
                            <tr>
                                <th width='95%'><strong>Título</strong></th>
                                <th width='0%' class='left'><strong>Situação</strong></th>
                                <th width='0%' class='left'><strong>Ações</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class='list_no_padding' colspan='4'>
                                    <ol class='sortable ui-sortable' >";

                                        echo($directIn->getPageList(0,0,""));

                                    echo "
                                    </ol>
                                </td>
                            </tr>
                        </tbody>
                    </table>";
            ?>
        </div>
<?php
    } else {
        echo "
            <div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Nenhum registro para a lista.
            </div>
        ";
    }
?>

<script type="text/javascript" src="/admin/modulos/conteudo/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">jQuery.noConflict();</script>
<script type="text/javascript" src="/admin/modulos/conteudo/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/admin/modulos/conteudo/js/jquery.ui.touch-punch.js"></script>
<script type="text/javascript" src="/admin/modulos/conteudo/js/jquery.mjs.nestedSortable.js"></script>