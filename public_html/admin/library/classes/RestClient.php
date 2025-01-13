<?php
class RestClient
{
	private $http_client;
	private $method;
	private $url;
	private $headers = Array();
	private $parameters =  Array();
	private $curl;

	public function __construct($params = array()) 
	{
		$this->curl = curl_init();
		$this->headers = array(
			'Content-Type: application/json'
		);

		if(!$params["url"]) {
			throw new Exception("You must set the URL to make a request.");
		} else {
			$this->url = $params["url"];
		}

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 180);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0); //Skip SSL Verification
		
		if (isset($params["parameters"]["auth_name"])&&isset($params["parameters"]["auth_pass"])) {
			curl_setopt($this->curl, CURLOPT_USERPWD, $params["parameters"]["auth_name"].':'.$params["parameters"]["auth_pass"]);
		}

		if ($params["parameters"]) {
			$this->parameters = array_merge($this->parameters, $params["parameters"]);
		}

		if($params["method"]) {
			$this->method = $params["method"];
		}

		if(isset($params["headers"])) {
			$this->headers = array_merge($this->headers, $params["headers"]);
		}

		if ($this->method){
			switch($this->method) {
			case 'post':
			case 'POST':
				$stringParameters = json_encode($this->parameters, JSON_UNESCAPED_UNICODE);
				curl_setopt($this->curl, CURLOPT_POST, true);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $stringParameters);
				break;
			case 'get':
			case 'GET':
				$this->url .= '?' . http_build_query($this->parameters);
				break;
			case 'put':
			case 'PUT':
				$stringParameters = json_encode($this->parameters, JSON_UNESCAPED_UNICODE);
				// $stringParameters = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $stringParameters);
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $stringParameters);
				break;
			case 'delete':
			case 'DELETE':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($this->parameters, JSON_UNESCAPED_UNICODE));
				break;
			}
		}
		curl_setopt($this->curl, CURLOPT_URL, $this->url);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
	}

	public function run() 
	{
		$response = curl_exec($this->curl);
		$error = curl_error($this->curl);

		if ($error) {
			throw new Exception("error: ".$error);
		}

		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

		curl_close($this->curl);

		return $response;
	}

}