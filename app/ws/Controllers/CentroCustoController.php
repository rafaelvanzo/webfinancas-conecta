<?php

/**
 * CentroCustoController short summary.
 *
 * CentroCustoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class CentroCustoController extends Controller
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
     * Summary of GetAll
     * @param mixed $params 
     */
    function GetAll($params){
        
        $items = array();

        $q = $params["term"];

        if (!$q || $q==""){
            $query = mysql_query("select id, cod_centro, nome, tp_centro
											from centro_resp 
											where situacao = 1
                                                and id > 0
												and tp_centro = 1
												order by cod_centro", $this->db->link_id);
        }else{
            $query = mysql_query("select id, cod_centro, nome, tp_centro
											from centro_resp 
											where situacao = 1
                                                and id > 0
												and cod_centro LIKE '%".$q."%' or nome LIKE '%".$q."%'
												having tp_centro = 1
												order by cod_centro
										 ", $this->db->link_id)or die(mysql_error());
        }

        while($consulta = mysql_fetch_assoc($query)){
            $key = $consulta['cod_centro'].' - '.$consulta['nome'];
            $value = $consulta['id'];
            $items[$key] = $value;
        }

        if(count($items)>0){
            $result = array();
            foreach ($items as $key=>$value) {
                //if (strpos(strtolower($key), $q) !== false) {
                array_push($result, array("id"=>$value, "descricao"=>$key));
                //}
                //if (count($result) > 11)
                    //break;
            }
            echo parent::array_to_json($result);
        }else{
            echo '[]';
        }
    }
}
