<?php

/**
 * Lancamento short summary.
 *
 * Lancamento description.
 *
 * @version 1.0
 * @author Fabio
 */

class Lancamento
{
    public $id;
    public $tipo;
    public $descricao;
    public $lancamento_pai_id;
    public $lancamento_recorrente_id;
    public $parcela_numero;
    public $qtd_parcelas;
    public $favorecido_id;
    public $forma_pgto_id;
    public $conta_id;
    public $conta_id_origem;
    public $conta_id_destino;
    public $documento_id;
    public $valor;
    public $frequencia;
    public $auto_lancamento;
    public $observacao;
    public $dt_emissao;
    public $dt_vencimento;
    public $sab_dom;
    public $dt_venc_ref;
    public $dt_compensacao;
    public $compensado;
    public $dt_competencia;
    public $mei_outros;
    public $fields = array();

    function __construct($params){
        $vars = get_class_vars(get_class($this));
        foreach($vars as $key => $value){
            if(array_key_exists(strtolower($key),$params) && $params[strtolower($key)] != ''){
                $this->fields[$key] = $params[strtolower($key)];
            }
        }
    }
}
