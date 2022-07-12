<?php
/**
 * @version 1.0
 * @author Rafael Vanzo
 * @Controller
 */

//Model
require_once('Models/Calendario.php');

//Controller Utilities (Padrão)
require_once('UtilitiesController.php');
require_once('Services/CategoriesService.php');

Class CalendarioController{

    public $db;
    public $dbUsuario;
    public $db_tabela;

function __construct($dbConnection)
{

    $this->db = DbConnection::GetDinamicConnection(); 

    $this->dbUsuario = DbConnection::GetConnection();


    $this->Config = ConfigService::GetConfiguracoes();

    $this->Util = new UtilitiesController($this->db);

    $this->db_tabela = 'agenda';
}
    
public function Index()
{
    $dbUsuario = $this->dbUsuario;
    /**Chama o Layout padrão */
    require_once 'Views/Shared/Layout.php';
    
}
 

/**
* ================================================================================= 
* ------------------------------------- VISUALIZAR --------------------------------  
* =================================================================================    
*/    

public function Visualizar($params)
{

    try{

        $id = str_replace('/','',$params['Id']);

        $where = ($params['Id'] > 0)? $where = " AND IdDoutor = ".$id : ''; 
        
        $retorno = $this->db->fetch_all_array('SELECT * FROM '.$this->db_tabela.' WHERE ( DataInicial BETWEEN "'.$params['start'].'" AND "'.$params['end'].'") AND Excluido = 0 '.$where);
       
        $agenda = array();


        foreach($retorno as $retorno)
        {

            $tpConsulta =  $this->db->fetch_assoc('SELECT Descricao FROM configConsultaProc WHERE Id ='.$retorno['IdConsulta']);
            $doutor =  $this->dbUsuario->fetch_assoc('SELECT Nome, Color FROM usuarios WHERE Id ='.$retorno['IdDoutor']);
            $paciente =  $this->db->fetch_assoc('SELECT nome FROM favorecidos WHERE id ='.$retorno['IdFavorecido']);

            array_push($agenda, array('id' => $retorno['Id'],  
                                        'title' => $tpConsulta['Descricao'].' | Paciente: '.$paciente['nome'].' ( Dr.: '.$doutor['Nome'].' ) ', 
                                        'start' => $retorno['DataInicial'], 
                                        'end' => $retorno['DataFinal'],
                                        'color' => $doutor['Color']));            
            
            }
            
 
        echo json_encode($agenda);    


    }catch(Exception $e){     


            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
        
        $this->db->close();

} 


/**
* ================================================================================= 
* ------------------------------------- CREATE ------------------------------------  
* =================================================================================  
*/    
public function Create($params)
{     
        
    try{                                                                                           
       
        $dados = new Calendario($params);  

        unset($dados->Fields['Id']);

        $this->db->query('start transaction');

            //Formata valor para o db
            $dados->Fields['Valor'] = $this->Util->FormatNumberDb($dados->Fields['Valor']);


            //Verficar intervalo da consulta
            $tempo = $this->db->fetch_assoc("SELECT Tempo From configConsultaProc WHERE Id =".$params['IdConsulta']);


            //Gerar data inicial e final com o intervalo de tempo da consulta 
            $data = self::IntervaloDatas($params['Data'], $tempo['Tempo'], $params['Horario']);

            $dados->Fields['DataInicial'] = $data['DataInicial'];
            $dados->Fields['DataFinal'] = $data['DataFinal'];


    	        //Add registro no db.
                $id = $this->db->query_insert($this->db_tabela, $dados->Fields); 
                

        $this->db->query('commit');

                                                                                               
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
        
        $this->db->close();
}

/**
* ================================================================================= 
* ------------------------------------- DETAILS -----------------------------------  
* =================================================================================  
*/   
/**
 * SELECT2: Preparar options para serem exibidos no detalhes
 * Preencha a Id, Nome da tabela, Nome do campo, Conexão com o banco  
 **/
public function Select2Details($id, $tabela, $nome, $db, $cpf = false)
{

    if($cpf == true)
        $dados_cpf = ' ,cpf_cnpj ';

        $retorno = $db->fetch_assoc('SELECT '.$nome.$dados_cpf.' FROM '.$tabela.' WHERE Id = '.$id);

        if($cpf == true)
            $retorno_cpf = ' ( '.$retorno['cpf_cnpj'].' ) ';

    return $id.'|'.$retorno[$nome].$retorno_cpf;

}



public function Details($params)
{
    
    try{         

        $id = $params['Id'];
    
            $retorno = $this->db->fetch_assoc('SELECT *, DATE_FORMAT(DataInicial, "%d/%m/%Y") as Data, DATE_FORMAT(DataInicial, "%H:%i:%s") as Horario FROM '.$this->db_tabela.' WHERE Id ='.$id);  

            //SELECT2: 
            $retorno['IdConsulta'] = self::Select2Details($retorno['IdConsulta'], 'configConsultaProc', 'Descricao', $this->db);
            $retorno['IdDoutor'] = self::Select2Details($retorno['IdDoutor'], 'usuarios', 'Nome', $this->dbUsuario);
            $retorno['IdFavorecido'] = self::Select2Details($retorno['IdFavorecido'], 'favorecidos', 'Nome', $this->db, true);

            if($retorno['IdResponsavel'] > 0){ 
                $retorno['IdResponsavel'] =  self::Select2Details($retorno['IdResponsavel'], 'favorecidos', 'Nome', $this->db, true); 
            }


            $retorno['Valor'] = $this->Util->FormatNumber($retorno['Valor']);

            

        echo json_encode($retorno);    

                                                            
    }catch(Exception $e){     

        
        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
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
    
    $categoria = $params;
    $tag = $params;

    $dados = new Calendario($params);                                                      

    $this->db->query('start transaction');

        //Formata valor para o db
        $dados->Fields['Valor'] = $this->Util->FormatNumberDb($dados->Fields['Valor']);    


        //Verficar intervalo da consulta
        $tempo = $this->db->fetch_assoc("SELECT Tempo From configConsultaProc WHERE Id =".$params['IdConsulta']);


         //Gerar data inicial e final com o intervalo de tempo da consulta 
         $data = self::IntervaloDatas($params['Data'], $tempo['Tempo'], $params['Horario']);

         $dados->Fields['DataInicial'] = $data['DataInicial'];
         $dados->Fields['DataFinal'] = $data['DataFinal'];

        
            //Verifica se a consulta foi reagendada
            $verificacao = $this->db->fetch_assoc('SELECT Observacao,  DATE_FORMAT(DataInicial, "%d/%m/%Y") as DataCompare FROM '.$this->db_tabela.' WHERE id ='.$params['Id']);


                if($params['Data'] !== $verificacao['DataCompare']){ 

                
                    $dados->Fields['Observacao'] .= ' ** Reagendamento: Do dia '.$verificacao['DataCompare'].' para '.$params['Data'].' ** ';

                    //Editar registros no db.
                    $this->db->query_update($this->db_tabela, $dados->Fields, 'id = '.$params['Id']);   

                        $dados->Fields['IdConsultaReagendada'] = $params['Id'];
                        $dados->Fields['Situacao'] = 3;
        
                        $this->db->query_insert('agendaReagendar', $dados->Fields); 

                }else{

                        //Editar registros no db.
                        $this->db->query_update($this->db_tabela, $dados->Fields, 'id = '.$params['Id']);                      

                }


                if($dados->Fields['Situacao'] == '1')
                {
                    self::ComunicarFinanceiro( $dados->Fields['Id'] );
                }

                //echo json_encode(array('situacao' => 0, 'Msg' => 'Algo deu errado!'));

        $this->db->query('commit'); 

                                                                                            
    }catch(Exception $e){     

        
    $this->db->query('rollback');

        $this->Util->LogErro($e->getMessage());  

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
            
        $dados = array('Excluido'=> 1 , 'DtExclusao' => date('Y-m-d H:i:s'));                      
        
        $this->db->query('start transaction');

            //Marca o registro como excluído.
            echo $params['Id'];
            $this->db->query_update($this->db_tabela, $dados, 'id = '.$params['Id']);               

        $this->db->query('commit');

                                                            
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->db->close();

}


/**
* ================================================================================= 
* --------------------------- INTERVALO DE DATAS DA AGENDA ------------------------  
* =================================================================================  
*/

public function IntervaloDatas($dataInicial, $tempo, $horario)
{

        $date = str_replace('/', '-', $dataInicial.' '.$horario);
        $dtInicial = date("Y-m-d H:i:s", strtotime($date));

		//$DataInicial = $newDate.' '.$horario;

        $DataFinal = date("Y-m-d H:i:s",strtotime("$dtInicial + $tempo minutes"));

    	$retorno = array('DataInicial' => $dtInicial, 'DataFinal' => $DataFinal);

		return $retorno;

}


/**
* ================================================================================= 
* ---------------------------------- FAVORECIDOS ----------------------------------  
* =================================================================================  
*/
public function Favorecidos($params)
{

    $query = (!empty($params['searchTerm']))? "SELECT id, nome, cpf_cnpj FROM favorecidos WHERE nome LIKE '".$params['searchTerm']."%' OR cpf_cnpj LIKE '".$params['searchTerm']."%' ORDER BY nome ASC" : "SELECT id, nome, cpf_cnpj FROM favorecidos ORDER BY nome ASC";

    $lista = $this->db->fetch_all_array($query);

    $retorno = array();

        foreach($lista as $lista)
        {
            array_push($retorno, array('id'=>$lista['id'], 'text' => $lista['nome']. " ( ".$lista['cpf_cnpj']." )"));
        }
    
    echo json_encode($retorno);
}



/**
* ================================================================================= 
* ------------------------------------- DOUTOR ------------------------------------  
* =================================================================================  
*/
public function Doutor($params)
{

    $query = (!empty($params['searchTerm']))? "SELECT id, nome FROM usuarios WHERE cliente_id = ".$_SESSION['cliente_id']."  AND nome LIKE '".$params['searchTerm']."%' AND Tipo = 2 AND Excluido = 0 ORDER BY nome ASC" : "SELECT id, nome FROM usuarios WHERE cliente_id = ".$_SESSION['cliente_id']." AND Tipo = 2 AND Excluido = 0 ORDER BY nome ASC";

    $lista = $this->dbUsuario->fetch_all_array($query);

    $retorno = array();

        foreach($lista as $lista)
        {
            array_push($retorno, array('id'=>$lista['id'], 'text' => $lista['nome']));
        }
    
    echo json_encode($retorno);
}




/**
* ================================================================================= 
* -------------------------- CONSULTAS / PROCEDIMENTOS ----------------------------  
* =================================================================================  
*/
public function configConsultaProc($params)
{
   
    $query = (isset($params['searchTerm']) && !empty($params['searchTerm']))? "SELECT Id, Descricao FROM configConsultaProc WHERE Descricao LIKE '".$params['searchTerm']."%' AND Excluido = 0 ORDER BY Descricao ASC" : "SELECT Id, Descricao FROM configConsultaProc WHERE Excluido = 0 ORDER BY Descricao ASC";

    $lista = $this->db->fetch_all_array($query);

    $retorno = array();

        foreach($lista as $lista)
        {
            array_push($retorno, array('id'=>$lista['Id'], 'text' => $lista['Descricao']));
        }
    
    echo json_encode($retorno);
   
}

/**
* ================================================================================= 
* ------------------------- SELECT HORARIOS DISPONIVEIS ---------------------------  
* =================================================================================  
*/

public function CalculoHorario($params)
{
/*
    $params['data'] = '05/06/2019';
    $params['idTipoConsulta'] = 3;
    $params['idDoutor'] = 1384;
    $params['idConsulta'] = NULL;
*/

        //Tempo configuração procedimento/consulta
        $tempo = $this->db->fetch_assoc('SELECT Tempo FROM configConsultaProc WHERE Id ='.$params['idTipoConsulta']);
        $tempo = $tempo['Tempo'] - 1;

        if($params['idConsulta'] != NULL || $params['idConsulta'] != '')
        {
           
            //Verifica qual é a data e o horario da consulta já salva para não invalidar essa data.
            $dataConsulta = $this->db->fetch_assoc('SELECT idDoutor, DATE_FORMAT(DataInicial, "%Y-%m-%d") as DataIni, DATE_FORMAT(DataInicial, "%H:%i:%s") as Horario FROM agenda WHERE Id ='.$params['idConsulta'].' AND Excluido = 0 ');
            
            $data = ' IdDoutor = '.$dataConsulta['idDoutor'].' AND DATE(DataInicial) = "'.$dataConsulta['DataIni'].'" AND TIME(DataInicial) = "'.$dataConsulta['Horario'].'"';

            //Diminui o tempo da configuração do procedimento no horário de inicio da consulta
            $date = (new DateTime($dataConsulta['Horario'], new DateTimeZone( 'America/Sao_Paulo')))->modify("-".$tempo." minutes");
            $horarioConsulta = $date->format('H:i:s'); 


        }else{
           
            $dataFormatada = $this->Util->FormatDateDB($params['data']);
            
            $data = ' IdDoutor = '.$params['idDoutor'].' AND DATE(DataInicial) = "'.$dataFormatada.'"';

            $horarioConsulta = '';
    
        }

        /**===================================================== */

            //$tempo['Tempo']$tempo = $this->db->fetch_assoc('SELECT Tempo FROM configConsultaProc WHERE Id ='.$params['idTipoConsulta']);

            $horarios = self::Intervalo("06:00:00", "23:00:00", 10); //$tempo['Tempo']

        /**===================================================== */

            $horarioIndiposnivel = $this->db->fetch_all_array('SELECT DATE_FORMAT(DataInicial, "%H:%i:%s") as horarioInicial, DATE_FORMAT(DataFinal, "%H:%i:%s") as horarioFinal FROM agenda WHERE '.$data.' AND Excluido = 0 ');

            //Se o intervalo de tempo entre as consultas for menor do que 10 minutos ele faz o calculo para diminuir o tempo do procedimento do inicio da consulta.
            if($tempo > 10)
                $horarioIndiposnivel = self::LiberarHorariosAnterior($horarioIndiposnivel, $tempo); //$tempo['Tempo']
            
            foreach($horarioIndiposnivel as $ho)
            {   

                foreach($horarios as $key => $value)
                {
                   
                    if(self::diferencaMinutos($ho['horarioInicial'], $ho['horarioFinal'], $value))
                    {
                        if($horarioConsulta != $ho['horarioInicial'])
                            unset($horarios[$key]);
                        
                    }               
                }

            }

 
                $retorno = '';
                
                foreach($horarios as $horas)
                {

                    $retorno .="<option value='".$horas."'>".$horas."</option>"; 

                }

                
        echo $retorno;
        

}


public function CalculoHorarioTeste($params)
{

    $params['data'] = '04/06/2020';
    $params['idTipoConsulta'] = 6;
    $params['idDoutor'] = 1549;
    $params['idConsulta'] = ""; //66
 /**/

        //Tempo configuração procedimento/consulta
        $tempo = $this->db->fetch_assoc('SELECT Tempo FROM configConsultaProc WHERE Id ='.$params['idTipoConsulta']);
        $tempo = $tempo['Tempo'] - 1;

        if($params['idConsulta'] != NULL || $params['idConsulta'] != '')
        {
           
            //Verifica qual é a data e o horario da consulta já salva para não invalidar essa data.
            $dataConsulta = $this->db->fetch_assoc('SELECT idDoutor, DATE_FORMAT(DataInicial, "%Y-%m-%d") as DataIni, DATE_FORMAT(DataInicial, "%H:%i:%s") as Horario FROM agenda WHERE Id ='.$params['idConsulta'].' AND Excluido = 0 ');
            
            //$data = ' IdDoutor = '.$dataConsulta['idDoutor'].' AND DATE(DataInicial) = "'.$dataConsulta['DataIni'].'" AND TIME(DataInicial) = "'.$dataConsulta['Horario'].'"';
            $data = ' IdDoutor = '.$dataConsulta['idDoutor'].' AND DATE(DataInicial) = "'.$dataConsulta['DataIni'].'"';
            
            //Diminui o tempo da configuração do procedimento no horário de inicio da consulta
            $date = (new DateTime($dataConsulta['Horario'], new DateTimeZone( 'America/Sao_Paulo')))->modify("-".$tempo." minutes");
            $horarioConsulta = $date->format('H:i:s'); 


        }else{
           
            $dataFormatada = $this->Util->FormatDateDB($params['data']);
            
            $data = ' IdDoutor = '.$params['idDoutor'].' AND DATE(DataInicial) = "'.$dataFormatada.'"';

            $horarioConsulta = '';
    
        }

        /**===================================================== */            

            $horarios = self::Intervalo("06:00:00", "23:00:00", 10);//$tempo['Tempo']

        /**===================================================== */

            $horarioIndiposnivel = $this->db->fetch_all_array('SELECT DATE_FORMAT(DataInicial, "%H:%i:%s") as horarioInicial, DATE_FORMAT(DataFinal, "%H:%i:%s") as horarioFinal FROM agenda WHERE '.$data.' AND Excluido = 0 ');
           
            //Se o intervalo de tempo entre as consultas for menor do que 10 minutos ele faz o calculo para diminuir o tempo do procedimento do inicio da consulta.
            if($tempo > 10)
                $horarioIndiposnivel = self::LiberarHorariosAnterior($horarioIndiposnivel, $tempo); //$tempo['Tempo']

      

            foreach($horarioIndiposnivel as $ho)
            {                  

                foreach($horarios as $key => $value)
                {
                   
                    if(self::diferencaMinutos($ho['horarioInicial'], $ho['horarioFinal'], $value))
                    {
                        echo $horarios[$key];
                        if($horarioConsulta != $ho['horarioInicial'])
                             unset($horarios[$key]);
                    }               
                }

            }

 
                $retorno = '';
                
                foreach($horarios as $horas)
                {

                    $retorno .="<option value='".$horas."'>".$horas."</option>"; 

                }

                
        echo $retorno;
        

}

// Função que adiciona o intervalo de tempo a mais antes do horário inicial, se o intervalo entre os procedimentos for maior do que 10 minutos.
public function LiberarHorariosAnterior($horarioIndiposnivel, $tempo)
{
    var_dump($tempo);
    foreach($horarioIndiposnivel as $key => $val)
    {
        $date = (new DateTime($horarioIndiposnivel[$key]['horarioInicial'], new DateTimeZone( 'America/Sao_Paulo')))->modify("-$tempo minutes");
        $horarioIndiposnivel[$key]['horarioInicial'] = $date->format('H:i:s');        
    }

    return $horarioIndiposnivel;
}

public function teste()
{
    $tempo = 60;
    $horarioInicial[0]['horarioInicial'] = '06:00:00';
    $teste = self::LiberarHorariosAnterior($horarioInicial, $tempo);

    var_dump($teste);
    //$date = (new DateTime($horarioInicial, new DateTimeZone( 'America/Sao_Paulo')))->modify("-".$tempo." minutes");
    //   echo $date->format('H:i:s');  
}

public function Intervalo($de, $ate, $minutos) {

    $retorno = array();

    $start = explode(':', $de);

    for($f = 0; $f<1000; $f++)
    {		
        $i = $minutos * $f;	
        $horas = date("H:i:s", mktime($start[0], $start[1]+$i, $start[2], 0, 0, 0));
        $retorno[$horas] = $horas;

        /* Para o for se o horário for = a 22h */
        if($horas == '22:00:00') { break; }
    }

    
    return $retorno;
}


public function diferencaMinutos($start, $end, $current)
{

    $currentTime = new DateTime($current, new DateTimeZone( 'America/Sao_Paulo'));
    $startTime = new DateTime($start, new DateTimeZone( 'America/Sao_Paulo'));
    $endTime = (new DateTime($end, new DateTimeZone( 'America/Sao_Paulo')))->modify("-1 minutes");

    if ($currentTime >= $startTime && $currentTime <= $endTime) {
     return true;
    }else{
     return false;
    }

}


/**
* ================================================================================= 
* --------------------------------- SELECT OPTIONS --------------------------------  
* =================================================================================  
*/
public static function SelectOption($tabela, $campo)
{
    $dados = $this->db->fetch_all_array('SELECT Id, '.$campo.' FROM '.$tabela.' WHERE Excluido = 0');
    
    $retorno = '';

        foreach($dados as $dados)
        {
            $retorno .= '<option value="'.$dados['Id'].'">'.$dados[$campo].'</option>';
        }
    
        return $retorno;

}

/**
* ================================================================================= 
* --------------------------------- SELECT OPTIONS --------------------------------  
* =================================================================================  
*/

public static function doutorListar($dbUsuario)
{
    $dados = $dbUsuario->fetch_all_array('SELECT id, nome FROM usuarios WHERE cliente_id = '.$_SESSION['cliente_id'].' AND Excluido = 0 AND Tipo = 2');
    
    $retorno = '<option value="">Todos</option>';

        foreach($dados as $dados)
        {
            $retorno .= '<option value="'.$dados['id'].'">'.$dados['nome'].'</option>';
        }
    
        return $retorno;
}


/**
* ================================================================================= 
* --------------------------------- SELECT OPTIONS --------------------------------  
* =================================================================================  
*/

public function ComunicarFinanceiro($idConsulta)
{
    
    $dados = $this->db->fetch_assoc('SELECT DATE_FORMAT(DataInicial, "%Y-%m-%d") AS DataInicial, Id, IdFavorecido, IdResponsavel, IdDoutor, IdConsulta, TipoPlano, IdLancamento FROM '.$this->db_tabela.' WHERE Id = '.$idConsulta);

    if($dados['IdLancamento'] == 0)
    {       
        
        $doutor = $this->dbUsuario->fetch_assoc('SELECT nome FROM usuarios WHERE id = '.$dados['IdDoutor']);

        $consulta = $this->db->fetch_assoc('SELECT Descricao, Valor FROM configConsultaProc WHERE id = '.$dados['IdConsulta']);

        $tipoPlano = ($dados['TipoPlano'] == 1)? 'Particular' : 'Plano de saúde';

        $descricao = $doutor['nome'].' - '.$consulta['Descricao'].' - '.$tipoPlano.' ( Lanc. Agenda )';

        $comp = explode('-', $dados['DataInicial']);
        $competencia = $comp['0'].'-'.$comp['1'].'-01';

        $conta_id = $this->db->fetch_assoc('SELECT id FROM contas ORDER BY id ASC LIMIT 0,1');

        $dados = array('descricao' => '** '.$descricao,                        
                       'dt_emissao' => $dados['DataInicial'], 
                       'dt_vencimento' => $dados['DataInicial'], 
                       'favorecido_id' => $dados['IdFavorecido'], 
                       'favorecido_id_dep' => $dados['IdResponsavel'],
                       'parcela_numero' => 1, 
                       'dt_competencia' => $competencia, 
                       'valor' => $consulta['Valor'],
                       'compensado' => '0', 
                       'tipo' => 'R',
                       'lancamento_pai_id' => '',
                       'lancamento_recorrente_id' => '',
                       'auto_lancamento' => 'M',
                       'dt_venc_ref' => '',
                       'qtd_parcelas' => '1',
                       'forma_pgto_id' => '0',
                       'conta_id' => $conta_id['id'],
                       'conta_id_origem' => '0',
                       'conta_id_destino' => '0',
                       'documento_id' => '0',
                       'frequencia' => '0',
                       'dt_compensacao' => '0000-00-00',
                       'observacao' => '',
                       'frequencia' => '0' );

        $id = $this->db->query_insert('lancamentos', $dados);

        $this->db->query_update($this->db_tabela, array('IdLancamento' => $id), ' Id ='.$idConsulta);

    }

}


/**
* ================================================================================= 
* ---------------------------------- ANIVERSÁRIOS ---------------------------------  
* =================================================================================  
*/    
public function Aniversario($params)
{    
    $w = date('W') - 1;
    $nivers = $this->db->fetch_all_array('SELECT nome, DATE_FORMAT(dtNascimento, "%d/%m/%Y") AS dtNascimento, telefone, celular, email FROM favorecidos WHERE WEEK(dtNascimento) = "'.$w.'" AND YEAR(dtNascimento) = "'.date('Y').'"');
    
    $retorno = '<table width="100%"><tr><th width="55%">Nome / E-mail</th><th width="15%">Data</th><th width="15%">Telefone</th><th width="15%">Celular</th></tr>';

    foreach($nivers as $niver)
    {
        $retorno .= '<tr><td>'.$niver['nome'].' <br> '.$niver['email'].'</td><td>'.$niver['dtNascimento'].'</td><td>'.$niver['ntelefoneome'].'</td><td>'.$niver['celular'].'</td></tr>';
    }

    $retorno .= '</table>';


    echo json_encode(array('lista'=>$retorno));

}


/**
* ================================================================================= 
* ------------------------------- TOTAL ANIVERSARIOS ------------------------------  
* =================================================================================  
*/    
public function TotalAniversario($params)
{    
    $w = date('W') - 1;
    
    $nivers = $this->db->fetch_assoc('SELECT count(id) as num FROM favorecidos WHERE WEEK(dtNascimento) = "'.$w.'" AND YEAR(dtNascimento) = "'.date('Y').'"');

    if($nivers['num'] > 0)
    {
        $retorno = '<span class="badge">'.$nivers['num'].'</span>';
    }else{
        $retorno = '';
    }


    echo json_encode(array('html'=>$retorno));

}

/**
* ================================================================================= 
* ----------------------- VEIRIFICAR CPF CADASTRO FAVORECIDO ----------------------  
* =================================================================================    
*/    

public function VerifyCadastroCPF($params)
{
    if($params['id'] != 0)
        $dados_consutla = $this->db->fetch_assoc('SELECT IdFavorecido, IdResponsavel, Situacao FROM agenda WHERE id ='.$params['id']);
    else
        $dados_consutla['Situacao'] = 0;

    /**
     * Verifica se o responsável existe e valida se o cpf esta cadastrado e se o responsável esta preenchido na consutla. 
     * Se o responsável não estiver cadastrado ele ignora a verificação
     */
    if($params['IdResponsavel'] != 0){

    
        $cpf = $this->db->fetch_assoc('SELECT cpf_cnpj, nome FROM favorecidos WHERE id = '.$params['IdResponsavel']);

            if($cpf == true && ( $cpf['cpf_cnpj'] !== '000.000.000-00' && !empty($cpf['cpf_cnpj'])) )
                $responsavel = true;
            else
                $responsavel = false;    

    }else{

        $responsavel = true;
    
    }


    /**
     * Verifica se o favorecido existe e valida se o cpf esta cadastrado 
     */
    if($params['IdFavorecido'] != 0){

    
        $cpf_favorecido = $this->db->fetch_assoc('SELECT cpf_cnpj, nome FROM favorecidos WHERE id = '.$params['IdFavorecido']);

            if($cpf_favorecido == true && ( $cpf_favorecido['cpf_cnpj'] !== '000.000.000-00' && !empty($cpf_favorecido['cpf_cnpj'])) )
                $favorecido = true;
            else
                $favorecido = false;
    

    }else{

        $favorecido = true;
    
    }

    echo json_encode(array( 'responsavel' => $responsavel, 
                            'resp_id' => $dados_consutla['IdResponsavel'],
                            'resp_nome' => $cpf['nome'],
                            'resp_cpf' => $cpf['cpf_cnpj'],
                            'favorecido' => $favorecido, 
                            'fav_id' => $dados_consutla['IdFavorecido'],
                            'fav_nome' => $cpf_favorecido['nome'],
                            'fav_cpf' => $cpf_favorecido['cpf_cnpj'],
                            'Situacao' => $dados_consutla['Situacao']));

}


} /* Fim Classe */  
?>