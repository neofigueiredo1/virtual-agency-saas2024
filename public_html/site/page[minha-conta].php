<?php

/** Identificadores de páginas
 * 60 -> Conteúdo de Cursos - Novo
 * 61 -> Conteúdo de Cursos - Editar
 * 64 -> Mensagens de Alunos
*/

$_SESSION["plataforma_url_back_login"] = $_SERVER['REQUEST_URI'];

$cursoId = (isset($_GET['cursoId'])?(int)$_GET['cursoId']:0);

switch ($pagina_uri) {
	case '/minha-conta':
	case '/minha-conta/':
	case '/minha-conta/meus-cursos':
	case '/minha-conta/meus-cursos/':
		if ($cursoId!=0) {//Detalhes do curso
			$m_ecommerce->getView("controller-area-do-usuario-meus-cursos-detalhes");
		}else{ //Lista de Cursos
			$m_ecommerce->getView("controller-area-do-usuario-meus-cursos");
		}
		break;

	case '/minha-conta/meus-cursos-a-venda':
	case '/minha-conta/meus-cursos-a-venda/':
		$m_ecommerce->getView("controller-area-do-usuario-meus-cursos-venda");
		break;

	case '/minha-conta/minha-pagina-de-vendas':
	case '/minha-conta/minha-pagina-de-vendas/':
		$m_ecommerce->getView("controller-area-do-usuario-minha-pagina-de-vendas");
		break;

	case '/minha-conta/meus-cursos-inscritos':
	case '/minha-conta/meus-cursos-inscritos/':
		if ($cursoId!=0) {//Detalhes do curso
			$m_ecommerce->getView("controller-area-do-usuario-meus-cursos-inscritos");
		}else{ //Lista de Cursos
			header("Location:/minha-conta/meus-cursos");
			exit();
		}
		break;
	
	case '/minha-conta/cadastro-de-produtos':
	case '/minha-conta/cadastro-de-produtos/':
		$m_curso->getView("controller-curso-conteudo");
		break;

	case '/minha-conta/cadastro-de-produtos-novo':
	case '/minha-conta/cadastro-de-produtos-novo/':
		$m_curso->getView("controller-curso-conteudo-novo");
		break;

	case '/minha-conta/cadastro-de-produtos-editar':
	case '/minha-conta/cadastro-de-produtos-editar/':
		$m_curso->getView("controller-curso-conteudo-editar");
		break;

	case '/minha-conta/minhas-vendas':
	case '/minha-conta/minhas-vendas/':
		$m_ecommerce->getView("controller-area-do-usuario-vendas-dashboard");
		$m_ecommerce->getView("controller-area-do-usuario-vendas");
		break;

	case '/minha-conta/extrato-na-financeira':
	case '/minha-conta/extrato-na-financeira/':
		$m_ecommerce->getView("controller-area-do-usuario-vendas-resumo-finan");
		break;

	case '/minha-conta/minhas-contas':
	case '/minha-conta/minhas-contas/':
		$m_ecommerce->getView("controller-area-do-usuario-contas-de-repasse");
		break;

	case '/minha-conta/minhas-vendas-repasse':
	case '/minha-conta/minhas-vendas-repasse/':
		break;

	case '/minha-conta/minhas-contas-nova-conta':
	case '/minha-conta/minhas-contas-nova-conta/':
		$m_ecommerce->getView("controller-area-do-usuario-contas-de-repasse-add");
		break;

	case '/minha-conta/minhas-contas-editar-conta':
	case '/minha-conta/minhas-contas-editar-conta/':
		$m_ecommerce->getView("controller-area-do-usuario-contas-de-repasse-edit");
		break;

	case '/minha-conta/minhas-compras':
	case '/minha-conta/minhas-compras/':
		$m_ecommerce->getView("controller-area-do-usuario-meus-pedidos");
		break;

	case '/minha-conta/mensagens-de-alunos':
	case '/minha-conta/mensagens-de-alunos/':
		$m_ecommerce->getView("controller-area-do-usuario-mensagens-alunos");
		break;
}

?>
<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
	
	<main class="bg-light pb-5" >
		
		<?php $m_banner->getView('view-interna-topo'); ?>

		<header class="wrapper wrapper-1340 py-3 bg-light d-none d-lg-block" style="min-height:90px;" >
			<a class="navbar-brand" href="#" >
		        <h1 class="cinza-1 fs-28 m-0" style="line-height:20px;" >
		            
	                <?php if (trim($pagina_data['titulo_mae']!="")): ?>
	                    <small class="fs-16" >
	                        Minha Conta
	                    </small><br/>
	                    <?php echo $pagina_data['titulo'];?>
	                <?php else: ?>
	                    Minha Conta
	                <?php endif ?>
	            
		        </h1>
		    </a>
		</header>

		<!-- Section Conteudo -->
		<section class="s_conteudo_padrao" >
			<div class="wrapper wrapper-1340" >

				<div class="d-lg-flex">
					<div>
						<?php $m_ecommerce->getView("view-area-do-usuario-menu"); ?>
					</div>
					<div class="flex-grow-1" >
						<?php
							switch ($pagina_uri) {
								case '/minha-conta':
								case '/minha-conta/':
								case '/minha-conta/meus-cursos':
								case '/minha-conta/meus-cursos/':
									if ($cursoId!=0) { //Detalhes do curso
										$m_ecommerce->getView("view-area-do-usuario-meus-cursos-detalhes");
									}else{ //Lista de Cursos
										$m_ecommerce->getView("view-area-do-usuario-meus-cursos");
									}
									break;

								case '/minha-conta/meus-cursos-a-venda':
								case '/minha-conta/meus-cursos-a-venda/':
									$m_ecommerce->getView("view-area-do-usuario-meus-cursos-venda");
									break;

								case '/minha-conta/minha-pagina-de-vendas':
								case '/minha-conta/minha-pagina-de-vendas/':
									$m_ecommerce->getView("view-area-do-usuario-minha-pagina-de-vendas");
									break;

								case '/minha-conta/meus-cursos-inscritos':
								case '/minha-conta/meus-cursos-inscritos/':
									$m_ecommerce->getView("view-area-do-usuario-meus-cursos-inscritos");
									break;

								case '/minha-conta/cadastro-de-produtos':
								case '/minha-conta/cadastro-de-produtos/':
									$m_curso->getView("view-curso-conteudo");
									break;

								case '/minha-conta/cadastro-de-produtos-novo':
								case '/minha-conta/cadastro-de-produtos-novo/':
									$m_curso->getView("view-curso-conteudo-novo");
									break;

								case '/minha-conta/cadastro-de-produtos-editar':
								case '/minha-conta/cadastro-de-produtos-editar/':
									$m_curso->getView("view-curso-conteudo-editar");
									break;

								case '/minha-conta/minhas-compras':
								case '/minha-conta/minhas-compras/':
									$m_ecommerce->getView("view-area-do-usuario-meus-pedidos");
									break;

								case '/minha-conta/minhas-vendas':
								case '/minha-conta/minhas-vendas/':
									$m_ecommerce->getView("view-area-do-usuario-vendas-dashboard");
									$m_ecommerce->getView("view-area-do-usuario-vendas");
									break;

								case '/minha-conta/extrato-na-financeira':
								case '/minha-conta/extrato-na-financeira/':
									$m_ecommerce->getView("view-area-do-usuario-vendas-resumo-finan");
									break;

									// todo o bloco de repasse vai sair na proxima atualizacao
									// case '/minha-conta/minhas-contas':
									// case '/minha-conta/minhas-contas/':
									// 	$m_ecommerce->getView("view-area-do-usuario-contas-de-repasse");
									// 	break;
									// case '/minha-conta/minhas-contas-nova-conta':
									// case '/minha-conta/minhas-contas-nova-conta/':
									// 	$m_ecommerce->getView("view-area-do-usuario-contas-de-repasse-add");
									// 	break;

									// case '/minha-conta/minhas-contas-editar-conta':
									// case '/minha-conta/minhas-contas-editar-conta/':
									// 	$m_ecommerce->getView("view-area-do-usuario-contas-de-repasse-edit");
									// 	break;

									// case '/minha-conta/minhas-vendas-repasse':
									// case '/minha-conta/minhas-vendas-repasse/':
									// 	break;

								case '/minha-conta/cronograma-de-estudo':
								case '/minha-conta/cronograma-de-estudo/':
									$m_curso->getView("view-cadastro-cronograma");
									break;

								case '/minha-conta/criar-meu-cronograma-de-estudo':
								case '/minha-conta/criar-meu-cronograma-de-estudo/':
									$m_curso->getView("view-cadastro-cronograma-cria");
									break;

								case '/minha-conta/meus-dados':
								case '/minha-conta/meus-dados/':
									$m_cadastro->getView("view-editar-meu-perfil");
									break;

								case '/minha-conta/mensagens-de-alunos':
								case '/minha-conta/mensagens-de-alunos/':
									$m_ecommerce->getView("view-area-do-usuario-mensagens-alunos");
									break;
							}
						?>
					</div>
				</div>

				

			</div>
		</section>
		<!-- ./Section Conteudo -->	
	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>