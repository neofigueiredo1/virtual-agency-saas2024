<?php

class Sis{

		/**
		* Essa função deleta o elemento desejado de um "array" unidimensional
		* @param $array @array ->  o array
		* @param $deleteIt @string -> o valor desejado para remover
		* @param $useOldKeys @boolean -> se 'false': então a função refaz o indice (de 0, 1, ...)
		*                          		se 'true': a função irá manter os indices antigos
		* @return boolean true, se esse valor estiver no array, caso contrario 'false', neste caso o array é o mesmo de antes.
		**/
		public static function deleteFromArray(&$array, $deleteIt, $useOldKeys = FALSE)
		{
		    $tmpArray = array();
		    $achou = FALSE;
		    foreach($array as $key => $valor)
		    {
		        if($valor !== $deleteIt)
		        {
		            if(FALSE === $useOldKeys)
		            {
		                $tmpArray[] = $valor;
		            }
		            else
		            {
		                $tmpArray[$key] = $valor;
		            }
		        }
		        else
		        {
		            $achou = TRUE;
		        }
		    }
		    $array = $tmpArray;
		    return $achou;
		}

		public static function validarCep($cep) {
	    	$cep = trim($cep);
	    	$avaliaCep = preg_match("/^[0-9]{5}-[0-9]{3}$/", $cep);
	    	if(!$avaliaCep) {
	        	return false;
	    	}else{
	        	return true;
	    	}
		}

		public static function goToProtocol($secure=''){
			if($secure == 's'){
				if( !isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on" ){
					header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
				}
			}else{
				if( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ){
					header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
				}
			}
		}

		public static function getGravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		   $url = 'http://www.gravatar.com/avatar/';
		   $url .= md5( strtolower( trim( $email ) ) );
		   $url .= "?s=$s&d=$d&r=$r";
		   if ( $img ) {
		      $url = '<img src="' . $url . '"';
		      foreach ( $atts as $key => $val )
		         $url .= ' ' . $key . '="' . $val . '"';
		      $url .= ' />';
		   }
		   return $url;
		}

		//Checa as permissões do usuário
		public static function checkPerm($perm)
		{
			$checkPerm = false;
			if(strpos($_SESSION['usuario']["permissao"],$perm)!==false)
			{
				$checkPerm = true;
			}
			if($_SESSION['usuario']['nivel']==1 || $_SESSION['usuario']['nivel']==2)
			{
				$checkPerm = true;
				/*Limita o acesso do Administrador*/
				if($perm=="xxx" && $_SESSION['usuario']['nivel']==2)
				{
					$checkPerm = false;
				}
			}
			return $checkPerm;
		}


		public static function arrayPutToPosition(&$array, $object, $position, $name = null)
		{
		        $count = 0;
		        $return = array();
		        foreach ($array as $k => $v)
		        {
		                // insert new object
		                if ($count == $position)
		                {
		                        if (!$name) $name = $count;
		                        $return[$name] = $object;
		                        $inserted = true;
		                }
		                // insert old object
		                $return[$k] = $v;
		                $count++;
		        }
		        if (!$name) $name = $count;
		        if (!$inserted) $return[$name];
		        $array = $return;
		        return $array;
		}

		public static function getConfirm($url, $message = 'Você deseja excluir os dados?'){

			return "javascript: if (confirm('" . $message . "')) { window.location='" . $url . "'; } else { return false; };";
			
		}

		public static function setAlert($msg, $type, $url = false) {

			$_SESSION['sis_mens'] = $msg;
			$_SESSION['sis_mens_tipo'] = $type;

			if ($url) {
				self::redirect($url);
			}

			die('<script>history.back();</script>');
		}

		public static function isPageInternal($page){
			if(isset($page) && is_int($page) && !empty($page) && $page !== 1){
				return true;
			}else{
				return false;
			}
		}

		public static function checkCaptcha($captchaSession, $string){
			if ($captchaSession === $string) {
				return true;
			} else {
				return false;
			}
		}

		public static function getMaritalStatus($cod){
			$states = array('Indefinido', 'Solteiro', 'Casado', 'Viúvo', 'Separado', 'Divorciado', 'Outros');
			if(!is_numeric($cod) || $cod > 7){
				$cod = 0;
			}
			return $states[$cod];
		}

		public static function getState($state=true){
			$states = array(
							'AC' => 'Acre',
							'AL' => 'Alagoas',
							'AP' => 'Amapá',
							'AM' => 'Amazonas',
							'BA' => 'Bahia',
							'CE' => 'Ceará',
							'DF' => 'Distrito Federal',
							'GO' => 'Goiás',
							'ES' => 'Espírito Santo',
							'MA' => 'Maranhão',
							'MT' => 'Mato Grosso',
							'MS' => 'Mato Grosso do Sul',
							'MG' => 'Minas Gerais',
							'PA' => 'Pará',
							'PB' => 'Paraiba',
							'PR' => 'Paraná',
							'PE' => 'Pernambuco',
							'PI' => 'Piauí',
							'RJ' => 'Rio de Janeiro',
							'RN' => 'Rio Grande do Norte',
							'RS' => 'Rio Grande do Sul',
							'RO' => 'Rondônia',
							'RR' => 'Rorâima',
							'SP' => 'São Paulo',
							'SC' => 'Santa Catarina',
							'SE' => 'Sergipe',
							'TO' => 'Tocantins'
							);
			if ($state === true) {
				return $states;
			} elseif (isset($states[$state])) {
				return $states[$state];
			}
		}

		public static function getHorario($hora){

			switch ($hora) {
				case "1":
					return "Manhã";
					break;
				case "2":
					return "Tarde";
					break;
				case "3":
					return "Noite";
					break;
				case "4":
					return "Manhã/Tarde";
					break;
				default:
					return "Indfefinido";
					break;
			}
		}

		public static function getFormacao($cod){

			switch ($cod) {
				case 1 :
					return "2º grau";
					break;
				case 2 :
					return "Graduação";
					break;
				case 3 :
					return "Pós-graduação";
					break;
				case 4 :
					return "MBA";
					break;
				case 5 :
					return "Mestrado";
					break;
				default:
					return "Indfefinido";
					break;
			}
		}

		public static function currPageUrl() {
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			$pageURL .= "://";
			if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			} else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			return $pageURL;
		}

		public static function redirect($url, $time=0)
		{
			$url = str_replace('&amp;', '&', $url);

			if($time > 0){
				header("Refresh: $time; URL=$url");
			}else{
				// ob_end_clean();
				header("Location: $url");
				exit;
			}
		}

		/*Retorna o status do regisro formatado*/
		public static function getStatusFormat($status = 0){
			$status = (int)$status;
			$status_ret = "";
			if($status===0){ $status_ret = "<span class='status_color_off'>Inativo</span>"; }
			if($status===1){ $status_ret = "<span class='status_color_on'>Ativo</span>"; }
			return $status_ret;
		}

		public static function getYesOrNotFormat($yesOrNot=0){
			$yesOrNot = (int)$yesOrNot;
			$yesOrNotReturn = "";
			if($yesOrNot===0){ $yesOrNotReturn = "<span class='status_color_off'>N&atilde;o</span>"; }
			if($yesOrNot===1){ $yesOrNotReturn = "<span class='status_color_on'>Sim</span>"; }
			return $yesOrNotReturn;
		}

		//Marca os checkbox que foram marcados
		public static function checked($item,$id){
			$item_array = explode(",",$item);
			for($i=0;$i<count($item_array);$i++){
				if($item_array[$i] == "-".$id."-"){
					return  "checked";
					break;
				}
			}
		}


		/**
		* Registra o log de atividade no sistema
		* @param int $modulo_codigo -> codigo do modulo, caso igual a 0 eh uma acao do sistema.
		* @param string 255 $modulo_area -> identifica a area do modulo.
		* @param string 255 $acao -> identifica a acao executada ex.: SELECT / INSERT / UPDATE / DELETE
		* @param int $reg_codigo -> chave de identificacao do registro
		* @param string 255 $reg_nome -> nome do registro.
		* @param string longtext $descricao -> descricao complementar
		* @return void
		*/
		public static function insertLog($modulo_codigo=0,$modulo_area="",$acao="",$reg_codigo=0,$reg_nome="",$descricao=""){
			$i = new HandleSql();
			$tlb_prefix = $i->getPrefix();
			$user_id = (isset($_SESSION['usuario']['id']))?$_SESSION['usuario']['id']:0;
			$user_ip = (!empty($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : "";
			$i->insert("INSERT INTO ".$tlb_prefix."_log (usuario_idx,modulo_codigo,modulo_area,registro_codigo,registro_nome,acao,descricao,ip_usuario)
			           	VALUES (".round($user_id).",".round($modulo_codigo).",'".$modulo_area."',".round($reg_codigo).",'".$reg_nome."','".$acao."','" . $descricao . "', '" . $user_ip . "')");
		}

		public static function config($var){
			$s = new HandleSql();
			$tlb_prefix = $s->getPrefix();
			$x = $s->select("SELECT valor FROM ".$tlb_prefix."_config WHERE nome Like '".$var."' LIMIT 0,1 ");
			if(is_array($x) && count($x)>0)
			{
				return $x[0]['valor'];
			}else{
				return false;
			}
		}

		//Retorna o padrão de mensagem enviada pelo site
		public static function returnMessageBody($subject)
		{
			$CLI_URL = Sis::config("CLI_URL");
			$CLI_URL = str_replace("https://","",$CLI_URL);
			$CLI_URL = str_replace("http://","",$CLI_URL);
			$CLI_URL = str_replace("/","",$CLI_URL);

			$body = "<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' /><title></title>
			</head><body style='margin: 10px auto; width: 800px; background: #f9f9f9; padding: 25px; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; text-decoration: none;'>
			<img src='https://".$CLI_URL."/".Sis::config("CLILOGO")."' /> <br><span style='font-size:20px;' >". $subject ."</span> &nbsp; em: ". date("d\/m\/Y \&\a\g\\r\a\\v\\e\;\s H:i:s") ."<br>
			<hr>
			<fieldset><legend style='font-size:18px;'><strong>Dados enviados</strong></legend>[HTML_DADOS]</fieldset>
			<table width='800' cellpadding='0' cellspacing='0' border='0' align='center' style='background-color:#fff; border-radius: 5px' >
				<tbody>
					<tr>
						<td bgcolor='#ffffff' style='padding: 10px 20px; border-radius: 3px;'>
							<small style='font-size:9px; text-align:justify;'>". Sis::config("CLI_MAIL_CONTATO"). "</small>
						</td>
					</tr>
					<tr>
						<td bgcolor='#ffffff' style='padding: 10px 20px; border-radius: 3px; border-top: 1px solid #eee;'>
							<p align=right style='color:#FF0000;font-size:11px;' >Ao responder esta mensagem, favor copiar para: ". Sis::config("CLI_MAIL_CONTATO"). " </p>
							<br>
						<p align=right><font style='font-size:9px;' >". Sis::config("CLI_NOME") ." ". date("Y") ." Todos os Direitos Reservados</font></p>
						</td>
					</tr>
				<tbody>
			</table></body></html>";

			return $body;
		}


		//Retorna o padrão de mensagem enviada pelo site para os clientes
		public static function returnMessageBodyClient($subject)
		{
			$CLI_URL = Sis::config("CLI_URL");
			$CLI_URL = str_replace("https://","",$CLI_URL);
			$CLI_URL = str_replace("http://","",$CLI_URL);
			$CLI_URL = str_replace("/","",$CLI_URL);
			
			$body = "<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' /><title></title>
			</head><body style='margin: 10px auto; width: 800px; background: #f9f9f9; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; text-decoration: none;padding:15px;' >
			
			
			<table width='800' cellpadding='0' cellspacing='0' border='0' align='center'  >
				<tbody>
					<tr>
						<td style='padding: 10px 20px;text-align:center;' width='50%' >
							<img src='https://".$CLI_URL."/".Sis::config("CLILOGO")."' />
						</td>
					</tr>
					<tr>
						<td style='padding: 10px 20px;font-size:18px;text-align:center;color:#000;' width='50%' >
							".$subject."
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset style='box-sizing: border-box;background-color:#fff;border-radius:8px;width:100%;max-width:757px;border:1px solid #eee;font-size:14px;padding:30px;margin: 10px auto;font-family:arial, sans-serif;line-height:130%;color:#333;'>[HTML_DADOS]</fieldset>

			<table width='800' cellpadding='0' cellspacing='0' border='0' align='center' >
				<tbody>
					<tr>
						<td style='padding: 10px 20px; border-radius: 3px;text-align:left;font-size:13px;'  width='50%' >
							&copy; ". Sis::config("CLI_NOME") ." ". date("Y") ." Todos os Direitos Reservados
						</td>
						<td style='padding: 10px 20px; border-radius: 3px; font-size:13px;text-align:right;'  width='50%' >
							em: ". date("d\/m\/Y \&\a\g\\r\a\\v\\e\;\s H:i:s") ."<br>
						</td>
					</tr>
				<tbody>
			</table></body></html>";

			return $body;
		}

		/*
		Função recursiva para retornar o tamanho em bytes de uma pasta e subpastas.
		*/
		public static function folderSize($path) {
			$totalSize = 0;
			$files = scandir($path);
			foreach($files as $t) {
				if (is_dir(rtrim($path, '/') . '/' . $t)) {
					if ($t<>"." && $t<>"..") {
						$size = sis::folderSize(rtrim($path, '/') . '/' . $t);
						$totalSize += $size;
					}
				} else {
					$size = filesize(rtrim($path, '/') . '/' . $t);
					$totalSize += $size;
				}
			}
			return $totalSize;
		}

		/**
		* Função para capturar o tamanho em bytes de uma pasta e todo seu contéudo.
		* Windows usando o componente com, nativo em servidores windows, FSO
		* Linux usando o executa um comando usando um ponteido
		* Caso do falha usa-se uma função auxiliar "folderSize"
		* @param string $path caminho completo da pasta.
		* @return interger
		*/
		public static function folderSizeWL($path) {
			$totalSize = 0;
			return $totalSize;
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			{
				if (class_exists("COM")) {
					$obj = new COM ( 'scripting.filesystemobject' );
					if ( is_object ( $obj ) )
					{
						$ref = $obj->getfolder ( $path );
						$totalSize = $ref->size;
						$obj = null;
					}
					else
					{
						$totalSize = self::folderSize($path);
					}
				}else{
					$totalSize = self::folderSize($path);
				}
			}else{
				try {
					$handle = popen ( '/usr/bin/du -sk ' . $path, 'r' );
					$size = fgets ( $handle, 4096);
					$size = substr ( $size, 0, strpos ( $size, "\t" ) );
					pclose ( $handle );
					$totalSize = $size;
				} catch (Exception $e) {
					throw $e;
					
				}
			}
			return $totalSize;
		}

		/**
		* Formata a saída em byte, kilobyte, MB, GB e TB
		* @param int $bytes -> valor em bytes para formatar
		*/
		public static function formatBytes($bytes, $precision = 2)
		{
		    $units = array('B', 'KB', 'MB', 'GB', 'TB');
		    for ($i=0; $i < count($units); $i++)
		    {
		    	if($bytes>1024){
					$bytes = $bytes/1024;
		    	}else{
		    		break;
		    	}
		    }
		    return (is_numeric($bytes))? number_format($bytes, $precision) . ' ' . $units[$i] : 0;
		}


		public static function isValidEmail($mail, $simple=true){
			if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
				return false;
			}
			if($simple == true){
				list($user, $host) = explode("@", $mail);
				if (!checkdnsrr($host, "MX") && !checkdnsrr($host, "A")){
					return false;
				}
			}
			return true;
		}

		public static function detectMobile(){
			if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT'])){
				return true;
			}else{
				return false;
			}
		}

		public static function jqueryEfx(){
			return array(
						 'Rolar para cima'       => 'scrollUp',
						 'Rolar para baixo'      => 'scrollDown',
						 'Rolar para a esquerda' => 'scrollLeft',
						 'Rolar para a direita'  => 'scrollRight',
						 'Rolar pela horizontal' => 'scrollHorz',
						 'Rolar para vertical'   => 'scrollVert',
						 'Deslizar no eixo de X' => 'slideX',
						 'Deslizar no eixo de Y' => 'slideY',
						 'Embaralhar'            => 'shuffle',
						 'Virar para cima'       => 'turnUp',
						 'Virar para baixo'      => 'turnDown',
						 'Virar para esquerda'   => 'turnLeft',
						 'Virar para direita'    => 'turnRight',
						 'Zoom'                  => 'zoom',
						 'Desvanecer'            => 'fade',
						 'Zoom e Desvanecer'     => 'fadeZoom',
						 'Mascarar no eixo de X' => 'blindX',
						 'Mascarar no eixo de Y' => 'blindY',
						 'Mascarar no eixo de Z' => 'blindZ',
						 'Crescer no eixo de X'  => 'growX',
						 'Crescer no eixo de Y'  => 'growY',
						 'Cortina no eixo de X'  => 'curtainX',
						 'Cortina no eixo de Y'  => 'curtainY',
						 'Ocultar'               => 'cover',
						 'Revelar'               => 'uncover',
						 'Sacudir'               => 'toss',
						 'limpar'                => 'wipe'
						 );
		}

		public static function getVideoThumb($url){
			$thumb = "";
			if (strpos($url,'youtube.com') !== false) {
				parse_str( parse_url( $url, PHP_URL_QUERY ), $parameter );
				$thumb = 'http://img.youtube.com/vi/' . $parameter['v'] . '/0.jpg';
			}
			if (strpos($url,'vimeo.com') !== false) {
				sscanf(parse_url($url, PHP_URL_PATH), '/%d', $videoId);
				$thumb = unserialize(file_get_contents("http://vimeo.com/api/v2/video/" . $videoId . ".php"));
				$thumb = $thumb[0]['thumbnail_medium'];
			}
			if (strpos($url,'dailymotion.com') !== false) {
				$thumb = substr($url, 0, 27) . 'thumbnail' . substr($url, 26, strlen($url));
			}
			return $thumb;
		}

		public static function getVimeoThumbUrl($videoID)
		{
			// Fonte: stackoverflow.com/questions/17156298/get-id-video-vimeo-with-regexp-preg-match/17156853#17156853

			$url = "http://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/".$videoID;
			
			var_dump($url);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);

				var_dump($response);
				exit();

			try {
				$videoData = json_decode($response);
				if ($videoData!==false) {
					return $videoData->thumbnail_url;
				}
			} catch (Exception $e) {
				die($e->getMessage());
				//Do Nothind
			}

			return 'none';
			
		}

		public static function getVideoIframe($url="",$fullscreen=false,$width=350,$height=250)
		{
			$source = "";
			if (strpos($url,'youtube.com') !== false) {
				parse_str( parse_url( $url, PHP_URL_QUERY ), $parameter );
				$source = '<iframe width="'.$width.'" height="'.$height.'" src="//www.youtube.com/embed/' . $parameter['v'] . '" frameborder="0" '.($fullscreen?'allowfullscreen':NULL).'></iframe>';
			}
			if (strpos($url,'vimeo.com') !== false) {
				sscanf(parse_url($url, PHP_URL_PATH), '/%d', $videoId);
				if (trim($videoId)!="") {
					$source = '<iframe src="//player.vimeo.com/video/'.$videoId.'" width="'.$width.'" height="'.$height.'" frameborder="0" '.($fullscreen?'webkitallowfullscreen mozallowfullscreen allowfullscreen':NULL).' ></iframe>';
				}
			}
			if (strpos($url,'dailymotion.com') !== false) {
				$thumb = substr($url, 0, 27) . 'thumbnail' . substr($url, 26, strlen($url));
				$url = str_replace("http://","",$url);
				$video_id = substr($url, 26, strlen($url));
				$source = '<iframe src="http://www.dailymotion.com/embed/video/'.$video_id.'" width="'.$width.'" height="'.$height.'" frameborder="0" ></iframe>';
			}
			return $source;
		}

		public static function generatePassword($size = 8, $uppercase = true, $numbers = true, $symbols = false) {
			$lettersTiny = 'abcdefghijklmnopqrstuvwxyz';
			$lettersUpper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$num = '1234567890';
			$symb = '!@#$%*-';
			$return = '';
			$characters = '';

			$characters .= $lettersTiny;
			if ($uppercase) $characters .= $lettersUpper;
			if ($numbers) $characters .= $num;
			if ($symbols) $characters .= $symb;

			$len = strlen($characters);
			for ($n = 1; $n <= $size; $n++) {
				$rand = mt_rand(1, $len);
				$return .= $characters[$rand-1];
			}
			return $return;
		}

		public static function desvar($string){
			$string = str_replace('&#8217;',"'", $string);
			$string = str_replace('&amp;', "&", $string);
			return $string;
		}

		public static function execQueryByFile($arquivo=false)
		{
			if($arquivo!==false)
			{
				$prefix = Connect::getPrefix();
				$sqliConn = Connect::getInstance();
				/*Le o arquivo base*/
				$dumpsql = file_get_contents($arquivo, true);
				$dumpsql = str_replace("[ADMPREFIX]",$prefix,$dumpsql);
				/*Executa o DUMP do sistema*/
				try{
				   $sqliConn->exec($dumpsql);
				   /*
				   $sqliConn->multi_query($dumpsql);
				   $sqliConn->close();
				   */
				   return true;
				}
				catch (PDOException $e)
				{
					echo $e->getMessage();
				   die();
				}
			}
		}

		/*Matriz de cores em hexadecimal, usadas para ilustrar no admin*/
		public static function getColorMatrix(){
			$cMatrix = array(
	                      (object) array("cor"=>"f15b24","corb"=>"c6481a"),
	                      (object) array("cor"=>"184891","corb"=>"0f3268"),
	                      (object) array("cor"=>"6da943","corb"=>"4d7b2d"),
	                      (object) array("cor"=>"fdd24f","corb"=>"e0b93a"),
	                      (object) array("cor"=>"2cc49b","corb"=>"2cb79a"),
	                      (object) array("cor"=>"f38844","corb"=>"e57746"),
	                      (object) array("cor"=>"ed1b24","corb"=>"c6161d"),
	                      (object) array("cor"=>"01a89e","corb"=>"007a72"),
	                      (object) array("cor"=>"0483c8","corb"=>"035c8d"),
	                      (object) array("cor"=>"d9c68c","corb"=>"ad9d6a"),
	                      (object) array("cor"=>"b42c06","corb"=>"872105"),
	                      (object) array("cor"=>"fc9a1f","corb"=>"cb7b16"),
	                      (object) array("cor"=>"dcd62a","corb"=>"ada81c"),
	                      (object) array("cor"=>"786544","corb"=>"50412a"),
	                      (object) array("cor"=>"85541a","corb"=>"5a3710"),
	                      (object) array("cor"=>"6ca690","corb"=>"4c7c69")
	                       );
			return $cMatrix;
		}

		public static function getColumnsFromTable($table){
			$sqlQuery = new HandleSql();
			return $sqlQuery->select("SHOW FULL COLUMNS FROM ".$sqlQuery->DB_PREFIX."_".$table."");
		}

		public static function isValidMd5($md5){

			return preg_match('/^[a-f0-9]{32}$/', $md5);
		}

		public static function validaCPF($cpf = null) {

		    // Verifica se um número foi informado
		    if(empty($cpf)) {
		        return false;
		    }

		    // Elimina possivel mascara
		    $cpf = preg_replace('[^0-9]', '', $cpf);
		    $cpf = str_replace('.','',$cpf);
		    $cpf = str_replace('-','',$cpf);
		    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
		    
		    // Verifica se o numero de digitos informados é igual a 11
		    if (strlen($cpf) != 11) {
		        return false;
		    }
		    // Verifica se nenhuma das sequências invalidas abaixo
		    // foi digitada. Caso afirmativo, retorna falso
		    else if ($cpf == '00000000000' ||
		        $cpf == '11111111111' ||
		        $cpf == '22222222222' ||
		        $cpf == '33333333333' ||
		        $cpf == '44444444444' ||
		        $cpf == '55555555555' ||
		        $cpf == '66666666666' ||
		        $cpf == '77777777777' ||
		        $cpf == '88888888888' ||
		        $cpf == '99999999999') {
		        return false;
		     // Calcula os digitos verificadores para verificar se o
		     // CPF é válido
		     } else {

		        for ($t = 9; $t < 11; $t++) {

		            for ($d = 0, $c = 0; $c < $t; $c++) {
		                $d += $cpf[$c] * (($t + 1) - $c);
		            }
		            $d = ((10 * $d) % 11) % 10;
		            if ($cpf[$c] != $d) {
		                return false;
		            }
		        }

		        return true;
		    }
		}

		public static function validaCNPJ($cnpj = null) {

	    	$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
			// Valida tamanho
			if (strlen($cnpj) != 14)
				return false;
			// Valida primeiro dígito verificador
			for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
			{
				$soma += $cnpj[$i] * $j;
				$j = ($j == 2) ? 9 : $j - 1;
			}
			$resto = $soma % 11;
			if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
				return false;
			// Valida segundo dígito verificador
			for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
			{
				$soma += $cnpj[$i] * $j;
				$j = ($j == 2) ? 9 : $j - 1;
			}
			$resto = $soma % 11;
			return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
			
		}


	} //End class