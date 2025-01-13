<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-5'))
{
    Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}

$tid = isset($_GET['tid']) ? $_GET['tid'] : "";
$bid = isset($_GET['bid']) ? $_GET['bid'] : "";
?>

<ol class="breadcrumb">
    <li><a href="?pag=<?php echo $pag; ?>&amp;act=tipo-list">Tipos de Banner</a></li>
    <li>
        <a href="?pag=<?php echo $pag; ?>&amp;tid=<?php echo $tid; ?>">
        <?php
        $listaTipo = $directIn->tipoListSelected($tid);
        if(isset($listaTipo) && $listaTipo !== false)
            foreach ($listaTipo as $listaTipoArr)
                echo $listaTipoArr['nome'];
        ?>
        </a>
    </li>
    <li>Estatísticas do banner</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de banner</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-add" <?php echo (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-4'))?"disabled='disabled'":""; ?> >Criar novo tipo</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add&tid=<?php echo $tid; ?>" <?php echo (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-3'))?"disabled='disabled'":""; ?> >Criar novo banner</a>
</div>

<hr />

    <?php
    $banner = $directIn->select("Select * From ".$directIn->getPrefix()."_banner Where banner_idx = " . $bid . "");

    $tipo_banner = $directIn->select("Select * From ".$directIn->getPrefix()."_banner_tipo Where tipo_idx = " . $tid . "");

    //Número de visualizações
    $visualizacao = $directIn->select("Select count(*) as NumReg From ".$directIn->getPrefix()."_banner_data Where banner_idx = " . $bid . " And tipo = 0");

    //Número de visualizações únicas
    $visualizacao_u = $directIn->select("Select COUNT(DISTINCT (REMOTE_ADDR)) as NumReg From ".$directIn->getPrefix()."_banner_data Where banner_idx=" . $bid . " And tipo = 0");

    //Número de cliques
    $clique = $directIn->select("Select count(*) as NumReg From ".$directIn->getPrefix()."_banner_data Where banner_idx=" . $bid . " And tipo = 1");
    //Número de cliques únicos
    $clique_u = $directIn->select("Select COUNT(DISTINCT (REMOTE_ADDR)) as NumReg From ".$directIn->getPrefix()."_banner_data Where banner_idx=" . $bid . " And tipo = 1");

    $referencia_clique_limite = " LIMIT 0,10 ";
    $referencia_visualizacao_limite = " LIMIT 0,10 ";
    $showAll = (isset($_GET['sa']))?$_GET['sa']:0;
    if($showAll==1){
        $referencia_clique_limite = "";
        $referencia_visualizacao_limite = "";
    }

    $referencia_clique = $directIn->select("Select HTTP_REFERER, count(HTTP_REFERER) as NumReg From ".$directIn->getPrefix()."_banner_data Where banner_idx=" . $bid . " And tipo = 1 Group By HTTP_REFERER HAVING Count(HTTP_REFERER)>0 ".$referencia_clique_limite);
    $referencia_visualizacao = $directIn->select("Select HTTP_REFERER, count(HTTP_REFERER) as NumReg From ".$directIn->getPrefix()."_banner_data Where banner_idx=" . $bid . " And tipo = 0 Group By HTTP_REFERER HAVING Count(HTTP_REFERER)>0 ".$referencia_visualizacao_limite);

    ?>

    <table class="table form" >
        <tr>
            <td class="first-child" style="font-size:16px;">Estatísticas para os acessos registrados ao banner</td>
            <td style="background:#fff;"></td>
        </tr>

        <tr><td class="td_spacer"></td></tr>
        <tr><td class="td_spacer"></td></tr>

        <tr>
            <td class="first-child">Nome do banner: <?php echo $banner[0]['nome']; ?></td>
        </tr>
        <tr>
            <td class="first-child">Tipo de banner: <?php echo $tipo_banner[0]['nome']; ?></td>
        </tr>
        <tr>
            <td class="first-child">Período determinado: <?php echo "de ".Date::fromMysql($banner[0]['data_publicacao'])." até ".Date::fromMysql($banner[0]['data_expiracao']); ?></td>
        </tr>
        <tr>
            <td class="first-child">Data da consulta: <?php echo date('d/m/Y'); ?></td>
        </tr>

        <tr><td class="td_spacer"></td></tr>
        <tr><td class="td_spacer"></td></tr>

        <tr>
          <td class="first-child">Total de impressões: <?php echo $visualizacao[0]['NumReg']; ?></td>
        </tr>

        <tr>
          <td class="first-child">Total de impressões únicas: <?php echo $visualizacao_u[0]['NumReg']; ?></td>
        </tr>

        <tr>
          <td class="first-child">Total de clicks: <?php echo $clique[0]['NumReg']; ?></td>
        </tr>

        <tr>
          <td class="first-child">Total de clicks únicos: <?php echo $clique_u[0]['NumReg']; ?></td>
        </tr>

        <tr><td class="td_spacer"></td></tr>
        <tr><td class="td_spacer"></td></tr>

        <tr>
          <td class="first-child">Endereços que originaram o acesso:</td>
        </tr>

        <tr>
          <td class="first-child">
              <?php
              foreach($referencia_clique as $r_c){
                echo "<a href='" . $r_c["HTTP_REFERER"] . "' target=_blank >" . $r_c["HTTP_REFERER"] . "</a><br>";
                echo "<img src='library/img/spacer.gif' height='10' style='background-color:#006600; width:" . (100/$clique[0]['NumReg'])*$r_c['NumReg']*3 . "px;' /> " . round((100/$clique[0]['NumReg'])*$r_c['NumReg']) . "%";
                echo "<hr size=1 style='color:#eeeeee' />";
              }
              ?>
          </td>
        </tr>


        <tr><td class="td_spacer"></td></tr>
        <tr><td class="td_spacer"></td></tr>

        <tr>
          <td class="first-child">Endereços onde foi impresso o banner:</td>
        </tr>
        <tr>
          <td class="first-child">
              <?php
                foreach($referencia_visualizacao as $r_v){
                    echo "<a href='" . $r_v["HTTP_REFERER"] . "' target=_blank >" . $r_v["HTTP_REFERER"] . "</a><br>";
                    echo "<img src='library/img/spacer.gif' height='10' style='background-color:#006600; width:" . (100/$visualizacao[0]['NumReg'])*$r_v['NumReg']*3 . "px;' /> " . round((100/$visualizacao[0]['NumReg'])*$r_v['NumReg']) . "%";
                    echo "<hr size=1 style='color:#eeeeee' />";
                }
              ?>
          </td>
        </tr>
        <tr>
            <td class="first-child">
                <hr />
                <?php if ($showAll==0): ?>
                    <input type="button" value="Exibir todos os registros" class="btn btn-primary" data-loading-text="Carregando..."  onclick="window.location.href='?pag=banner&act=stats&bid=<?php echo $bid; ?>&tid=<?php echo $tid; ?>&sa=1';" />
                <?php else: ?>
                    <input type="button" value="Exibir apenas os 10 mais" class="btn btn-primary" data-loading-text="Carregando..."  onclick="window.location.href='?pag=banner&act=stats&bid=<?php echo $bid; ?>&tid=<?php echo $tid; ?>';" />
                <?php endif ?>

            </td>
        </tr>

    </table>
