<?php
/**
 * @version 1.0
 * @author Rafael Vanzo
 * @Controller
 */

//Model
require_once('Models/Pacientes.php');

//Controller Utilities (Padrão)
require_once('UtilitiesController.php');
require_once('Services/CategoriesService.php');

Class relatoriosController{

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
    
public static function Index()
{
    
    /**Chama o Layout padrão */
    require_once 'Views/Shared/Layout.php';
    
}
 
/**
* ================================================================================= 
* -------------------------------- GERAR RELATÓRIO --------------------------------  
* =================================================================================    
*/    

public function Gerar($params)
{

    $where = self::Filtro($params); 
    
    $dados = $this->db->fetch_all_array('SELECT DATE_FORMAT(DataInicial, "%d/%m/%Y %H:%i:%s") AS DataInicial, Id, IdFavorecido, IdResponsavel, IdDoutor, IdConsulta, TipoPlano, Situacao FROM '.$this->db_tabela.$where.' ORDER BY DataInicial, IdDoutor');

        $retorno = '';

        foreach($dados as $dados)
        {


            $paciente = $this->db->fetch_assoc('SELECT nome FROM favorecidos WHERE id = '.$dados['IdFavorecido']);

            $responsavel = $this->db->fetch_assoc('SELECT nome FROM favorecidos WHERE id = '.$dados['IdResponsavel']);

            $doutor = $this->dbUsuario->fetch_assoc('SELECT nome FROM usuarios WHERE id = '.$dados['IdDoutor']);

            $consulta = $this->db->fetch_assoc('SELECT Descricao FROM configConsultaProc WHERE id = '.$dados['IdConsulta']);

            $tipoPlano = ($dados['TipoPlano'] == 1)? 'Particular' : 'Plano de saúde';


                $retorno .='<tr>
                                <td>'.$dados['DataInicial'].'</td>
                                <td>'.$paciente['nome'].'</td>
                                <td>'.$consulta['Descricao'].'</td>
                                <td>'.$tipoPlano.'</td>
                                <td>'.$responsavel['nome'].'</td>                                
                                <td>'.$doutor['nome'].'</td>
                                <td>'.self::Situacao($dados['Situacao'], $dados['Id']).'</td>
                            </tr>';

        }

        (empty($retorno))? $retorno = '<tr><td colspan="7" align="center"> NÃO EXITEM CONSULTAS / PROCEDIMENTOS NO PERÍODO. </td></tr>' : '' ;


    echo $retorno;    

}


private function Filtro($params)
{

    $dtInicial = self::ConvertDataSQL($params['DataInicial']);

    unset($params['DataInicial']);

    $dtFinal = self::ConvertDataSQL($params['DataFinal']);

    unset($params['DataFinal']);


        $retorno = ' WHERE DATE(DataInicial) BETWEEN ("'.$dtInicial.'") AND ("'.$dtFinal.'") AND ';


       $array = self::ClearArray($params);
       
            foreach($array as $key => $value)
            {

                $retorno .= $key.'= '.$value.' AND ';

            }

         return $retorno .= 'Excluido = 0';

}


private function ClearArray($params)
{

    foreach($params as $key => $value)
    {

        if($value != '0' && empty($value))
            unset($params[$key]);

    }
    
    return $params;
}


private function ConvertDataSQL($params)
{

    $date = str_replace('/', '-', $params);

    $retorno = date("Y-m-d", strtotime($date));

    return $retorno;
}


private function Situacao($params, $id)
{

    switch($params)
    {
        case 0:
            return 'Aguardando'; 
            break;
         case 1:
            return 'Atendido'; 
            break;
         case 2:
            return 'Faltou'; 
            break;
         case 3:
            return '<a href="javascript://" onClick="javacsript:ModalList('.$id.');" >Reagendada</a>'; 
            break;
    }

}


public function ListReagendadas($params)
{

    $dados = $this->db->fetch_all_array('SELECT *, DATE_FORMAT(DataInicial, "%d/%m/%Y %H:%i:%s") AS DataInicial FROM agendaReagendar WHERE IdConsultaReagendada ='.$params['Id'].' ORDER BY DataInicial');


    $retorno = '';

        foreach($dados as $dados)
        {

            
        $paciente = $this->db->fetch_assoc('SELECT nome FROM favorecidos WHERE id = '.$dados['IdFavorecido']);

        $consulta = $this->db->fetch_assoc('SELECT Descricao FROM configConsultaProc WHERE id = '.$dados['IdConsulta']);

        $tipoPlano = ($dados['TipoPlano'] == 1)? 'Particular' : 'Plano de saúde';


            $retorno .='<tr>
                            <td>'.$dados['DataInicial'].'</td>
                            <td>'.$paciente['nome'].'</td>
                            <td>'.$consulta['Descricao'].'</td>
                            <td>'.$tipoPlano.'</td>          
                        </tr>';

        }

    echo $retorno;
}



} /* Fim Classe */  
?>