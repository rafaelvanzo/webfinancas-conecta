<?php

/**
 * RemessaLexdataController short summary.
 *
 * RemessaLexdataController description.
 *
 * @version 1.0
 * @author Fabio
 */
class RemessaLexdataController {

    /**
     * Download das remessas via FTP
     */
    static function GetRemessaFtp(){

        //Abre conexão FTP
        $ftp_username = 'ftpwb';
        $ftp_userpass = '@#$ftp2017';
        $ftp_server = "www.lexdata.com.br";
        $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
        $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

        //Retorna lista de arquivos do ftp
        $arquivos = ftp_nlist($ftp_conn, "boletos/");
        //var_dump($arquivos);

        foreach($arquivos as $arquivo){
            
            if(pathinfo($arquivo, PATHINFO_EXTENSION)=='txt'){

                $server_file = "boletos/$arquivo";
                $local_file = ROOT."lexdata/Remessas/$arquivo";

                // initiate download
                $download = ftp_nb_get($ftp_conn, $local_file, $server_file, FTP_ASCII);

                while ($download == FTP_MOREDATA){
                    // do whatever you want
                    // continue downloading
                    $download = ftp_nb_continue($ftp_conn);
                }

                if ($download != FTP_FINISHED){
                    echo "Error downloading $server_file";
                    exit(1);
                }

                //Move remessa para pasta "processados" no servidor da Lexdata
                ftp_rename($ftp_conn,$server_file,"boletos/processados/$arquivo");
            }
        }

        //Fecha conexão FTP
        ftp_close($ftp_conn);
    }

    /**
     * Leitura dos boletos dentro das remessas
     * @return array
     */
    static function LerRemessa(){

        $caminhoArquivosRem = ROOT.'lexdata/Remessas/*.txt';
		$arrayArquivosRem = glob($caminhoArquivosRem);
        
        $arrayBoletos = array();

        if($arrayArquivosRem){

            foreach($arrayArquivosRem as $arquivo){
                
                //Abre arquivo
                $remessa = new SplFileObject($arquivo);
                
                //Posiciona no primeiro boleto
                $remessa->seek(1);

                $loop = true;

                while($loop){
                    
                    $linha = $remessa->current();

                    //Registro de boleto
                    if(substr($linha,0,1) == '1'){ //0: Header; 1: Boleto; 9: Final do arquivo
                        
                        //Nosso número
                        $nossoNumero = ltrim(substr($linha,54,19),'0');

                        //Competência
                        $dtCompetencia = substr($linha,110,7); //mm/yyyy

                        //Valor
                        $valor = number_format(substr($linha,126,11).'.'.substr($linha,137,2),2,',','.'); //11,2

                        //Vencimento
                        $dtVencimento = substr($linha,120,2).'/'.substr($linha,122,2).'/20'.substr($linha,124,2); //ddmmy //Verificar com o Érico qual das três datas de vencimento da remessa deve ser utilizada

                        //Emissão
                        $dtEmissao = substr($linha,150,2).'/'.substr($linha,152,2).'/20'.substr($linha,154,2); //ddmmy
                        
                        //Cpf/Cnpj do sacado
                        if(substr($linha,219,1) == '2'){
                            $inscricao = 'CNPJ';
                            $cpfCnpj = Mascara::Formatar(substr($linha,220,14),'##.###.###/####-##');
                        }else{
                            $inscricao = 'CPF';
                            $cpfCnpj = Mascara::Formatar(substr($linha,223,11),'###.###.###-##');
                        }
                        

                        //Nome do favorecido
                        $favorecido = trim(substr($linha,234,35));

                        //Logradouro (inclui o número)
                        $logradouro = trim(substr($linha,274,75));

                        //Bairro
                        $bairro = trim(substr($linha,349,30));

                        //Cep
                        $cep = substr($linha,379,8);

                        //Cidade
                        $cidade = trim(substr($linha,387,30));

                        //Uf
                        $uf = strtoupper(substr($linha,417,2));

                        array_push($arrayBoletos,
                            array(
                                'favorecido'=>array(
                                    'tp_favorecido'=>1,
                                    'nome'=>$favorecido,
                                    'inscricao'=>$inscricao,
                                    'cpf_cnpj'=>$cpfCnpj,
                                    'logradouro'=>$logradouro,
                                    'bairro'=>$bairro,
                                    'cidade'=>$cidade,
                                    'uf'=>$uf,
                                    'cep'=>$cep
                                    ),
                                'lancamento'=>array(
                                    'tipo'=>'R',
                                    'qtd_parcelas'=>1,
                                    'compensado'=>0,
                                    'ct_resp_lancamentos'=>'',
                                    'frequencia'=>30,
                                    'auto_lancamento'=>'M',
                                    'descricao'=>'Honorários',
                                    'conta_id'=>2,
                                    'valor'=>$valor,
                                    'dt_competencia'=>$dtCompetencia,
                                    'dt_emissao'=>$dtEmissao,
                                    'dt_vencimento'=>$dtVencimento
                                    ),
                                'nosso_numero'=>$nossoNumero,
                                'arquivo'=>$arquivo
                            )
                        );

                        $remessa->next();

                        //print_r($arrayBoletos);
                        //echo '<br><br>';

                    }elseif(substr($linha,0,1) == '9'){

                        $loop = false;
                        $remessa = null;
                        //echo 'final do arquivo <br><br>';

                    }else{
                        
                        $remessa->next();
                    }
                }

                //Move remessa para pasta "Processadas" no Web Finanças
                rename($arquivo,ROOT."lexdata/Remessas/Processadas/".pathinfo($arquivo,PATHINFO_BASENAME));
            }
        }

        //echo 'fim do loop';

        return $arrayBoletos;
    }


     /**
     * Leitura dos boletos dentro do db do SUFOL
     * @return array
     */
    function NovosBoletosSUFOL(){
        
        //Conexão com o db do SUFOL
        $conexao = "host=177.53.174.212 port=15780 dbname=dbsufol user=wf password=webfinancas";
        $dbSufol = pg_connect($conexao);

        //Select no db SUFOL
        $query = pg_query($dbSufol, "select nomesacado, cnpjcpf, nossonumero, mes, ano, valorapagar, datavencimento, cnpjcpf, nomesacado, enderecosacado, bairrosacado, cidadesacado, ufsacado, cepsacado from view_boletos where datavencimento >= '".date('Y-m-01')."' and valorpago is null"); //where datavencimento >= '".date('Y-m-01')."' and valorpago is null
        $resultado = pg_fetch_all($query);

        //Conexão com o banco de dados da Lexdata no Web Finanças
        $dbLexdataWf = new Database("mysql.webfinancas.com","webfinancas22", "W2BSISTEMAS", "webfinancas22");
        
        $arrayBoletos = array();

        if($resultado == true){

            foreach($resultado as $boletos){ 

                if(strlen($boletos['nossonumero']) == 12){
           
                    $nosso_numero = ltrim($boletos['nossonumero'],'0'); 
                    $boletoExiste = $dbLexdataWf->fetch_assoc("SELECT nosso_numero FROM boletos where nosso_numero = ".$nosso_numero);
            
                    if($boletoExiste == false){ 
            
                                //Nosso número
                                 $nossoNumero = $nosso_numero;

                                //Competência
                                $dtCompetencia = $boletos['ano'].'-'.str_pad($boletos['mes'], 2, '0', STR_PAD_LEFT).'-01'; //mm/yyyy

                                //Valor
                                $valor = number_format($boletos['valorapagar'], 2, ',', ''); //11,2

                                //Vencimento
                                $dtVencimento = date('d/m/Y', strtotime($boletos['datavencimento']));

                                //Emissão
                                $dtEmissao = date('d/m/Y');
                        
                                //Cpf/Cnpj do sacado
                                if(strlen($boletos['cnpjcpf']) == '14'){
                                    $inscricao = 'CNPJ';
                                    $cpfCnpj = Mascara::Formatar($boletos['cnpjcpf'],'##.###.###/####-##');
                                }else{
                                    $inscricao = 'CPF';
                                    $cpfCnpj = Mascara::Formatar($boletos['cnpjcpf'],'###.###.###-##');
                                }
                        

                                //Nome do favorecido
                                $favorecido = trim($boletos['nomesacado']);

                                //Logradouro (inclui o número)
                                $logradouro = trim($boletos['enderecosacado']);

                                //Bairro
                                $bairro = trim($boletos['bairrosacado']);

                                //Cep
                                $cep = substr($boletos['cepsacado'], 0, 5).'-'.$antes = substr($boletos['cepsacado'], 5);

                                //Cidade
                                $cidade = trim($boletos['cidadesacado']);

                                //Uf
                                $uf = strtoupper($boletos['ufsacado']);


                     array_push($arrayBoletos,
                                        array(
                                            'favorecido'=>array(
                                                'tp_favorecido'=>1,
                                                'nome'=>$favorecido,
                                                'inscricao'=>$inscricao,
                                                'cpf_cnpj'=>$cpfCnpj,
                                                'logradouro'=>$logradouro,
                                                'bairro'=>$bairro,
                                                'cidade'=>$cidade,
                                                'uf'=>$uf,
                                                'cep'=>$cep
                                                ),
                                            'lancamento'=>array(
                                                'tipo'=>'R',
                                                'qtd_parcelas'=>1,
                                                'compensado'=>0,
                                                'ct_resp_lancamentos'=>'',
                                                'frequencia'=>30,
                                                'auto_lancamento'=>'M',
                                                'descricao'=>'Honorários',
                                                'conta_id'=>2,
                                                'valor'=>$valor,
                                                'dt_competencia'=>$dtCompetencia,
                                                'dt_emissao'=>$dtEmissao,
                                                'dt_vencimento'=>$dtVencimento
                                                ),
                                            'nosso_numero'=>$nossoNumero,
                                            'arquivo'=>$arquivo
                                        )
                                    );

                            }//Fim if

                       }//Fim foreach

                } //if verificação tamanho do nosso número
    
        }

              $dbLexdataWf->close();
              
              //print_r($arrayBoletos);
              return $arrayBoletos;
    }
    
}

?>