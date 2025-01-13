
<!-- Sessao Entre em contato e hospede seu curso conosco -->
<section class="s_entre_em_contato bg-azul py-md-5" >
   <div class="w-100 mxw-650 m-auto py-5 px-3" >
      
      <div class="mb-5" >
         <h2 class="branco text-center mxw-450 m-auto" ><?php echo Sis::config("SESSAO-HOSPEDE-SE-CURSO-TITULO"); ?></h2>
         <p class="branco text-center"><?php echo Sis::config("SESSAO-HOSPEDE-SE-CURSO-DESCRICAO"); ?></p>
      </div>

      <form id="hospeda_frm" class="contato_frm" name="hospeda_frm" role="form" action="" >
         <input type="hidden" name="postact" value="sendContato" />
         <input type="hidden" name="origem" value="hospedeSeuCurso" />

         <div id='contato_form_sucesso' class="contato_form_sucesso alert alert-success fs-18 cormo-medium" style='display:none;' >
            Sua mensagem foi enviada com sucesso!
         </div>
         
         <div id='contato_form_preloader' class="contato_form_preloader alert text-center fs-18 cormo-medium branco" style='display:none;' >
            <img src="/assets/images/preloader.gif" align="absmiddle" style="display:inline-block;margin:0px;" width="150" /><br/>
            <small><i>Aguarde, enviando sua mensagem...</i></small>
         </div>

         <div id="contato-help" class="contato-help alert alert-danger fs-18 cormo-medium" style='display:none;' >Preencha os campos abaixo corretamente.</div>

         <div class="clear clear_gray"></div>

         <div class="contato_form_body">

            <div class="form-group">
               <label class="branco">Nome</label>
               <input type="text" class="form-control" name="nome" placeholder="Seu nome" data-required="true" />
            </div>
            <div class="form-group">
               <label class="branco">E-mail</label>
               <input type="email" class="form-control" name="email" placeholder="email@exemplo.com.br" data-required="true" />
            </div>
            <div class="form-group">
               <label class="branco">Telefone</label>
               <input type="text" class="form-control mask_spcelphones" id="hc_telefone" name="telefone" placeholder="(xx) x xxxx-xxxx" data-required="true" />
            </div>
            <div class="form-group">
               <label class="branco">Mensagem</label>
               <textarea class="form-control" id="hc_mensagem" name="mensagem" rows="5" ></textarea>
            </div>
            <div class="d-flex justify-content-center mt-4">
               <input type="button" class="btn btn-azul-1 mxw-650 d-flex align-items-center justify-content-center" onclick="javascript:Util.checkFormRequire(document.hospeda_frm,'#contato-help',sendContato);" value="Enviar" />
            </div>

         </div>
         
      </form>
   </div>
</section>