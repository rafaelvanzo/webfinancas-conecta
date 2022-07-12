<?php

/**
 * CategoriaController short summary.
 *
 * CategoriaController description.
 *
 * @version 1.0
 * @author Fabio
 */
class CategoriaController extends Controller
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
            $query = mysql_query("select id, cod_conta, nome, tp_conta
											from plano_contas
											where situacao = 0
                                                and id > 0
												and tp_conta = 1
												order by cod_conta", $this->db->link_id);
        }else{
            $query = mysql_query("select id, cod_conta, nome, tp_conta
											from plano_contas 
											where situacao = 0
                                                and id > 0
												and cod_conta LIKE '%".$q."%' or nome LIKE '%".$q."%'
												having tp_conta = 1
												order by cod_conta
			 							 ", $this->db->link_id)or die(mysql_error());
        }

        while($consulta = mysql_fetch_assoc($query)){
            $key = $consulta['cod_conta'].' - '.$consulta['nome'];
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
