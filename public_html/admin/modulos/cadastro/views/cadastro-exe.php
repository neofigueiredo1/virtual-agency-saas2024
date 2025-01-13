<?php
	// include "../../config.php";
	$exe = (isset($_POST['exe'])) ? (int)$_POST['exe'] : 0;
	$cep = (isset($_POST['cep'])) ? $_POST['cep'] : 0;

	if($exe==1){
		//$data = file_get_contents('http://apps.widenet.com.br/busca-cep/api/cep/'.$cep.'.str');
        $ch = curl_init('http://apps.widenet.com.br/busca-cep/api/cep/'.$cep.'.str');
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
        
        
		// var_dump($data);
		if($data !== false){
			$stringArr = explode("&", $data);

			if(count($stringArr)>4){
				for ($i=1; $i < count($stringArr); $i++) {
					$stringArr2[] = explode("=", $stringArr[$i]);
				}
				$rua = ($stringArr2[0][0]=="address")?$stringArr2[0][1]:$stringArr2[4][1];
				$rua = explode("+-+", $rua);
				$rua = urldecode(str_replace("+", " ", $rua[0]));

				$estado = ($stringArr2[1][0]=="state")?$stringArr2[1][1]:$stringArr2[3][1];
				$estado = urldecode(str_replace("+", " ", $estado));

				$cidade = urldecode(str_replace("+", " ", $stringArr2[2][1]));

				$bairro = ($stringArr2[3][0]=="district")?$stringArr2[3][1]:$stringArr2[1][1];
				$bairro = urldecode(str_replace("+", " ", $bairro));
				echo $rua."&".$bairro."&".$cidade."&".$estado;
			}else{
				echo "erro";
			}

		}else{
			echo "erro";
		}
		die();
	}
?>