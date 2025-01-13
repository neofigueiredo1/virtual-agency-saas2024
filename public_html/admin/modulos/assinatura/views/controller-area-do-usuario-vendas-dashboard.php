<?php
    if (!isset($_SESSION['plataforma_usuario'])) {
        ob_clean();
        header("Location: /login-cadastro");
        exit();
    }

    // if ((int)$_SESSION['plataforma_usuario']['perfil']!=1) {
    //     ob_clean();
    //     header("Location: /minha-conta");
    //     exit();
    // }

    global $ini_totais,$vendas,$periodo,$pagamentoStatusText,$cursoId,$cursoText,$msgSession,$cursosLista,$vencimentosFuturos;

    $ecomVendas = new EcommerceVendas();

    $ecomCursos = new EcommerceCurso();
    $cursosLista = $ecomCursos->getCursosProdutor(array('produtor'=>(int)$_SESSION['plataforma_usuario']['id']));
    
    // var_dump($subAccountData);
    // exit();
    
    //Valores padrão
    $pagamentoStatus = 0; //Todos
    $pagamentoStatusText = 'Todos';
    $cursoId = 0; //Todos
    $cursoText = 'Todos os cursos';
    $periodo = array('ini'=>date("Y-m-d 00:00:00",strtotime("-1 months")),'fim'=> date("Y-m-d 23:59:59"));

    if (!isset($_SESSION['filtros_ecommerce_vendas_s'])) {
        $_SESSION['filtros_ecommerce_vendas_s']=Array();
        $_SESSION['filtros_ecommerce_vendas_s']['periodo'] = $periodo;
        $_SESSION['filtros_ecommerce_vendas_s']['pagamento_status'] = $pagamentoStatus;
    } 

    $goFilter = (isset($_POST['goFilter']))?(int)$_POST['goFilter']:0;
    if ($goFilter==2) {

        $_SESSION['filtros_ecommerce_vendas_s']['curso_nome'] = "";
        $_SESSION['filtros_ecommerce_vendas_s']['curso'] = 0;
        $_SESSION['filtros_ecommerce_vendas_s']['periodo'] = $periodo;
        $_SESSION['filtros_ecommerce_vendas_s']['pagamento_status'] = -1;

        $status = (isset($_POST['status']))?(int)$_POST['status']:0;
        $curso = (isset($_POST['curso']))?(int)$_POST['curso']:0;
        $curso_nome = (isset($_POST['curso_nome']))? trim($_POST['curso_nome']) :'';
        $por_periodo = (isset($_POST['por_periodo']))?(int)$_POST['por_periodo']:0;
        $data_de = (isset($_POST['data_de']) && $_POST['data_de'] != "") ? Text::clean($_POST['data_de']) : "";
        $data_ate = (isset($_POST['data_ate']) && $_POST['data_ate'] != "") ? Text::clean($_POST['data_ate']) : "";

        if ($por_periodo==1) {
            if ($data_de != "" && $data_ate != "") {
                $_SESSION['filtros_ecommerce_vendas_s']['periodo']['ini'] = $data_de;
                $_SESSION['filtros_ecommerce_vendas_s']['periodo']['fim'] = $data_ate;
            }
        }
        if ((int)$status>0) {
            $_SESSION['filtros_ecommerce_vendas_s']['pagamento_status'] = $status;
        }
        if ((int)$curso>0) {
            $_SESSION['filtros_ecommerce_vendas_s']['curso'] = $curso;
            $_SESSION['filtros_ecommerce_vendas_s']['curso_nome'] = $curso_nome;
        }

        header("Location: /minha-conta/minhas-vendas");
        exit();

    }

    $cursoId = (isset($_SESSION['filtros_ecommerce_vendas_s']['curso']))?$_SESSION['filtros_ecommerce_vendas_s']['curso']:0;
    if (isset($_SESSION['filtros_ecommerce_vendas_s']['curso_nome'])) {
        if (trim($_SESSION['filtros_ecommerce_vendas_s']['curso_nome'])!="") {
            $cursoText = $_SESSION['filtros_ecommerce_vendas_s']['curso_nome'];
        }
    }
    
    $pagamento_status = (isset($_SESSION['filtros_ecommerce_vendas_s']['pagamento_status']))?$_SESSION['filtros_ecommerce_vendas_s']['pagamento_status']:0;
    switch ($pagamento_status){
        case 1:
            $pagamentoStatusText = 'Pendente';
            break;
        case 2:
            $pagamentoStatusText = 'Pago';
            break;
        case 3:
            $pagamentoStatusText = 'Cancelado';
            break;
        default:
            $pagamentoStatusText = 'Todos';
            break;
    }
    
    if (is_array($cursosLista)&&count($cursosLista)>0){
        $ini_totais = $ecomVendas->getVendasTotal($_SESSION['filtros_ecommerce_vendas_s']['periodo']);
        $vendas = $ecomVendas->getContagemPorPeriodo($_SESSION['filtros_ecommerce_vendas_s']['periodo'],'data_cadastro');

        $vencimentosFuturos = $ecomVendas->getVencimentosFuturos();
    }
    
    $msgSession = null;
    if(isset($_SESSION['plataforma_alerts']) && is_array($_SESSION['plataforma_alerts']))
    {
        $msgSession=$_SESSION['plataforma_alerts'];
        unset($_SESSION['plataforma_alerts']);
    }

?>