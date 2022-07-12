<?php
/**
 * Class para manipular arquivos e imagens no WEBEDIT/MODULI
 * 
 * @version 1.0
 * @author Rafael Vanzo
 * @Services
 */

class PositionService
{

    private $db;
    private $dbTabela;

    function __construct($db)
    {

        $this->db = $db;
        $this->dbTabela = 'banner';

    }


/** 
* ================================================================================= 
* ------------------------------------- CREATE ------------------------------------ 
* ================================================================================= 
*/

public function Create()
{

    try{                                                            
            
        $this->db->query('start transaction');

        //Insere o banner na posição 1 e altera a posição de todos os outros banners
        $Posicao = $this->db->fetch_all_array('SELECT Id, Posicao FROM '.$this->dbTabela.' WHERE Tipo = "Site" AND Excluido = 0');


            foreach($Posicao as $Posicao){

                $Dados['Posicao'] = $Posicao['Posicao'] + 1;

                $this->db->query_update($this->dbTabela, $Dados, 'id = '.$Posicao['Id']);

            }

            return 1;

    }catch(Exception $e){     

            
        $this->db->query('rollback');
        

            $util = new UtilitiesController($this->db); 

            //$util->LogErro($e->getMessage());  

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }

    $this->db->close();

}
/** 
* ================================================================================= 
* ------------------------------------- CHANGE ------------------------------------ 
* ================================================================================= 
*/ 

public function Change($params)
{

    try{                                                            
            
        $this->db->query('start transaction');
        
        
            //Localiza as IDs
            $PosicaoId01 = $this->db->fetch_assoc('SELECT Id FROM '.$this->dbTabela.' WHERE Excluido = 0 AND Tipo = "Site" AND Posicao = '.$params['PosicaoAtual'], $this->db->link_id);

            $PosicaoId02 = $this->db->fetch_assoc('SELECT Id FROM '.$this->dbTabela.' WHERE Excluido = 0 AND Tipo = "Site" AND Posicao = '.$params['Posicao'], $this->db->link_id);


            //Substitui as Posições
            $this->db->query_update($this->dbTabela, array('Posicao' => $params['Posicao']), 'Id = '.$PosicaoId01['Id'], $this->db->link_id);
            
            $this->db->query_update($this->dbTabela, array('Posicao' => $params['PosicaoAtual']), 'Id = '.$PosicaoId02['Id'], $this->db->link_id);


        $this->db->query('commit');

                                                            
    }catch(Exception $e){     

        
        $this->db->query('rollback');
        

            $util = new UtilitiesController($this->db); 

            $util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->db->close();

}


 
 

/** 
* ================================================================================= 
* ------------------------------------- DELETE ------------------------------------ 
* ================================================================================= 
*/ 

public function Delete($id)
{
    
    try{

    $this->db->query('start transaction');

        
        $PosicaoAtual = $this->db->fetch_assoc('SELECT Posicao FROM '.$this->dbTabela.' WHERE Id = '.$id);

        $QueryAtualizarPosicao = 'SELECT Id, Posicao FROM '.$this->dbTabela.' WHERE Excluido = 0 AND Tipo = "Site" AND  Posicao > '.$PosicaoAtual['Posicao'];


            if($this->db->numRows($QueryAtualizarPosicao) > 0)
            {  

        
                $AtualizarPosicao = $this->db->fetch_all_array($QueryAtualizarPosicao);


                foreach($AtualizarPosicao as $AtualizarPosicao)
                {
                

                    $NovaPosicao = $AtualizarPosicao['Posicao'] - 1;

                    $dadosAtualizados = array('Posicao' => $NovaPosicao);


                    $this->db->query_update($this->dbTabela, $dadosAtualizados, 'Id = '.$AtualizarPosicao['Id']);
                    
                }


            }


    $this->db->query('commit');


    }catch(Exception $e){     

                    
        $this->db->query('rollback');
        

            $util = new UtilitiesController($this->db); 

            $util->LogErro($e->getMessage());  

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
    
    }


}


} /* Fim Class */
?>
