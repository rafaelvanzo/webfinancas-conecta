<?php

/**
 * FormaPagamentoController short summary.
 *
 * FormaPagamentoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class FormaPagamentoController extends Controller
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

        $q = $params["query"];

        if (!$q || $q==""){
            $query = mysql_query("select id, forma from forma_pagamento order by forma", $this->db->link_id);
        }else{
            $query = mysql_query("select id, forma from forma_pagamento where forma LIKE '%".$q."%' order by forma", $this->db->link_id)or die(mysql_error());
        }

        while($consulta = mysql_fetch_assoc($query)){
            $key = $consulta['forma'];
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
