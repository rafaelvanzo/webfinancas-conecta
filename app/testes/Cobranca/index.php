<?php
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/php/Database.class.php";
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/controllers/cobrancaController.php";
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/servicos/mensagem/MensagemHelper.php";

/**
 * Summary of VerificarConfiguracoes
 * @return Exception|string
 */
function Verificar_Configuracoes()
{
    try
    {
        //Arrange
        $configuracaoHelper = new ConfiguracaoHelper();

        //Act
        $configuracao = $configuracaoHelper->configuracoes;

        //Assert
        if(count($configuracao) > 0)
            $assert = true;
        else
            $assert = false;
    }
    catch(Exception $e)
    {
        $assert = false;
    }

    return $assert;
}

/**
 * Summary of ConectarComBancosDeDados
 */
function Conectar_Com_Banco_De_Dados_Principal_Da_W2b()
{
    //Arrange
    $cobranca = new CobrancaController();

    //Act
    $cobranca->ConectarDbW2b();

    //Assert
    if($cobranca->dbW2b->link_id)
        return true;
    else
        return false;
}

/**
 * Summary of ConectarComBancosDeDadosWf
 */
function Conectar_Com_Banco_De_Dados_Principal_Do_Wf()
{
    //Arrange
    $cobranca = new CobrancaController();

    //Act
    $cobranca->ConectarDbWf();

    //Assert
    if($cobranca->dbWf->link_id)
        return true;
    else
        return false;
}

/**
 * Summary of ConectarComBancosDeDadosWfVmSistemas
 */
function Conectar_Com_Banco_De_Dados_Da_Vm_Sistemas()
{
    //Arrange
    $cobranca = new CobrancaController();

    //Act
    $cobranca->ConectarDbVmSistemas();

    //Assert
    if($cobranca->dbVmSistemas->link_id)
        return true;
    else
        return false;
}

/**
 * Summary of InstanciarClasseCobranca
 * @return Exception|string
 */
function Instanciar_Classe_Cobranca()
{
    //Act
    $cobranca = new CobrancaController();

    //Assert
    if($cobranca)
        $assert = true;
    else
        $assert = false;

    return $assert;
}

function Retornar_Conta_Financeira_Da_Vm_Sistemas()
{
    //Arrange
    $cobranca = new CobrancaController();

    //Act
    $contaFinanceira = $cobranca->GetContaFinanceiraVmSistemas();
    
    //Assert
    if($contaFinanceira)
        $assert = true;
    else
        $assert = false;

    return $assert;
}

function Enviar_Boleto_De_Cobranca_Ao_Cliente()
{
    $mensagem = array(
        "view" => "_Cobranca.php",
        "inicioVigencia" => "12/03/2018",
        "fimVigencia" => "11/04/2018",
        "periodo" => "Mensal",
        "servicosContratados" => array(array("nome"=>"Web Finanças","valor"=>"59.90")),
        "faturasAtrasadas" => array(
            array("sequencial" => "64", "lancamento_id" => "1180", "dt_vencimento"=>"2018-02-05","valor"=>"59.90"),
            array("sequencial" => "64", "lancamento_id" => "1180", "dt_vencimento"=>"2018-02-05","valor"=>"59.90"),
            array("sequencial" => "64", "lancamento_id" => "1180", "dt_vencimento"=>"2018-02-05","valor"=>"59.90"),
            array("sequencial" => "64", "lancamento_id" => "1180", "dt_vencimento"=>"2018-02-05","valor"=>"59.90")),
        "chave" => "123",
        "valorTotal" => "600"
        );
    print_r($mensagem);
    //$mensagemHelper = new MensagemHelper();
    //$act = $mensagemHelper->EnviarEmail("fabio@web2business.com.br","Relacionamento Web Finanças",$mensagem);
    
    //return $act->status;
}

function ExecutarTeste($funcao)
{
    $executar = call_user_func($funcao);

    if(!$executar)
    {
        echo "$funcao: ERRO";
        break;
    }
    
    echo "$funcao: OK <br>";
}

//ExecutarTeste("Verificar_Configuracoes");
//ExecutarTeste("Conectar_Com_Banco_De_Dados_Principal_Da_W2b");
//ExecutarTeste("Conectar_Com_Banco_De_Dados_Principal_Do_Wf");
//ExecutarTeste("Conectar_Com_Banco_De_Dados_Da_Vm_Sistemas");
//ExecutarTeste("Instanciar_Classe_Cobranca");
//ExecutarTeste("Retornar_Conta_Financeira_Da_Vm_Sistemas");
ExecutarTeste("Enviar_Boleto_De_Cobranca_Ao_Cliente");
?>