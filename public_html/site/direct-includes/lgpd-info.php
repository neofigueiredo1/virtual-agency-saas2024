<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_POST['lgpdAccepted'])) {
    //Abre a sessão do LGPD
    $_SESSION['sessionLGPD'] = true;
    exit();
}
$sessionLGPD = (isset($_SESSION['sessionLGPD']))?true:false;
if (!$sessionLGPD):
?>
<!-- Inicio bloco lgpd -->

<style type="text/css">
    
    #sec-lgpd{
        position: fixed;
        max-width:100%;
        bottom: -10px;
        justify-content: center;
        height: 1px;
        overflow: hidden;
        transition: all 0.3s ease-in;
        -moz-transition: all 0.3s ease-in;
        -webkit-transition: all 0.3s ease-in;
        z-index: 999999999999999999999;
        left: 0px;
        right: 0px;

    }
    #sec-lgpd.sec-lgpd-visible{
        height: 140px;
        transition: all 0.3s ease-out;
        -moz-transition: all 0.3s ease-out;
        -webkit-transition: all 0.3s ease-out;
    }

    .sec-lgpd-container{
        background-color: #fff;
        padding: 15px;
        border-radius: 5px;
        width:100%;
        max-width:900px;
        display: flex;
        box-shadow: 0px 3px 6px 1px rgba(0,0,0,0.5);
        height: auto;
        position: absolute;
        bottom:-120px;
    }

    .sec-lgpd-visible .sec-lgpd-container{
        bottom:20px;
    }
    
    .sec-lgpd_button-box{
        height: 100%;
        display: flex;
        align-items: center;
    }

    .sec-lgpd_button-box,
    .sec-lgpd_text-box{
        padding: 10px;
    }

    .sec-lgpd_text{
        font-family: inherit;
        font-size:13px;
        color: #333333;
    }
    
    #sec-lgpd .sec-lgpd-container{
        transition: all 0.3s ease-in;
        -moz-transition: all 0.3s ease-in;
        -webkit-transition: all 0.3s ease-in;
    }
    
    #sec-lgpd.sec-lgpd-visible .sec-lgpd-container{
        transition: all 0.3s ease-out;
        -moz-transition: all 0.3s ease-out;
        -webkit-transition: all 0.3s ease-out;
    }

    .sec-lgpd-link{
        color: #000 !important;
        font-size: inherit;
        font-weight: 500;
    }

    .botao_lgpd{
        border-radius: 8px !important;
        margin: 0px 20px;
        padding: 10px 20px;
    }

    #boxSettings{
        height: 100%;
        width: 100%;
        /*position: absolute;*/
        position: fixed;
        right: 0px;
        top: 0px;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.2);
    }

    #boxTermos{
        position: absolute;
        height: 100%;
        max-width: 450px;
        width: 100%;
        background-color: #fff;
        /* left: auto; */
        /* top: 100px; */
        right: 0px;
        overflow-y: auto;
    }

    .label_check_item{
        position: relative;
        display: inline-block;
        width: 45px;
        height: 25px;
        margin-bottom: 0;
    }

    .item_check_cookies{
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #f2f1f1;
        border: 1px solid #ddd;
        transition: all .2s ease-in 0s;
        -moz-transition: all .2s ease-in 0s;
        -o-transition: all .2s ease-in 0s;
        -webkit-transition: all .2s ease-in 0s;
        border-radius: 20px;
    }

    .item_check_cookies.ativo{
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: transparent;
        border: 1px solid #660BB4;
    }

    .item_check_cookies::before{
        content: "";
        position: absolute;
        height: 21px;
        width: 21px;
        bottom: 1px;
        background-color: #7d7d7d;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 20px;
    }

    .item_check_cookies.ativo::before{
        background-color: #660BB4;
        border: 1px solid #660BB4;
        right: 0px;
        transition: all .2s ease-in 0s;
        -moz-transition: all .2s ease-in 0s;
        -o-transition: all .2s ease-in 0s;
        -webkit-transition: all .2s ease-in 0s;
    }

    @media screen and (max-width:767px){
        .sec-lgpd_text{ font-size:13px; }
        #sec-lgpd.sec-lgpd-visible{
            height: 160px;
        }
    }

    @media screen and (max-width:550px){
        .sec-lgpd_text{ font-size:13px; }
        #sec-lgpd.sec-lgpd-visible{
            height: 400px;
            bottom:5px;
        }
        .sec-lgpd-container{
            display: block;
            bottom:10px;
        }
        #sec-lgpd{
            left: 10px;
            right: 10px;
        }
        .sec-lgpd-container .btn{
            width:100%;
        }
        .sec-lgpd-visible
        .sec-lgpd-container{
            bottom:10px;
        }
    }
</style>

<script type="text/javascript" >
    let ele = null;
    window.addEventListener('load',function(){
        ele = document.querySelector("#sec-lgpd");
        ele.classList.add("sec-lgpd-visible");
        let secLgpdLink = document.querySelector(".sec-lgpd_accept-button");
            secLgpdLink.addEventListener('click',function(e){
                lgpdAccept();
                ele.classList.remove("sec-lgpd-visible");
            },false);
    });
    let lgpdAccept = function(){
        var ajax = new XMLHttpRequest();
            ajax.open("POST", "/site/direct-includes/lgpd-info.php", true);
            ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ajax.send("lgpdAccepted=true");
            ajax.onreadystatechange = function() {
                if (ajax.readyState == 4 && ajax.status == 200) {
                    var data = ajax.responseText;
                    console.log(data);
                }
            };
    };
</script>

<div id="sec-lgpd" class="sec-lgpd-animated" style="display:flex;">
    <div class="sec-lgpd-container">
        <div class="">
            <span class="sec-lgpd_text" >
                Usamos cookies em nosso site para fornecer a experiência mais relevante, lembrando suas preferências e visitas repetidas. Ao clicar em “Aceitar”, você concorda com o uso de TODOS os cookies de acordo com a nossa
                <a class="sec-lgpd-link" target="_blank" href="/politica-de-privacidade" > Pol&iacute;tica de Privacidade.</a>
                
            </span>
        </div>
        <form method="post" name="form_acept_all" class="sec-lgpd_button-box" >
            <button type="button" class="sec-lgpd_accept-button btn btn-azul botao_lgpd" > Aceitar </button>
        </form>
    </div>
</div>
<?php endif; ?>
<!-- Final bloco lgpd -->