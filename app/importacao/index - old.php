<?php

require_once 'Database.class.php';
require_once 'excel_reader2.php';
require_once 'Categoria.php';

//conecta ao banco principal da w2b
$dbW2b = new Database("mysql.web2business.com.br", 'web2business', "W2BSISTEMAS", 'web2business');

//conecta ao banco principal do web finanças
$dbWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

//cliente_id da contabilidade à qual os clientes pertencem
$parceiro_id = 244;

//Ler excel com lista de clientes e inserir no banco de dado da W2B
//-----------------------------------------------------------------------------------------

$array_erros = array();

$data = new Spreadsheet_Excel_Reader("clientes.xls");

$linhas = $data->rowcount();

//$colunas= $data->colcount();

$arrayClientesAgrupados = array();

$grupoEconomicoQtd = 1;

$dbWfCli = 0;

try{

    echo 'Importação iniciada '.date('Y-m-d H:i:s').' <br>';

    for($i=2;$i<=4;$i++){

        try{

            $dbW2b->query('start transaction');
            $dbWf->query('start transaction');

            $nome = trim($data->val($i,2));

            if($nome!=''){

                //verifica se cliente já está cadastrado
                $cadastrado = trim($data->val($i,1)) * 1;

                if($cadastrado != 1){

                    //cadastra cliente na w2b
                    $array_cadastro = array();

                    $array_cadastro['situacao'] = 1;
                    $array_cadastro['parceiro_id'] = 244;

                    $array_cadastro['nome'] = $data->val($i,2);
                    $array_cadastro['tp_cadastro'] = 1;

                    if($data->val($i,17) == 'J'){
                        $array_cadastro['inscricao'] = 'CNPJ';
                        $array_cadastro['cpf_cnpj'] = $data->val($i,3);
                    }elseif($data->val($i,17) != 'F'){
                        $array_cadastro['inscricao'] = 'CPF';
                        $array_cadastro['cpf_cnpj'] = $data->val($i,6);
                    }

                    $array_cadastro['email'] = $data->val($i,8);
                    $array_cadastro['telefone'] = $data->val($i,4);
                    $array_cadastro['celular'] = $data->val($i,5);
                    
                    $array_cadastro['logradouro'] = $data->val($i,10);
                    $array_cadastro['numero'] = $data->val($i,11);
                    $array_cadastro['complemento'] = $data->val($i,12);
                    $array_cadastro['bairro'] = $data->val($i,13);
                    $array_cadastro['uf'] = $data->val($i,14);
                    $array_cadastro['cidade'] = $data->val($i,15);
                    $array_cadastro['cep'] = $data->val($i,16);

                    $array_cadastro['dt_cadastro'] = date('Y-m-d H:i:s');
                    
                    //if(!$dbW2b->query_insert('clientes',$array_cadastro))
                    //  throw new Exception('Erro ao inserir cliente', 1);
                    $dbW2b->query_insert('clientes',$array_cadastro);
                    if(!$cliente_id = mysql_insert_id($dbW2b->link_id))
                        throw new Exception('Erro ao inserir cliente', 1);

                    array_push($arrayClientesAgrupados,$cliente_id);

                    //busca um banco de dados no web finanças para alocar os registros do cliente
                    $cliente_db = $dbWf->fetch_assoc('select min(id) id, db from clientes_db where situacao = 0');
                    if(!$dbWf->query('update clientes_db set cliente_id = '.$cliente_id.', situacao = 1 where id = '.$cliente_db['id']))
                        throw new Exception('Erro ao selecionar banco de dados para o cliente', 2);

                    //insere usuário no banco de dados do web finanças
                    $array_usuario = array();
                    $array_usuario['cliente_id'] = $cliente_id;
                    $array_usuario['grupo_id'] = 1;
                    $array_usuario['cliente_db_id'] = $cliente_db['id'];
                    $array_usuario['email'] = 'usuario'.$cliente_id.'@webfinancas.com';
                    $array_usuario['senha'] = md5('123456');
                    $array_usuario['situacao'] = 1;
                    $array_usuario['dt_cadastro'] = date('Y-m-d H:i:s');
                    $array_usuario['financeiro'] = 1; //registro que o usuário se cadastrou para o sistema financeiro
                    
                    $dbWf->query_insert('usuarios',$array_usuario);
                    //if(!$dbWf->query_insert('usuarios',$array_usuario))
                      //  throw new Exception('Erro ao inserir usuário', 3);
                    if(!$usuario_id = mysql_insert_id($dbWf->link_id))
                        throw new Exception('Erro ao inserir usuário', 3);

                    //registra situação de acesso do cliente no web finanças
                    $array_situacao = array();
                    $array_situacao['cliente_id'] = $cliente_id;
                    $array_situacao['situacao'] = 1;
                    //if(!$dbWf->query_insert('cli_acesso_situacao',$array_situacao))
                      //  throw new Exception('Erro ao inserir situação de acesso do cliente', 4);
                    $dbWf->query_insert('cli_acesso_situacao',$array_situacao);
                    if(!$cli_acesso_situacao_id = mysql_insert_id($dbWf->link_id))
                        throw new Exception('Erro ao inserir situação de acesso do cliente', 4);

                    //registra sistema contratado pelo cliente
                    $array_sistema = array("sistema_id"=>1,"cliente_id"=>$cliente_id);
                    $dbW2b->query_insert('sistemas_clientes',$array_sistema);
                    //if(!$dbW2b->query_insert('sistemas_clientes',$array_sistema))
                      //  throw new Exception('Erro ao definir sistema contratado pelo cliente', 5);
                    if(!$sistemas_clientes_id = mysql_insert_id($dbW2b->link_id))
                        throw new Exception('Erro ao definir sistema contratado pelo cliente', 5);

                    //registra plano contratado pelo cliente
                    $array_plano_contratado = array(
                            'ano' => 2017,
                            'cliente_id' => $cliente_id,
                            'plano_id' => 7,
                            'vl_plano' => 0,
                            'periodo' => 1,
                            'dia_vencimento' => 0,
                            'dt_cadastro' => date('Y-m-d H:i:s')
                        );
                    $dbWf->query_insert('clientes_planos',$array_plano_contratado);
                    //if(!$dbWf->query_insert('clientes_planos',$array_plano_contratado))
                      //  throw new Exception('Erro ao definir plano contratado pelo cliente', 6);
                    if(!$clientes_planos_id = mysql_insert_id($dbWf->link_id))
                        throw new Exception('Erro ao definir plano contratado pelo cliente', 6);

                    //conecta ao banco de dados do cliente
                    $dbWfCli = new Database("mysql.webfinancas.com", $cliente_db['db'], "W2BSISTEMAS", $cliente_db['db']);

                    $dbWfCli->query('start transaction');

                    //carregar plano de contas
                    $categoria = new Categoria();
                    $params = array('modelo'=>'odontologico');
                    $categoria->CarregarPlanoContas($dbWfCli, $params);
                    
                }else{
                    
                    array_push($arrayClientesAgrupados,$data->val($i,18));

                }

            }else{
                
                //insere clientes no grupo econômico
                if(count($arrayClientesAgrupados)>1){
                    
                    //inserir novo grupo
                    $array_grupo_economico = array(
                            'nome' => 'Grupo Econômico '.$grupoEconomicoQtd,
                            'dt_cadastro' => date('Y-m-d H:i:s')
                        );
                    $dbWf->query_insert('grupos_economicos',$array_grupo_economico);
                    //if(!$dbWf->query_insert('grupos_economicos',$array_grupo_economico))
                      //  throw new Exception('Erro ao criar grupo econômico', 7);

                    $grupoEconomicoId = mysql_insert_id($dbWf->link_id);

                    foreach($arrayClientesAgrupados as $clienteId){

                        //inserir clientes no grupo
                        $array_grupo_economico_integrante = array(
                                'grupo_id' => $grupoEconomicoId,
                                'cliente_id' => $clienteId,
                                'dt_cadastro' => date('Y-m-d H:i:s')
                            );
                        $dbWf->query_insert('grupos_economicos_integrantes',$array_grupo_economico_integrante);
                        //if(!$dbWf->query_insert('grupos_economicos_integrantes',$array_grupo_economico_integrante))
                          //  throw new Exception('Erro ao inserir cliente no grupo econômico', 8);
                    }

                    $grupoEconomicoQtd++;
                }

                //zera array de clientes agrupados
                $arrayClientesAgrupados = array();

            }

            $dbW2b->query('commit');
            $dbWf->query('commit');
            
            if($dbWfCli!=0){
                $dbWfCli->query('commit');
                $dbWfCli->close();
                $dbWfCli = 0;
            }

        }
        catch(Exception $e){

            $dbW2b->query('rollback');
            $dbWf->query('rollback');
            
            if($dbWfCli!=0)
                $dbWfCli->query('rollback');

            //if(in_array($e->getCode(),array(1,2,3,4,5,6)))
                $erro = $e->getMessage();
            //else
              //  $erro = 'Erro ao carregar plano de contas';

            array_push($array_erros,array('cliente_id'=>$cliente_id, 'cliente'=>$array_cadastro['nome'], 'db_id' => $cliente_db['id'], 'erro' => $erro));

            throw new Exception();

        }
    }

    $dbW2b->close();
    $dbWf->close();

    echo 'Importação finalizada '.date('Y-m-d H:i:s').' <br><br>';

}catch(Exception $e){

    $dbW2b->close();
    $dbWf->close();

    if(count($array_erros)>0){

        foreach($array_erros as $erro){
            echo 'cliente_id: '.$erro['cliente_id'].' <br>';
            echo 'cliente: '.$erro['cliente'].' <br>';
            echo 'db_id: '.$erro['db_id'].' <br>';
            echo 'erro: '.$erro['erro'].' <br><br>';
        }

    }

}

?>