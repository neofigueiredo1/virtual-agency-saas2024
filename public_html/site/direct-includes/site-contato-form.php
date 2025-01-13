<?php
   $SUPORTE_MOTIVOS_CONTATO = explode(PHP_EOL,Sis::config("SUPORTE-MOTIVOS-CONTATO"));
?>
<div class="box_form pricipal" style="width:100%;" >
   
   <form id="contato_frm" class="contato_frm" name="contato_frm" role="form" action="" >
      <input type="hidden" name="postact" value="sendContato" />
      <input type="hidden" name="origem" value="contato" />

      <div id='contato_form_sucesso' class="contato_form_sucesso alert alert-success fs-18 cormo-medium" style='display:none;' >
         Sua mensagem foi enviada com sucesso!
      </div>
      
      <div id='contato_form_preloader' class="contato_form_preloader alert text-center fs-18 cormo-medium" style='display:none;' >
         <img src="/assets/images/preloader.gif" align="absmiddle" style="display:inline-block;margin:0px;" width="150" /><br/>
         <small><i>Aguarde, enviando sua mensagem...</i></small>
      </div>

      <div id="contato-help" class="contato-help alert alert-danger fs-18 cormo-medium" style='display:none;' >Preencha os campos abaixo corretamente.</div>

      <div class="clear clear_gray"></div>

      <div class="contato_form_body">
         <div class="form-row">
            <div class="form-group col-lg-12 mb-3">
               <!-- <label>Nome</label> -->
               <input type="text" class="form-control" id="c_nome" name="nome" data-required="true" placeholder="Nome" />
            </div>
         </div>
         <div class="form-row">
            <div class="form-group col-lg-6 mb-3">
               <!-- <label>E-mail</label> -->
               <input type="email" class="form-control" id="c_email" name="email" data-required="true" placeholder="E-mail" />
            </div>
            <div class="form-group  col-lg-6 mb-3">
               <!-- <label>Telefone</label> -->
               <input type="text" class="form-control mask_spcelphones" id="c_telefone" name="telefone" placeholder="Telefone" />
            </div>
         </div>
         <div class="form-row" >
            <div class="form-group col-lg-6">
               <label class="d-flex align-items-center justify-content-center btn w-100 perfil-tipo-1 btn-azul-1 rounded-12" >
                  <input type="radio" name="perfil" value="Professor" style="position:absolute;visibility:hidden;" checked 
                     onclick="javascript:
                     $('.perfil-tipo-1').removeClass('btn-outline-azul-1'); 
                     $('.perfil-tipo-1').addClass('btn-azul-1'); 
                     $('.perfil-tipo-2').addClass('btn-outline-azul-1'); 
                     $('.perfil-tipo-2').removeClass('btn-azul-1'); 
                     $('.mat-novato-desc').slideDown('fast'); 
                     $('.mat-vetereano-desc').slideUp('fast'); ">
                  Professor
               </label>
            </div>
            <div class="form-group col-lg-6">
               <label class="d-flex align-items-center justify-content-center btn w-100 perfil-tipo-2 btn-outline-azul-1 rounded-12" >
                  <input type="radio" name="perfil" value="Aluno" style="position:absolute;visibility:hidden;"
                     onclick="javascript: 
                     $('.perfil-tipo-2').removeClass('btn-outline-azul-1'); 
                     $('.perfil-tipo-2').addClass('btn-azul-1'); 
                     $('.perfil-tipo-1').addClass('btn-outline-azul-1'); 
                     $('.perfil-tipo-1').removeClass('btn-azul-1'); 
                     $('.mat-novato-desc').slideUp('fast'); 
                     $('.mat-vetereano-desc').slideDown('fast'); ">
                  Aluno
               </label>
            </div>
         </div>
         <div class="form-group">
            <select class="form-control" id="c_assunto" name="assunto" placeholder="Motivo de contato" >
               <option value="" >Motivo de contato</option>
               <?php foreach ($SUPORTE_MOTIVOS_CONTATO as $key => $s_motivos ): ?>
                  <?php if (trim($s_motivos)!=''): ?>
                     <option value="<?php echo $s_motivos; ?>" ><?php echo $s_motivos; ?></option>
                  <?php endif ?>
               <?php endforeach ?>
            </select>
         </div>
         <div class="form-row">
            <div class="form-group col-lg-12 mb-3">
               <input type="text" class="form-control" id="c_nome_curso" name="nome_curso" placeholder="Curso (opcional)" />
            </div>
         </div>
         <div class="form-group">
            <!-- <label>Mensagem</label> -->
            <textarea rows="5" class="form-control" id="c_mensagem" data-required="true" name="mensagem" placeholder="Mensagem" ></textarea>
         </div>

         <div class="form-group mt-2 text-center text-md-right">
            <input type="button" class="btn btn-azul-1 mxw-200" onclick="javascript:Util.checkFormRequire(document.contato_frm,'#contato-help',sendContato);" value="Enviar" />
         </div>
      </div>
      
      
   </form>

    <div class="clear" ></div>
</div>