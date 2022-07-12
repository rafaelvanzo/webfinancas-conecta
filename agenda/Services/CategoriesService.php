<?php
/**
 * Class para manipular arquivos e imagens no WEBEDIT/MODULI
 * 
 * @version 1.0
 * @author Rafael Vanzo
 * @Services
 */

class CategoriesService
{

    private $db;
    private $dbTabela;

    function __construct($db)
    {

        $this->db = $db;
        $this->dbTabela = 'categorias';

    }

/** 
* ================================================================================= 
* ------------------------------------- CREATE ------------------------------------ 
* ================================================================================= 
*/ 

public function Create($params)
{

    try{ 

        $pai = $params['pai'];

        $nome = $params['nome'];
        
        $tabela = $params['tabela'];


        $verificar = $this->db->fetch_assoc('SELECT Nome FROM '.$this->dbTabela.' WHERE Nome = "'.$nome.'" AND Tabela = "'.$tabela.'" AND Excluido = 0');

    
        if($verificar['Nome'] == false && $pai == 0){ 

            $Id = $this->db->query_insert($this->dbTabela, array('Nome' => $nome, 'Pai' => $pai, 'tabela' => $tabela) );

            $retorno = array('Situacao' => 1, 'Id' => $Id, 'Nome' => $nome);


        }elseif($pai > 0){


            $Id = $this->db->query_insert($this->dbTabela, array('Nome' => $nome, 'Pai' => $pai, 'tabela' => $tabela) );

            $retorno = array('Situacao' => 1, 'Id' => $Id, 'Nome' => $nome);


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

        $pai = $params['pai'];

        $nome = $params['nome'];

        $tabela = $params['tabela'];

        $id = $params['id'];


        $verificar = $this->db->fetch_assoc('Select Nome from '.$this->dbTabela.' where Nome = "'.$nome.'" AND Tabela = "'.$tabela.'" AND Excluido = 0');
    
        if($verificar['Nome'] == false && $pai == 0){ 
            

            $Id = $this->db->query_update($this->dbTabela, array('Nome' => $nome, 'Pai' => $pai, 'tabela' => $tabela) , 'Id ='.$id);

            $retorno = array('Situacao' => 1, 'Id' => $Id, 'Nome' => $nome);


        }elseif($pai > 0){


            $verificar = $this->db->fetch_assoc('Select Nome from '.$this->dbTabela.' where Id = "'.$id.'" AND Tabela = "'.$tabela.'" AND Pai = 0');

            
            if($verificar == false){

                $Id = $this->db->query_update($this->dbTabela, array('Nome' => $nome, 'Pai' => $pai) , 'Id ='.$id);

                $retorno = array('Situacao' => 1, 'Id' => $Id, 'Nome' => $nome);

            }else{

                $retorno = array('Situacao' => 0, 'Msg' => 'Não é possível alterar categoria para subcategoria.'); 

            }


        }else{

            $retorno = array('Situacao' => 0, 'Msg' => 'Categoria já possui cadastro.');         

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

        $verificar = $this->db->fetch_all_array('Select Id From '.$this->dbTabela.' where Pai = '.$id.' AND Excluido = 0'); 


        if($verificar == false){

           
            $this->db->query_update($this->dbTabela, array('Excluido' => 1, 'DTExclusao' => date('Y-m-d H:i:s')), 'Id ='.$id);

             $retorno = array('Situacao' => 1, 'Msg' => 'Categoria excluida com sucesso.'); 

        }else{
            
            $retorno = array('Situacao' => 0, 'Msg' => 'Não foi possível excluir a categoria, porque existem subcategorias associadas.'); 

        }

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

    $TodasCategorias = '';

    $categoria = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE Pai = 0 AND Tabela = "'.$tabela.'" AND Excluido = 0'); 

    if($categoria != false){

    foreach($categoria as $categoria){
                
             $vsEditarCat = "'".$categoria['Nome']."','".$categoria['Id']."', '0'";
             $excluirCat = "'".$categoria['Id']."'";

             $TodasCategorias .= '<tr class="Cat-'.$categoria['Id'].'">														
                                    <td>
                                        <b><i class="fa fa-angle-right"></i> '.$categoria['Nome'].'</b>
                                    </td>	
                                        <td class="actions-hover actions-fade">
                                            <a href="javascript://" onclick="vsEditarCat('.$vsEditarCat.'); "><i class="fa fa-pencil"></i></a>
                                            <a href="javascript://" onclick="excluirCat('.$excluirCat.');" class="delete-row"><i class="fa fa-trash-o"></i></a>
                                        </td>													
                                </tr>';
                                

            $subCategoria = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE Pai = '.$categoria['Id'].' AND Tabela = "'.$tabela.'" AND Excluido = 0');
            
            
            foreach($subCategoria as $subCategoria){
                
                $vsEditarCat = "'".$subCategoria['Nome']."' ,'".$subCategoria['Id']."', '".$categoria['Id']."'";
                $excluirCat = "'".$subCategoria['Id']."'";

                $TodasCategorias .= '<tr>														
                                        <td>
                                             &nbsp;&nbsp; <i class="fa fa-angle-double-right"></i> '.$subCategoria['Nome'].'                                                
                                        </td>	
                                            <td class="actions-hover actions-fade">
                                                <a href="javascript://" onclick="vsEditarCat('.$vsEditarCat.');"><i class="fa fa-pencil"></i></a>
                                                <a href="javascript://" onclick="excluirCat('.$excluirCat.');" class="delete-row"><i class="fa fa-trash-o"></i></a>
                                            </td>													
                                    </tr>';


            }

        }


        }else{
        
            $TodasCategorias = '<tr><td colspan="2" align="center">Não existem categorias.</td></tr>';
        
        }

         return $TodasCategorias;

}


/** 
* ================================================================================= 
* ------------------------------ LISTAR CATEGORIAS PAI -------------------------------- 
* ================================================================================= 
*/ 

public function ListarPai($params)
{
       
    try{
        
        $tabela = $params['tabela'];

        $categoriaPai = '<option class="categoriaPai" val="0"></option>';

        $categoria = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE Pai = 0 AND Tabela = "'.$tabela.'" AND Excluido = 0'); 


        if($categoria != false){


                foreach($categoria as $categoria){
                    
                
                    $categoriaPai .= '<option class="categoriaPai" value="'.$categoria['Id'].'">'.$categoria['Nome'].'</option>';

                }

            }

             return $categoriaPai;
            
    
    }catch(Exception $e){                                   
    
        $this->db->query('rollback');
    
        parent::LogErro($e->getMessage());                     

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
    
    }

}


public function ListarForm($params)
{

    $tabela = $params['tabela'];
    

        $TodasCategorias = '';
           
            $categoria = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE Pai = 0 AND tabela = "'.$tabela.'" AND Excluido = 0'); 

            if($categoria != false){

                foreach($categoria as $categoria){

                    $TodasCategorias .= '<div class="checkbox-custom checkbox-default'.$categoria['Id'].'">
                                        <input type="checkbox" id="ckCategoria'.$categoria['Id'].'" class="Ck-Categoria" name="Categoria'.$categoria['Id'].'" value="'.$categoria['Id'].'" ><label for="ckCategoria'.$categoria['Id'].'"><b>'.$categoria['Nome'].'</b></label> </div>';
                                        

                    $subCategoria = $this->db->fetch_all_array('SELECT Id, Nome FROM '.$this->dbTabela.' WHERE Pai = '.$categoria['Id'].' AND tabela = "'.$tabela.'" AND Excluido = 0');
                    
                    foreach($subCategoria as $subCategoria){
                    
                    $TodasCategorias .= '<div class="checkbox-custom checkbox-default'.$subCategoria['Id'].'" style="margin-left:10px;">
                                        <input type="checkbox" id="ckCategoria'.$subCategoria['Id'].'" class="Ck-Categoria" name="Categoria'.$subCategoria['Id'].'" value="'.$subCategoria['Id'].'" ><label for="ckCategoria'.$subCategoria['Id'].'">'.$subCategoria['Nome'].'</label> </div>';
                                

                    }

                }
        

            }else{

                $TodasCategorias = '<div style="margin-top:40px; text-align:center">Não existem categorias.</div>';

            }   

        return $TodasCategorias;

}
    
/** 
* ================================================================================= 
* --------- RELACIONAR CATEGORIAS COM OS REGISTROS DAS OUTRAS TABELAS ------------- 
* ================================================================================= 
*/ 

private function ArraysCatRelation($params)
{
    
    $retorno = array();

    $i = 1;    
    $count = 1;
    $totalArray = count($params);

    while($i <= $totalArray)
    {
        
        if(array_key_exists('Categoria'.$i, $params))
        {                           
            array_push($retorno, $params['Categoria'.$i]);
            $count ++;
        }
        
        $i++;
    }

    return $retorno;

}

public function CreateCatRelation($tabela, $params, $id)
{
    
    $dados = self::ArraysCatRelation($params); 
    
    $totalArray = count($dados) - 1;
    $c = 0;

        while($c <= $totalArray)
        {            
            $this->db->query_insert("categoriasTagsTabelas", array('Tabela' => $tabela, 'Tipo' => 'c', 'IdCatTag' => $dados[$c], 'IdTabela' => $id) );

            $c ++;
        }
    
} 

public function EditCatRelation($tabela, $params, $id)
{

    $this->db->query('DELETE FROM categoriasTagsTabelas WHERE Tipo = "c" AND  IdTabela ='.$id);


    $dados = self::ArraysCatRelation($params);
    
    $totalArray = count($dados) - 1;
    $c = 0;

        while($c <= $totalArray)
        {            
            $this->db->query_insert("categoriasTagsTabelas", array('Tabela' => $tabela, 'Tipo' => 'c', 'IdCatTag' => $dados[$c], 'IdTabela' => $id) );

            $c ++;
        }

} 

public function DetailsCatRelation($tabela, $id)
{

    $retorno = $this->db->fetch_all_array('SELECT IdCatTag FROM categoriasTagsTabelas WHERE Tipo = "c" AND Tabela = "'.$tabela.'" AND IdTabela='.$id);

    return $retorno;

}


} /* Fim Class */
?>