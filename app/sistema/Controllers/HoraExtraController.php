<?php

require_once(ROOT_SISTEMA.'Models/HoraExtra.php');

/**
 * FaltaController short summary.
 *
 * FaltaController description.
 *
 * @version 1.0
 * @author Fabio
 */
class HoraExtraController
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
    //function Details($params){
    //}

    /**
     * Incluir hora extra
     * @param mixed $params
     */
    function Create($params){
        $horaExtra = new HoraExtra($params);

        $horaExtraId = $this->db->query_insert('func_horas_extras', $horaExtra->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Hora extra cadastrada com sucesso', 'hora_extra_id' => $horaExtraId));
    }

    /**
     * Editar hora extra
     * @param mixed $params
     */
    function Edit($params){
        
        $horaExtra = mysql_fetch_assoc(mysql_query('select id from func_horas_extras where id = '.$params['id'], $this->db->link_id));
        
        if($horaExtra){

            $horaExtra = new HoraExtra($params);

            $this->db->query_update('func_horas_extras', $horaExtra->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Hora extra atualizada com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar falta.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Hora extra não encontrada.'));
    
    }

    /**
     * Excluir hora extra
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_horas_extras where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Hora extra excluída com sucesso'));
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

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_horas_extras');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $queryHoraExtra = "select * from func_horas_extras";

        //}else{
            
          //  $queryHoraExtra = "select * from func_horas_extras where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryHoraExtra, $this->db->link_id));

        $queryHoraExtra = mysql_query($queryHoraExtra.' order by dt_hora_extra desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($horaExtra = mysql_fetch_assoc($queryHoraExtra)){
            
            $dtHoraExtra = $this->db->sql_to_data($horaExtra['dt_hora_extra']);
            $opcoes = '
                <a href="'.$horaExtra['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-hora-extra" data-hora-extra-id="'.$horaExtra['id'].'" id="link-exc-hora-extra-'.$horaExtra['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$horaExtra['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-hora-extra" data-hora-extra-id="'.$horaExtra['id'].'" data-dt-hora-extra="'.$dtHoraExtra.'" data-qtd-hora-extra="'.$horaExtra['qtd_hora_extra'].'" data-percent-hora-extra="'.$horaExtra['percentual'].'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('dtHoraExtra'=>$dtHoraExtra,'qtdHoraExtra'=>$horaExtra['qtd_hora_extra'],'percentual'=>$horaExtra['percentual'],'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}


?>