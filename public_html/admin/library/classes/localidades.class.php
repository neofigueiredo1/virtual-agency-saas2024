<?php 

/**
 * 
 */
class LocalidadesUtil
{
	
	public $addressFound;

	function __construct(){
		$this->addressFound = json_decode('{
								"cep": "",
								"endereco": "",
								"complemento": "",
								"bairro": "",
								"cidade": "",
								"uf": ""
							}');
	}

	public function getAddressByCEP($cep){

		$cep = str_replace(".","",$cep);
		$cep = str_replace("-","",$cep);

		try {

			$addressFounded = self::getCEPWithViacep($cep);	
			if ($addressFounded) {
				return $this->addressFound;
			}else{
				$addressFounded = self::getCEPWithWidenet($cep);
				//tenta outro canal
				if ($addressFounded) {
					return $this->addressFound;
				}
			}
			return false;

		} catch (Exception $e) {
			return false;
		}

	}

	private function getCEPWithViacep($cep){

		try {

			$urlCEP = 'http://viacep.com.br/ws/'.$cep.'/json/';
		    // var_dump($urlCEP);

			$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_URL, $urlCEP);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            curl_setopt($ch, CURLOPT_SSLVERSION,3); 

	            $dataRequest = curl_exec($ch);

	            curl_close($ch);
	    	
	    	// var_dump($dataRequest);
	    	// exit();

	    	$dataCEP = json_decode($dataRequest);
	    	if (!isset($dataCEP->erro)) { //Address Found
	    		//Tratamento para remover comentários dos correios nos logradouros
	    		$logradouro = trim( (explode("-",$dataCEP->logradouro))[0] );
	    		$this->addressFound->cep = $cep;
	    		$this->addressFound->endereco = $logradouro;
	    		$this->addressFound->complemento = $dataCEP->complemento;
	    		$this->addressFound->bairro = $dataCEP->bairro;
	    		$this->addressFound->cidade = $dataCEP->localidade;
	    		$this->addressFound->uf = $dataCEP->uf;
	    		return true;
	    	}else{
	    		return false;
	    	}

	    } catch (Exception $e) {
			throw $e;
	    }
	    
	}

	private function getCEPWithWidenet($cep){

		try {
		    
		    //$data = file_get_contents('http://apps.widenet.com.br/busca-cep/api/cep/'.$cep.'.str');
		    $urlCEP = 'https://apps.widenet.com.br/busca-cep/api/cep/'.$cep.'.json';
		    // var_dump($urlCEP);
		    $ch = curl_init($urlCEP);
	            curl_setopt($ch, CURLOPT_HEADER, 0);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	            $dataRequest = curl_exec($ch);

	            curl_close($ch);
	    	// var_dump($dataRequest);
	    	$dataCEP = json_decode($dataRequest);
	    	if ((boolean)$dataCEP->ok){ //Address Found
	    		$logradouro = trim( (explode("-",$dataCEP->address))[0] );
	    		$this->addressFound->cep = $cep;
	    		$this->addressFound->endereco = $logradouro;
	    		$this->addressFound->complemento = '';
	    		$this->addressFound->bairro = $dataCEP->district;
	    		$this->addressFound->cidade = $dataCEP->city;
	    		$this->addressFound->uf = $dataCEP->state;
	    		return true;
	    	}else{
	    		return false;
	    	}

	    } catch (Exception $e) {
			throw $e;
	    }
	    
	}

}

