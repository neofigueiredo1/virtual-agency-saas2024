<?php

require_once($_SERVER['DOCUMENT_ROOT'].DS.'admin'.DS.'library'.DS.'vendor'.DS.'dompdf'.DS.'vendor'.DS.'autoload.php');

// Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Classe controla os metodos que relacionam a compra do curso e sua inscricao ativa.
*/
class EcommerceCurso extends HandleSql
{
	private $TB_PEDIDO;
	private $TB_PEDIDO_ITENS;

	private $TB_CURSO;
	private $TB_CURSO_INSCRITOS;
	private $TB_CURSO_MENSAGENS;
	private $TB_CURSO_CERTIFICADOS;

	private $TB_MODULO;
	private $TB_MODULOS_LIKES_STARS;
	private $TB_MODULO_MIDIAS;

	private $TB_CURSO_MIDIAS;
	private $TB_CURSO_MIDIAS_LOG;
	private $TB_CURSO_MIDIAS_STARS;

	private $TB_CADASTRO;
	private $TB_CADASTRO_CURSO_AFILIADO;


	function __construct(){
		parent::__construct();
		$this->TB_PEDIDO = $this->DB_PREFIX . "_ecommerce_pedido";
		$this->TB_PEDIDO_ITENS = $this->DB_PREFIX . "_ecommerce_pedido_itens";
		
		$this->TB_CURSO = $this->DB_PREFIX . "_curso";
		$this->TB_CURSO_INSCRITOS = $this->DB_PREFIX . "_curso_inscritos";
		$this->TB_CURSO_MENSAGENS = $this->DB_PREFIX . "_curso_mensagens";
		$this->TB_CURSO_CERTIFICADOS = $this->DB_PREFIX . "_curso_certificados";

		$this->TB_MODULO = $this->getPrefix() . "_curso_modulos";
		$this->TB_MODULO_ITENS = $this->getPrefix() . "_curso_modulos_likes_stars";
		$this->TB_MODULO_MIDIAS = $this->getPrefix() . "_curso_modulos_midias";
		
		$this->TB_CURSO_MIDIAS = $this->getPrefix() . "_curso_midias";
		$this->TB_CURSO_MIDIAS_LOG = $this->getPrefix() . "_curso_midias_log";
		$this->TB_CURSO_MIDIAS_STARS = $this->getPrefix() . "_curso_midias_stars";


		$this->TB_CADASTRO = $this->DB_PREFIX . "_cadastro";
		$this->TB_CADASTRO_CURSO_AFILIADO = $this->DB_PREFIX . "_cadastro_afiliado_curso";

	}

	
	/**
	 * Carrega a lista de cursos do usuário baseado em suas compras na plataforma.
	*/
	public function getCursos($filters=array(),$registrosPorPagina=0,$paginaAtual=0)
	{

		$queryWhere = "";
		if (is_array($filters)&&count($filters)>0) {
			if (array_key_exists('curso',$filters)) {
				$queryWhere .= " And tbCurso.curso_idx=" . (int)$filters['curso'];
			}
		}

		$queryStr = "
			SELECT DISTINCT tbCurso.curso_idx,tbCurso.produtor_idx,tbCurso.inscricao_gratis,tbCurso.nome,tbCurso.descricao_curta,tbCurso.descricao_longa,tbCurso.imagem,tbCurso.imagem_aluno,tbCurso.certificado_logo,tbCurso.certificado_assina,tbCurso.certificado_emitir,
				tbCurso.facebook_pixel_id,tbCurso.google_tag_manager_id,
				tbPedido.pedido_idx,
				tbPedido.afiliado_idx,
				tbPedido.status as pedido_status,
				tbCursoInsc.expira,
				tbCursoInsc.expira_data,
				tbCursoInsc.data_cadastro as data_inscricao,
				tbCadAfl.certificado_logo as afiliado_certificado_logo,
				tbCadAfl.certificado_assina as afiliado_certificado_assina,
				tbAfl.certificado_emitir as afiliado_certificado_emitir,
				(
					SELECT count(sqtbMM.midia_idx) AS total
					FROM ".$this->TB_MODULO_MIDIAS." as sqtbMM
					INNER JOIN ".$this->TB_CURSO_MIDIAS." as sqtbMidia ON sqtbMM.midia_idx=sqtbMidia.midia_idx
					INNER JOIN ".$this->TB_MODULO." as tbMod ON tbMod.modulo_idx=sqtbMM.modulo_idx
					WHERE sqtbMidia.status=1 And sqtbMidia.tipo=1 And tbMod.curso_idx=tbCurso.curso_idx
				) as aulasTotal,
				(
					SELECT count(DISTINCT sqtbMM.midia_idx) AS total
					FROM ".$this->TB_MODULO_MIDIAS." as sqtbMM
					INNER JOIN ".$this->TB_CURSO_MIDIAS." as sqtbMidia ON sqtbMM.midia_idx=sqtbMidia.midia_idx
					INNER JOIN ".$this->TB_MODULO." as tbMod ON tbMod.modulo_idx=sqtbMM.modulo_idx
					INNER JOIN ".$this->TB_CURSO_MIDIAS_LOG." as tbLogs ON tbLogs.midia_idx=sqtbMM.midia_idx
					WHERE sqtbMidia.status=1 And sqtbMidia.tipo=1 And tbMod.curso_idx=tbCurso.curso_idx
					And tbLogs.cadastro_idx='". (int)$_SESSION['plataforma_usuario']['id'] ."'
					And tbLogs.data_inicio IS NOT NULL
					And tbLogs.data_fim IS NOT NULL
				) as aulasAssistidasTotal,
				(
					SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( CAST(tbTemp.video_duracao as TIME)  ) ) ) AS timeSum
					FROM (
						SELECT DISTINCT sqtbMidia_b.midia_idx, sqtbMidia_b.video_duracao,tbMod_b.curso_idx,tbLogs_b.cadastro_idx
						FROM ".$this->TB_MODULO_MIDIAS." as sqtbMM_b
						INNER JOIN ".$this->TB_CURSO_MIDIAS." as sqtbMidia_b ON sqtbMM_b.midia_idx=sqtbMidia_b.midia_idx
						INNER JOIN ".$this->TB_MODULO." as tbMod_b ON tbMod_b.modulo_idx=sqtbMM_b.modulo_idx
						INNER JOIN ".$this->TB_CURSO_MIDIAS_LOG." as tbLogs_b ON tbLogs_b.midia_idx=sqtbMM_b.midia_idx
						WHERE 
							sqtbMidia_b.status=1
							And sqtbMidia_b.tipo=1
							And tbLogs_b.data_inicio IS NOT NULL
							And tbLogs_b.data_fim IS NOT NULL
					) as tbTemp
					WHERE 
						tbTemp.curso_idx=tbCurso.curso_idx
						And tbTemp.cadastro_idx='". (int)$_SESSION['plataforma_usuario']['id'] ."'
				) as aulasAssistidasTotalHoras,
				(
					SELECT count(cMens.message_idx) AS total
					FROM ".$this->TB_CURSO_MENSAGENS." as cMens
					WHERE cMens.m_read=0 And cMens.curso_idx=tbCurso.curso_idx And cMens.m_to=". (int)$_SESSION['plataforma_usuario']['id'] ."
				) as mensagensUnreadTotal

			FROM ". $this->TB_CURSO ." as tbCurso
				INNER JOIN ". $this->TB_CURSO_INSCRITOS." as tbCursoInsc ON tbCurso.curso_idx = tbCursoInsc.curso_idx
				LEFT JOIN ". $this->TB_PEDIDO." as tbPedido ON tbCursoInsc.pedido_idx = tbPedido.pedido_idx
				LEFT JOIN ". $this->TB_CADASTRO_CURSO_AFILIADO." as tbAfl ON tbAfl.cadastro_idx = tbPedido.afiliado_idx And tbAfl.curso_idx = tbCurso.curso_idx
				LEFT JOIN ". $this->TB_CADASTRO." as tbCadAfl ON tbCadAfl.cadastro_idx=tbPedido.afiliado_idx
			Where tbCursoInsc.cadastro_idx=".$_SESSION['plataforma_usuario']['id'].$queryWhere." Order By data_inscricao DESC";

			// var_dump($queryStr);
			// exit();

		if ((int)$registrosPorPagina>0 && (int)$paginaAtual>0) {
			return parent::selectPage($queryStr,$registrosPorPagina,$paginaAtual);
		}else{
			return parent::select($queryStr);
		}
	
	}

	
	/**
	 * Carrega a lista de cursos do usuário baseado em suas compras na plataforma.
	*/
	public function getCursosProdutor($filters=array(),$registrosPorPagina=0,$paginaAtual=0)
	{
		$queryWhere = "";
		if (is_array($filters)&&count($filters)>0) {
			if (array_key_exists('curso',$filters)) {
				$queryWhere .= " And tbCurso.curso_idx=" . (int)$filters['curso'];
			}
			if (array_key_exists('produtor',$filters)) {
				$queryWhere .= " And tbCurso.produtor_idx=" . (int)$filters['produtor'];
			}
		}
		$queryStr = "
			SELECT tbCurso.*,
			(
				SELECT count(sqtbMM.midia_idx) AS total
				FROM ".$this->TB_MODULO_MIDIAS." as sqtbMM
				INNER JOIN ".$this->TB_CURSO_MIDIAS." as sqtbMidia ON sqtbMM.midia_idx=sqtbMidia.midia_idx
				INNER JOIN ".$this->TB_MODULO." as tbMod ON tbMod.modulo_idx=sqtbMM.modulo_idx
				WHERE sqtbMidia.status=1 And sqtbMidia.tipo=1 And tbMod.curso_idx=tbCurso.curso_idx
			) as aulasTotal,
			( SELECT count(tbCursoInsc.cadastro_idx) as totalInsc FROM ". $this->TB_CURSO_INSCRITOS ." as tbCursoInsc WHERE tbCursoInsc.curso_idx=tbCurso.curso_idx ) as totalInscritos
			FROM ". $this->TB_CURSO ." as tbCurso
			Where tbCurso.curso_idx<>0 ".$queryWhere." Order By tbCurso.nome ASC";

		if ((int)$registrosPorPagina>0 && (int)$paginaAtual>0) {
			return parent::selectPage($queryStr,$registrosPorPagina,$paginaAtual);
		}else{
			return parent::select($queryStr);
		}
	
	}

	public function getCursoInscritos($filters=array(),$registrosPorPagina=0,$paginaAtual=0)
	{
		$queryWhere = "Where tbCurso.produtor_idx=" . $_SESSION['plataforma_usuario']['id'] ." ";
		if (is_array($filters)&&count($filters)>0) {
			if (array_key_exists('curso',$filters)) {
				$queryWhere .= " And tbCInsc.curso_idx=" . (int)$filters['curso'];
			}
		}
		$queryStr = "
			SELECT tbCInsc.*,tbCICad.nome_completo,tbCICad.email,tbCICad.telefone_resid,tbCICad.celular
			FROM ". $this->TB_CURSO_INSCRITOS ." as tbCInsc
			INNER JOIN ". $this->TB_CADASTRO ." as tbCICad ON tbCInsc.cadastro_idx=tbCICad.cadastro_idx
			INNER JOIN ". $this->TB_CURSO ." as tbCurso ON tbCInsc.curso_idx=tbCurso.curso_idx
			".$queryWhere." Order By tbCInsc.data_cadastro DESC";

		if ((int)$registrosPorPagina>0 && (int)$paginaAtual>0) {
			return parent::selectPage($queryStr,$registrosPorPagina,$paginaAtual);
		}else{
			return parent::select($queryStr);
		}
	
	}

	public function getCursoCertificadoCodigo($codigo)
	{
		$codigo = Text::clean($codigo);
		$codigo = str_replace('CER-','',$codigo);
		$codigo = str_replace('cer-','',$codigo);

		$queryStr = "SELECT tbCCert.*, tbCad.nome_completo as cadastro_nome,tbCur.nome as curso_nome  FROM ". $this->TB_CURSO_CERTIFICADOS ." as tbCCert
		INNER JOIN ". $this->TB_CADASTRO ." as tbCad ON tbCad.cadastro_idx = tbCCert.cadastro_idx
		INNER JOIN ". $this->TB_CURSO ." as tbCur ON tbCur.curso_idx = tbCCert.curso_idx
		Where certificado_codigo='" . $codigo ."' ";
		return parent::select($queryStr);
	}

	public function getCursoCertificado($cursoId,$cadastroId)
	{
		$queryStr = "SELECT tbCCert.* FROM ". $this->TB_CURSO_CERTIFICADOS ." as tbCCert
		Where curso_idx=" . $cursoId ." And cadastro_idx=".(int)$cadastroId." ";
		return parent::select($queryStr);
	}

	public function registraCursoCertificado($request)
	{
		$dataCertificado = Array(
			'curso_idx' => $request['curso_idx'],
			'cadastro_idx' => $request['cadastro_idx'], 
			'data_emissao' => date('Y-m-d H:i:s'), 
			'curso_horas' => $request['curso_horas'],
			'certificado_codigo' => $request['certificado_codigo'], 
			'certificado_arquivo' => $request['certificado_arquivo']
		);
		try {
			self::sqlCRUD($dataCertificado, '', $this->TB_CURSO_CERTIFICADOS, '', 'I', 0, 0);	
		} catch (Exception $e) {
			throw $e;
		}
	}


	public function emitirCertificado($cursoId)
	{

		//Consulta se o certificado já foi emitido.
		$certificado = self::getCursoCertificado($cursoId,$_SESSION['plataforma_usuario']['id']);
		
		$arquivoCert = "";

		if (is_array($certificado)&&count($certificado)>0) { //caso sim, disponibiliza o arquivo para download.
			
			$arquivoCert = $_SERVER['DOCUMENT_ROOT'].DS."sitecontent".DS."curso".DS."certificados".DS.$certificado[0]['certificado_arquivo'];

		}else{//caso não, gera o arquiov, registra no banco e disponibiliza o arquivo para download.

			$ecomCadastro = new EcommerceCadastro();

			$curso = self::getCursos( array('curso'=>$cursoId,'cadastro'=>$_SESSION['plataforma_usuario']['id']) );
			$curso = $curso[0];

			$aulasTotal = $curso['aulasTotal'];
		    $aulasAssitidasTotal = $curso['aulasAssistidasTotal'];
		    $aulasAssitidasTotalPercent = ($aulasTotal>0)?(100/$aulasTotal)*$aulasAssitidasTotal:0;

		    if ( $curso['certificado_emitir']==0 ) {
		    	die("<script>alert('O curso não emite certificado.');history.back();</script>");
		    	exit();
		    }
		    if ($aulasAssitidasTotalPercent<90) {
		    	die("<script>alert('Você precisa concluir 90% das aulas para emitir seu certificado.');history.back();</script>");
		    	exit();
		    }

			$afiliado_certificado_logo = $curso['afiliado_certificado_logo'];
			$afiliado_certificado_assina = $curso['afiliado_certificado_assina'];
			$afiliado_certificado_emitir = $curso['afiliado_certificado_emitir'];

			$produtor = $ecomCadastro->getProdutorByCursoID($cursoId);
			$produtor = $produtor[0];
			
			// print_r($curso);
			// exit();

			// $aulasAssistidasTotalHoras = date('H\h',strtotime($curso['aulasAssistidasTotalHoras']));
			$aulasAssistidasTotalHoras = $curso['aulasAssistidasTotalHoras'];
			$aulasAssistidasTotalHorasArr = explode(":",$aulasAssistidasTotalHoras);
			$aulasAssistidasTotal_horas = $aulasAssistidasTotalHorasArr[0] . "h ";
			// $aulasAssistidasTotal_minutos = $aulasAssistidasTotalHorasArr[1] . "m ";
			// $aulasAssistidasTotal_segundos = $aulasAssistidasTotalHorasArr[2] . "s ";
			$aulasAssistidasTotalHoras_str = $aulasAssistidasTotal_horas; //. $aulasAssistidasTotal_minutos . $aulasAssistidasTotal_segundos;

			if ((int)$aulasAssistidasTotalHorasArr[0]<=0) {
				die("<script>alert('Não foi possível obter o seu certificado, tenta novamente mais tarde, se o problema persistir entre em contato.');history.back();</script>");
		    	exit();
			}

			$codigoNotIsUniq = true;
            while($codigoNotIsUniq){
                $codigo = uniqid();
                //Valida Código.
                $resultCheckCodigo = parent::sqlCRUD(array('certificado_codigo'=>$codigo,'orderby'=>' LIMIT 0,1'), 'certificado_codigo', $this->TB_CURSO_CERTIFICADOS, '', 'S', 0, 0);
                $codigoNotIsUniq = (is_array($resultCheckCodigo)&&count($resultCheckCodigo)>0);
            }

			$html = '
				<style>
					@page{ margin:0px 0px !important; }
					*{ font-family:\'Arial\',\'sans-serif\'; }
					h1{font-size:20px;}
					h2{font-size:16px;margin:0px;padding:0px;}
					hr{border:0px;border-bottom:2px solid #000;margin:20px 0px;}
					hr.title{border:0px;border-bottom:1px solid #ccc;margin:10px 0px;}
					.data_info{font-size:13px;}
					.assinatura{ position:fixed;bottom:50px;font-size:13px;text-align:center;width:100%;}
				</style>
			';

			$pathFile = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));

			$certificadoFileName = "CERT-CONEXO-".$codigo.".pdf";
			$certificadoPathFile = $pathFile . DS . 'sitecontent'. DS .'curso'. DS .'certificados'. DS;
			
			$logo_pathFile = $pathFile . DS . 'assets'. DS .'images'. DS .'instituto-conexo-logo.png';
			$assina_pathFile = $pathFile . DS . 'assets'. DS .'images'. DS .'conexo-assinatura-conexo.png';

			$logoProdutor_pathFile = $pathFile . DS . 'sitecontent'. DS .'cadastro'. DS . $produtor['certificado_logo'];

			$logoCurso_pathFile = $pathFile . DS . 'sitecontent'. DS .'curso'. DS . 'curso'. DS .'images'. DS .$curso['certificado_logo'];
			$logoCurso_image = (file_exists($logoCurso_pathFile) && trim((string)$curso['certificado_logo'])!='' )?'<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($logoCurso_pathFile)).'}}" height="95" />':'';

			$certBack_pathFile = $pathFile . DS . 'assets'. DS .'images'. DS .'certificado-background.png';

			$assinaProdutor_pathFile = $pathFile . DS . 'sitecontent'. DS .'cadastro'. DS . $produtor['certificado_assina'];
			$assinaCurso_pathFile = $pathFile . DS . 'sitecontent'. DS .'curso'. DS . 'curso'. DS .'images'. DS .$curso['certificado_assina'];

			if ($afiliado_certificado_emitir==1){

				$assinaAfiliado_pathFile = $pathFile . DS . 'sitecontent'. DS .'cadastro'. DS . $curso['afiliado_certificado_assina'];
				$logoAfiliado_pathFile = $pathFile . DS . 'sitecontent'. DS .'cadastro'. DS . $curso['afiliado_certificado_logo'];
				
				$logoCurso_image = (file_exists($logoAfiliado_pathFile))?'<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($logoAfiliado_pathFile)).'}}" height="95" />':'';
				$assinaCurso_pathFile = (file_exists($assinaAfiliado_pathFile))?$assinaAfiliado_pathFile:'';

			}


			$cursoAssinaPartsTable = "";
			if (trim($curso['certificado_assina'])!='' && trim($curso['certificado_assina'])!='null') {
				$cursoAssinaPartsTable = '
					<td align="right" >
						<div style="text-align:center;" >
							<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($assinaProdutor_pathFile)).'}}" style="border-bottom:2px solid #000" height="100" /><br>
							'.$produtor['nome_completo'].'
							<br>
							<small>Produtor</small>
						</div>
					</td>
					<td align="center" >
						<div style="text-align:center;" >
							<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($assinaCurso_pathFile)).'}}" style="border-bottom:2px solid #000" height="100" /><br>
							Assinatura da Instituição
						</div>
					</td>
				';

			}else{
				$cursoAssinaPartsTable = '
					<td align="center" colspan="2" >
						<div style="text-align:center;" >
							<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($assinaProdutor_pathFile)).'}}" style="border-bottom:2px solid #000" height="100" /><br>
							'.$produtor['nome_completo'].'
							<br>
							<small>Produtor</small>
						</div>
					</td>
				';
			}

			// var_dump($logoProdutor_pathFile);
			// exit();

			// http://localhost:8080/assets/images/instituto-conexo-logo.png
			// <td><img src="data:image/png;base64,{{'.base64_encode(file_get_contents($logo_pathFile)).'}}" height="75" /></td>

			$html .= '
					<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($certBack_pathFile)).'}}" width="100%" height="100%" style="position:absolute;z-index:0;" />
					<div style="width:1043px;height:729px;padding:30px 40px;display:block;position:relative;" >
						<div style="width:963px;height:665px;padding:30px 40px;border:2px solid #003585;border-radius:10px;" >
							<table cellspacing="0" cellpadding="0" border="0" width="100%" style="position:relative;" >
								<tr>
									<td align="left" ><img src="data:image/png;base64,{{'.base64_encode(file_get_contents($logoProdutor_pathFile)).'}}" height="95" /></td>
									<td align="right" >'.$logoCurso_image.'</td>
								</tr>
								<tr>
									<td colspan="2" style="height:490px;padding-top:25px;" valign="top" >

										<div style="font-size:60px;color:#003585;width:100%;text-align:center;padding-bottom:70px;" >CERTIFICADO</div>
										<div style="font-size:26px;color:#333333;width:100%;text-align:center;padding-bottom:50px;">
											Certificamos que '.$_SESSION['plataforma_usuario']['nome'].', concluiu o curso <br/>
											'.$curso['nome'].' com carga horária de '.$aulasAssistidasTotalHoras_str.'.
										</div>
										<table cellspacing="0" cellpadding="0" border="0" width="100%" style="position:relative;" >
											<tr>'.$cursoAssinaPartsTable.'</tr>
										</table>
										
									</td>
								</tr>
								<tr>
									<td align="left" >
										<div style="display:inline-block;margin:0px;padding:0px;" >
											<div style="width:190px;font-size:13px;padding-top:3px;display:inline-block;" >
												Este curso foi veiculado pela <br/>
												plataforma de ensino e saúde
											</div>
											<img src="data:image/png;base64,{{'.base64_encode(file_get_contents($logo_pathFile)).'}}" height="40" style="margin:0px;" />
										</div>
									</td>
									<td align="right" valign="top" >
										<div style="text-align:right;width:300px;font-size:13px;display:inline-block;margin:0px;padding:0px;" >
											Para verificar a autenticidade deste certificado, <br />
											acesse institutoconexo.com.br e insira o <br />
											código verificador <b style="color:#003585" >CER-'.$codigo.'</b>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
					';


				// echo $html;
				// exit();
			
			
			$html .= '';


			//Cria o registro do Certificado emitido.
			 $dataCertificado = Array(
			 	'curso_idx' => $curso['curso_idx'],
			 	'cadastro_idx' => $_SESSION['plataforma_usuario']['id'],
			 	'curso_horas' => $curso['aulasAssistidasTotalHoras'],
			 	'certificado_codigo' => $codigo, 
			 	'certificado_arquivo' => $certificadoFileName
			 );
			 self::registraCursoCertificado($dataCertificado);

			$arquivoCert = $certificadoPathFile.$certificadoFileName;

			ob_clean();
			// dompdf class
			
			$options = new Options();
			$options->set('isRemoteEnabled', true);

			//Processa o documento de declearação.
			$dompdf = new Dompdf($options);
			// HTML que será transformado em PDF
			$dompdf->loadHtml($html);
			// (Opcional) Tipo do papel e orientação
			$dompdf->setPaper('A4', 'landscape');
			// Render HTML para PDF
			$dompdf->render();
			
			$output = $dompdf->output();
    		file_put_contents($arquivoCert, $output);
			
			// // Download do arquivo
			// $dompdf->stream($certificadoFileName);
			// echo $html;

		}


		ob_clean();
		header("Expires: 0");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		$ext = pathinfo($certificado[0]['certificado_arquivo'], PATHINFO_EXTENSION);
		$basename = pathinfo( $arquivoCert , PATHINFO_BASENAME);

		header("Content-type: application/".$ext);
		header('Content-length: '.filesize($arquivoCert));
		header("Content-Disposition: attachment; filename=\"$basename\"");
		ob_clean();
		flush();

		readfile($arquivoCert);
		exit;
		

	}

}
