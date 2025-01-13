<?php
if (!isset($_SESSION['plataforma_usuario'])) {
    ob_clean();
    header("Location: /login-cadastro");
    exit();
}


global $m_cadastro;

$caction = isset($_POST['lpcaction'])?trim($_POST['lpcaction']):'';

// print_r($_POST);
// exit();

switch ($caction) {
    case 'lpInfoSave':
        
        try {
            
            $arrayData = Array(
                'lp_title' => isset($_POST['cc_lp_title'])?Text::clean($_POST['cc_lp_title']):'',
                'lp_descricao' => isset($_POST['cc_lp_descricao'])?Text::clean($_POST['cc_lp_descricao']):'',
                'lp_url' => isset($_POST['cc_lp_url'])?Text::friendlyUrl(Text::clean($_POST['cc_lp_url'])):''
            );

            if (trim($arrayData['lp_url'])=="") {
                $arrayData['lp_url'] = Text::friendlyUrl($usuarioConta[0]['nome_completo']);
            }

            //verifica se a URL ja está sendo usada
            if ($m_cadastro->LP_urlSlugExists($arrayData['lp_url'])) {
                die('<script>alert("A URL informada já está sendo usada.");history.back();</script>');
            }

            //Salva as informacoes da LP
            $m_cadastro->LP_infoSave($arrayData);
            echo '<script>alert("Informações salvas com sucesso.");</script>';

        } catch (Exception $e) {
            die('<script>alert("Não foi possível salvar as informações.");history.back();</script>');
        }

        break;
    
    case 'lpInfoHeaderSave':
        
        try {
            
            $arrayData = Array(
                'lp_header' => isset($_POST['cc_lp_header'])?Text::clean($_POST['cc_lp_header']):''
            );
            //Salva as informacoes da LP
            $m_cadastro->LP_infoHeaderSave($arrayData);
            echo '<script>alert("Informações salvas com sucesso.");</script>';

        } catch (Exception $e) {
            die('<script>alert("Não foi possível salvar as informações.");history.back();</script>');
        }

        break;

    case 'lpInfoFooterSave':
        
        try {
            
            $arrayData = Array(
                'lp_footer' => isset($_POST['cc_lp_footer'])?Text::clean($_POST['cc_lp_footer']):''
            );
            //Salva as informacoes da LP
            $m_cadastro->LP_infoFooterSave($arrayData);
            echo '<script>alert("Informações salvas com sucesso.");</script>';

        } catch (Exception $e) {
            die('<script>alert("Não foi possível salvar as informações.");history.back();</script>');
        }

        break;
}