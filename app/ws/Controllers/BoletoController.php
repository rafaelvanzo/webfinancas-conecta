<?php
require_once(ROOT_MODULOS.'/lancamento/class/Lancamento.class.php');
require_once(ROOT_MODULOS.'/lancamento/class/Recebimento.class.php');
require_once(ROOT_MODULOS.'/boleto/class/Boleto.class.php');
require_once(ROOT_MODULOS.'/boleto/class/Boleto.Banestes..class.php');
require_once(ROOT_MODULOS.'/boleto/class/Boleto.Bb.class.php');
require_once(ROOT_MODULOS.'/boleto/class/Boleto.Cef.class.php');
require_once(ROOT_MODULOS.'/boleto/class/Boleto.Santander..class.php');
require_once(ROOT_MODULOS.'/boleto/class/Boleto.Sicoob.class.php');

/**
 * BoletoController short summary.
 *
 * BoletoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class BoletoController
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
     * Criar boleto
     * @param mixed $params 
     */
    function Create($params){
        
        //buscar conta_id e codigo do banco
        $queryString = '
            select c.id, b.codigo
            from lancamentos l 
            join contas c on l.conta_id = c.id 
            join bancos b on c.banco_id = b.id 
            where l.id = '.$params['lancamento_id'];
        
        $conta = $this->db->fetch_assoc($queryString);

        $params['conta_id'] = $conta['id'];
        $params['cod_banco'] = $conta['codigo'];

        //Gera chave do boleto
        $recebimento = new Recebimento();

        $chave = $recebimento->boletosChaveGerar($this->db,$params,$params['cliente_id']);

        if(!$chave)
            echo json_encode(array('status'=>0,'msg'=>'Banco indisponível para emissão de boleto.'));
        else
            //echo json_encode(array('status'=>1,'msg'=>'','link'=>'https://www.webfinancas.com/boleto/'.$chave.'/1'));
            echo Controller::array_to_json(array('status'=>1,'msg'=>'','link'=>'https://www.webfinancas.com/boleto/'.$chave.'/1'));
    }

    /**
     * Gerar campo nosso número do boleto
     * baseando-se no sequencial enviado pela requisição
     * @param mixed $params 
     */
    function GerarNossoNumero($params){

    }
}
