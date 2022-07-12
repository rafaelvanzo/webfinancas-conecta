<?php
/*
echo "Hora de início: ",date("H:i:s"),"<br><br>";

require("php/Database.class.php");

$host = "mysql.webfinancas.com";
$usuario = "webfinancas";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinancas";

$db = new Database($host,$usuario,$senha,$db_usuario);

$array_clientes_db = $db->fetch_all_array("select db from clientes_db where cliente_id > 7");
//$array_clientes_db = $db->fetch_all_array("select db from clientes_db");

$db->close();


foreach($array_clientes_db as $cliente_db){
        
    $host = "mysql.webfinancas.com";
	$usuario = $cliente_db["db"];
	$senha = "W2BSISTEMAS";
	$db_usuario = $cliente_db["db"];

	$conexao_cliente_db = new Database($host,$usuario,$senha,$db_usuario);

    try{

        $conexao_cliente_db->query('start transaction');
*/
/*
        $query = "

        
        
        ALTER TABLE `custeio_material` ADD `dt_vencimento` DATE NOT NULL AFTER `valor`;
          
          
      
      


        ";
*/
/*
        $anexos = $conexao_cliente_db->fetch_all_array('SELECT * FROM lnct_anexos');

        foreach($anexos as $anexo)
        {
            
            if(substr($anexo['nome_arquivo'], -3) === 'pdf')
            {
                if(substr($anexo['nome_arquivo'], -4) === '.pdf')
                {
                    //echo 'Com extensão: '.$anexo['nome_arquivo'];
                }else{

                   $dados['nome_arquivo'] = str_replace('pdf', '.pdf', $anexo['nome_arquivo']);
                   $dados['nome_arquivo_org'] = str_replace('pdf', '.pdf', $anexo['nome_arquivo_org']);


                   $conexao_cliente_db->query_update('lnct_anexos', $dados, 'id ='.$anexo['id']);
                   echo 'Com extensão: '.$anexo['nome_arquivo'].'<br>';
                }
            }
            
        }


        //$query = 'ALTER TABLE `lancamentos` ADD `favorecido_id_dep` INT NOT NULL AFTER `favorecido_id`';

        //if(!$erro = $conexao_cliente_db->query($query))
          //  throw new Exception($erro);

        //$query = 'ALTER TABLE `remessa_contabil` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;';

       // if(!$erro = $conexao_cliente_db->query($query))
            //throw new Exception($erro);
        
        //$query = 'ALTER TABLE `clientes_cf` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;';

        //if(!$erro = $conexao_cliente_db->query($query))
            //throw new Exception($erro);
        
        //$query = 'ALTER TABLE `clientes_favorecidos` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;';

        //if(!$erro = $conexao_cliente_db->query($query))
            //throw new Exception($erro);

        //$query = 'ALTER TABLE `clientes_pl_config` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;';

        //if(!$erro = $conexao_cliente_db->query($query))
            //throw new Exception($erro);
        
        //$query = "ALTER TABLE `lnct_anexos` ADD `dt_competencia` DATE AFTER `dt_visualizacao`";
        //if(!$erro = $conexao_cliente_db->query($query))
            //throw new Exception($erro);
        
        //$query = "SHOW COLUMNS FROM `lnct_anexos` where field = 'dt_visualizacao'";
        //if($conexao_cliente_db->numRows($query)==0)
          //  echo $cliente_db["db"].' <br><br>';

        //$query = "ALTER TABLE `contas` CHANGE `multa` `multa` DECIMAL(4,2) NOT NULL;";
        //$conexao_cliente_db->query($query);

        $conexao_cliente_db->query('commit');

        //$configuracao = $conexao_cliente_db->fetch_assoc('select pasta_raiz from arq_doc_monitor_config');
        //echo str_replace('/','\\',$configuracao['pasta_raiz']).'<br><br>';

        //update arq_doc_monitor_config set pasta_raiz = '//DESKTOP-BE9VHFM/WFDocMonitor2'

        $conexao_cliente_db->close();
            
    }

    catch(Exception $e){
        echo $e->getMessage();
        $conexao_cliente_db->query('rollback');
        $conexao_cliente_db->close();
    }

}

echo "Hora de término: ",date("H:i:s"),"<br><br>";
*/
?>