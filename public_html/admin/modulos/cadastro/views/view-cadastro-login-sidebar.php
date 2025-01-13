<?php global $csrfToken; ?>
<div class="my-130">
    <div class="accordion-item accordion--cadastro" >
        <div>
            <h2 class="quicksand-bold preto-medio text-uppercase cursor mb-45" >
            	JÁ POSSUI CADASTRO?
                <span class="d-block quicksand-light text-initial">Entre na sua conta</span>
            </h2>
            <form action="" name="form_login" method="post" class="mb-45 px-md-2" id="form-login" >
            	<input type="hidden" name="ac" value="auth" />
            	<input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>" />
				
				<div class="alert alert-danger mb-20" id="error-box" >Preencha os dados corretamente para continuar!</div>
				
				<div class="placeholder-label" >
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Seu e-mail" />
                    <label for="email" >E-mail <span class='asterisco' >*</span></label>
                </div>
                <div class="placeholder-label" >
                    <input type="password" name="senha" class="form-control" placeholder="Sua senha" required />
                    <label for="email" >Senha <span class='asterisco' >*</span></label>
                </div>
                <div class="p-0" >
                    <div class="form-check d-inline-block mr-5" >
                        <input type="checkbox" class="form-check-input" id="lembrar_dados" name="lembrar_dados" value="1" />
                        <label class="form-check-label" for="lembrar_dados" >Lembrar meus dados</label>
                    </div>
                    <a href="#" class="link-recupera-senha" onclick="javascript:if(document.form_login.email.value!=''){ MJS_Cadastro.recoverPass(document.form_login); }else{ $('#error-box').slideDown('normal',function(){ setTimeout(function(){ $('#error-box').fadeOut(); },3000) }); }" > Recuperar minha senha </a>
                </div>
                <div class="text-right" >
                    <button type="button" class="btn btn-outline-secondary" onclick="javascript:Util.checkFormRequire(document.form_login, '#error-box', MJS_Cadastro.login);" >Entrar</button>
                </div>
            </form>
        </div>
    </div>
    <hr />
    <div class="accordion-item accordion--cadastro mt-5" >
    	<h2 class="quicksand-bold preto-medio text-uppercase mb-45 cursor">
        	NÃO POSSUI CADASTRO?
            <span class="d-block quicksand-light text-initial">Crie agora uma conta</span>
        </h2>

        <form action="" name="form_cadastro" method="post" class="mt-n3 px-md-2" id="form-cadastro" >
        	
        	<input type="hidden" name="ac" value="newRegister" />
        	<input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>" >

        	<div class="alert alert-danger mb-20" id="error-box-cad" >Preencha os dados corretamente para continuar!</div>

        	<div class="placeholder-label">
				<input type="text" class="form-control" placeholder="Seu nome completo" name="nome_completo" id="nome_completo" required />
				<label for="nome_completo" >Nome <span class='asterisco' >*</span></label>
			</div>
			<div class="placeholder-label">
				<input type="email" class="form-control" placeholder="Seu e-mail" id="email2" name="email" required />
				<label for="email2" >E-mail <span class='asterisco' >*</span></label>
			</div>
			<div class="placeholder-label">
				<input type="text" class="mask_telefone form-control" name="telefone_resid" id="telefone_resid" placeholder="Nº do seu telefone" maxlength="16" required />
				<label for="telefone_resid" >Telefone <span class='asterisco' >*</span></label>
			</div>
			<div class="placeholder-label">
				<input type="password" class="form-control" placeholder="Sua senha" id="senha2" name="senha" required />
				<label for="senha2" >Senha <span class='asterisco' >*</span></label>
			</div>
			<div class="placeholder-label">
				<input type="password" class="form-control" placeholder="Confirme a sua senha" id="senha_conf" name="senha_conf" required />
				<label for="senha_conf" >Confirme a senha <span class='asterisco' >*</span></label>
			</div>

            <div class="p-0">
                <div class="form-check mr-5" >
                    <input type="checkbox" class="form-check-input" id="receber_boletim" name="receber_boletim" value="1" />
                    <label class="form-check-label fs-16" for="receber_boletim" >Desejo receber novidades do site no meu e-mail</label>
                </div>
                <div class="form-check mr-5" >
                    <input type="checkbox" class="form-check-input" name="termos" id="termos" value="1" />
                    <label class="form-check-label fs-16" for="termos" >Li, compreendi, e aceito os <a href="/termos-e-condicoes-de-uso" >termos de uso</a></label>
                </div>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-outline-secondary" onclick="javascript:Util.checkFormRequire(document.form_cadastro, '#error-box-cad', MJS_Cadastro.register);" >Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<?php if (false): ?>

	<div class="a-login row" >
		<div class="col-md-5 offset-md-1">


			<div class="forms_container">

				<h2>Já possui cadastro? <span class="bold">Entre na sua conta</span></h2>

				<!-- FORM - LOGIN -->
				<form action="" name="form_login" method="post" class="form_login" id="form-login">
					<input type="hidden" name="exe" value="1">
					
					<div class="alert alert-danger mb-20" id="error-box" >Preencha os dados corretamente para continuar!</div>
					
					<div class="form-group" >
						<div class="input-group input-group-lg" >
							<span class="input-group-addon  glyphicon glyphicon-envelope" id="sizing-addon1" ></span>
							<input type="email" name="email" class="form-control" placeholder="E-mail" aria-describedby="sizing-addon1" data-required="true" placeholder="Digite o seu e-mail"
							value="<?php echo (isset($emailSession))?$emailSession:""; ?>" >
						</div>
					</div>
					<div class="form-group" >
						<div class="input-group input-group-lg" >
							<span class="input-group-addon glyphicon glyphicon-asterisk" id="sizing-addon1"></span>
							<input type="password" name="senha" class="form-control" data-required="true" value="" placeholder="Senha" aria-describedby="sizing-addon1">
						</div>
					</div>
					<div class="form-check d-flex justify-content-around align-items-center" >
						
						<div class="lembrar-dados d-flex align-items-center" >
			                <input type="checkbox" class="form-check-input"  id="lembrar_dados" name="lembrar_dados" value="1" />
			                <label class="form-check-label" for="lembrar_dados">Lembrar dados</label>
			            </div>
						<a href="#" class="recuperar-senha" onclick="javascript:if(document.form_login.email.value!=''){ document.form_login.exe.value=2; document.form_login.submit(); }else{ $('#error-box').slideDown('normal',function(){ setTimeout(function(){ $('#error-box').fadeOut(); },3000) }); }" >Recuperar senha</a>

					</div>
					<div class="form-group" >
						<input type="button" name="submit-1" class="btn btn btn-login" value="Entrar" onclick="javascript:checkFormRequire(document.form_login, '#error-box');" />
					</div>

					<?php if (false): ?>
					<div class="form-group" >
						<a href="" class="btn btn-t3 btn-vasado btn-100" style="margin-top: 4px;" 
							onclick="javascript: $('.form-esqueceu').slideUp('fast', function(){ $('.form-confirmar').slideDown(); }); return false;"
						>Confirmar meu cadastro</a>
					</div>
					<?php endif ?>

				</form>
				<div class="clear"></div>
				
				
			</div>

		</div>
		<div class="col-md-5 offset-md-1">

			<div class="forms_container">

				<h2>Não possui cadastro? <span class="bold">Crie agora uma conta</span></h2>
		    	
				<form action="" name="form_cadastro_novo" method="post" class="form_login" id="form-cadastro-novo" >
					<div class="alert alert-danger subtitulo-t5 mt-light" id="error-box-cadastro-novo" >Por favor, preencha os dados corretamente!</div>
					<div class="alert alert-danger subtitulo-t5 mt-light" id="msg-box-cadastro-novo" >Por favor, preencha os dados corretamente!</div>
					<input type="hidden" name="exe" value="3" >
					
					<div class="form-group" >
						<input type="name" class="form-control" placeholder="Nome" name="nome_completo" data-required="true"  >
						
					</div>
					<div class="form-group" >
						<input type="name" class="form-control" placeholder="E-mail" name="email" data-required="true" >
					</div>
					<div class="form-group" >
						<input type="password" class="form-control" placeholder="Senha" name="senha" data-required="true" >
						
					</div>
					<div class="form-group" >
						<input type="password" class="form-control" placeholder="Confirma senha" name="senha_conf" data-required="true" >
						
					</div>
					<div class="form-check d-flex mb-3 justify-content-between align-items-center">
						<input type="checkbox" class="form-check-input" id="receber_boletim"  name="receber_boletim" value="1" >
						<label class="form-check-label" for="receber_boletim">Desejo receber novidades do site no meu e-mail.</label>
					</div>
					<div class="form-check d-flex mb-3 justify-content-between align-items-center">
						<input type="checkbox" class="form-check-input" id="termos"  name="termos" value="1" >
						<label class="form-check-label" for="termos" >Li, compreendi, e aceito os <a href="/termos-e-condicoes-de-uso" >termos de uso</a>.</label>
					</div>
					<input type="button" name="submit-3" class="btn btn-login" value="Começar"
						onclick="javascript:checkFormRequire(document.form_cadastro_novo, '#error-box-cadastro-novo', checkCadastroInicial);" />
				</form>

			</div>


		</div>	
	</div>



<?php endif ?>
