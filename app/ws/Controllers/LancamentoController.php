<?php

//require_once('Models/Lancamento.php');
require_once(ROOT_MODULOS.'/lancamento/class/Lancamento.class.php');
require_once(ROOT_MODULOS.'/lancamento/class/Recebimento.class.php');
require_once(ROOT_MODULOS.'/lancamento/class/Pagamento.class.php');
require_once(ROOT_MODULOS.'/lancamento/class/Transferencia.class.php');

/**
 * LancamentoController short summary.
 *
 * LancamentoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class LancamentoController
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
     * Criar lançamento
     * @param mixed $params 
     */
    function Create ($params){

        //Quantidade de posições no array de múltiplos lançamentos
        //Usado para chamar a função Create recursivamente quando houver mais de um lançamento a ser incluído
        $qtdMultiplosLancamentos = 0;

        //Array $arrayIdMultiplosLancamentos que retorna id a de múltiplos lançamentos inseridos
        //Este array e a posição id_multiplos_lancamentos do array $params são criados dentro da função Create para armazenar os lançamentos incluídos recursivamente e retorná-los à aplicação cliente
        if(isset($params['id_multiplos_lancamentos']))
            $arrayIdMultiplosLancamentos = $params['id_multiplos_lancamentos'];
        else
            $arrayIdMultiplosLancamentos = array();

        if($params['multi_lancameto'] == 1) //Verifica se é um array de múltiplos lançamentos. A propriedade multi_lancamento não é mais necessária. Pode-se usar a propriedade lancamentos para múltiplos lançamentos.
        { 

            $params['lancamentos'] = json_decode(str_replace('\"','"',$params['lancamentos']), true);
            $paramsLancamento = $params['lancamentos'][0];
            unset($params['lancamentos'][0]);
            $qtdMultiplosLancamentos = count($params['lancamentos']);

        }
        elseif(isset($params['lancamentos'])) //Acessa próximo lançamento do array de múltiplos lançamentos. Opção alternativa à anterior.
        {
            
            $paramsLancamento = $params['lancamentos'][0];
            unset($params['lancamentos'][0]);
            $qtdMultiplosLancamentos = count($params['lancamentos']);

        }
        else //Apenas um lançamento enviado à API.
        {
            $paramsLancamento = $params['lancamento'];
        }

        //start: Instancia classe por tipo de lançamento
        if($paramsLancamento['tipo'] == 'R')
            $lancamento = new Recebimento($this->db, $paramsLancamento);
        elseif($paramsLancamento['tipo'] == 'P')
            $lancamento = new Pagamento($this->db, $paramsLancamento);
        else
            $lancamento = new Transferencia($this->db, $paramsLancamento);
        //end: Instancia classe por tipo de lançamento

        if($paramsLancamento['compensado'] == 0){
            $dt_compensacao = $lancamento->lancamento_dados['dt_compensacao'];
            $lancamento->lancamento_dados['dt_compensacao'] = "";
        }
        
        //Monta string com lançamento do centro de custo e plano de contas
        //Está ocorrendo algum problema no json_encode, que impede a inclusão do centro de custo e categoria para o lançamento
        if($paramsLancamento['tipo'] == 'R' || $paramsLancamento['tipo'] == 'P'){
            //$array_ctr_plc = array(
              //      'plano_contas_id' => $paramsLancamento['categoria_id'],
                //    'centro_resp_id' => $paramsLancamento['centro_custo_id'],
                  //  'porcentagem' => 100,
                    //'operacao' => 1
                //);
            //$paramsLancamento['ct_resp_lancamentos'] = json_encode($array_ctr_plc);
            $paramsLancamento['ct_resp_lancamentos'] = '[{"plano_contas_id":'.($paramsLancamento['categoria_id'] ? $paramsLancamento['categoria_id'] : 0).',"centro_resp_id":'.($paramsLancamento['centro_custo_id'] ? $paramsLancamento['centro_custo_id'] : 0).',"porcentagem":100,"operacao":1}]';
        }

        $qtd_parcelas = $paramsLancamento['qtd_parcelas'];

        //Lançamento não parcelado
        if( $qtd_parcelas == 0 || ($qtd_parcelas == 1 && !isset($paramsLancamento['valor_entrada'])) ){

            //a classe Recebimento.class utiliza quantidade 1 para lançamento não parcelado
            //para manter compatilbildiade com sistemas de terceiros, a quantidade um é atribuída aqui
            $paramsLancamento['qtd_parcelas'] = 1;

            $lancamentoId = $lancamento->lancamentoIncluir($this->db, $paramsLancamento);

            //Se forem múltiplos lançamentos, adicionar ao array para manter o registro durante a recursividade até que o resultado seja retornado pela API
            if(isset($params['lancamentos']))
                array_push($arrayIdMultiplosLancamentos,$lancamentoId);
            
        }
        else //Se for lançamento parcelado, verifica se há valor de entrada para compensar a primeira parcela
        { 
            //Se a propriedade valor_entrada existir, compensa lançamento de entrada e recalcula valor das demais parcelas
            if(isset($paramsLancamento['valor_entrada'])){

                //Adiciona uma parcela para considerar o lançamento de entrada como uma das parcelas
                $qtd_parcelas += 1;
                $paramsLancamento['qtd_parcelas'] = $qtd_parcelas;
                $lancamento->lancamento_dados['qtd_parcelas'] = $qtd_parcelas;

                //Insere valor da parcela nos parâmetros do lançamento pois é necessário na função de edição
                $paramsLancamento['valor_parcela'] = $this->db->valorFormat($lancamento->lancamento_dados['valor'] / $paramsLancamento['qtd_parcelas']);

                //Inclui todos os lançamentos em parcelas iguais
                $lancamentoId = $lancamento->lancamentoIncluir($this->db, $paramsLancamento);

                //Formata valor de entrada
                $valor_entrada = $this->db->valorToDouble($paramsLancamento['valor_entrada']);
                
                //Se valor de entrada for igual a zero ele recebe um valor igual ao das parcelas
                if($valor_entrada == 0){

                    $valor_entrada = $this->db->valorToDouble($paramsLancamento['valor']) / $qtd_parcelas;

                }else{

                    //Define o valor das demais parcelas
                    $valor_parcela = $this->db->valorToDouble($paramsLancamento['valor']) / ($qtd_parcelas - 1);

                    //Busca todas as parcelas incluídas com exceção da parcela de entrada
                    $parcelas = $this->db->fetch_all_array('select id from lancamentos where lancamento_pai_id = '.$lancamentoId.' and id <> '.$lancamentoId.' for update');
                    $array_parcela_id = array();
                    foreach($parcelas as $parcela){
                        array_push($array_parcela_id, $parcela['id']);
                    }
                    $parcelas_id = join(',',$array_parcela_id);

                    //Atualiza valor das parcelas
                    $this->db->query('update lancamentos set valor = '.$valor_parcela.' where id in ('.$parcelas_id.')');

                }

                //Compensa a parcela de entrada se compensar_entrada for igual a 1
                if( isset($paramsLancamento['compensar_entrada']) && $paramsLancamento['compensar_entrada'] == 1 )
                    $compensado = 1;
                else
                    $compensado = 0;

                $array_dados = array( 
                            'lancamento_id' => $lancamentoId,
                            'compensado' => $compensado,
                            'conta_id' => $lancamento->lancamento_dados['conta_id'],
                            'rcr' => false,
                            'ct_resp_lancamentos' => ''
                        );

                $lancamento->__construct($this->db, $this->db->fetch_assoc('select * from lancamentos where id = '.$lancamentoId));
                $lancamento->lancamento_dados['lancamento_pai_id'] = $lancamentoId;
                $lancamento->lancamento_dados['qtd_parcelas'] = $qtd_parcelas;
                $lancamento->lancamento_dados['parcela_numero'] = 1;
                $lancamento->lancamento_dados['valor'] = $valor_entrada;
                $lancamento->lancamento_dados['dt_emissao'] = $this->db->data_to_sql($paramsLancamento['dt_emissao']);
                $lancamento->lancamento_dados['dt_competencia'] = $this->db->data_to_sql("01/".$paramsLancamento['dt_competencia']);
                $lancamento->lancamento_dados['dt_vencimento'] = $this->db->data_to_sql($paramsLancamento['dt_vencimento']);
                $lancamento->lancamento_dados['dt_compensacao'] = $dt_compensacao;
                $lancamento->lancamento_dados['compensado'] = $compensado;
                $lancamento->lancamentoEditar($this->db,$array_dados);
            }
            else //Sem valor de entrada
            {
                //Insere valor da parcela nos parâmetros do lançamento pois é necessário na função de edição
                $paramsLancamento['valor_parcela'] = $this->db->valorFormat($lancamento->lancamento_dados['valor'] / $paramsLancamento['qtd_parcelas']);

                //Inclui todos os lançamentos em parcelas iguais
                $lancamentoId = $lancamento->lancamentoIncluir($this->db, $paramsLancamento);
            }

            //Insere id do lançamento de entrada, caso exista, e de todas as parcelas no resultado que será retornado pela API
            $lancamentosInseridos = $this->db->fetch_all_array("select id from lancamentos where lancamento_pai_id = $lancamentoId");
            foreach($lancamentosInseridos as $lancamentoInserido)
            {
                array_push($arrayIdMultiplosLancamentos,$lancamentoInserido["id"]);
            }
        }

        //Após a inclusão ter sido concluída
        //----------------------------------------------------------------------------------------------------------------------------------------------------

        //Chama a função recursivamente se ainda houver múltiplos lançamentos
        if($qtdMultiplosLancamentos > 0){

            $params = array('lancamentos' => array_values($params['lancamentos']), 'id_multiplos_lancamentos' => $arrayIdMultiplosLancamentos);
            self::Create($params);

            //Retorna id's da inclusão de múltiplos lançamentos
        }elseif(count($arrayIdMultiplosLancamentos)>0){

            echo json_encode(array('status' => 1, 'msg' => '', 'lancamento_id' => $arrayIdMultiplosLancamentos));

            //Retorna id da inclusão sem recursividade
        }else{

            if($lancamentoId)
                echo json_encode(array('status' => 1, 'msg' => '', 'lancamento_id' => $lancamentoId));
            else
                echo json_encode(array('status' => 0, 'msg' => 'Saldo insuficiente'));
        }

    }

    /**
     * Editar lançamento
     * @param mixed $params
     */
    function Edit ($params){

        try{
        
            $this->db->query('start transaction');

            $findLnct = $this->db->fetch_assoc("select tipo from lancamentos where id = ".$params['lancamento']['lancamento_id']);

            if($findLnct){
                
                //Monta string com lançamento do centro de custo e plano de contas
                //Á princípio, a categoria e o centro de custo que já estiverem no lançamento serão excluídos e os novos serão registrado.
                //Futuramente fazer a verificação dos que já estão cadastrados.
                if(($findLnct['tipo'] == 'R' || $findLnct['tipo'] == 'P') && ($params['lancamento']['categoria_id'] || $params['lancamento']['centro_custo_id'])){
                    $this->db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$params['lancamento']['lancamento_id']);
                    $params['lancamento']['ct_resp_lancamentos'] = '[{"plano_contas_id":'.($params['lancamento']['categoria_id'] ? $params['lancamento']['categoria_id'] : 0).',"centro_resp_id":'.($params['lancamento']['centro_custo_id'] ? $params['lancamento']['centro_custo_id'] : 0).',"porcentagem":100,"operacao":1}]';
                }

                //start: Busca todos os dados do lançamento para não ser necessário requisitar a API informando todos as propriedades
                $lancamentoClass = new Lancamento();
                $exibirLancamento = $lancamentoClass->lancamentosExibir($this->db,array("lancamento_id"=>$params['lancamento']['lancamento_id']));
                //end: Busca todos os dados do lançamento para não ser necessário requisitar a API informando todos as propriedades

                //start: Mescla os dados existentes do lançamento com os parâmetros enviados pelo cliente à API 
                $exibirLancamento = array_replace($exibirLancamento["lancamento"],$params["lancamento"]);
                //end: Mescla os dados existentes do lançamento com os parâmetros enviados pelo cliente à API 

                $params['lancamento']['tipo'] = $findLnct['tipo'];

                if($findLnct['tipo'] == 'R')

                    $lancamento = new Recebimento($this->db, $exibirLancamento);

                elseif($findLnct['tipo'] == 'P')

                    $lancamento = new Pagamento($this->db, $exibirLancamento);

                else

                    $lancamento = new Transferencia($this->db, $exibirLancamento);

                $editar = $lancamento->lancamentoEditar($this->db, $exibirLancamento);

                if($editar){
                
                    $this->db->query('commit');

                    echo json_encode(array('status' => 1, 'msg' => ''));
                
                }else{
                
                    throw new Exception('0');
                
                }

            }else{
                
                throw new Exception('-1');
                
            }        
        
        }catch(Exception $e){
            
            $this->db->query('rollback');

            if($e->getMessage() == '0')

                echo json_encode(array('status' => 0, 'msg' => 'Saldo insuficiente'));

            elseif($e->getMessage() == '-1')
    
                echo json_encode(array('status' => -1, 'msg' => 'Lançamento não encontrado'));

        }

    }

    /**
     * Excluir lançamento
     * @param mixed $params 
     */
    function Delete ($params){
        
        //Exclui múltiplos lançamentos
        if(is_array($params['lancamento_id'])){
            
            echo self::DeleteArray($params['lancamento_id']);
            
        //Exclui um lançamento e/ou suas parcelas
        }else{
        
            $findLnct = $this->db->fetch_assoc("select tipo from lancamentos where id = ".$params['lancamento_id']);

            if($findLnct){

                if($findLnct['tipo'] == 'R')
                    $lancamento = new Recebimento();
                elseif($findLnct['tipo'] == 'P')
                    $lancamento = new Pagamento();
                else
                    $lancamento = new Transferencia();

                if($params['excluir_parcelas'] == 1){
                    
                    $queryLnct = mysql_query('select id as lancamento_id from lancamentos where lancamento_pai_id = '.$params['lancamento_id'].' and compensado = 0', $this->db->link_id);

                    while($lnct = mysql_fetch_assoc($queryLnct)){
                        $lancamento->lancamentoExcluir($this->db, $lnct);
                    }

                    echo json_encode(array('status' => 1, 'msg' => ''));

                }else{
                    
                    $excluir = $lancamento->lancamentoExcluir($this->db, $params);

                    if($excluir)
                        echo json_encode(array('status' => 1, 'msg' => ''));
                    else
                        echo json_encode(array('status' => 0, 'msg' => 'Saldo insuficiente'));
                }
                
            }else{
                echo json_encode(array('status' => -1, 'msg' => 'Lançamento não encontrado'));
            }

        }
        
    }

    /**
     * Excluír múltiplos lançamentos
     * @param mixed $params
     */
    function DeleteArray($lancamentos){

        $this->db->query('start transaction');
        
        foreach($lancamentos as $id){

            try{

                $findLnct = $this->db->fetch_assoc("select tipo, compensado from lancamentos where id = ".$id);

                if($findLnct){

                    if($findLnct['compensado'] == 0){
                        
                        if($findLnct['tipo'] == 'R')
                            $lancamento = new Recebimento();
                        elseif($findLnct['tipo'] == 'P')
                            $lancamento = new Pagamento();
                        else
                            $lancamento = new Transferencia();

                        $excluir = $lancamento->lancamentoExcluir($this->db, array('lancamento_id' => $id));

                        //if(!$excluir){
                            //throw new Exception('0');
                            
                    }

                }else{

                    //echo json_encode(array('status' => -1, 'msg' => 'Lançamento não encontrado'));
                    
                    //throw new Exception('-1'); //Não lança exceção para lançamento não encontrado, pois, afinal, ele seria excluído

                }

            }catch(Exception $e){

                $this->db->query('rollback');

                $status = $e->getMessage();

                if($status == '0')
            
                    return json_encode(array('status' => 0, 'msg' => 'Saldo insuficiente'));
            
                //elseif($status == '-1')
            
                    //return json_encode(array('status' => -1, 'msg' => 'Lançamento não encontrado'));

            }

        }

        $this->db->query('commit');

        return json_encode(array('status' => 1, 'msg' => ''));
        
    }

    /**
     * Retorna lançamento pela id especificada
     * @param mixed $params 
     */
    function Get($params){

        $isLancamentoExist = $this->db->fetch_assoc('select id from lancamentos where id = '.$params['lancamento_id']);

        if($isLancamentoExist){
         
            $lancamento = Lancamento::lancamentosExibir($this->db, array('lancamento_id' => $params['lancamento_id'], 'tp_lancamento' => $params['tipo']));

            echo Controller::array_to_json(array('status' => 1, 'msg' => '', 'lancamento' => $lancamento['lancamento']));
        
        }else{
            
            echo Controller::array_to_json(array('status' => 0, 'msg' => 'Lançamento não encontrado.'));
        }
    }
}
