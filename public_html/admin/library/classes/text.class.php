<?php

	class Text{

		public static function personalizeUrl($action=0, $pageId=0, $title='', $url='', $table=''){
			if($action ==  1){
				$where = "";
				if ($pageId !== 0){
					$where = "pagina_idx <> " . $pageId . " AND ";
				}

				if($url!=""){
					$url = str_replace("/", "", $url);

					$sqlQuery = new HandleSql;
					$res = $sqlQuery->select("SELECT * FROM " . $table . " WHERE " . $where . " (titulo like '" . trim($title) . "' Or url_rewrite like '" . trim($url) . "')");
					// var_dump($res);
					if(is_array($res) && count($res)>0){
						echo "error";
					}else{
						echo "ok";
					}
				}

			}else{
				$friendlyUrl = $url;
				if(trim($url) == ""){
					$friendlyUrl = self::friendlyUrl($title);
				}
				$where = "";
				if ($pageId !== 0){
					$where = "pagina_idx <> " . $pageId . " AND ";
				}
				if($title!="")
				{
					$sqlQuery = new HandleSql();

					$res = $sqlQuery->select("SELECT pagina_idx FROM " . $table . " WHERE " . $where . " url_rewrite='" . $friendlyUrl . "' ");
					if(is_array($res) && count($res) > 0){

						$friendlyUrlArr = explode("-", $friendlyUrl);
						if(is_array($friendlyUrlArr)){
							if(end($friendlyUrlArr) == "i" || end($friendlyUrlArr) == "ii" || end($friendlyUrlArr) == "iii"){
								$friendlyUrl .= "i";
							}else{
								$friendlyUrl .= "-i";
							}
						}
						self::personalizeUrl(0, $pageId, $title, $friendlyUrl, $table);
						// echo $friendlyUrl;
					}else{
						echo $friendlyUrl;
					}
				}
			}
		}

		public static function toAscii($string, $replace=array(), $delimiter='-') {
			if( !empty($replace) ) {
				$string = str_replace((array)$replace, ' ', $string);
			}

			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

			return $clean;
		}

		public static function stripVar($value='') {
			return trim(self::clean(strip_tags(stripslashes($value))));
		}

		public static function abbreviateString($text, $limit, $threePoints = '...') {
			$totalCharacters = 0;
			//Retorna o texto em plain/text
			$text = self::stripVar($text);
			//Cria um array com todas as palavras do texto
			$vectorWords = explode(" ",$text);
			if(strlen($text) <= $limit):
				$threePoints = "";
			$newText = $text;
			else:
			//Começa a criar o novo texto resumido.
				$newText = "";
			//Acrescenta palavra por palavra na string enquanto ela
			//não exceder o tamanho máximo do resumo
			for($i = 0; $i <count($vectorWords); $i++):
				$totalCharacters += strlen(" ".$vectorWords[$i]);
			if($totalCharacters <= $limit)
				$newText .= ' ' . $vectorWords[$i];
			else break;
			endfor;
			endif;
			return $newText . $threePoints;
		}

		public static function clean($value)
		{
			if (!is_string($value))
				return $value;
			$value = str_replace("'", "&lsquo;", $value);
			// $value = str_replace(" delete ", "_delete_", $value);
			// $value = str_replace(" insert ", "_insert_", $value);
			// $value = str_replace(" update ", "_update_",$value);
			// $value = str_replace(" exec ", " _exec_",$value);
			// $value = str_replace(" create ", " _create_ ",$value);
			// $value = str_replace(" drop ", " _drop_ ",$value);
			// $value = str_replace(" use ", " _use_ ",$value);
			$value = trim($value);

			return $value;
		}

		public static function friendlyUrl($string, $separator = '-'){
			$string = (string)$string;
			$string = self::normalize($string);
			$string = trim($string);
			$string = Text::stripVar($string);
			$string = strtolower($string);
			$string = trim(preg_replace("/[^ A-Za-z0-9_]/", " ", $string));
			$string = str_replace(" ", $separator, $string);
			$string = str_replace("_", $separator, $string);
			$string = preg_replace("/[ -]+/", $separator, $string);
			return $string;
		}

		public static function reformatCSVString($strInput){

			// Replace accented characters
			$strInput = str_replace("\u00c0", "À", $strInput);
			$strInput = str_replace("\u00c1", "Á", $strInput);
			$strInput = str_replace("\u00c2", "Â", $strInput);
			$strInput = str_replace("\u00c3", "Ã", $strInput);
			$strInput = str_replace("\u00c4", "Ä", $strInput);
			$strInput = str_replace("\u00c5", "Å", $strInput);
			$strInput = str_replace("\u00c6", "Æ", $strInput);
			$strInput = str_replace("\u00c7", "Ç", $strInput);
			$strInput = str_replace("\u00c8", "È", $strInput);
			$strInput = str_replace("\u00c9", "É", $strInput);
			$strInput = str_replace("\u00ca", "Ê", $strInput);
			$strInput = str_replace("\u00cb", "Ë", $strInput);
			$strInput = str_replace("\u00cc", "Ì", $strInput);
			$strInput = str_replace("\u00cd", "Í", $strInput);
			$strInput = str_replace("\u00ce", "Î", $strInput);
			$strInput = str_replace("\u00cf", "Ï", $strInput);
			$strInput = str_replace("\u00d1", "Ñ", $strInput);
			$strInput = str_replace("\u00d2", "Ò", $strInput);
			$strInput = str_replace("\u00d3", "Ó", $strInput);
			$strInput = str_replace("\u00d4", "Ô", $strInput);
			$strInput = str_replace("\u00d5", "Õ", $strInput);
			$strInput = str_replace("\u00d6", "Ö", $strInput);
			$strInput = str_replace("\u00d8", "Ø", $strInput);
			$strInput = str_replace("\u00d9", "Ù", $strInput);
			$strInput = str_replace("\u00da", "Ú", $strInput);
			$strInput = str_replace("\u00db", "Û", $strInput);
			$strInput = str_replace("\u00dc", "Ü", $strInput);
			$strInput = str_replace("\u00dd", "Ý", $strInput);

			//Now lower case accents
			$strInput = str_replace("\u00df", "ß", $strInput);
			$strInput = str_replace("\u00e0", "à", $strInput);
			$strInput = str_replace("\u00e1", "á", $strInput);
			$strInput = str_replace("\u00e2", "â", $strInput);
			$strInput = str_replace("\u00e3", "ã", $strInput);
			$strInput = str_replace("\u00e4", "ä", $strInput);
			$strInput = str_replace("\u00e5", "å", $strInput);
			$strInput = str_replace("\u00e6", "æ", $strInput);
			$strInput = str_replace("\u00e7", "ç", $strInput);
			$strInput = str_replace("\u00e8", "è", $strInput);
			$strInput = str_replace("\u00e9", "é", $strInput);
			$strInput = str_replace("\u00ea", "ê", $strInput);
			$strInput = str_replace("\u00eb", "ë", $strInput);
			$strInput = str_replace("\u00ec", "ì", $strInput);
			$strInput = str_replace("\u00ed", "í", $strInput);
			$strInput = str_replace("\u00ee", "î", $strInput);
			$strInput = str_replace("\u00ef", "ï", $strInput);
			$strInput = str_replace("\u00f0", "ð", $strInput);
			$strInput = str_replace("\u00f1", "ñ", $strInput);
			$strInput = str_replace("\u00f2", "ò", $strInput);
			$strInput = str_replace("\u00f3", "ó", $strInput);
			$strInput = str_replace("\u00f4", "ô", $strInput);
			$strInput = str_replace("\u00f5", "õ", $strInput);
			$strInput = str_replace("\u00f6", "ö", $strInput);
			$strInput = str_replace("\u00f8", "ø", $strInput);
			$strInput = str_replace("\u00f9", "ù", $strInput);
			$strInput = str_replace("\u00fa", "ú", $strInput);
			$strInput = str_replace("\u00fb", "û", $strInput);
			$strInput = str_replace("\u00fc", "ü", $strInput);
			$strInput = str_replace("\u00fd", "ý", $strInput);
			$strInput = str_replace("\u00ff", "ÿ", $strInput);
			return $strInput;
		}

		public static function normalize($string){
			$chars = array(
					   'Š'=>'S', 'š'=>'s', 'Đ'=>'D', 'đ'=>'d', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
					   'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
					   'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
					   'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
					   'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
					   'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
					   'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
					   'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
			);
			// $nameToParse = preg_replace('/[^a-zA-Z0-9 ]/s', '', strtr($string, $chars));
			/**
			 * Alterado para aceitar "-" (hífens), em palavras como "comporte-se"
			 */
			$nameToParse = preg_replace('/[^a-zA-Z0-9- ]/s', '', strtr($string, $chars));
			$nameToParse = str_replace(" ", "_", $nameToParse);
			return $nameToParse;
		}

		public static function getOnlyNumber($string){
			preg_match_all('!\d+!', $string, $matches);
			return implode("",$matches[0]);
		}

		public static function mask($val, $mask)
		{
			$maskared = '';
			$k = 0;
			for($i = 0; $i<=strlen($mask)-1; $i++)
			{
				if($mask[$i] == '#')
				{
					if(isset($val[$k]))
						$maskared .= $val[$k++];
				}
				else
				{
					if(isset($mask[$i]))
						$maskared .= $mask[$i];
				}
			}
			return $maskared;
		}

		

	}