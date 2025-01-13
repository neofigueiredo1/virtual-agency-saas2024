<?php
global $cadastroNewsFormID;
if (isset($cadastroNewsFormID)) {
	$cadastroNewsFormID++;
}else{
	$cadastroNewsFormID=1;
}

$areasInteresse = self::getAreasInteresse();
?>


<section id="cmd_form_newsletter_<?php echo $cadastroNewsFormID; ?>" class="section-newsletter newsletter bg-azul py-5" >
	<form 
		action="#" 
		method="post" 
		name="form_newsletter_<?php echo $cadastroNewsFormID; ?>"
		id="form_newsletter_<?php echo $cadastroNewsFormID; ?>"
	>
	<div class="wrapper wrapper-1340" >

		<div class="newsletter_help alert" style="display:none;" ></div>

		<div class="d-md-flex align-items-center justify-content-center flex-wrap" >
			
			<div class="box-conteudo-newsletter branco d-flex align-items-center justify-content-center pr-md-4 py-3"  >
				<i class="far fa-envelope fs-30" ></i> &nbsp;&nbsp; <span class="fs-20" >Assine nossa <strong>newsletter</strong></span>
			</div>

			<div class="row w-100 box-form-newsletter mx-auto" >
				<div class="col-md-6">
					
					<div class="row mb-3 mb-md-0">
						<div class="col-sm-6 mb-3 mb-sm-0">
							<input class="form-control form-newsletter mr-sm-2" type="nome" id="nome" name="nome" placeholder="Seu nome" >
						</div>
						<div class="col-sm-6 ">
							<input class="form-control form-newsletter mr-sm-2" type="email" id="email" name="email" placeholder="Seu e-mail" >
						</div>
					</div>

				</div>
				<div class="col-md-6">
					
					<div class="row">
						<div class="col-sm-6 mb-3 mb-sm-0" >
							<select name="area_interesse" id="news_area_interesse" class="form-control" ><option value="0" >Área de interesse</option>
								<?php foreach ($areasInteresse as $key => $areaInteresse): ?>
									<option value="<?php echo $areaInteresse['interesse_idx'] ?>" ><?php echo $areaInteresse['nome'] ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="col-sm-6" >
							<input type="button" class="btn btn-azul-1" name="enviar" value="Cadastrar"
								onclick="checkCadastroNewsletter(document.form_newsletter_<?php echo $cadastroNewsFormID; ?>,'cmd_form_newsletter_<?php echo $cadastroNewsFormID; ?>');"
							/>
						</div>
					</div>

				</div>
			</div>


		</div>

	</div>
	</form>
</section>