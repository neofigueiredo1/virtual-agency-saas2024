<?php
class EcommerceXML
{

	protected $path;

	function __construct(){
		$this->path = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."xml".DIRECTORY_SEPARATOR."source".DIRECTORY_SEPARATOR;
	}

	/**
	* Lista os artprints
	* @param $params mixed - Passa os parametros de pesquisa de um ou vários artprints.
	* @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	*/
	public function getResource($resource,$params)
	{
		//Verifica se existe um arquivo gerado do xml.
		$filename = $this->path.$resource;
		if (file_exists($filename)){
		    $filedate = new DateTime(date('Y-m-d H:i:s', strtotime(filemtime($filename))));
		    $datetime2 = new DateTime(date('Y-m-d H:i:s'));
		    $interval = $filedate->diff($datetime2);
			$intervalo = $interval->format('%s');
			if ($intervalo>24) {
				self::generateResource($resource,$params);
			}
		}else{
			self::generateResource($resource,$params);
		}
		return $filename;
	}

	public function generateResource($resource,$params)
	{
		$params['withCategoriaNome'] = true;

		$ecomProduto = new EcommerceProduto();
	    $ecomProdutoCategoria = new EcommerceProdutoCategoria();
	    
	    $produtos = $ecomProduto->produtosListAll($params);
	    if (!(is_array($produtos)&&count($produtos)>0)){
	    	throw new Exception("Sem produtos para o recurso solicitado", 1);
	    }

	    //Processa a criação do arquivo
	    $XML_SOURCE = '<?xml version="1.0"?>
	    <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	    	<channel>
	    		<title>'.Sis::config('CLI_TITULO').'</title>
	    		<link>http://' . $_SERVER['HTTP_HOST'] . '</link>
	    		<description>'.Sis::config('CLI_DESCRICAO').'</description>';

	    foreach ($produtos as $key => $produto) {
	    	// var_dump($produto);
	    	// exit();
	    	$url_detalhes = "https://" . $_SERVER['HTTP_HOST'] . "/produto/".Text::friendlyUrl($produto['nome'].'-'.$produto['codigo']);
	    	$p_imagem="";
	    	if(!is_null($produto['imagem']) && trim($produto['imagem'])!="" && trim($produto['imagem'])!="null"){
	    		$p_imagem = "https://" . $_SERVER['HTTP_HOST'] . "/sitecontent/ecommerce/produto/images/g/".$produto['imagem'];
			}

			$produto_desconto = 0;
            $produto_valor = $produto['valor'];
            $produto_valor_final = $produto['valor'];
            if($produto['em_oferta']==1){
				$produto_desconto = $produto['valor']-$produto['em_oferta_valor'];
                $produto_valor_final = $produto['em_oferta_valor'];
            }

            $itemNome = $produto['nome'];
			$itemNome = html_entity_decode($itemNome);
			$itemNome = str_replace("&","&amp;",$itemNome);
            $descricao_curta = strip_tags($produto['descricao_curta']);
            $descricao = (trim($descricao_curta)!="") ? $descricao_curta : $itemNome ;

            $descricao = html_entity_decode($descricao);
            $descricao = str_replace("&","&amp;",$descricao);
            $descricao = preg_replace("/\r|\n/", "", $descricao);

	    	$XML_SOURCE .= '<item>
					<g:id>'.$produto['codigo'].'</g:id>
					<g:title>'. $itemNome .'</g:title>
					<g:description>'. $descricao .'</g:description>
					<g:link>'.$url_detalhes.'</g:link>
					<g:image_link>'.$p_imagem.'</g:image_link>
					<g:brand>'.Sis::config('CLI_NOME').'</g:brand>
					<g:condition>new</g:condition>
					<g:availability>in stock</g:availability>
					<g:price>'. number_format($produto_valor_final,2,".","") .' BRL</g:price>
					<g:product_type>'.$produto['sessao_nome'].' / '.$produto['categoria_nome'].'</g:product_type>
					<g:google_product_category>188</g:google_product_category>
				</item>';
	    }

		$XML_SOURCE .= '</channel></rss>';

		$filename = $this->path.$resource;
		//Elimina o atual
		if (file_exists($filename)) {
			unlink($filename);
		}
		
		try {
			//Escreve o arquivo.
			$file = fopen($filename,"w");
			fwrite($file,$XML_SOURCE);
			fclose($file);
		} catch (Exception $e) {
			throw $e;
		}

	}

	public function getResourceLinks($resource){
		
		$ecomProdutos = new EcommerceProduto();
		$ecomProdutosCategoria = new EcommerceProdutoCategoria();
		$retorno=""; 
		switch ($resource) {
			case 'produtos':
			
				$fProdutos = $ecomProdutos->produtosListAll();

				if(is_array($fProdutos) && count($fProdutos) > 0){
					$retorno = "<table style='font-family:arial;font-size:13px;border-collapse:collapse' cellspacing='0' cellpadding='10' >";
					foreach ($fProdutos as $key => $produto) {
						$retorno .= '<tr><td style="border:#cccccc 1px dashed;" >
								'.$produto['nome'].' 
								</td><td style="border:#cccccc 1px dashed;"  >
								<a href="http://'.$_SERVER['HTTP_HOST'].'/xml/produtos/'.$produto['produto_idx'].'" target="_blank" >http://'.$_SERVER['HTTP_HOST'].'/xml/produtos/'.$produto['produto_idx'].'</a>
							</td></tr>';
					}
					$retorno .= "</table>";
				}
				break;

			case 'categorias':
				$categorias = $ecomProdutosCategoria->categoriasListAll();
				if(is_array($categorias) && count($categorias) > 0){
					$retorno = "<table style='font-family:arial;font-size:13px;border-collapse:collapse' cellspacing='0' cellpadding='10' >";
					foreach ($categorias as $key => $categoria) {
						$retorno .= '<tr><td style="border:#cccccc 1px dashed;" >
								'.$categoria['sessao_nome'].' / '.$categoria['nome'].' 
								</td><td style="border:#cccccc 1px dashed;"  >
								<a href="http://'.$_SERVER['HTTP_HOST'].'/xml/produtos/categorias/'.$categoria['categoria_idx'].'" target="_blank" >http://'.$_SERVER['HTTP_HOST'].'/xml/produtos/categorias/'.$categoria['categoria_idx'].'</a>
							</td></tr>';
					}
					$retorno .= "</table>";
				}
				break;
		}

		return $retorno;
	}


}