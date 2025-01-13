<?php 
global $subAccountData;
?>

	

	<div class="d-md-flex justify-content-between align-items-center mb-3" >
		<a href="/minha-conta/minhas-vendas" class="btn btn-danger w-auto px-5 branco" >Voltar</a>
	</div>

	<section>
	    <!--Grid row-->
	    <div class="d-flex flex-md-row flex-nowrap">
	        
	        <!--Grid column-->
	        <div class="col mb-2 px-1" >
	            <!-- Card -->
	            <div class="card bg-info branco">
						<div class="card-body">
							<p class="text-uppercase small mb-2">
								<strong>Saldo disponível para saque</strong>
							</p>
							<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
								<?php echo $subAccountData->balance_available_for_withdraw ?>
							</h5>
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->

	        <!--Grid column-->
	        <div class="col mb-2 px-1" >
	            <!-- Card -->
	            <div class="card bg-success branco">
						<div class="card-body">
							<p class="text-uppercase small mb-2">
								<strong>Saldo a receber</strong>
							</p>
							<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
								<?php echo $subAccountData->receivable_balance ?>
							</h5>
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->
	        
	    </div>
	    <!--Grid row-->
	    <!--Grid row-->
	    <div class="d-flex flex-md-row flex-nowrap">
	    	

	    	<!--Grid column-->
	        <div class="col mb-2 px-1" >
	            <!-- Card -->
	            <div class="card bg-default preto">
						<div class="card-body">
							<p class="text-uppercase small mb-2">
								<strong>Recebimentos este m&ecirc;s</strong>
							</p>
							<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
								<?php echo $subAccountData->volume_this_month ?>
							</h5>
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->

	        <!--Grid column-->
			<div class="col mb-2 px-1">
				<!-- Card -->
				<div class="card bg-default preto">
					<div class="card-body">
						<p class="text-uppercase small mb-2"><strong>Recebimentos no último m&ecirc;s</strong></p>
						<h5 class="font-weight-bold fs-32 text-nowrap mb-0">
							<?php echo $subAccountData->volume_last_month ?>
						</h5>
					</div>
				</div>
				<!-- Card -->
			</div>
			<!--Grid column-->
			

	        <!--Grid column-->
			<div class="col mb-2 px-1">
				<!-- Card -->
				<div class="card bg-default preto">
					<div class="card-body">
						<p class="text-uppercase small mb-2"><strong>Contestação</strong></p>
						<h5 class="font-weight-bold fs-32 text-nowrap mb-0">
							<?php echo $subAccountData->balance_in_protest ?>
						</h5>
					</div>
				</div>
				<!-- Card -->
			</div>
			<!--Grid column-->
	        
	    </div>
	    <!--Grid row-->
	     <!--Grid row-->
	    <div class="d-flex flex-md-row flex-nowrap">
	        
	        <!--Grid column-->
	        <div class="col mb-2 px-1" >
	            <!-- Card -->
	            <div class="card bg-default preto">
						<div class="card-body">
							<p class="text-uppercase small mb-2">
								<strong>Tarifas este mês</strong>
							</p>
							<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
								<?php echo $subAccountData->taxes_paid_this_month ?>
							</h5>
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->


	        <!--Grid column-->
			<div class="col mb-2 px-1">
				<!-- Card -->
				<div class="card bg-default preto">
					<div class="card-body">
						<p class="text-uppercase small mb-2"><strong>Saldo</strong></p>
						<h5 class="font-weight-bold fs-32 text-nowrap mb-0">
							<?php echo $subAccountData->balance ?>
						</h5>
					</div>
				</div>
				<!-- Card -->
			</div>
			<!--Grid column-->


	       
	        <!--Grid column-->
	        <div class="col mb-2 px-1" >
	            <!-- Card -->
	            <div class="card bg-default preto">
						<div class="card-body">
							<p class="text-uppercase small mb-2">
								<strong>Em transito</strong>
							</p>
							<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
								<?php echo $subAccountData->protected_balance ?>
							</h5>
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->
	    </div>
	    <!--Grid row-->
	</section>
