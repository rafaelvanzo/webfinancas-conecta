<?php

/**
 * HonorarioController short summary.
 *
 * HonorarioController description.
 *
 * @version 1.0
 * @author Fabio
 */
class HonorarioController
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
     * Índice de honorários contábeis
     * @param mixed $params 
     */
    function DataTable($params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        $iTotalDisplayRecords = 0;
        
        //start: período
        $mes = $params['mes'];
        $ano = $params['ano'];
        $dtIni = $ano.'-'.$mes.'-01';
        $dtFim = date('Y-m-d', strtotime('+1 month', strtotime($dtIni)));
        //end: período

        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

        $queryHonorario = "select * from honorarios where dt_vencimento >= '".$dtIni."' and dt_vencimento < '".$dtFim."'";

        //}else{
            
           // $queryArquivo = "select * from honorarios where dt_vencimento >= '".$dtIni."' and dt_vencimento < '".$dtFim."'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryHonorario, $this->db->link_id));

        $queryHonorario = mysql_query($queryHonorario.' order by dt_vencimento desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($honorario = mysql_fetch_assoc($queryHonorario)){
            $dtVencimento = $this->db->sql_to_data($honorario['dt_vencimento']);
            $visualizado = ($honorario['visualizado']==1)? 'Sim' : 'Não';
            $compensado = ($honorario['compensado']==1)? 'Sim' : 'Não';
            $valor = $this->db->valorFormat($honorario['valor']);
            $opcoes = ($honorario['compensado']==0)? '<a href="" title="Download" class="smallButton greyishB download" data-link="'.$honorario['link'].'" data-id="'.$honorario['id'].'"><img src="images/icons/light/download.png" width="10"></a>' : '-';
            array_push($aaData,array('dt_vencimento'=>$dtVencimento,'valor'=>'R$ '.$valor,'compensado'=>$compensado,'visualizado'=>$visualizado,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo Util::array_to_json($retorno);
    }

    /**
     * Summary of DownloadHonorario
     * @param mixed $params
     */
    function DownloadHonorario($params){
        
        $this->db->query('update honorarios set visualizado = 1 where id = '.$params['id']);
        
        header('location:https://www.webfinancas.com/boleto/'.$params['link']);
    }
}
