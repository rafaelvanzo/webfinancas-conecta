<?php

require_once(ROOT_SISTEMA.'Models/Ferias.php');

/**
 * FeriasController short summary.
 *
 * FeriasController description.
 *
 * @version 1.0
 * @author Fabio
 */
class FeriasController
{

    private $db;

    /**
     * Construtor
     * @param Database $dbConnection 
     */
    function __construct(Database $dbConnection = null){
        $this->db = $dbConnection;
    }

    /**
     * Summary of Details
     * @param mixed $params 
     */
    function Details($params){
        $ferias = mysql_fetch_assoc(mysql_query('select * from func_ferias where id = "'.$params['id'].'"', $this->db->link_id));

        if($ferias['dt_periodo_ini']!='')
            $ferias['dt_periodo_ini'] = DataBase::sql_to_data($ferias['dt_periodo_ini']);

        if($ferias['dt_periodo_fim']!='')
            $ferias['dt_periodo_fim'] = DataBase::sql_to_data($ferias['dt_periodo_fim']);

        if($ferias['dt_ferias_ini']!='')
            $ferias['dt_ferias_ini'] = DataBase::sql_to_data($ferias['dt_ferias_ini']);

        if($ferias['dt_ferias_fim']!='')
            $ferias['dt_ferias_fim'] = DataBase::sql_to_data($ferias['dt_ferias_fim']);

        echo Util::array_to_json($ferias);
    }

    /**
     * Criar salário
     * @param mixed $params
     */
    function Create($params){
        $ferias = new Ferias($params);

        $feriasId = $this->db->query_insert('func_ferias', $ferias->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Ferias cadastradas com sucesso', 'ferias_id' => $feriasId));
    }

    /**
     * Editar salário
     * @param mixed $params 
     */
    function Edit($params){
        
        $ferias = mysql_fetch_assoc(mysql_query('select id from func_ferias where id = '.$params['id'], $this->db->link_id));
        
        if($ferias){

            $ferias = new Ferias($params);

            $this->db->query_update('func_ferias', $ferias->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Ferias atualizadas com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar ferias.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    
    }

    /**
     * Excluir ferias
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_ferias where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Ferias excluídas com sucesso'));
    }

    /**
     * Summary of DataTable
     * @param mixed $db
     * @param mixed $params
     * @return string
     */
    function DataTable($params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        //$iTotalRecords = $db->numRows('select id from lancamentos');
        $iTotalDisplayRecords = 0;

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_ferias');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $queryFerias = "select * from func_ferias";

        //}else{
            
          //  $queryFerias = "select * from func_ferias where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryFerias, $this->db->link_id));

        $queryFerias = mysql_query($queryFerias.' order by id desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($ferias = mysql_fetch_assoc($queryFerias)){
            
            $dtPeriodoIni = $this->db->sql_to_data($ferias['dt_periodo_ini']);
            $dtPeriodoFim = $this->db->sql_to_data($ferias['dt_periodo_fim']);
            $dtFeriasIni = $this->db->sql_to_data($ferias['dt_ferias_ini']);
            $dtFeriasFim = $this->db->sql_to_data($ferias['dt_ferias_fim']);
            $opcoes = '
                <a href="'.$ferias['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-ferias" data-ferias-id="'.$ferias['id'].'" id="link-exc-'.$ferias['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$ferias['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-ferias" data-ferias-id="'.$ferias['id'].'" data-dt-periodo-ini="'.$dtPeriodoIni.'" data-dt-periodo-fim="'.$dtPeriodoFim.'" data-dt-ferias-ini="'.$dtFeriasIni.'" data-dt-ferias-fim="'.$dtFeriasFim.'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('dt_periodo_ini'=>$dtPeriodoIni,'dt_periodo_fim'=>$dtPeriodoFim,'dt_ferias_ini'=>$dtFeriasIni,'dt_ferias_fim'=>$dtFeriasFim,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}
?>