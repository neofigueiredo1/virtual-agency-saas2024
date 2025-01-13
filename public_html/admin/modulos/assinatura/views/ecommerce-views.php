<?php

require_once("classes/ecommerce.carrinho.class.php");
require_once("classes/ecommerce.pagamento.class.php");
require_once("classes/ecommerce.pedido.class.php");
require_once("classes/ecommerce.cadastro.class.php");
require_once("classes/ecommerce.curso.class.php");
require_once("classes/ecommerce.cupom.class.php");

require_once("classes/ecommerce.xml.class.php");

require_once("classes/ecommerce.vendas.class.php");
// require_once("classes/ecommerce.cadastro.contas.repasse.class.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ecommerce_views extends HandleSql
{
	
	function __construct(){
		
	}

	/**
	 * Function que retorna um arquivo da pasta /views do módulo
	 * @param string $nome -> Nome da view a ser exibida
	 * @return void
	 */
	public function getView($nome="")
	{
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/modulos/ecommerce/views/' . $nome . '.php')){
			require($nome.'.php');
		}else{
			echo 'View não encontrada';
		}
	}

	/**
	 * Envio de e-mail para o e-commerce
	 * @param String $mailFrom - E-mail de onde a mensagem vai partir
	 * @param String $nameFrom - Nome do de quem está enviando a mensagem
	 * @param String $mailTo - E-mail para onde será enviado
	 * @param String $nameTo - Nome da pessoa que receberá a mensagem
	 * @param String $subject - Assunto da mensagem
	 * @param String $message - Corpo da mensagem
	 * @return boolean
	 */
	public function sendMailECommerce($mailFrom="", $nameFrom="", $mailTo="", $nameTo="", $subject, $message)
	{

		$CLI_MAIL_CONTATO = explode(",",trim(Sis::config("CLI_MAIL_CONTATO")));
        $fromEmail = trim($CLI_MAIL_CONTATO[0]);
        
		$mailFrom = ($mailFrom=="") ? $fromEmail : $mailFrom;
		$nameFrom = ($nameFrom=="") ? Sis::config("CLI_NOME") : $nameFrom;
		$mailTo = ($mailTo=="") ? Sis::config("CLI_MAIL_CONTATO") : $mailTo;
		$nameTo = ($nameTo=="") ? Sis::config("CLI_NOME") : $nameTo;
        
        if(class_exists("PHPMailer\PHPMailer\PHPMailer")){
			$mail = new PHPMailer;
			$mail->CharSet     = "UTF-8";
			$mail->ContentType = "text/html";

			$mail->IsSMTP();
			$mail->Host = Sis::config("CLI_SMTP_HOST");
			if(Sis::config("CLI_SMTP_PORTA")!="undefined"){ $mail->Port = Sis::config("CLI_SMTP_PORTA"); }
			if(Sis::config("CLI_SMTP_CONEXAO")){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

			if(Sis::config("CLI_SMTP_MAIL")!="")
			{
				$mail->SMTPAuth    = true;
				$mail->Username    = Sis::config("CLI_SMTP_MAIL");
				$mail->Password    = Sis::config("CLI_SMTP_PASS");
			}
			$mail->From        = $mailFrom;
			$mail->FromName    = $nameFrom;

			$mail->AddAddress(trim($mailTo),$nameTo);
			if (trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO"))!="") {
				$mail->AddBCC(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")));
			}

			$mail->AddReplyTo($mailTo, $nameTo);
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->Send();
		}
		
	}

} // End class
?>