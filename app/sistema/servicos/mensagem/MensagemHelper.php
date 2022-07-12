<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/sistema/php/Util.php';

/**
 * MensagemHelper short summary.
 *
 * MensagemHelper description.
 *
 * @version 1.0
 * @author Fabio
 */
class MensagemHelper
{
    function EnviarEmail($destinatario,$assunto,$conteudo)
    {
        $url = 'http://www.web2business.com.br/api/Email/EnviarMensagem';
        
        $dados = array(
            'nomeRemetente' => "Web Finanças",
            'emailRemetente' => "no-reply@webfinancas.com",
            'nomeDestinatario' => $destinatario,
            'emailDestinatario' => $destinatario,
            'assunto' => $assunto,
            'mensagem' => $conteudo
            );

        $dados = json_encode($dados);

        return json_decode(Util::CurlRequest($url,$dados),true);
    }
}
