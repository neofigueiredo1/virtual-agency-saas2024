<!DOCTYPE html>
<!--/*
 * DirectIn - Sistema de Adminsitraçao
 * Copyright (C) <?php echo date('Y'); ?> Being Serviços Ltda.
 * Para maiores informaçoes acesse: http://www.being.com.br/
 * ou envie um e-mai para: contato@being.com.br
 */-->
<!--[if lt IE 7]>      <html xmlns="http://www.w3.org/1999/xhtml" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html xmlns="http://www.w3.org/1999/xhtml" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html xmlns="http://www.w3.org/1999/xhtml" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html xmlns="http://www.w3.org/1999/xhtml" class="no-js"> <!--<![endif]-->

    <head>

        <title><?php echo $siteTitle; ?></title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />

        <link rel="canonical" href="<?php echo $pageCanonical; ?>">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1" >

        <meta name="description" content="<?php echo $pageDescription; ?>">
        <meta name="keywords" content="<?php echo $pagePalavraChave; ?>">
        <meta name="author" content="<?php echo Sis::config('CLI_NOME');?>">

        <meta name="format-detection" content="telephone=no" >

        <meta property="og:title" content="<?php echo $siteTitle; ?>" />
        <meta property="og:description" content="<?php echo $pageDescription; ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://<?php echo $pageCanonical; ?>/"/>
        <meta property="og:image" content="http://<?php echo $pageCanonical; ?>/site/images/clilogo.gif"/>
        <meta property="og:site_name" content="<?php echo $siteTitle; ?>"/>

        <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="/assets/images/favicon/site.webmanifest">

        <link rel="stylesheet" type="text/css" href="/assets/css/deps.min.css">
        <link rel="stylesheet" type="text/css" href="/assets/css/app.min.css">

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-T43G7EQGZM"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-T43G7EQGZM');
        </script>
        <!-- End Global site tag (gtag.js) - Google Analytics -->

        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        </script>
        <!-- End Facebook Pixel Code -->

        <!-- platform_event_tracker -->
        <?php if (isset($_SESSION["platform_event_tracker"])): ?>
            <script><?php echo $_SESSION["platform_event_tracker"]; ?></script>
        <?php
        unset($_SESSION["platform_event_tracker"]);
        endif ?>
        <!-- xx platform_event_tracker xx -->

    </head>
    <body class="<?php echo $bodyClass; ?> <?php echo Text::toAscii($pagina_data['titulo']); ?>" >
        <noscript>
            <?php if (isset($_SESSION["platform_event_tracker_facebook_pixel_id"])): ?>
                <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $_SESSION["platform_event_tracker_facebook_pixel_id"] ?>&ev=PageView&noscript=1" />
            <?php
            unset($_SESSION["platform_event_tracker_facebook_pixel_id"]);
        endif ?>
        </noscript>

    <?php
        // $accessTokenIsOk = $m_cadastro->checkAccessToken();
        // if (!$accessTokenIsOk){
        //     header("Location:/");
        //     exit();
        // }
    ?>
