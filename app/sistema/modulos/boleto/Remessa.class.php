<?php
require_once 'RemessaBb.class.php';
require_once 'RemessaCaixa.class.php';
require_once 'RemessaSicoob.class.php';

class Remessa{
    
    public $banco;
    
    function __construct($codBanco){
        
        switch($codBanco){
            case '001';
                $this->banco = new RemessaBb();
                break;
            case '104'; 
                $this->banco = new RemessaCaixa();
                break;
            case '756'; 
                $this->banco = new RemessaSicoob();
                break;
        }
    }
    
    //Funчуo remove os . - /  do CPF e CNPJ
    function limpaCPF_CNPJ($valor){
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        return $valor;
    }
}
?>