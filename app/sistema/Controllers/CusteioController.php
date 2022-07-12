<?php

require_once(ROOT_SISTEMA.'Models/Custeio.php');

/**
 * CusteioController short summary.
 *
 * CusteioController description.
 *
 * @version 1.0
 * @author Rafael
 */
class CusteioController
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
       

        //Calculo do custo fixo por dia e hora
        $CalculoDiaHora = self::CalculoDiaHora($dtIni); 


        $query = "select * from custeio";

        $iTotalDisplayRecords = mysql_num_rows(mysql_query($query, $this->db->link_id));

        $query = mysql_query($query.' order by id limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($registro = mysql_fetch_assoc($query)){


                $dados = self::DadosCusteio($registro['id'], $registro['nomeId'], $mes, $ano);
              
                
                    if($registro['custoHoraDia'] == 1)
                    {
                        $valorCusteio = $CalculoDiaHora['custoMinuto'] * $registro['qtd']; 
                        $tpCusto = "Custo p/ minuto";
                    }
                    else if($registro['custoHoraDia'] == 2)
                    {
                        $valorCusteio = $CalculoDiaHora['custoHora'] * $registro['qtd']; 
                        $tpCusto = "Custo p/ hora";
                    } 
                    else if($registro['custoHoraDia'] == 3)
                    {
                        $calcvalorCusteiouloHD = $CalculoDiaHora['custoDia'] * $registro['qtd']; 
                        $tpCusto = "Custo p/ dia";
                    } 


                        $valorFinal = $valorCusteio + $dados['valor'];

                        $valorFinal = $this->db->valorFormat($valorFinal);

                        $valorPraticado = $this->db->valorFormat($registro['valorComparacao']);
                        
                
                        $opcoes = '<div class="tbWFoption"><a href="javascript://" title="Excluir" class="smallButton btTBwf redB excluir" data-excluir-id="'.$registro['id'].'" onclick="excluir();"><img src="images/icons/light/close.png" width="10"></a> <a href="javascript://" title="Editar" class="smallButton btTBwf greyishB details" onclick="details('.$registro['id'].');"><img src="images/icons/light/pencil.png" width="10"></a></div>';          

            array_push($aaData, array('nome'=>$dados['nome'], 'tempo' => $registro['qtd'], 'tpCusto' => $tpCusto, 'valorCusteio' => $this->db->valorFormat($valorCusteio), 'valorVariavel' => $this->db->valorFormat($dados['valor']), 'valorTotal'=> $valorFinal, 'valorPraticado' => $valorPraticado, 'opcoes'=>$opcoes));
        
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo Util::array_to_json($retorno);
    }


    /**
     * ================================================================================================
     */

    /**
     * Calculo de custo por dia e hora
     * @param mixed $params
     */

    function ShowCusto($params)
    {
        $date = explode('/', $params['mes']);
        $retorno = self::CalculoDiaHora($date[1].'-'.$date[0].'-01');

        echo Util::array_to_json(array('custoMinuto' => $this->db->valorFormat($retorno['custoMinuto']), 'custoHora' => $this->db->valorFormat($retorno['custoHora']) , 'custoDia' => $this->db->valorFormat($retorno['custoDia']), 'custoTotal' => $this->db->valorFormat($retorno['valorTotal']) ) );
    }


    /**
     * Calculo por minuto, horas e dias
     */
    function CalculoDiaHora($date)
    {
       $data = explode('-', $date);

        //$dataFim = date('Y-m-d', strtotime('+1 month', strtotime($dataInicio)));

            $valor = $this->db->fetch_assoc("SELECT SUM(L.valor) AS valorTotal FROM plano_contas P 
                                            INNER JOIN ctr_plc_lancamentos C ON C.plano_contas_id = P.id 
                                            INNER JOIN lancamentos L ON L.id = C.lancamento_id
                                            WHERE L.tipo = 'P' AND P.tpCategoria = 2 AND ( MONTH(L.dt_vencimento) = ".$data['1']." AND YEAR(L.dt_vencimento) = ".$data['0']." ) ");


            $custoConfig = $this->db->fetch_assoc("SELECT hora, dia FROM custeio_config LIMIT 0, 1");


            $lancamentosAdd = $this->db->fetch_assoc("SELECT SUM(valor) AS valor FROM custeio_lancamentos WHERE ( MONTH(dt_vencimento) = ".$data['1']." AND YEAR(dt_vencimento) = ".$data['0']." ) OR ( dt_vencimento = '0000-00-00' ) ");

            $valorTotal = round($lancamentosAdd['valor'] + $valor['valorTotal']);


            $custoHora = round($valorTotal / $custoConfig['hora'], 2);

            $custoMinuto = round($custoHora / 60, 2); 

            $custoDia = round($valorTotal / $custoConfig['dia'], 2); 

        
        $retorno = array('valorTotal' => $valorTotal, 'custoMinuto' => $custoMinuto, 'custoHora' => $custoHora, 'custoDia' => $custoDia);

        return $retorno;

    }

    /**
     * Calculo de custo do material
     * @param mixed $params
     */
    function DadosCusteio($id, $nomeId, $mes, $ano)
    {
            
             $material = $this->db->fetch_assoc("SELECT SUM(valor) AS valor FROM custeio_material WHERE custeioId = ".$id." AND ( (MONTH(dt_vencimento) >='".$mes."' AND YEAR(dt_vencimento) >= '".$ano."') OR (dt_vencimento = '0000-00-00') )" );

               $valor = (!empty($material['valor']))? $material['valor'] : "0.00";
                

            $nomeCusteio = $this->db->fetch_assoc("SELECT nome FROM custeio_nome WHERE id = ".$nomeId);
    
        return array('valor' => $valor, 'nome' => $nomeCusteio['nome']);
    
    }

    /**
     * ================================================================================================
     */

    /**
     * Criar nome comum do custeio
     * @param mixed $params
     */
    function CreateNome($params){
        $nome = new Custeio($params);

        $nomeId = $this->db->query_insert('custeio_nome', $nome->fields);
            
        return Util::array_to_json(array('status' => 1, 'msg' => 'Custeio cadastrada com sucesso', 'funcao_id' => $nomeId));
    }
    
     /**
     * Criar custeio
     * @param mixed $params
     */
    function Create($params){

        unset($params['id']);

        $custeio = new Custeio($params); 

        $custeio->fields['valorComparacao'] = $this->db->valorToDouble($custeio->fields['valorComparacao']);

            $custeioId = $this->db->query_insert('custeio', $custeio->fields);

            $params['custeioId'] = $custeioId;

            self::CreateMaterial($params);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Custeio cadastrado com sucesso', 'funcao_id' => $custeioId));
    }

     /**
     * Criar custeio material
     * @param mixed $params
     */
    function CreateMaterial($params){

        $custeioMaterial = new Custeio($params);

         $total = count($params);

                $i = 1;
                while($i <= $total)
                {
                    
                    if(array_key_exists('material'.$i, $params))
                    {
                        
                        $custoUnitario = $this->db->valorToDouble($params['custoUnitario'.$i]);
                        $valor = $this->db->valorToDouble($params['valor'.$i]);
                        $valorComparacao = $this->db->valorToDouble($params['valorComparacao'.$i]);
                        $dt_vencimento = self::dt_formart_db($params['dt_vencimento'.$i]);


                        $custeioMaterial = array('material' => $params['material'.$i],
                                                 'qtdMaterial' => $params['qtdMaterial'.$i],
                                                 'custoUnitario' => $custoUnitario,
                                                 'valor' => $valor,
                                                 'custeioId' => $params['custeioId'],
                                                 'dt_vencimento' => $dt_vencimento);

                        $custeioMaterialId = $this->db->query_insert('custeio_material', $custeioMaterial);

                    }

                    $i++;

                }

                
        //return Util::array_to_json(array('status' => 1, 'msg' => 'Material custeio cadastrada com sucesso'));
    }


     /**
     * Editar custeio
     * @param mixed $params
     */
    function Edit($params){

        $custeio = new Custeio($params); 

            $this->db->query('DELETE FROM custeio_material WHERE custeioId = '.$params['id']);

            $this->db->query_update('custeio', $custeio->fields, ' id='.$params['id']);

            $params['custeioId'] = $params['id'];          

            self::CreateMaterial($params);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Custeio editado com sucesso'));
    }



    /**
     * Summary of AutoCompleteFuncao
     * @param mixed $params 
     */
    function AutoCompleteFuncao($params){

        $q = $params["term"];

        if (!$q || $q==""){
            $query = mysql_query("select id, nome from custeio_nome order by nome");
        }else{
            $query = mysql_query("select id, nome from custeio_nome where nome LIKE '%".$q."%' order by nome")or die(mysql_error());
        }

        $items = array();

        while($consulta = mysql_fetch_assoc($query)){
            array_push($items, $consulta);
        }

        $this->db->close();

        if(count($items)>0){
            $result = array();
            $achou = false;
            foreach ($items as $item) {//foreach ($items as $key=>$value) {
                //if ( (strpos(strtolower($key), $q) !== false) ) {
                array_push($result, array("id"=>$item['id'], "label"=>$item['nome'], "value" => strip_tags($item['nome'])));
                //}
                $keyL = strtolower($item['nome']);
                $qL = strtolower($q);
                if( $keyL == $qL )
                    $achou=true;
                //if (count($result) > 11)
                  //  break;
            }
            if($q!="" && !$achou)
                array_push($result, array("id"=>"add", "label"=>strip_tags($q)." (ADICIONAR)", "value" => strip_tags($q)));
            echo Util::array_to_json($result);
        }else{
            $result = array();
            array_push($result, array("id"=>"add", "label"=>strip_tags($q)." (ADICIONAR)", "value" => strip_tags($q)));
            echo Util::array_to_json($result);
            //echo '[]';
        }
    }


     /**
     * Incluir nome do custeio via auto completar
     * @param mixed $params 
     */
    function IncluirCusteioAc($params){
        echo self::CreateNome($params);
    }

    /**
     * Salvar configurações
     * @params mixed $params
     */
    function SalvarConfig($params)
    {
        $this->db->query_update('custeio_config', array('hora' => $params['hora'], 'dia' => $params['dia']), ' id = 1 ');

        echo Util::array_to_json( array('status' => 1) );
    }

    /**
     * Excluir custeio
     */
    function Excluir($params)
    {

            $this->db->query('DELETE FROM custeio WHERE id ='.$params['id']);

            $this->db->query('DELETE FROM custeio_material WHERE custeioId ='.$params['id']);

        echo Util::array_to_json( array('status' => 1) );

    }

    /**
     * Details Custeio
     */
    function Details($params)
    {   
        
        $date = explode('/', $params['date']);

        $custeio = $this->db->fetch_assoc('SELECT * FROM custeio WHERE id ='.$params['id']);

            $nomeCusteio = $this->db->fetch_assoc('SELECT id, nome FROM custeio_nome WHERE id ='.$custeio['nomeId']);          


                $retorno['status'] = 1;
                $retorno['id'] = $params['id'];
                $retorno['nome'] = $nomeCusteio['nome'];
                $retorno['nomeId'] = $nomeCusteio['id'];
                $retorno['custoHoraDia'] = $custeio['custoHoraDia'];
                $retorno['qtd'] = $custeio['qtd'];
                $retorno['valorComparacao'] = $this->db->valorFormat($custeio['valorComparacao']);
                


                $custeioMaterial = $this->db->fetch_all_array('SELECT *, date_format(dt_vencimento, "%d/%m/%Y") as dt_vencimento FROM custeio_material WHERE custeioId ='.$custeio['id'].' AND ( ( MONTH(dt_vencimento) = '.$date['0'].' AND YEAR(dt_vencimento) = '.$date['1'].' ) OR ( dt_vencimento = "0000-00-00" ) )');                  

                    $i = 1;
                    foreach($custeioMaterial as $custeioMaterial)
                    {                       
                        $retorno['material'.$i] = $custeioMaterial['material'];
                        $retorno['qtdMaterial'.$i] = $custeioMaterial['qtdMaterial'];
                        $retorno['custoUnitario'.$i] = $this->db->valorFormat($custeioMaterial['custoUnitario']);
                        $retorno['valor'.$i] = $this->db->valorFormat($custeioMaterial['valor']);
                        $retorno['dt_vencimento'.$i] = $custeioMaterial['dt_vencimento'];

                        $i++;
                    }

                    $retorno['materialTotal'] = $i; //$i - 1

                    echo Util::array_to_json( $retorno );

    }


    /**
     * Details Lançamentos
     */
    function DetailsLancamentos($params)
    {   
        
        $date = explode('/', $params['date']);

        
        /** Retorna as configurações */
        $retorno = $this->db->fetch_assoc('SELECT dia, hora FROM custeio_config WHERE id = 1');

        /** Retorna os lançamentos */
        $sql = 'SELECT P.nome, L.descricao, DATE_FORMAT(L.dt_vencimento, "%d/%m/%Y") as dt_vencimento, L.valor 
        FROM plano_contas P 
        INNER JOIN ctr_plc_lancamentos C ON C.plano_contas_id = P.id 
        INNER JOIN lancamentos L ON  L.id = C.lancamento_id 
        WHERE P.tpCategoria = 2 AND L.tipo = "P" AND ( ( MONTH(L.dt_vencimento) ='.$date['0'].' AND YEAR(L.dt_vencimento) ='.$date['1'].' ) OR ( L.dt_vencimento = "0000-00-00" ) )';


        $lancamentos = $this->db->fetch_all_array($sql);
        $lan = '';

        if(count($lancamentos) > 0){

            foreach($lancamentos as $lanc)
            {

                $lan .= '<tr class="odd"><td class="updates newUpdate left">'.$lanc['nome'].'</td><td class="updates newUpdate left">'.$lanc['descricao'].'</td><td class="updates newUpdate right">'.$lanc['dt_vencimento'].'</td><td class="updates newUpdate right">'.$lanc['valor'].'</td></tr>';

            }

        }else{

            $lan .= '<tr><td colspan="4" class="updates newUpdate">Não existem lançamentos</td></tr>';
        }

        $retorno['lancamentos'] = $lan;



        /** Retorna os lancamentos adicionados dentro do modal no custeio */
        $add = $this->db->fetch_all_array('SELECT descricao, valor, dt_vencimento FROM custeio_lancamentos WHERE ( MONTH(dt_vencimento) = '.$date['0'].' AND YEAR(dt_vencimento) = '.$date['1'].' ) OR ( dt_vencimento = "0000-00-00" ) ');                  

            $i = 1;
            foreach($add as $add)
            {                       
                $retorno['descricao'.$i] = $add['descricao'];
                $retorno['dt_vencimento'.$i] = self::dt_formart($add['dt_vencimento']);
                $retorno['valor'.$i] = $this->db->valorFormat($add['valor']);

                $i++;
            }

            $retorno['lancamentoTotal'] = $i - 1;



                echo Util::array_to_json( $retorno );

    }

    /**
     * Atualiza as informações do modal composição custeio 
     **/    
    function EditLancamentos($params)
    {
            $configParams = array('hora' => $params['hora'], 'dia' => $params['dia']);            

            $this->db->query_update('custeio_config', $configParams, ' id = 1');

            $this->db->query('DELETE FROM custeio_lancamentos');

            $teste = self::CreateLancamentos($params);
        
        echo Util::array_to_json(array('status' => 1, 'msg' => $teste.'Composição do custeio atualizado com sucesso'));
    }

    /**
     * Exclui os lancamentos antigos e Cria os novos lancamento adicionados no modal composição custeio
     */
    function CreateLancamentos($params)
    {

        $total = count($params);

                $i = 1;
                while($i <= $total)
                {
                    
                    if(array_key_exists('descricao'.$i, $params))
                    {
                                         
                        $valor = $this->db->valorToDouble($params['valor'.$i]);

                        $add = array('descricao' => $params['descricao'.$i],
                                     'valor' => $valor,
                                     'dt_vencimento' => self::dt_formart_db($params['dt_vencimento'.$i]));

                        $this->db->query_insert('custeio_lancamentos', $add);

                    }

                    $i++;

                }

    }


    function dt_formart_db($origDate)
    {     

        $date = str_replace('/', '-', $origDate );
        $newDate = date("Y-m-d", strtotime($date));        

        $newDate = ($newDate == "1969-12-31")? NULL : $newDate;
            
        return $newDate;        

    }



    function dt_formart($origDate)
    {     

        $newDate = date("d/m/Y", strtotime($origDate));        

        $newDate = ($newDate == "31/12/1969")? "00/00/0000" : $newDate;
            
        return $newDate;        

    }



}