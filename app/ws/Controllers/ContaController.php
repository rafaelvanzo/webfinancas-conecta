<?php

/**
 * ContaController short summary.
 *
 * ContaController description.
 *
 * @version 1.0
 * @author Fabio
 */
class ContaController extends Controller
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
            $query = mysql_query("select c.id, b.nome, c.descricao
												from contas c
												left join bancos b on c.banco_id = b.id
												order by c.descricao
											 ", $this->db->link_id)or die(mysql_error());
        }else{
            $query = mysql_query("select c.id, b.nome, c.descricao
												from contas c
												left join bancos b on c.banco_id = b.id
												where ( (b.nome LIKE '%".$q."%') or (c.descricao LIKE '%".$q."%') or ( concat(c.descricao,' - ',b.nome) LIKE '%".$q."%' ) )
													order by c.descricao
											 ", $this->db->link_id)or die(mysql_error());
        }

        while($consulta = mysql_fetch_assoc($query)){
            if($consulta['nome']){
                $banco_nome = ' - '.$consulta['nome'];
            }else{
                $banco_nome = "";
            }
            $key = $consulta['descricao'].$banco_nome;
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
