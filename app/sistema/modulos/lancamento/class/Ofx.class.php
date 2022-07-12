<?php
class Ofx {

    private $ofxFile;

    public function __construct($ofxFile) {
        $this->ofxFile = $ofxFile;
    }

    /*
     * Converte o arquivo OFX para XML
     */

    public function getOfxAsXML() {
		
          // 1. Leia no arquivo
          $cont = file_get_contents($this->ofxFile);
          // 2. Separe e remova o cabeçalho
          $bline = strpos($cont,"<OFX>");
          $head = substr($cont,0,$bline-2);
          $ofx = substr($cont,$bline-1);
          // 3. Examine tags que possam estar terminadas de forma imprópria
          $ofxx = $ofx;
          $tot=0;
          while ($pos = strpos($ofxx,'<')) {
            $tot++;
            $pos2 = strpos($ofxx,'>');
            $ele = substr($ofxx,$pos+1,$pos2-$pos-1);
            if (substr($ele,0,1) =='/'){ //registra tags de fechamento
							$sla[] = substr($ele,1);
						}else{ //registra tags de abertura
							$als[] = $ele;
						}
            $ofxx = substr($ofxx,$pos2+1);
          }
          
					$als_cont = array_count_values($als);
					$sla_cont = array_count_values($sla);
					$adif = array();
					foreach($als_cont as $key => $value){
						$key_exist = array_key_exists($key,$sla_cont);
						if( !$key_exist || $als_cont[$key] != $sla_cont[$key]){
							$adif[] = $key;
						}
					}

          $ofxy = $ofx;

					// 4. Termine aquelas que precisam de terminação
          foreach ($adif as $dif) {
            $dpos = 0;
						$found_close_dif = false; //registra que na busca anterior a tag encontrada foi de fechamento
            while ($dpos = strpos($ofxy,$dif,$dpos+1)) {
              $npos = strpos($ofxy,'<',$dpos+1);
							if( substr($ofxy,$npos+1,strlen($dif)+1)== ('/'.$dif) ){ //não substitui se for tag de fechamento após o sinal "<"
								$found_close_dif = true; //registra que é tag de fechamento para não fazer substituição na próxima busca
								$dpos = $npos+1; //incrementa uma posição no ponteiro de busca para continuar a pesquisa pela tag
							}elseif($found_close_dif){ //não substitui se na ultima busca a tag foi de fechamento
								$found_close_dif = false; //reseta variavel para o caso da próxima tag estar aberta
								$dpos = $npos+1; //incrementa uma posição no ponteiro de busca para continuar a pesquisa pela tag
							}else{ //substitiu
								$ofxy = substr_replace($ofxy,"</$dif>\n<",$npos,1);
								$dpos = $npos+strlen($dif)+3;
							}
            }
          }
          // 5. Lide com caracteres especiais
          $ofxy = str_replace('&','&amp;',$ofxy);
					// 6. Grave a cadeia de caracteres resultante na tela
					return utf8_encode($ofxy);
		}

    /*
     * Retorna o Saldo da conta na data de exportação do extrato
     */

    public function getBalance() {
        $xml = new SimpleXMLElement($this->getOfxAsXML());
        $balance = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->BALAMT;
        $dateOfBalance = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->DTASOF;
        $date = strtotime(substr($dateOfBalance, 0, 8));
        $dateToReturn = date('Y-m-d', $date);

        return Array('date' => $dateToReturn, 'balance' => $balance);
    }

    /*
     * Retora um array de objetos com as transações
     * 
     * DTPOSTED => Data da Transação
     * TRNAMT   => Valor da Transação
     * TRNTYPE  => Tipo da Transação (Débito ou Crédito)
     * MEMO     => Descrição da transação
     */

    public function getTransactions() {
        $xml = new SimpleXMLElement($this->getOfxAsXML());
        $transactions = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->STMTTRN;
        return $transactions;
    }

    /**
     * Retorna o código do banco e número da conta
     */
    public function getAccount(){
        $xml = new SimpleXMLElement($this->getOfxAsXML());
        $bankId = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->BANKID;
        $accountId = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->ACCTID;

        $bankId = trim($bankId);
        $bankId = ltrim($bankId ,'0');
        $bankId = str_pad($bankId,3,'0',STR_PAD_LEFT);

        $accountId = trim($accountId);
        $accountId = ltrim($accountId ,'0');
        $accountId = str_replace(array('.','-'),array('',''),$accountId);

        return Array('bankId' => $bankId, 'accountId' => $accountId);
    }

}
?>