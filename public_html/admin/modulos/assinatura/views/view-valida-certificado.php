<?php
global $certificadoData,$certificadoCodigo;
?>
<div class="card mt-3 p-4" >
    <div class="container" >
        
        <?php if (is_null($certificadoCodigo)): ?>
            <form name="form_valida_certificado" action="" method="POST" >
                <div id="validaCert-help" class="validaCert-help alert alert-danger fs-18 cormo-medium" style='display:none;' >Preencha o campo abaixo corretamente.</div>
                <div class="form-group">
                    <label for="cert_codigo">Informe o código do seu certificado</label>
                    <input type="text" class="form-control" id="cert_codigo" name="cert_codigo" aria-describedby="certHelp" placeholder="CER-XXXXXXXXXXXX" data-required="true" />
                    <small id="certHelp" class="form-text text-muted" >Ele fica localizado na parte inferior direita.</small>
                </div>
                <button type="button"  onclick="javascript:Util.checkFormRequire(document.form_valida_certificado,'#validaCert-help');"  class="btn btn-primary">Validar</button>
            </form>
        <?php else: ?>
            <?php if (is_array($certificadoData)&&count($certificadoData)): ?>
               

                <h3 class="text-success" >
                    Certificado válido!<br>
                    <small class="fs-16">CER-<?php echo $certificadoData[0]['certificado_codigo'] ?> emitido em: <?php echo date('d/m/Y',strtotime($certificadoData[0]['data_emissao'])); ?> </small>
                </h3>
                 
                 Este certificado foi emitido para <b><?php echo $certificadoData[0]['cadastro_nome'] ?></b>, por ter concluído a carga horária de <b><?php echo date('H\h',strtotime($certificadoData[0]['curso_horas'])); ?></b> no curso <b><?php echo $certificadoData[0]['curso_nome'] ?></b>


            <?php else: ?>
                <div class="alert alert-warning d-flex" >
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="pl-2" >
                        O Código informado não está associado a nenhum certificado emitido pelo Instituto.
                    </div>
                </div>
                <a href="/valide-seu-certificado" class="btn btn-primary">Voltar e validar outro código</a>
            <?php endif ?>
        <?php endif ?>

    </div>
</div>