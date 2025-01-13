<?php
include_once('config.php');

//Padrão de retorno para as requisições.
$dados_retorno = json_decode('{"error":"0","message":"","resource":"null"}');

// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
// try{
//     NoCSRF::check('csrf_token',$_POST,true,60*10,false);
// }catch( Exception $e ){
//     var_dump($e->getMessage());
//     exit();
//     $dados_retorno->message = "Não foi possível completar sua solicitação, certifique-se de que a página carregou corretamente.";
//     if (ob_get_length()>0) ob_end_clean();
//     echo json_encode($dados_retorno);
//     exit();
// }

$ac = isset($_GET['ac']) ? Text::clean($_GET['ac']) : '' ;
$ac = isset($_POST['ac']) ? Text::clean($_POST['ac']) : $ac ;

switch($ac) { 
    /**
    * Salva uma nota relacionada a aula do curso.
    */
    case 'moduloNotaSave':
       
        $moduloId = isset($_POST['moduloId']) ? (int)$_POST['moduloId'] : 0 ;
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
        $notaId = isset($_POST['notaId']) ? (int)$_POST['notaId'] : 0 ;

        $nota = isset($_POST['nota']) ? Text::clean($_POST['nota']) : '' ;

        if ($moduloId!=0 && trim($nota)!='') {

            $cursoModuloNota = new CursoModuloNotas();

            try {
                
                $notaId = $cursoModuloNota->save($moduloId,$midiaId,$notaId,$nota);
                $dados_retorno->error = 0;
                $dados_retorno->message = "sucesso";
                $dados_retorno->resource = $notaId;

            } catch (Exception $e) {
                $dados_retorno->error = 1;
                $dados_retorno->message = $e->getMessage();
            }
        }

        break;

    /**
    * Carrega uma nota relacionada a aula do curso.
    */
    case 'moduloNotaLoad':
       
        $moduloId = isset($_POST['moduloId']) ? (int)$_POST['moduloId'] : 0 ;
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
       
        $notaData = json_decode('{"id":"0","nota":""}');

        if ($moduloId!=0 && $midiaId!=0) {

            $cursoModuloNota = new CursoModuloNotas();

            try {

                $notaReg = $cursoModuloNota->all(array('modulo'=>$moduloId,'midia'=>$midiaId));
                if (is_array($notaReg) && count($notaReg)>0) {
                    $notaData->id = $notaReg[0]['nota_idx'];
                    $notaData->nota = $notaReg[0]['nota'];
                }
                $dados_retorno->error = 0;
                $dados_retorno->message = "sucesso";
                $dados_retorno->resource = $notaData;

            } catch (Exception $e) {
                $dados_retorno->error = 1;
                $dados_retorno->message = $e->getMessage();
            }
        }

        break;

    /**
    * Registra a avaliacao do aluno para uma aula.
    */
    case 'moduloAvaliaSave':
        
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
        $nota = isset($_POST['nota']) ? (int)$_POST['nota'] : 0 ;
        $comentario = isset($_POST['comentario']) ? Text::clean($_POST['comentario']) : '' ;

        if ((int)$nota==0) {
            $dados_retorno->error = 1;
            $dados_retorno->message = "Informe sua nota para a aula.";
        }
       
        if ($midiaId!=0 && $dados_retorno->error==0) {

            $cursoModulo = new CursoModulo();

            try {
                $notaReg = $cursoModulo->midiaSetStars($midiaId,$nota,$comentario);
                $dados_retorno->error = 0;
                $dados_retorno->message = "sucesso";
            } catch (Exception $e) {
                $dados_retorno->error = 1;
                $dados_retorno->message = $e->getMessage();
            }
        }

        break;

    /**
    * Registra a avaliacao do aluno para uma aula.
    */
    case 'moduloPlayerPlay':
        
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
        
        if ($midiaId!=0){

            $cursoModulo = new CursoModulo();
            //Valida se a midia de video aula chegou ao seu limite de 3 visualizações.

            $limitReach = $cursoModulo->mudiaLogReachLimit($midiaId);
            if ($limitReach) { //Chegou ao limite
                $dados_retorno->error = 1;
                $dados_retorno->message = "limite_reach";
            }else{ //Não chegou ao limite
                //Registra o log de inicio do video.
                $cursoModulo->mudiaLogAdd($midiaId,'video_aula');
                $dados_retorno->message = "video_stated";
            }

        }

        break;

    /**
    * Registra a avaliacao do aluno para uma aula.
    */
    case 'moduloPlayerEnded':
        
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
        
        if ($midiaId!=0) {
            //Registra o log de inicio do video.
            $cursoModulo = new CursoModulo();
            $checkStartForEnd = true; //Atualiza a data de fim em um log já iniciado.
            $cursoModulo->mudiaLogAdd($midiaId,'video_aula',$checkStartForEnd);
            $dados_retorno->message = "video_ended";
        }

        break;


    /**
    * Obtem a lista de aulas para montar o cronograma do Aluno.
    */
    case 'cronAulasList':
        
        $moduloId = isset($_POST['moduloId']) ? (int)$_POST['moduloId'] : false ;
        $moduloId = ((int)$moduloId<=0)?false:$moduloId;

        $cursoId = isset($_POST['cursoId']) ? (int)$_POST['cursoId'] : false ;
        $cursoId = ((int)$cursoId<=0)?false:$cursoId;

        $cursoModulo = new CursoModulo();
        $mod_aulas = $cursoModulo->moduloMidias($cursoId,$moduloId,1);

        if (is_array($mod_aulas)&&count($mod_aulas)>0) {
            $aulas_data="";
            foreach ($mod_aulas as $key => $aula) {

                $duracao_aula = (int)$aula['video_duracao'];
                $duracao_aula_str = $duracao_aula." minutos";
                if ($duracao_aula<=0) {
                    $duracao_aula_str = " Não definido";
                }

                $aulas_data .= '
                    <div id="item_aula_'.$aula['midia_idx'].'" class="item-aula"
                        data-midia-id="'.$aula['midia_idx'].'"
                        data-midia-duracao="'.(int)$aula['video_duracao'].'"
                    >
                        <a href="javascript:;" class="btn_add btn btn-sm fs-12 btn-azul-1 float-right w-auto rounded-10 py-1 h-auto" onclick="javascript:oAppCron.aulaAdd('.$aula['midia_idx'].');" >incluir</a>
                        <a href="javascript:;" class="btn_del btn btn-sm fs-12 btn-danger float-right w-auto rounded-10 py-1 h-auto" onclick="javascript:oAppCron.aulaDel('.$aula['midia_idx'].');" >remover</a>

                        <strong>'.$aula['nome'].'</strong><br>
                        <small>
                            Tempo: '.$duracao_aula_str.'<br/>
                            Curso: '.$aula['curso_nome'].'<br/>
                            Módulo: '.$aula['modulo_nome'].'<br/>
                        </small>
                        <input type="hidden" name="midiaid[]" value="'.$aula['midia_idx'].'" />

                    </div>
                ';


            }
            
            $dados_retorno->message = "sucesso";
            $dados_retorno->resource = $aulas_data;

        }else{
            $dados_retorno->error = 1;
            $dados_retorno->message = "Sem aulas para a lista solicitada.";
        }
        

        break;


    /**
    * Obtem a lista de aulas para montar o cronograma do Aluno.
    */
    case 'cronPreview':

        $data_ini = (isset($_POST['data_ini'])) ? $_POST['data_ini'] : '';
        $data_fim = (isset($_POST['data_fim'])) ? $_POST['data_fim'] : '';

        $midiasIds = (isset($_POST['midiasIds'])) ? $_POST['midiasIds'] : '';

        $dia_0_tempo = (isset($_POST['dia_0_tempo'])) ? (int)$_POST['dia_0_tempo'] : 0;
        $dia_1_tempo = (isset($_POST['dia_1_tempo'])) ? (int)$_POST['dia_1_tempo'] : 0;
        $dia_2_tempo = (isset($_POST['dia_2_tempo'])) ? (int)$_POST['dia_2_tempo'] : 0;
        $dia_3_tempo = (isset($_POST['dia_3_tempo'])) ? (int)$_POST['dia_3_tempo'] : 0;
        $dia_4_tempo = (isset($_POST['dia_4_tempo'])) ? (int)$_POST['dia_4_tempo'] : 0;
        $dia_5_tempo = (isset($_POST['dia_5_tempo'])) ? (int)$_POST['dia_5_tempo'] : 0;
        $dia_6_tempo = (isset($_POST['dia_6_tempo'])) ? (int)$_POST['dia_6_tempo'] : 0;

        $weekTotalTime = $dia_0_tempo + $dia_1_tempo + $dia_2_tempo + $dia_3_tempo + $dia_4_tempo + $dia_5_tempo + $dia_6_tempo;

        $_midiasIds = str_replace("!!",",",$midiasIds);
        $_midiasIds = str_replace("!","",$_midiasIds);

        //Valida os dados enviados.
        if ($_midiasIds=="") {
            $dados_retorno->error = 1;
            $dados_retorno->message = "Nenhum aula selecionada.";
        }elseif (!Date::isDate($data_ini) || !Date::isDate($data_fim)) {
            $dados_retorno->error = 1;
            $dados_retorno->message = "A data informada não é válida.";
        }elseif ($weekTotalTime==0) {
            $dados_retorno->error = 1;
            $dados_retorno->message = "O tempo nos dias da semana dentro do período informado é inferior ao tempo necessário para assistir as aulas selecionadas.";
        }

        $arrAulasCronograma = array();
        $arr_midiasIds = explode(",",$_midiasIds);
        foreach ($arr_midiasIds as $key => $cronAula) {
            
            $aulaData = array(
                'order'=>$key,
                'aula_idx'=>$cronAula,
                'nome'=>'',
                'modulo_nome'=>'',
                'aula_data'=>null,
                'aula_tempo'=>''
            );
            $arrAulasCronograma['aula_'.$cronAula] = $aulaData;
        }

        $minutos_total = 0;

        $cursoModulo = new CursoModulo();
        $aulasSelecionadas = $cursoModulo->midiaGetMidia($_midiasIds);
        if (is_array($aulasSelecionadas)&&count($aulasSelecionadas)>0) {
            foreach ($aulasSelecionadas as $key => $aulaSelecionada) {
                $arrAulasCronograma['aula_'.$aulaSelecionada['midia_idx']]['aula_tempo'] = $aulaSelecionada['video_duracao'];
                $arrAulasCronograma['aula_'.$aulaSelecionada['midia_idx']]['nome'] = $aulaSelecionada['nome'];
                $arrAulasCronograma['aula_'.$aulaSelecionada['midia_idx']]['modulo_nome'] = $aulaSelecionada['modulo_nome'];
                $arrAulasCronograma['aula_'.$aulaSelecionada['midia_idx']]['curso_nome'] = $aulaSelecionada['curso_nome'];

                $minutos_total += $aulaSelecionada['video_duracao'];
            }
        }

        // var_dump($arrAulasCronograma);


        $hours = ($minutos_total / 60);
        $rhours = (float)$hours;
        $minutes = ($hours - $rhours) * 60;
        $rminutes = (int)$minutes;
        $rhours = ($rminutes>0)?$rhours+1:$rhours;


        // //Tempo em horas destinadas ao período de estudo.
        $weekDaysTimes = array(0,0,0,0,0,0,0);
        $weekDaysTotalTime = 0;

        $dtIni = new DateTime($data_ini);
        $dtFim = new DateTime($data_fim);

        // var_dump($dtIni);
        // var_dump($dtFim);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($dtIni, $interval, $dtFim);

        foreach ($period as $dt){

            //obtem o tempo reservado de estudo para o dia da semana nessa data.
            $varDay = "dia_".$dt->format("w")."_tempo";
            $tempo_do_dia_minutos = $$varDay * 60;
            $tempo_do_dia_minutos_usado = 0;


            foreach ($arrAulasCronograma as $key => $aulaCronograma) {
            
                //Associa o dia a aula de acordo com o disponivel no dia habilitado.
                if ($tempo_do_dia_minutos_usado<=$tempo_do_dia_minutos) {
                    //Ainda tem tempo livre no dia.
                    
                    if ($aulaCronograma['aula_data']==null) { //caso a aula ainda não esteja associada a um dos dias.
                        
                        if ( (($tempo_do_dia_minutos-$tempo_do_dia_minutos_usado)>0)
                            &&
                            $aulaCronograma['aula_tempo'] <= (($tempo_do_dia_minutos-$tempo_do_dia_minutos_usado)+($aulaCronograma['aula_tempo']/2)))
                        {
                            $arrAulasCronograma[$key]['aula_data'] = $dt->format("Y-m-d");
                            $tempo_do_dia_minutos_usado += (int)$aulaCronograma['aula_tempo'];
                        }else{
                            //Encerra o laco das aulas e segue para o dia seguinte.
                            break;
                        }
                        
                    }

                }

            }
            reset($arrAulasCronograma);

            // echo $dt->format("l Y-m-d H:i:s\n");
        }

        $resourceOUTPut = "";
        $MesAtual="";
        $AnoAtual="";
        $AnoDia="";
        
        $SemanaCounter=1;
        $aulaCounter=0;

        $currentDate='';
        $currentSemanaIni='';
        $currentSemanaFim='';

        $resourceOUTPut .= "<div class='periodo-grupo' >";

        foreach ($arrAulasCronograma as $key => $aulaSelecionada) {
            
            $diaSemanaHoje = date("w",strtotime($aulaSelecionada['aula_data']));

            $diaSemanaIni = ($diaSemanaHoje==0)?$aulaSelecionada['aula_data']: ( date("Y-m-d",strtotime($aulaSelecionada['aula_data'] . ' -'.$diaSemanaHoje.' day')) ); 
            $diaSemanaFim = date("Y-m-d",strtotime($diaSemanaIni . ' +6 day') );

            if ($diaSemanaIni!=$currentSemanaIni && $diaSemanaFim!=$currentSemanaFim){
                
                $currentSemanaIni = $diaSemanaIni;
                $currentSemanaFim = $diaSemanaFim;

                if($aulaCounter>0){
                    $resourceOUTPut .= "</div></div>
                        </div><div class='periodo-grupo' >";
                    $currentDate='';
                }

                $resourceOUTPut .= '
                    <h3>Semana '.$SemanaCounter.' <div class="d-block d-md-inline-block" > - De '. date('d/m/Y',strtotime($currentSemanaIni)) .' até '. date('d/m/Y',strtotime($currentSemanaFim)) .'</div> </h3>
                    <div class="title_header d-md-flex d-none" >
                        <div class="cron_col_1" >Dia da semana</div>
                        <div class="cron_col_2" >Aula</div>
                    </div>
                ';
                $SemanaCounter++;
            }

            if ($currentDate != $aulaSelecionada['aula_data']) {
                if ($currentDate=='') {//Inicio
                    $resourceOUTPut .= '
                        <div class="cron_aula_dia_semana d-md-flex" >
                            <div class="cron_col_1" ><div class="dia_semana" ><div class="d-inline-block d-md-block mr-3 mr-md-0 " >'.Date::getWeekDay($aulaSelecionada['aula_data']).'</div>'.date('d/m/Y',strtotime($aulaSelecionada['aula_data'])).'</div></div>
                            <div class="cron_col_2" >
                    ';
                }else{
                    $resourceOUTPut .= '
                             </div>
                        </div>
                        <div class="cron_aula_dia_semana d-md-flex" >
                            <div class="cron_col_1" ><div class="dia_semana" ><div class="d-inline-block d-md-block mr-3 mr-md-0 " >'.Date::getWeekDay($aulaSelecionada['aula_data']).'</div>'.date('d/m/Y',strtotime($aulaSelecionada['aula_data'])).'</div></div>
                            <div class="cron_col_2" >     
                    ';
                }
                $currentDate=$aulaSelecionada['aula_data'];
            }

            $resourceOUTPut .= '
                <div class="cron_aula_item" >
                    <b>#'.($aulaSelecionada['order']+1).' - '.$aulaSelecionada['nome'].'</b><br/>
                    <small>
                        Duração: '.$aulaSelecionada['aula_tempo'].' minutos<br/>
                        Módulo: '.$aulaSelecionada['modulo_nome'].'<br/>
                        Curso: '.$aulaSelecionada['curso_nome'].'
                    </small>
                </div>
            ';

            if ($aulaCounter == (count($arrAulasCronograma)-1) ) {
                $resourceOUTPut .= '
                     </div>
                </div>
                ';
            }

            $aulaCounter++;

        }

        $resourceOUTPut .= "</div>";

        $_SESSION['cronograma_aulas_selecionadas'] = serialize($arrAulasCronograma);

        // echo($resourceOUTPut);

        $dados_retorno->error = 0;
        $dados_retorno->message = "success";
        $dados_retorno->resource = $resourceOUTPut;

    break;

    /**
    * Cria o cronograma do Aluno.
    */
    case 'cronCreate':

        try {

            $arrAulasCronograma = unserialize($_SESSION['cronograma_aulas_selecionadas']);
            $cursoCron = new CursoCronograma();
            $cursoCron->saveCronograma($arrAulasCronograma);

            $dados_retorno->message = "success";
            
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }

    break;

    /**
    * Cria o cronograma do Aluno.
    */
    case 'cronDelete':

        try {

            $cursoCron = new CursoCronograma();
            $cursoCron->removeCronograma();
            $dados_retorno->message = "success";
            
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }

    break;

    /**
    * Envia nova mensagem do aluno ao produtor.
    */
    case 'moduloComentariosLoad':

        global $cursoId,$midiaId;
        $cursoId = isset($_POST['cursoId']) ? (int)$_POST['cursoId'] : 0 ;
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
        echo $m_curso->getView("view-curso-mensagens-aluno");
        exit();

    break;


    /**
    * Envia nova mensagem do aluno ao produtor.
    */
    case 'cursoMensSend':

        $cursoId = isset($_POST['cursoId']) ? (int)$_POST['cursoId'] : 0 ;
        $midiaId = isset($_POST['midiaId']) ? (int)$_POST['midiaId'] : 0 ;
        $messageId = isset($_POST['messageId']) ? (int)$_POST['messageId'] : 0 ;
        $message = isset($_POST['message']) ? Text::clean($_POST['message']) : '' ;

        try {

            $cursoMens = new CursoMensagens();

            $requestData = Array(
                'curso_idx' => $cursoId,
                'midia_idx' => $midiaId,
                'm_reply_to' => $messageId,
                'message' => $message
            );
            $cursoMens->m_insert($requestData);

            $dados_retorno->message = "success";

        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }

    break;


    /**
    * Envia nova mensagem do aluno ao produtor.
    */
    case 'cursoMensRead':

        $messageId = isset($_POST['messageId']) ? (int)$_POST['messageId'] : 0 ;
        try {
            $cursoMens = new CursoMensagens();
            $cursoMens->setReadMessage($messageId);
            $dados_retorno->message = "success";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }

    break;
    
}

if (ob_get_length()>0) ob_end_clean();
echo json_encode($dados_retorno);
exit();

?>