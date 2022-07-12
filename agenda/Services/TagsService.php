<?php
/**
 * Class para manipular arquivos e imagens no WEBEDIT/MODULI
 * 
 * @version 1.0
 * @author Rafael Vanzo
 * @Services
 */

class TagsService
{

    private $db;
    private $dbTabela;

    function __construct($db)
    {

        $this->db = $db;
        $this->dbTabela = 'tags';

    }

/** 
* ================================================================================= 
* ------------------------------------- CREATE ------------------------------------ 
* ================================================================================= 
*/ 

public function Create($params)
{

    try{ 

        $nome = $params['nome'];

        $tabela = $params['tabela'];

        $verificar = $this->db->fetch_assoc('SELECT Nome FROM '.$this->dbTabela.' WHERE Nome = "'.$nome.'" AND tabela = "'.$tabela.'" AND Excluido = 0');
    
        if($verificar['Nome'] == false){ 
            

            $id = $this->db->query_insert($this->dbTabela, array('Nome' => $nome, 'tabela' => $tabela) );

            $retorno = array('Situacao' => 1, 'Id' => $id, 'Nome' => $nome);


        }else{

            $retorno = array('Situacao' => 0, 'Msg' => 'O nome utilizado já existe.');         

        }
        
            return json_encode($retorno);



    }catch(Exception $e){

            
        $this->db->query('rollback');

        parent::LogErro($e->getMessage());        
        
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */        

    }

        $this->db->close();

}

 
 
/** 
* ================================================================================= 
* -------------------------------------- EDIT ------------------------------------- 
* ================================================================================= 
*/ 

public function Edit($params)
{

    try{    

        $nome = $params['nome'];

        $id = $params['id'];

        $tabela = $params['tabela'];


        $verificar = $this->db->fetch_assoc('SELECT Nome FROM '.$this->dbTabela.' WHERE Nome = "'.$nome.'"  AND tabela = "'.$tabela.'" AND Excluido = 0');

    
            if($verificar['Nome'] == false){ 
                
                $NomeAntigo = $this->db->fetch_assoc('SELECT Nome FROM '.$this->dbTabela.' WHERE Id ='.$id);

                $AlterarTag = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE tabela = "'.$tabela.'" AND Nome like "%'.$NomeAntigo['Nome'].'%"' );


                    foreach($AlterarTag as $AlterarTag){

                        $SubstituirNome = str_replace($NomeAntigo['Nome'], $nome, $AlterarTag['Nome']);

                        $this->db->query_update($this->dbTabela, array('Nome' => $SubstituirNome, 'tabela' => $tabela) , 'Id ='.$AlterarTag['Id']);
                    }

                $id = $this->db->query_update($this->dbTabela, array('Nome' => $nome, 'tabela' => $tabela) , 'Id ='.$id);

                $retorno = array('Situacao' => 1, 'Id' => $id, 'Nome' => $nome);


            }else{

                $retorno = array('Situacao' => 0, 'Msg' => 'Tag já possui cadastro.');         

            }
        
            return json_encode($retorno);


    }catch(Exception $e){

            
        $this->db->query('rollback');

        parent::LogErro($e->getMessage());         

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
        
    }

        $this->db->close();

}



/** 
* ================================================================================= 
* ------------------------------------- DELETE ------------------------------------ 
* ================================================================================= 
*/ 

public function Delete($params)
{
       
    try{
        
        $id = $params['id'];

            $this->db->query_update($this->dbTabela, array('Excluido' => 1, 'DTExclusao' => date('Y-m-d H:i:s')), 'Id ='.$id);

            $retorno = array('Situacao' => 1, 'Msg' => 'Tag excluida com sucesso.'); 

        return json_encode($retorno);

    
    }catch(Exception $e){                                   
    
        $this->db->query('rollback');
    
        parent::LogErro($e->getMessage());                     

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
    
    }

}


/** 
* ================================================================================= 
* ----------------------------------- LISTAR -------------------------------------- 
* ================================================================================= 
*/ 

public function Listar($params)
{

    $tabela = $params['tabela'];

    $TodasTag = '';

    $tag = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE tabela = "'.$tabela.'" AND Excluido = 0'); 

        if($tag != false){

            foreach($tag as $tag){
                
                        
                    $vsEditarCat = "'".$tag['Nome']."','".$tag['Id']."', '0'";

                    $excluirCat = "'".$tag['Id']."'";

                    $TodasTag .= '<tr class="Cat-'.$tag['Id'].'">														
                                            <td>
                                                <b><i class="fa fa-angle-right"></i> '.$tag['Nome'].'</b>
                                            </td>	
                                                <td class="actions-hover actions-fade">
                                                    <a href="javascript://" onclick="vsEditarTag('.$vsEditarCat.'); "><i class="fa fa-pencil"></i></a>
                                                    <a href="javascript://" onclick="excluirTag('.$excluirCat.');" class="delete-row"><i class="fa fa-trash-o"></i></a>
                                                </td>													
                                        </tr>';
                                    

                }

            }else{

                $TodasTag = '<tr><td colspan="2" align="center">Não existem tags.</td></tr>';

            }

         return $TodasTag;

}

/** 
* ================================================================================= 
* -------------------------------- LISTAR FORM ------------------------------------ 
* ================================================================================= 
*/ 

public function ListarForm($params)
{

    $tabela = $params['tabela'];

    $Tags = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE tabela = "'.$tabela.'" AND Excluido = 0 ORDER BY Nome');

    $retorno = '';

    if($Tags != false){

        $n = 1; 

        foreach($Tags as $Tags){ 
            
                $retorno .= '<div class="checkbox-custom checkbox-default qtd-TAG'.$Tags['Id'].'">																
                                <input type="checkbox" id="checkboxStyloTAG'.$Tags['Id'].'" class="Ck-Tag" name="Tag'.$Tags['Id'].'" value="'.$Tags['Id'].'" >
                                    <label for="checkboxStyloTAG'.$Tags['Id'].'">'.$Tags['Nome'].'
                                </label> 
                            </div>';
                                                       
        $n +=1;
    
        }

    }else{

        $retorno = '<div style="margin-top:40px; text-align:center">Não existem tags.</div>';

    }

    return $retorno;
}
    
/** 
* ================================================================================= 
* ------------ RELACIONAR TAGS COM OS REGISTROS DAS OUTRAS TABELAS ---------------- 
* ================================================================================= 
*/ 

private function ArraysTagRelation($params)
{
    
    $retorno = array();

    $i = 1;    
    $count = 1;
    $totalArray = count($params);

    while($i <= $totalArray)
    {
        
        if(array_key_exists('Tag'.$i, $params))
        {                           
            array_push($retorno, $params['Tag'.$i]);
            $count ++;
        }
        
        $i++;
    }

    return $retorno;

}

public function CreateTagRelation($tabela, $params, $id)
{
    
    $dados = self::ArraysTagRelation($params); 
    
    $totalArray = count($dados) - 1;
    $c = 0;

        while($c <= $totalArray)
        {            
            $this->db->query_insert("categoriasTagsTabelas", array('Tabela' => $tabela, 'Tipo' => 't', 'IdCatTag' => $dados[$c], 'IdTabela' => $id) );

            $c ++;
        }
    
} 

public function EditTagRelation($tabela, $params, $id)
{

    $this->db->query('DELETE FROM categoriasTagsTabelas WHERE Tipo = "t" AND IdTabela ='.$id);


    $dados = self::ArraysTagRelation($params);
    
    $totalArray = count($dados) - 1;
    $c = 0;

        while($c <= $totalArray)
        {            
            $this->db->query_insert("categoriasTagsTabelas", array('Tabela' => $tabela, 'Tipo' => 't', 'IdCatTag' => $dados[$c], 'IdTabela' => $id) );

            $c ++;
        }

} 

public function DetailsTagRelation($tabela, $id)
{

    $retorno = $this->db->fetch_all_array('SELECT IdCatTag FROM categoriasTagsTabelas WHERE Tipo = "t" AND Tabela = "'.$tabela.'" AND IdTabela='.$id);

    return $retorno;

}



} /* Fim Class */
?>
