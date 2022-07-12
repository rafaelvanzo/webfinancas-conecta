<?php

class Categoria {

    //CARREGAR PLANO DE CONTAS
    //======================================================================================================================================================================================

    function CarregarPlanoContas($db,$params){

        //As abas do excel iniciam no índice zero
        //As colunas e linhas iniciam no índice um

        //start: Lê excel com o plano de contas selecionado
        $arquivo = $params['modelo'].'.xls';
    
        $xls = new Spreadsheet_Excel_Reader($arquivo);
        $linhas = $xls->rowcount();
        //end: Lê excel com o plano de contas selecionado

        //start: Inclui categorias no plano de contas
        //VERIFICAR SE O PRIMEIRO NÍVEL DE TODOS OS MODELOS SEMPRE TERÃO A MAIOR PROFUNDIDADE IGUAL
        if($params['modelo']=='engenharia')
            $maiorNivel = 4;
        elseif($params['modelo']=='odontologico')
            $maiorNivel = 2;

        $dtCadastro = date('Y-m-d');
    
        for($i = 2; $i <= $linhas; $i++){

            $codigo = $xls->val($i,1);

            $arrayCodigo = explode('.',$codigo);
            $nivel = count($arrayCodigo);

            $arrayInsert = array();

            $arrayInsert['cod_conta'] = $codigo;
            $arrayInsert['nome'] = utf8_encode($xls->val($i,2));
            $arrayInsert['clfc_fc'] = $xls->val($i,3);
            $arrayInsert['clfc_dre'] = $xls->val($i,4);
            $arrayInsert['nivel'] = $nivel;
            $arrayInsert['tp_conta'] = ($nivel == $maiorNivel)? 1 : 2; //1: Analítico; 2: Sintético
            $arrayInsert['posicao'] = $arrayCodigo[$nivel-1];
            $arrayInsert['situacao'] = 0; //0: Ativo; 1:Inativo
            $arrayInsert['dt_cadastro'] = $dtCadastro;
            $arrayInsert['dedutivel'] = $xls->val($i,5);

            $db->query_insert('plano_contas',$arrayInsert);
        }
    
        //Inclui categoria Geral
        $arrayInsert = array();
        $arrayInsert['cod_conta'] = '0';
        $arrayInsert['hierarquia'] = '0';
        $arrayInsert['nome'] = 'Não alocado';
        $arrayInsert['situacao'] = 0; //0: Ativo; 1:Inativo
        $arrayInsert['dt_cadastro'] = $dtCadastro;
        $db->query_insert('plano_contas',$arrayInsert);
        $db->query('update plano_contas set id = 0 where cod_conta = "0"');
        //end: Inclui categorias no plano de contas
    
        //start: Atualiza conta_pai_id e hierarquia das categorias incluídas
        $query = mysql_query('select * from plano_contas where id > 0 order by id',$db->link_id);
        while($categoria = mysql_fetch_assoc($query)){

            $arrayUpdate = array();
        
            if($categoria['nivel']==1){
                $arrayUpdate['conta_pai_id'] = 0;
                $arrayUpdate['hierarquia'] = $categoria['id'];
            }else{
            
                $qtdCaracteres = strrpos($categoria['cod_conta'],'.');
                $codigoPai = substr($categoria['cod_conta'],0,$qtdCaracteres);

                $categoriaPai = $db->fetch_assoc('select id, hierarquia from plano_contas where cod_conta = "'.$codigoPai.'"');
                $arrayUpdate['hierarquia'] = $categoriaPai['hierarquia'].','.$categoria['id'];

                $arrayUpdate['conta_pai_id'] = $categoriaPai['id'];
            }

            $db->query_update('plano_contas',$arrayUpdate,'id = '.$categoria['id']);
        }
        //end: Atualiza conta_pai_id e hierarquia das categorias incluídas
    }

}
?>