<?php
class Remessa {

    function LerRemessa($params){

        $caminhoArquivosRem = ROOT.'lexdata/Remessas/*.txt';
		$arrayArquivosRem = glob($caminhoArquivosRem);
        
        $arrayBoletos = array();

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
                    if(substr($linha,219,1) == '1'){
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
                                'descricao'=>utf8_encode('Honorários'),
                                'conta_id'=>27,
                                'valor'=>$valor,
                                'dt_competencia'=>$dtCompetencia,
                                'dt_emissao'=>$dtEmissao,
                                'dt_vencimento'=>$dtVencimento
                                ),
                            'nosso_numero'=>$nossoNumero
                        )
                    );

                    $remessa->next();

                    print_r($arrayBoletos);
                    echo '<br><br>';

                }elseif(substr($linha,0,1) == '9'){

                    $loop = false;
                    $remessa = null;
                    //echo 'final do arquivo <br><br>';

                }else{
                
                    $remessa->next();
                }
            }
        }

        //echo 'fim do loop';

        return $arrayBoletos;
    }

}

?>