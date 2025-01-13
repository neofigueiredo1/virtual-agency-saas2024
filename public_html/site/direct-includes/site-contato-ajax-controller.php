<?php

require_once("config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// $sac = isset($_POST['sac']) ? Text::clean($_POST['sac']) : 0;

$postact = isset($_POST['postact']) ? Text::clean($_POST['postact']) : '';

switch (trim($postact)) {
    case 'sendContato':
    
        $origem = isset($_POST['origem']) ? Text::clean($_POST['origem']) : "";
        if (trim($origem)=="") {
            ecit();
        }

        switch ($origem) {
            case 'hospedeSeuCurso': ///institucional - hospede seu curso conosco

                // Dados do formulário
                $nome     = isset($_POST['nome']) ? Text::clean(strip_tags($_POST['nome'])) : "Não informado";
                $email    = isset($_POST['email']) ? Text::clean(strip_tags($_POST['email'])) : "";
                $telefone = isset($_POST['telefone']) ? Text::clean(strip_tags($_POST['telefone'])) : "Não informado";
                $mensagem = isset($_POST['mensagem']) ? Text::clean(strip_tags($_POST['mensagem'])) : "Não informado";

                $toEmail = Sis::config("CLI_MAIL_CONTATO");
                $assunto = "Quero hospedar o meu curso.";

                $corpo_mensagem = "
                <table width='100%' cellpadding='0' cellspacing='10' border='0'>
                    <tr>
                        <td width='32%' valign='top'>
                            <b>Nome:</b> " . $nome . "<br>
                            <b>Email:</b> " . $email . "<br/>
                            <b>Telefone:</b> " . $telefone . "<br/>
                            <hr />
                            <b>Mensagem:</b><br />
                            " . $mensagem . "
                        </td>
                    </tr>
                </table>";

            break;

            case 'contato': ///institucional - fale Conosco
                    
                    // Dados do formulário
                    $assunto    = isset($_POST['assunto']) ? Text::clean(strip_tags($_POST['assunto'])) : "Dúvidas gerais";
                    $nome       = isset($_POST['nome']) ? Text::clean(strip_tags($_POST['nome'])) : "Não informado";
                    $nome_curso = isset($_POST['nome_curso']) ? Text::clean(strip_tags($_POST['nome_curso'])) : "Não informado";
                    $email      = isset($_POST['email']) ? Text::clean(strip_tags($_POST['email'])) : "";
                    $telefone   = isset($_POST['telefone']) ? Text::clean(strip_tags($_POST['telefone'])) : "Não informado";
                    $perfil     = isset($_POST['perfil']) ? Text::clean(strip_tags($_POST['perfil'])) : "Não informado";
                    
                    $mensagem = isset($_POST['mensagem']) ? Text::clean(strip_tags($_POST['mensagem'])) : "Não informado";

                    $toEmail = Sis::config("CLI_MAIL_CONTATO");
                    $assunto = "Contato pelo site.";

                    $corpo_mensagem = "
                    <table width='100%' cellpadding='0' cellspacing='10' border='0'>
                        <tr>
                            <td width='32%' valign='top'>
                                <b>Motivo:</b> " . $assunto . "<br>
                                <b>Curso:</b> " . $nome_curso . "<br>
                                <b>Perfil:</b> " . $perfil . "<br>
                                <b>Nome:</b> " . $nome . "<br>
                                <b>Email:</b> " . $email . "<br/>
                                <b>Telefone:</b> " . $telefone . "<br/>
                                <hr />
                                <b>Mensagem:</b><br />
                                " . $mensagem . "
                            </td>
                        </tr>
                    </table>";

                break;
            case 'entraremosEmContato': ///seja-um-produtor - entraremos em contato
                // Dados do formulário
                $nome     = isset($_POST['nome']) ? Text::clean(strip_tags($_POST['nome'])) : "Não informado";
                $telefone = isset($_POST['telefone']) ? Text::clean(strip_tags($_POST['telefone'])) : "Não informado";
                $email    = isset($_POST['email']) ? Text::clean(strip_tags($_POST['email'])) : "";
                
                $toEmail = Sis::config("CLI_MAIL_CONTATO");
                $assunto = "Entramos em contato";

                $corpo_mensagem = "
                <table width='100%' cellpadding='0' cellspacing='10' border='0'>
                    <tr>
                        <td width='32%' valign='top'>
                            <b>Nome:</b> " . $nome . "<br>
                            <b>Email:</b> " . $email . "<br/>
                            <b>Telefone:</b> " . $telefone . "<br/>
                        </td>
                    </tr>
                </table>";

            break;
        }

        $HTML_mensagem = Sis::returnMessageBodyClient($assunto);
        $HTML_mensagem = str_replace("[HTML_DADOS]",$corpo_mensagem,$HTML_mensagem) ;

        // ob_clean();
        // echo $HTML_mensagem;
        // exit();

        // ob_clean();
        // echo "ok";
        // exit();

        // var_dump(class_exists("PHPMailer"));
        // exit();

        if(class_exists("PHPMailer\PHPMailer\PHPMailer")){
            try {
                $mail = new PHPMailer;
                $mail->CharSet     = "UTF-8";
                $mail->ContentType = "text/html";

                $mail->isSMTP();
                $mail->isHTML(true);
                $mail->SMTPDebug = 2;

                $mail->Host = Sis::config("CLI_SMTP_HOST");
                if(Sis::config("CLI_SMTP_PORTA")!=""){ $mail->Port = Sis::config("CLI_SMTP_PORTA"); }
                if(Sis::config("CLI_SMTP_CONEXAO")!=""){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

                if(Sis::config("CLI_SMTP_MAIL")!="")
                {
                    $mail->SMTPAuth    = true;
                    $mail->Username    = Sis::config("CLI_SMTP_MAIL");
                    $mail->Password    = Sis::config("CLI_SMTP_PASS");
                }
                $mail->From        = Sis::config("CLI_SMTP_MAIL");
                $mail->FromName    = Sis::config("CLI_NOME");

                $cli_email = explode(",",$toEmail);
                
                for($xx=0;$xx<count($cli_email);$xx++)
                {
                    if($xx==0){
                        $mail->AddAddress(trim($cli_email[$xx]));
                    }else{
                        $mail->AddCC(trim($cli_email[$xx]));
                    }
                }
                $mail->Subject = $assunto;
                $mail->Body = $HTML_mensagem;
                
                // $mail->SMTPOptions = array (
                //     'ssl' => array (
                //         'verify_peer' => false,
                //         'verify_peer_name' => false,
                //         'allow_self_signed' => true
                //     )
                // );
                
                // var_dump($mail);

                if (!$mail->Send()) {
                    // echo 'Mailer Error: ' . $mail->ErrorInfo;
                    // exit();
                }else{
                    // ob_clean();
                    // echo "ok";
                    // exit();
                }

            } catch (phpmailerException $e) {
                // var_dump($mail);
                // echo $e->errorMessage(); //Pretty error messages from PHPMailer
            } catch (Exception $e) {
                // var_dump($mail);
                // echo $e->getMessage(); //Boring error messages from anything else!
            }

            ob_clean();
            echo "ok";
            exit();
        }

    break;
}

?>