<?php
class HandleSql{

    public $content = false;
    public $conn;
    public $DB_PREFIX;
    
    function __construct(){
        $this->DB_PREFIX = Connect::getPrefix();
        $this->conn = Connect::getInstance();
    }

    public function getPrefix(){
        return $this->DB_PREFIX;
    }

    public function select($query,$class=''){
        try{
            if (is_null($this->conn)) {
                $this->conn = Connect::getInstance();
            }
            $exec = $this->conn->query($query);
            if(!$exec){
                $_erro = $this->conn->errorInfo();
                throw new connException("DBQueryFail: QUERY[".$query."] ERROR[".$_erro[0]." - ".$_erro[1]." - ".$_erro[2].']');
            }
            if ($class!="") {
                $result = $exec->fetchAll(PDO::FETCH_CLASS, $class);
            }else{
                $result = $exec->fetchAll(PDO::FETCH_ASSOC);
            }
            return $result;
        }catch (PDOException $e){
            throw $e;
        }
    }

    public function selectPage($qry,$registrosPorPagina=0,$paginaAtual=0)
    {
        $totalPaginas   = 1;
        $totalRegistros = 0;
        if($paginaAtual<1){ $paginaAtual=1; }
        $startRange = ($registrosPorPagina * $paginaAtual) - $registrosPorPagina;
        if($startRange<0){ $startRange=0; }
        $LimitRange = " Limit ".$startRange.", ".$registrosPorPagina;
        //Ajusta a query para obter o número total de linhas
        $ireplaceCount = 1;
        $qry = preg_replace("/SELECT/","Select SQL_CALC_FOUND_ROWS ",$qry,$ireplaceCount).$LimitRange;

        $result = false;
        if (is_null($this->conn)) {
            $this->conn = Connect::getInstance();
        }
        try{
            $exec = $this->conn->query($qry);
            if(!$exec){
                $_erro = $this->conn->errorInfo();
                throw new connException("DBQueryFail: QUERY[".$qry."] ERROR[".$_erro[0]." - ".$_erro[1]." - ".$_erro[2].']');
            }
            $execTotal = $this->conn->query("SELECT FOUND_ROWS()");
            $resultTotal = $execTotal->fetchAll(PDO::FETCH_ASSOC);
            $totalRegistros = $resultTotal[0]['FOUND_ROWS()'];
        }catch (Exception $e){
            die("Falha: " .$e->getMessage());
        }

        /*Implementa a paginacao*/
        $totalPaginas   = ceil($totalRegistros / $registrosPorPagina);
        if($paginaAtual>$totalPaginas){ $paginaAtual=$totalPaginas; }

        // try{
        //     $exec = $this->conn->query($qry);
        // }catch (Exception $e){
        //     die($e->getMessage());
        // }

        $result = $exec->fetchAll(PDO::FETCH_ASSOC);
        return (object) array("resultado"=>$result,"totalRegistros"=>$totalRegistros,"totalPaginas"=>$totalPaginas,"paginaAtual"=>$paginaAtual);
    }

    public function insert($query){
        if (is_null($this->conn)) {
            $this->conn = Connect::getInstance();
        }
        try{
            $exec = $this->conn->query($query);
            if(!$exec){
                $_erro = $this->conn->errorInfo();
                throw new connException("DBQueryFail: QUERY[".$query."] ERROR[".$_erro[0]." - ".$_erro[1]." - ".$_erro[2].']');
            }
            return $this->conn->lastInsertId();
        }catch (Exception $e){
            throw $e;
        }
        return $this->conn->lastInsertId();
    }

    public function update($qry){
        if (is_null($this->conn)) {
            $this->conn = Connect::getInstance();
        }
        try{
            $exec = $this->conn->query($qry);
            if(!$exec){
                $_erro = $this->conn->errorInfo();
                throw new connException("DBQueryFail: QUERY[".$qry."] ERROR[".$_erro[0]." - ".$_erro[1]." - ".$_erro[2].']');
            }
        }catch (Exception $e){
            die($e->getMessage());
        }
        return true;
    }

    public function delete($qry){
        if (is_null($this->conn)) {
            $this->conn = Connect::getInstance();
        }
        try{
            $this->conn->query($qry);
        }catch (Exception $e){
            die($e->getMessage());
        }
        return true;
    }

    public function sqlCRUD($matrix,$matrixSelectFields,$table,$logMessage,$performAction,$page=0,$pageNumRows)
    {
        $sqlQuery="";
        $acao="";
        $reg_codigo=0;
        $reg_nome="";
        $retorno;
        switch($performAction){
            case "S" : //Seleciona
                $sqlSelectFields= (trim($matrixSelectFields)!="") ? $matrixSelectFields : " * " ;
                $sqlOrderBy = "";
                $sqlWhere = "";
                if(is_array($matrix) && count($matrix)>0){
                    $sqlWhere = (count($matrix)==1 && array_key_exists("orderby", $matrix) ) ? "" : " Where " ;
                    $nCount = 0;
                    foreach($matrix as $mIndex)
                    {
                        $and = ($nCount>0) ? " And " : "";
                        $field = key($matrix);
                        $value = $mIndex;
                        if($field=="orderby")
                        {
                            $sqlOrderBy = $mIndex;
                        }else{
                            if(is_numeric($value)){
                                $typeCompares = " = ";
                            }else{
                                $value = "'%" . $mIndex . "'";
                                $typeCompares = " Like ";
                            }
                            $sqlWhere .= $and." ".$field.$typeCompares.$value;
                            $nCount++;
                        }
                        next($matrix);
                    }

                }
                $sqlQuery = "Select ".$sqlSelectFields." From ".$table.$sqlWhere." ".$sqlOrderBy;
                if($page==0)
                {
                    $retorno = self::select($sqlQuery);
                }else{
                    $retorno = self::selectPage($sqlQuery,$pageNumRows,$page);
                }
                break;
            case "I" ://Inserir
                if(is_array($matrix) && count($matrix)>0)
                {
                    $nCount = 0;
                    $tableFields = "";
                    $tableFieldsData = "";
                    foreach($matrix as $mIndex)
                    {
                        $delimiter = ($nCount>0) ? "," : "";
                        $field = key($matrix);

                        $value = "'" . $mIndex . "'";
                        $tableFields .= $delimiter.$field;
                        $tableFieldsData .= $delimiter.$value;
                        next($matrix);
                        $nCount++;
                    }
                }
                $sqlQuery = "INSERT INTO ".$table."(".$tableFields.") values(".$tableFieldsData.") ";
                $retorno = self::insert($sqlQuery);
                $reg_codigo=$retorno;
                $acao="INSERT";
                break;
            case "U" : //Edita
                $WhereUpdate = "";
                if(is_array($matrix) && count($matrix)>0)
                {
                    $nCount = 0;
                    $tableFieldsDataEdit = "";
                    foreach($matrix as $mIndex)
                    {
                        $field = key($matrix);
                        $value = "'" . $mIndex . "'";
                        if($nCount==0){
                            $WhereUpdate = " Where ".$field."=".$value;
                        }else{
                            $delimiter = ($nCount>1) ? "," : "";
                            $tableFieldsDataEdit .= $delimiter.$field."=".$value;
                        }
                        next($matrix);
                        $nCount++;
                    }
                }
                $sqlQuery = "UPDATE ".$table." SET ".$tableFieldsDataEdit." ".$WhereUpdate;
                $retorno = self::update($sqlQuery);
                $acao="UPDATE";
                break;
            case "D" : //Delete
                $sqlWhere = "";
                if(is_array($matrix) && count($matrix)>0){
                    $nCount = 0;
                    $sqlWhere = " Where ";
                    foreach($matrix as $mIndex)
                    {
                        $and = ($nCount>0) ? " And " : "";
                        $field = key($matrix);
                        $value = $mIndex;

                        if(is_numeric($value)){
                            $typeCompares = " = ";
                        }else{
                            $value = "'" . $mIndex . "'";
                            $typeCompares = " Like ";
                        }
                        $sqlWhere .= $and." ".$field.$typeCompares.$value;
                        next($matrix);
                        $nCount++;
                    }
                }
                if (trim($sqlWhere)=="") {
                    throw new Exception("Não é possível executar o comando sem a cláusula WHERE", 1);
                }
                $sqlQuery = "DELETE FROM ".$table.$sqlWhere;
                $retorno = self::delete($sqlQuery);
                $acao="DELETE";
                break;
        }
        /*Grava o log desta ação*/
        if(is_array($logMessage))
        {
            $_log_modulo_codigo=((array_key_exists('modulo_codigo', $logMessage)))?$logMessage['modulo_codigo']:0;
            $_log_modulo_area=((array_key_exists('modulo_area', $logMessage)))?$logMessage['modulo_area']:"";
            $_log_reg_codigo=((array_key_exists('reg_codigo', $logMessage)))?$logMessage['reg_codigo']:$reg_codigo;
            $_log_reg_nome=((array_key_exists('reg_nome', $logMessage)))?$logMessage['reg_nome']:$reg_nome;
            $_log_acao=((array_key_exists('acao', $logMessage)))?$logMessage['acao']:$acao;
            $_log_descricao=((array_key_exists('descricao', $logMessage)))?$logMessage['descricao']:"";
            Sis::insertLog($_log_modulo_codigo,$_log_modulo_area,$_log_acao,$_log_reg_codigo,$_log_reg_nome,$_log_descricao);
        }
        return $retorno;
    }

    /*
    Método para realizar backup de tabelas específicas do MYSQL
    */
    public function MySqlDump($module){

        require "mysqldump.class.php";

        $link = $this->conn;
        $tables = array();
        $result = $link->query('SHOW TABLES');
        foreach ($result->fetchAll(PDO::FETCH_NUM) as $key => $row) {
            if(strpos($row[0],$this->DB_PREFIX."_".$module)!==false)
            {
                $tables[] = $row[0];
            }
        }

        $dumpSettings = array(
            'include-tables' => $tables,
            'compress' => 'NONE',
            'no-data' => false,
            'add-drop-database' => false,
            'add-drop-table' => true,
            'single-transaction' => false,
            'lock-tables' => false,
            'add-locks' => true,
            'extended-insert' => true,
            'disable-foreign-keys-check' => true
        );

        $filePath = BASE_PATH.DS."admin".DS."modulos".DS.$module.DS."sql-backup".DS;
        if(!file_exists($filePath)){ mkdir($filePath); }
        $fileName = 'db-backup-'.time().'-'.(md5(implode(",",$tables))).'.sql';

        $dump = new Mysqldump(Connect::getNome(), Connect::getUser(), Connect::getPass(), Connect::getHost(), 'mysql', $dumpSettings);
        $dump->start($filePath.$fileName);
    }

}//End class
?>