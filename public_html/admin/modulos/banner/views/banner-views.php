<?php
class banner_views extends HandleSql{

	function __construct(){
		parent::__construct();
	}

	public function getView($nome="")
	{
		if(file_exists('admin/modulos/banner/views/' . $nome . '.php')){
			require( $nome . '.php');
		}else{
			echo 'View não encontrada';
		}
	}

	public function listByType($tipo=0,$subtipo=0)
	{
			$banners = parent::select("Select Banner.*,bTipo.nome as tipo_nome From ". $this->getPrefix() ."_banner as Banner
			    Inner Join ". $this->getPrefix() ."_banner_tipo as bTipo ON bTipo.tipo_idx = Banner.tipo_idx
			                          		Where Banner.status=1 And Banner.tipo_idx=".round($tipo)." And Banner.subtipo_banner=".round($subtipo)."
			                          		ORDER BY Banner.ranking DESC ");
			return $banners;
	}

	public function getBanner($tipo=0,$subtipo=0,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=0,$cycle_pause=0,$arrayImages = 0,$prioridade=0){
		global $modulos_scripts;
		$dataBanner = "";
		if($tipo!=0)
		{
			$sql_limite = "";
			if($lista==1){ $randomize=0; }

			$sql_orderby = " ORDER BY Banner.ranking DESC ";
			if($randomize==1){
				$sql_orderby = " ORDER BY RAND() ";
				// if($limite==0){ $limite=1; }
			}

			if($limite>0){ $sql_limite = " LIMIT ".$limite ; }

			//Ajustado para o horario de verao
			$hora = date("H");
			if($hora<0){ $hora=23; }
			$horario=0;
			if($hora>=6 && $hora<17){$horario=1;}
			elseif($hora>=17 && $hora<18){$horario=4;}
			elseif($hora>=18 && $hora <=23){$horario=3;}
			elseif($hora>=0 && $hora <6){$horario=3;}

			/*if ($pagina != 1) {
				$wherePagina = "And (Banner.pagina Like '%-".round($pagina)."-%' Or Banner.pagina Like '%-0-%' OR Banner.pagina Like '')";
			}else{*/
				if ($prioridade === 1) {
					$wherePagina = " And Banner.pagina Like '%-".round($pagina)."-%' ";
				}else{
					$wherePagina = "And (Banner.pagina Like '%-".round($pagina)."-%' Or Banner.pagina Like '%-0-%' OR Banner.pagina Like '')";
				}

			//}

			//$mysql_sys = new manipula_sql;
			$banner_tipo = parent::select("Select * From ". $this->getPrefix() ."_banner_tipo Where tipo_idx=".round($tipo)." ");
			$banners = parent::select("Select Banner.* From ". $this->getPrefix() ."_banner as Banner Where Banner.status=1 And Banner.tipo_idx=".round($tipo)." And Banner.subtipo_banner=".round($subtipo)." " . $wherePagina . " And (Banner.indica_data=0 Or (indica_data=1 And Banner.data_publicacao < now() And Banner.data_expiracao > now())) And (Banner.horario=0 Or Banner.horario=".round($horario).") ".$sql_orderby.$sql_limite);

			if (count($banners) == 0 && $prioridade==0) {
				$wherePagina = " And (Banner.pagina Like '%-0-%' OR Banner.pagina Like '') ";
				$banners = parent::select("Select Banner.* From ". $this->getPrefix() ."_banner as Banner Where Banner.status=1 And Banner.tipo_idx=".round($tipo)." And Banner.subtipo_banner=".round($subtipo)." " . $wherePagina . " And (Banner.indica_data=0 Or (indica_data=1 And Banner.data_publicacao < now() And Banner.data_expiracao > now())) And (Banner.horario=0 Or Banner.horario=".round($horario).") ".$sql_orderby.$sql_limite);
			}

			$tipo_largura = 0;
			$tipo_altura = 0;
			if($banner_tipo && is_array($banner_tipo) ){
				$tipo_largura = $banner_tipo[0]['largura'];
				$tipo_altura = $banner_tipo[0]['altura'];

				$count=0;
				if ($arrayImages === 0) {
					$count=0;

					$dataBanner .= "<div id='banner-tipo-cmd-".$tipo."' class='banner_tipo_cmd'   >";
					if($lista==1){
						$dataBanner .= "<ul id='banner-tipo-lista-".$tipo."' >";
						$zindex = 500;
					}else{
						$dataBanner .= "<div id='banner-tipo-lista-".$tipo."' >";
					}
					if(is_array($banners) && count($banners)>0){
						foreach($banners as $banner) {

							if($banner['monitor_impressao']==1)
							{
								$http_referer = Sis::currPageUrl();
								$remote_addr  = $_SERVER['REMOTE_ADDR'];
								parent::insert("INSERT INTO ". $this->getPrefix() ."_banner_data(banner_idx,tipo,http_referer,remote_addr) values('" . $banner['banner_idx'] . "',0,'" . $http_referer . "','" . $remote_addr . "')");
							}

							$first = "";
							$last = "";
							if($count==0){ $first = "fisrt"; }
							if($count==(count($banners)-1)){ $last = "last"; }

							$aLink_abre = "";
							$aLink_fecha = "";
							if($banner['url']!="" && $banner['url']!="#")
							{
								if($banner['monitor_clique']==1)
								{
									$BannerBaseUrlGo = "http://" . $_SERVER["HTTP_HOST"] . "/admin/modulos/banner/views/banner.get.click.php?b=" . $banner['banner_idx'] . "&hRef=" . urlencode(Sis::currPageUrl());
									$aLink_abre = "<a href='" . $BannerBaseUrlGo . "' class='banner_link' target='".$banner['alvo']."' >";
									$aLink_fecha = "</a>";
								}else{
									$BannerBaseUrlGo = $banner['url'];
									$aLink_abre = "<a href='" . $BannerBaseUrlGo . "' class='banner_link' target='".$banner['alvo']."' >";
									$aLink_fecha = "</a>";
								}
							}

							if($lista==1){ $dataBanner .= "<li id='banner-lista-".$banner['banner_idx']."' class='odd ".$first." ".$last." ' style='z-index:" . $zindex . "' >"; }

								$dataBanner .= "<div id='banner-".$banner['banner_idx']."' class='banner odd ".$first." ".$last."' title='" . $banner["descricao"] . "' style='background-image:url(/".PASTA_CONTENT."/banner/".$banner["arquivo"].")' >" . $aLink_abre . "<img src='/".PASTA_CONTENT."/banner/".$banner["arquivo"]."' />". $aLink_fecha . "</div>"  ;

							if($lista==1){
								$dataBanner .= "</li>";
								$zindex--;
							}

							$count++;
						}
					}
					if($lista==1){ $dataBanner .= "</ul>"; }else{ $dataBanner .= "</div>"; }
					$dataBanner .= "</div>";
					if ($cycle!=0 && count($banners)>0)
					{
						$cycle_pause_vrl = "";
						if($cycle_pause==1){
							//Pausa o Cycle.
							$cycle_pause_vrl = '$("#banner-tipo-lista-' . $tipo . '").cycle("pause");';
						}
						$modulos_scripts .=
						'<script>
							function onAfter(curr, next, opts)
							{
								 var Left = $(opts.prev);
							    var Right = $(opts.next);
							    var index = opts.currSlide;
							    index == 0 ? Left.css("visibility","hidden") : Left.css("visibility","visible");
							    index == opts.slideCount - 1 ? Right.css("visibility","hidden") : Right.css("visibility","visible");
							}
							$(window).on("load", function() {
								$("#banner-tipo-lista-' . $tipo . '").cycle({
									fx: "' . $banner_tipo[0]['animacao'] . '",
									speed: ' . $banner_tipo[0]['animacao_velocidade']*1000 . ',
									timeout: ' . $banner_tipo[0]['animacao_tempo']*1000 . ',
									next:   "#tipo-' . $tipo . '-next",
    								prev:   "#tipo-' . $tipo . '-prev",
    								pager   : "#pager-' . $tipo . '-nav",
    								after   : onAfter,
    								nowrap  : false,
    								clip : "r2l"
								});
								'.$cycle_pause_vrl.'
							});
						</script>';

					}
				}else{
					return $banners;
					if (is_array($banners) && count($banners) > 0) {


						// if(stripos(getenv( "HTTP_USER_AGENT" ), 'MSIE') !== FALSE || stripos(getenv( "HTTP_USER_AGENT" ), 'Internet Explorer') !== FALSE) {
						// 	$modulos_scripts .= '
						// 	<script>
						// 		addLoadEvent(function(){
						// 			$.vegas({ src:"/sitecontent/banner/' . $banners[0]['arquivo'] . '" });
						// 		});
						// 	</script>';
						// }else{
						// 	$dataBanner .= '
						// 	<style>
						// 	body{
						// 		background: url(/sitecontent/banner/' . $banners[0]['arquivo'] . ') no-repeat center center fixed;
						// 		-webkit-background-size: cover;
						// 		-moz-background-size: cover;
						// 		-o-background-size: cover;
						// 		background-size: cover;
						// 		background-position: center;
						// 	}
						// 	</style>
						// 	';

						// }


					};
				}

			}

		}

		return $dataBanner;

	}

	public function getBannerData($tipo=0,$subtipo=0,$pagina=0,$randomize=0,$limite=0){

		$sql_limite = "";

		$sql_orderby = " ORDER BY Banner.ranking DESC ";
		if($randomize==1)
			$sql_orderby = " ORDER BY RAND() ";

		if($limite>0)
			$sql_limite = " LIMIT ".$limite ;

		//Ajustado para o horario de verao
		$hora = date("H");
		if($hora<0){ $hora=23; }
		$horario=0;
		if($hora>=6 && $hora<17){$horario=1;}
		elseif($hora>=17 && $hora<18){$horario=4;}
		elseif($hora>=18 && $hora <=23){$horario=3;}
		elseif($hora>=0 && $hora <6){$horario=3;}

		if ($pagina != 1) {
			$wherePagina = "And (Banner.pagina Like '%-".round($pagina)."-%' Or Banner.pagina Like '%-0-%' OR Banner.pagina Like '')";
		}else{
			$wherePagina = "And (Banner.pagina Like '%-".round($pagina)."-%' Or Banner.pagina Like '%-0-%' OR Banner.pagina Like '')";
		}

		return parent::select("Select Banner.* From ". $this->getPrefix() ."_banner as Banner Where Banner.status=1 And Banner.tipo_idx=".round($tipo)." And Banner.subtipo_banner=".round($subtipo)." " . $wherePagina . " And (Banner.indica_data=0 Or (indica_data=1 And Banner.data_publicacao < now() And Banner.data_expiracao > now())) And (Banner.horario=0 Or Banner.horario=".round($horario).") ".$sql_orderby.$sql_limite);

	}

}
