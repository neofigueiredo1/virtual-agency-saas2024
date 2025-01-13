<?php
	
	$login = true;

	$rpass = (isset($_GET['rpass'])) ? Text::clean($_GET['rpass']) : 0;

	$accountType = (isset($_GET['account'])) ? Text::clean($_GET['account']) : '';
	
	$rpassArr = explode("-", base64_decode($rpass));

	// var_dump($rpass);
	if(is_array($rpassArr) && count($rpassArr) == 2){
		$rpass = $rpassArr[1];
		$dataArr = explode("_", base64_decode($rpassArr[0]));
        if(Sis::isValidMd5($rpass) && date('Ymd') == $dataArr[0] && (int)date('His')){
			$login = false;
		}
	}

	$codeConfirm = (isset($_GET['codeconfirm'])) ? Text::clean($_GET['codeconfirm']) : "";
	if($codeConfirm!=""){
    	$codeConfirm = base64_decode($codeConfirm);
        if(Sis::isValidMd5($codeConfirm)){
            self::getCadastroConfirmation($codeConfirm);
		}
	}

	$act = (isset($_GET['act'])) ? trim($_GET['act']) : '';
	if ($act=='logout') {
		self::logout();
	}

	$exe = (isset($_POST['exe'])) ? (int)$_POST['exe'] : 0;
	
	switch ($exe) {
		case 1:
			self::login();
			break;
		case 2:
			self::geraCodigoNovaSenha();
			break;
		case 3:
			self::cadastroInsertInicial();
			break;
		case 4:
			self::salvaNovaSenha($rpass);
			break;
		case 5:
			self::confirmaCadastro();
			break;
	}
	
	$_SESSION["plataforma_url_back_cadastro"] = $_SERVER['REQUEST_URI'];

	$areasInteresse = self::getAreasInteresse();

?>



<style type="text/css">
	/* The flip card container - set the width and height to whatever you want. We have added the border property to demonstrate that the flip itself goes out of the box on hover (remove perspective if you don't want the 3D effect */
	.flip-card {
	  background-color: transparent;
	  
	  width: 100%;
	  height: 100%;
	  
	  perspective: 1000px; /* Remove this if you don't want the 3D effect */
	}

	/* This container is needed to position the front and back side */
	.flip-card-inner {
	  position: relative;
	  width: 100%;
	  height: 100%;
	  text-align: center;
	  transition: transform 0.8s;
	  transform-style: preserve-3d;
	}

	/* Do an horizontal flip when you move the mouse over the flip box container */
	.flip-card.active_zone .flip-card-inner {
	  transform: rotateY(180deg);
	}

	/* Position the front and back side */
	.flip-card-front, .flip-card-back {
	  position: absolute;
	  width: 100%;
	  height: 100%;
	  -webkit-backface-visibility: hidden; /* Safari */
	  backface-visibility: hidden;
	}

	/* Style the front side (fallback if image is missing) */
	.flip-card-front{
		z-index:0;
	}

	/* Style the back side */
	.flip-card-back {
	z-index:1;
	  transform: rotateY(180deg);
	}

	#login_zone,
	#register_zone{
		min-height:600px;
		height:100%;
		transition:all 0.2s ease;
		-moz-transition:all 0.2s ease;
		-webkit-transition:all 0.2s ease;
	}

	#register_zone.active_zone{
		height:900px;
		transition:all 0.2s ease;
		-moz-transition:all 0.2s ease;
		-webkit-transition:all 0.2s ease;
	}

	@media screen and (max-width: 767px) {
		#register_zone.active_zone{
			height:1200px;
		}
	}

</style>



<section class="s_login">
	<?php
		$emailSession = "";
		$msgSession = null;
		if(isset($_SESSION['plataforma_login_usuario_alerts']) && is_array($_SESSION['plataforma_login_usuario_alerts']))
		{
			$msgSession=$_SESSION['plataforma_login_usuario_alerts'];
			unset($_SESSION['plataforma_login_usuario_alerts']);
		}
	   
		if(isset($_SESSION['plataforma_cadastro_usuario_erros']) && is_array($_SESSION['plataforma_cadastro_usuario_erros']))
		{
			$msgSession=$_SESSION['plataforma_cadastro_usuario_erros'];
			unset($_SESSION['plataforma_cadastro_usuario_erros']);
		}

	   if ($msgSession!=null) {

	      $tipo="warning";
	      switch($msgSession['tipo'])
	      {
	         case 1 : $tipo="warning";  $icone = "<i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;";
	         break;
	         case 2 : $tipo="info";     $icone = "<i class='fa fa-info-circle'></i>&nbsp;&nbsp;";
	         break;
	         case 3 : $tipo="success";  $icone = "<i class='fa fa-check-circle-o'></i>&nbsp;&nbsp;";
	         break;
	         case 4 : $tipo="danger";   $icone = "<i class='fa fa-ban'></i>&nbsp;&nbsp;";
	         break;
	      }
	   	?>

	      <div id="alert-bts" style="display: block;" class="alert alert-<?php echo $tipo; ?> m-0 rounded-0 text-center">
	         <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	         <?php echo $icone.$msgSession['mensagem']; ?>
	      </div>
	      <?php
	      	if(array_key_exists('email', $msgSession)){
	      		$emailSession = $msgSession['email'];
	      	}
	         //unset($msgSession);
	   	// }else{
	      ?>
	      <?php
	   }
	?>

		<div class="row p-0 m-0 justify-content-center align-items-center" >
		<?php if ($login): ?>
		
			<div class="col-lg-6 align-self-stretch p-0" >

				<div id="login_zone" class="flip-card <?php echo (trim($accountType)==''?'active_zone':''); ?>" >
  					<div class="flip-card-inner" >

						<div class="flip-card-front d-flex align-items-center justify-content-center text-center order-2 h-100 p-4" 

							style="background-image:url(/assets/images/_temp/retangulo-azul.png);background-size:cover;background-position: center;" >
							
						    <div class="">
								<!-- <figure>
					                <img src="/assets/images/_temp/logo-branca.png" alt="Conexo">
					            </figure> -->
				                <figcaption class="mxw-500" >
				                	
				                	<h2 class="branco">Bem vindo de volta</h2>
		                			<p class="branco fs-16"><?php echo Sis::config('CADASTRO-BEM-VINDO'); ?></p>
				                	
				                	<button type="button" class="btn btn-outline-light btn-lg mxw-500"
				                		onclick="javascript:$('#login_zone').addClass('active_zone');$('#register_zone').removeClass('active_zone');" 
				                	>Login</button>

				                </figcaption>
					        </div>

				        </div>

				        <div class="flip-card-back d-flex align-items-center justify-content-center text-center order-2 h-100 p-4 bg-white">
					      	
					      	
					      	<article class="mxw-500 d-none ">
			                	
			                	<form class="formulario">
			                		<div class="form-group">
				    					<input type="e-mail" class="form-control rounded-100" placeholder="Email">
				    				</div>
			                		<div class="form-group">
				    					<input type="password" class="form-control rounded-100" placeholder="Senha">
				    				</div>
			                	</form>
			                	<a href="#" class="btn btn-lg mxw-500 btn-azul-1 d-flex justify-content-center align-items-center m-auto">Login</a>

			                </article>



			                <div class=" mxw-500" >

				                <div class="login-container" >
									<h2 class="azul">Bem vindo! Faça seu login</h2>
				                	<p class="cinza fs-16"><?php echo Sis::config('CADASTRO-BEM-VINDO-FACA-LOGIN'); ?></p>

									<form action="" name="form_login" method="post" class="form_login mt-4" id="form-login">
										<input type="hidden" name="exe" value="1">

										<div class="alert alert-danger mb-20" id="error-box" style="display:none" >Preencha os dados corretamente para continuar!</div>

										<div class="form-group" >
											<!-- <label for="email">E-mail*</label> -->
											<input type="email" name="email" class="form-control rounded-100" placeholder="Digite seu e-mail cadastrado" aria-describedby="sizing-addon1" data-required="true" 
											value="<?php echo (isset($emailSession))?$emailSession:""; ?>" >
										</div>

										<div class="form-group mb-2" >
											<!-- <label for="senha">Senha*</label> -->
											<input type="password" name="senha" class="form-control rounded-100" data-required="true" value="" placeholder="Digite sua senha" aria-describedby="sizing-addon1">
										</div>
										<div class="py-3 text-center" >
											
											<div class="form-check  form-check-inline">
												<input type="checkbox" class="form-check-input"  id="lembrar_dados" name="lembrar_dados" value="1" />
												<label class="form-check-label check-cadastro" for="lembrar_dados">Lembrar dados</label>
											</div>

										</div>
										<div class="form-row justify-content-center justify-content-center align-items-center" >


											<input type="button" name="submit-1" class="btn btn-lg btn-azul-1 d-flex justify-content-center align-items-center" value="Acessar" onclick="javascript:Util.checkFormRequire(document.form_login, '#error-box');" />

											<a href="javascript:;" class="recuperar-senha p-3" onclick="javascript:$('.login-container').slideUp('fast');$('.recupera-senha-container').slideDown('fast');" >Esqueceu a senha?</a>

										</div>
									</form>
									
								</div>

								<div class="recupera-senha-container" style="display:none;" >
									<h2 class="azul" >
										<small>Esqueceu sua senha?</small><br/>
										Faça a recuperação de senha
									</h2>

									<form action="" name="form_recuperacao_senha" method="post" class="form_recuperacao_senha mt-4" id="form-recuepracao-senha" >
										<input type="hidden" name="exe" value="2">

										<div class="alert alert-danger mb-20" id="error-box-recuperacao-senha" style="display:none" >Preencha os dados corretamente para continuar!</div>

										<div class="form-group" >
											<label for="email">Informe seu e-mail cadastrado*</label>
											<input type="email" name="email" class="form-control rounded-100" placeholder="Digite seu e-mail" aria-describedby="sizing-addon1" data-required="true" 
											value="" >
										</div>

										<div class="form-row justify-content-center align-items-center" >


											<input type="button" name="submit-1" class="btn btn-azul-1 px-5" value="Recuperar senha" onclick="javascript:Util.checkFormRequire(document.form_recuperacao_senha, '#error-box-recuperacao-senha');" />
											
											<a href="#" class="recuperar-senha p-3" onclick="javascript:$('.login-container').slideDown('fast');$('.recupera-senha-container').slideUp('fast');" >Voltar para o Login</a>

										</div>
									</form>
									
								</div>

							</div>


					    </div>
				

					</div> <!-- flip-card -->
				</div> <!-- flip-card-inner -->



			</div>


			<div class="col-lg-6 align-self-stretch p-0" >

				<div id="register_zone" class="flip-card <?php echo (trim($accountType)=='produtor'?'active_zone':''); ?>" >
  					<div class="flip-card-inner" >

						<div class="flip-card-front d-flex align-items-center justify-content-center text-center h-100 p-4" 

							style="background-image:url(/assets/images/_temp/retangulo-azul.png);background-size:cover;background-position: center;" >
							
						    <div class="">
								<div class="mxw-500 branco" >
				                	
				                	<h2>Criar conta</h2>
				                	<p><?php echo Sis::config("CADASTRO-CRIAR-CONTA"); ?></p>

				                	<button type="button" class="btn btn-outline-light btn-lg mxw-500"
				                		onclick="javascript:$('#login_zone').removeClass('active_zone');$('#register_zone').addClass('active_zone');" 
				                	>Cadrastar</button>

				                </div>
					        </div>

				        </div>

				        <div class="flip-card-back d-flex align-items-center justify-content-center text-center h-100 p-4  bg-white">
					      	
					      	<article class="mxw-500 ">
			                	
		                		<h2 class="text-center azul">Crie sua conta!</h2>
				    			<p class="text-center cinza"><?php echo Sis::config("CADASTRO-CRIE-SUA-CONTA"); ?></p>

					    		<form action="" name="form_cadastro_novo" method="post" id="form-cadastro-novo"  >
					    			<input type="hidden" name="exe" value="3" >

					    			<div class="alert alert-danger subtitulo-t5 mt-light" id="error-box-cadastro-novo" style="display:none;" >
										Por favor, preencha os dados corretamente!
									</div>
									<div class="alert alert-danger subtitulo-t5 mt-light" id="msg-box-cadastro-novo" style="display:none;" ></div>

									<?php if (false): ?>
										<div class="form-row" >
											<div class="form-group col-md-6">
												<label class="d-flex align-items-center justify-content-center btn w-100 perfil-tipo-1 btn-<?php echo (trim($accountType)==''?'':'outline-'); ?>azul" >
													<input type="radio" name="perfil" value="0" style="position:absolute;visibility:hidden;" <?php echo (trim($accountType)==''?'checked':''); ?> 
														onclick="javascript:
														$('.perfil-tipo-1').removeClass('btn-outline-azul'); 
														$('.perfil-tipo-1').addClass('btn-azul'); 
														$('.perfil-tipo-2').addClass('btn-outline-azul'); 
														$('.perfil-tipo-2').removeClass('btn-azul'); 
														$('.mat-novato-desc').slideDown('fast'); 
														$('.mat-vetereano-desc').slideUp('fast'); ">
													Sou aluno
												</label>
											</div>
											<div class="form-group col-md-6">
												<label class="d-flex align-items-center justify-content-center btn w-100 perfil-tipo-2 btn-<?php echo (trim($accountType)=='produtor'?'':'outline-'); ?>azul" >
													<input type="radio" name="perfil" value="1" style="position:absolute;visibility:hidden;" <?php echo (trim($accountType)=='produtor'?'checked':''); ?>
														onclick="javascript: 
														$('.perfil-tipo-2').removeClass('btn-outline-azul'); 
														$('.perfil-tipo-2').addClass('btn-azul'); 
														$('.perfil-tipo-1').addClass('btn-outline-azul'); 
														$('.perfil-tipo-1').removeClass('btn-azul'); 
														$('.mat-novato-desc').slideUp('fast'); 
														$('.mat-vetereano-desc').slideDown('fast'); ">
													Sou produtor
												</label>
											</div>
										</div>
									<?php endif ?>

									<div class="form-row">
					    				<div class="form-group col-md-12">
					    					<input type="text" class="form-control" name="nome_completo" placeholder="Seu nome completo" data-required="true" >
					    				</div>
					    			</div>
					    			<div class="form-row">
					    				<div class="form-group col-md-6">
					    					<input type="e-mail" class="form-control" name="email" placeholder="E-mail" data-required="true" >
					    				</div>
					    				<div class="form-group col-md-6">
					    					<input type="text" class="form-control mask_spcelphones" name="telefone" placeholder="Telefone" data-required="true" />
					    				</div>
					    			</div>
					    			<div class="form-row">
					    				<div class="form-group col-md-12">
					    					<select name="tema_interesse" id="" class="custom-select" >
					    						<option value="0" >Tema de seu interesse (opcional)</option>
					    						<?php foreach ($areasInteresse as $key => $areaInteresse): ?>
					    							<option value="<?php echo $areaInteresse['interesse_idx'] ?>" ><?php echo $areaInteresse['nome'] ?></option>	
					    						<?php endforeach ?>
					    					</select>
					    				</div>
					    			</div>
					    			<div class="form-row">
					    				<div class="form-group col-md-6">
					    					<input type="password" class="form-control" name="senha" placeholder="Senha" data-required="true" >
					    				</div>
					    				<div class="form-group col-md-6">
					    					<input type="password" class="form-control" name="senha_conf" placeholder="Confirmar senha" data-required="true" >
					    				</div>
					    			</div>
					    			<div class="form-row">
					    				<div class="form-group col-md-12" >
					    					<input type="text" class="form-control" name="breve_curriculo" placeholder="Área de atuação (opcional)" />
					    				</div>
					    			</div>
					    			<!-- <div class="form-group">
					    				<textarea name="breve_curriculo" class="form-control pt-3" rows="6" placeholder="Breve curr&iacute;culo (opcional)"></textarea>
					    			</div> -->
					    			

					    			<!-- <div class="d-flex justify-content-center">
						    			<div class="form-group mt-2 mb-4">
						    				<div class="form-check">
						    					<input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
						    					<label class="form-check-label" for="invalidCheck">
						    						Concordo com os <a href="/termos-e-condicoes" class="preto" ><strong>Termos e Pol&iacute;ticas</strong></a>
						    					</label>
						    				</div>
						    			</div>
						    		</div> -->


									<div class="form-check form-check-inline text-left">
										<input type="checkbox" class="form-check-input" id="receber_boletim"  name="receber_boletim" value="1" >
										<label class="form-check-label check-cadastro" for="receber_boletim" > Desejo receber novidades do site no meu e-mail. </label>
									</div>
									<div class="form-check form-check-inline text-left mt-1">
										<input type="checkbox" class="form-check-input" id="termos"  name="termos" value="1" >
										<label class="form-check-label check-cadastro" for="termos" >Li, compreendi, e aceito os <a href="/politica-de-privacidade/" target="_blank" class="text-under"><strong class="preto" >termos de uso</strong></a>.</label>
									</div>
									<div class="text-center mt-3">
										<button type="button" name="submit-3" class="btn btn-azul-1 rounded-40" onclick="javascript:Util.checkFormRequire(document.form_cadastro_novo, '#error-box-cadastro-novo', checkCadastroInicial);" >Cadastrar</button>
									</div>
						    		
					    		</form>

			                </article>

					    </div>
				

					</div> <!-- flip-card -->
				</div> <!-- flip-card-inner -->



			</div>
		<?php else: ?>
			<div class="col-lg-6 align-self-stretch p-0" >
				<div id="login_zone" class="d-flex align-items-center justify-content-center" >
  					

  					<div class="recovery-container mxw-500 w-100 p-3">
							<h2>Recuperação de senha</h2>
							<form action="" name="form_login" method="post" class="form_login" id="form-login" >
								<input type="hidden" name="exe" value="4" >
								<div class="alert alert-danger" id="error-box" style="display:none;" >Preencha os dados corretamente para continuar!</div>
								<div class="alert alert-danger" id="error-box-combinacao-pass" style="display:none;" >Confirme sua nova senha corretamente!</div>
								<label for="senha" >Senha</label>
								<input type="password" name="senha" class="form-control transition" data-required="true" placeholder="Digite uma nova senha" value="" />
								<label for="senha_confirm" class="mt-3">Confirmação de Senha</label>
								<input type="password" name="senha_confirm" class="form-control transition" data-required="true" placeholder="Confirme a sua nova senha" value="" />

								<div class="row align-items-center mt-md-5 mt-3">
									<div class="col-md-6 text-md-left text-center">
										<a href="/login-cadastro" class="show-form check-cadastro"><i class="fa fa-chevron-left mr-2" ></i> Voltar para o login</a>
									</div>
									<div class="col-md-6 text-md-right text-center mt-md-0 mt-3">
										<input type="button" name="submit-4" class="btn btn-lg btn-azul-1 d-flex justify-content-center align-items-center" value="Continuar" onclick="javascript:Util.checkFormRequire(document.form_login, '#error-box', checkSenhaReset);" />
									</div>
								</div>

								<div class="clear"></div>
							</form>
						</div>


  				</div>
			</div>
		<?php endif ?>

		</div>


</section>





















<?php if (false): ?>
	

<main class="auth-container" >

	<section class="wrapper wrapper-1360 d-block d-lg-flex align-items-center justify-content-between" >
		
		<div class="logo-area p-3 text-center" >
			
			<figure class="responsive" >
				<img src="/assets/images/conexo-logo-white.svg" alt="conexo" class="img-fluid m-auto" />
			</figure>

			<a href="javascript:;" onclick="javascript:conexoComprar();"
				class="btn bg-light verde px-3 px-md-5 mb-4 py-3 w-auto" style="font-weight:bold;" >Comprar o curso completo.</a>
			
		</div>
		<div class="auth-area d-flex justify-content-end" >

			<div class="login-register-container" >

				<?php
					$emailSession = "";
					$msgSession = null;
					if(isset($_SESSION['plataforma_login_usuario_alerts']) && is_array($_SESSION['plataforma_login_usuario_alerts']))
					{
						$msgSession=$_SESSION['plataforma_login_usuario_alerts'];
						unset($_SESSION['plataforma_login_usuario_alerts']);
					}
				   
					if(isset($_SESSION['plataforma_cadastro_usuario_erros']) && is_array($_SESSION['plataforma_cadastro_usuario_erros']))
					{
						$msgSession=$_SESSION['plataforma_cadastro_usuario_erros'];
						unset($_SESSION['plataforma_cadastro_usuario_erros']);
					}

				   if ($msgSession!=null) {

				      $tipo="warning";
				      switch($msgSession['tipo'])
				      {
				         case 1 : $tipo="warning";  $icone = "<i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;";
				         break;
				         case 2 : $tipo="info";     $icone = "<i class='fa fa-info-circle'></i>&nbsp;&nbsp;";
				         break;
				         case 3 : $tipo="success";  $icone = "<i class='fa fa-check-circle-o'></i>&nbsp;&nbsp;";
				         break;
				         case 4 : $tipo="danger";   $icone = "<i class='fa fa-ban'></i>&nbsp;&nbsp;";
				         break;
				      }
				   	?>

				      <div id="alert-bts" style="display: block;" class="alert alert-<?php echo $tipo; ?>">
				         <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				         <?php echo $icone.$msgSession['mensagem']; ?>
				      </div>
				      <?php
				      	if(array_key_exists('email', $msgSession)){
				      		$emailSession = $msgSession['email'];
				      	}
				         //unset($msgSession);
				   	// }else{
				      ?>
				      <?php
				   }
				?>

				<div class="card" >

					<?php if ($login): ?>


					<div class="login-container" >
						<h2 class="title_card" >
							<small>Já sou cadastrado</small><br/>
							Faça seu login
						</h2>

						<form action="" name="form_login" method="post" class="form_login mt-4" id="form-login">
							<input type="hidden" name="exe" value="1">

							<div class="alert alert-danger mb-20" id="error-box" style="display:none" >Preencha os dados corretamente para continuar!</div>

							<div class="form-group" >
								<label for="email">E-mail*</label>
								<input type="email" name="email" class="form-control" placeholder="Digite seu e-mail cadastrado" aria-describedby="sizing-addon1" data-required="true" 
								value="<?php echo (isset($emailSession))?$emailSession:""; ?>" >
							</div>

							<div class="form-group mb-2" >
								<label for="senha">Senha*</label>
								<input type="password" name="senha" class="form-control" data-required="true" value="" placeholder="Digite sua senha" aria-describedby="sizing-addon1">
							</div>
							<div class="py-3 text-center " >
								
								<div class="form-check  form-check-inline">
									<input type="checkbox" class="form-check-input"  id="lembrar_dados" name="lembrar_dados" value="1" />
									<label class="form-check-label check-cadastro" for="lembrar_dados">Lembrar dados</label>
								</div>

							</div>
							<div class="form-row justify-content-md-between justify-content-center align-items-center" >

								<a href="javascript:;" class="recuperar-senha p-3" onclick="javascript:$('.login-container').slideUp('fast');$('.recupera-senha-container').slideDown('fast');" >Esqueceu a senha?</a>

								<input type="button" name="submit-1" class="btn btn-lg btn-verde px-5" value="Acessar" onclick="javascript:Util.checkFormRequire(document.form_login, '#error-box');" />
							</div>
						</form>
						
					</div>

					<div class="recupera-senha-container" style="display:none;" >
						<h2 class="title_card" >
							<small>Esqueceu sua senha?</small><br/>
							Faça a recuperação de senha
						</h2>

						<form action="" name="form_recuperacao_senha" method="post" class="form_recuperacao_senha mt-4" id="form-recuepracao-senha" >
							<input type="hidden" name="exe" value="2">

							<div class="alert alert-danger mb-20" id="error-box-recuperacao-senha" style="display:none" >Preencha os dados corretamente para continuar!</div>

							<div class="form-group" >
								<label for="email">Informe seu e-mail cadastrado*</label>
								<input type="email" name="email" class="form-control" placeholder="Digite seu e-mail" aria-describedby="sizing-addon1" data-required="true" 
								value="" >
							</div>

							<div class="form-row justify-content-md-between justify-content-center align-items-center" >

								<a href="#" class="recuperar-senha p-3" onclick="javascript:$('.login-container').slideDown('fast');$('.recupera-senha-container').slideUp('fast');" >Voltar para o Login</a>

								<input type="button" name="submit-1" class="btn btn-verde px-5" value="Recuperar senha" onclick="javascript:Util.checkFormRequire(document.form_recuperacao_senha, '#error-box-recuperacao-senha');" />
							</div>
						</form>
						
					</div>

					<div class="register-container">
						
						<h2 class="title_card" >
							<small>Ainda não é cadastrado?</small><br/>
							Faça agora mesmo o seu cadastro
						</h2>
						<form action="" name="form_cadastro_novo" method="post" class="form_login mt-4" id="form-cadastro-novo" >
							<input type="hidden" name="exe" value="3" >

							<div class="alert alert-danger subtitulo-t5 mt-light" id="error-box-cadastro-novo" style="display:none;" >
								Por favor, preencha os dados corretamente!
							</div>
							<div class="alert alert-danger subtitulo-t5 mt-light" id="msg-box-cadastro-novo" style="display:none;" ></div>

							<div class="row">
								<div class="col-md-12" >
									<div class="form-group" >
										<label for="nome_completo">Nome</label>
										<input type="text" class="form-control" placeholder="Seu nome completo" name="nome_completo" data-required="true"  >
									</div>
								</div>

								<div class="col-md-6" >
									<div class="form-group" >
										<label for="email">E-mail</label>
										<input type="text" class="form-control" placeholder="Seu e-mail" name="email" data-required="true" >
									</div>
								</div>
								<div class="col-md-6" >
									<div class="form-group" >
										<label for="telefone">Telefone</label>
										<input type="text" class="form-control mask_spcelphones" placeholder="Seu Telefone" name="telefone" data-required="true" >
									</div>
								</div>

								<div class="col-md-6" >
									<div class="form-group" >
										<label for="local_residencia">Local de residência</label>
										<input type="text" class="form-control" placeholder="Seu local de residência" name="local_residencia" data-required="true" >
									</div>
								</div>
								<div class="col-md-6" >
									<div class="form-group" >
										<label for="ano_residencia">Ano de residência</label>
										<input type="text" class="form-control" placeholder="Seu ano de residência" name="ano_residencia" data-required="true" >
									</div>
								</div>

								<?php if (false): ?>
									<div class="col-md-6" >
										<div class="form-group" >
											<label for="data_nascimento">Idade <small>(data de nascimento)</small></label>
											<input type="text" class="form-control mask_data" placeholder="Seu data de nascimento" name="data_nascimento" data-required="true" >
										</div>
									</div>
									<div class="col-md-6" >
										<div class="form-group" >
											<label for="genero" >Sexo</label>
											<select class="form-control" name="genero" data-required="true" >
												<option value="0" ></option>
												<option value="1" >Masculino</option>
												<option value="2" >Feminino</option>
												<option value="3" >Prefiro não informar</option>
											</select>
										</div>
									</div>
								<?php endif ?>


								<div class="col-md-6" >
									<div class="form-group" >
										<label for="senha">Senha</label>
										<input type="password" class="form-control" placeholder="Entre 6 a 8 dígitos" name="senha" data-required="true" >
									</div>
								</div>
								<div class="col-md-6" >
									<div class="form-group" >
										<div class="form-group" >
											<label for="senha_conf">Confirme sua senha</label>
											<input type="password" class="form-control" placeholder="Confirme sua senha" name="senha_conf" data-required="true" >
										</div>
									</div>
								</div>


							</div>

							<div class="form-check form-check-inline">
								<input type="checkbox" class="form-check-input" id="receber_boletim"  name="receber_boletim" value="1" >
								<label class="form-check-label check-cadastro" for="receber_boletim">Desejo receber novidades do site no meu e-mail.</label>
							</div>
							<div class="form-check form-check-inline mt-1">
								<input type="checkbox" class="form-check-input" id="termos"  name="termos" value="1" >
								<label class="form-check-label check-cadastro" for="termos" >Li, compreendi, e aceito os <a href="/politica-de-privacidade/" target="_blank" class="text-under"><strong>termos de uso</strong></a>.</label>
							</div>
							<div class="text-center mt-3">
								<input type="button" name="submit-3" class="btn btn-lg btn-verde px-5" value="Cadastrar" onclick="javascript:Util.checkFormRequire(document.form_cadastro_novo, '#error-box-cadastro-novo', checkCadastroInicial);" />
							</div>
						</form>

					</div>

					<?php else: ?>

						<div class="recovery-container">
							<h2>Recuperação de senha</h2>
							<form action="" name="form_login" method="post" class="form_login" id="form-login" >
								<input type="hidden" name="exe" value="4" >
								<div class="alert alert-danger" id="error-box" style="display:none;" >Preencha os dados corretamente para continuar!</div>
								<div class="alert alert-danger" id="error-box-combinacao-pass" style="display:none;" >Confirme sua nova senha corretamente!</div>
								<label for="senha" >Senha</label>
								<input type="password" name="senha" class="form-control transition" data-required="true" placeholder="Digite uma nova senha" value="" />
								<label for="senha_confirm" class="mt-3">Confirmação de Senha</label>
								<input type="password" name="senha_confirm" class="form-control transition" data-required="true" placeholder="Confirme a sua nova senha" value="" />

								<div class="row align-items-center mt-md-5 mt-3">
									<div class="col-md-6 text-md-left text-center">
										<a href="/login" class="show-form check-cadastro"><i class="fa fa-chevron-left mr-2" ></i> Voltar para o login</a>
									</div>
									<div class="col-md-6 text-md-right text-center mt-md-0 mt-3">
										<input type="button" name="submit-4" class="btn btn-lg btn-verde px-5" value="Continuar" onclick="javascript:Util.checkFormRequire(document.form_login, '#error-box', checkSenhaReset);" />
									</div>
								</div>

								<div class="clear"></div>
							</form>
						</div>

					<?php endif; ?>

				</div>


				<?php if ($login): ?>

					<div class="register-access-link text-center mt-3">
						<a href="javascript:;" 
							onclick="
								$('.login-container').slideUp('fast');
								$('.recupera-senha-container').slideUp('fast');
								$('.register-container').slideDown('fast');
								$('.login-access-link').fadeIn('fast');
								$('.register-access-link').fadeOut('fast');
							"
						>
							Ainda não é cadastrado?<br/>
							<strong>Faça agora mesmo o seu cadastro</strong>
						</a>
					</div>

					<div class="login-access-link text-center mt-3">
						<a href="javascript:;" 
							onclick="
								$('.login-container').slideDown('fast');
								$('.register-container').slideUp('fast');
								$('.login-access-link').fadeOut('fast');
								$('.register-access-link').fadeIn('fast');
							" >
							Já sou cadastrado<br/>
							<strong>Faça o seu login</strong>
						</a>
					</div>

				<?php endif; ?>


			</div>

			
			
		</div>

	</section>
	
</main>


<?php endif ?>



