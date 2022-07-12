<?php

/**
 * CURLService short summary.
 *
 * CURLService description.
 *
 * @version 1.0
 * @author Fabio & Rafael
 */
class CURLService
{
    /**
     * Summary of Request
     * @param mixed $params 
     */
    public static function Request($params)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(

          CURLOPT_URL => $params["url"],

          CURLOPT_RETURNTRANSFER => true,

          CURLOPT_ENCODING => "",

          CURLOPT_MAXREDIRS => 10,

          CURLOPT_TIMEOUT => 30,

          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

          CURLOPT_CUSTOMREQUEST => $params["customRequest"] //GET, POST, DELETE

        ));

        if($params["postFields"])
            curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($params["postFields"]));

        if($params["httpReader"])

            curl_setopt($curl,CURLOPT_HTTPHEADER,$params["httpReader"]);

        else

            curl_setopt($curl,CURLOPT_HTTPHEADER,array("Content-Type: application/json")); //o padrão json

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

            return "cURL Error #:" . $err;

        } else {

            return $response;
            
        }
    }

/** 
* ================================================================================= 
* ---------------- ENVIO DE ARQUIVO PARA OUTRO SERVIDOR C/ CURL ------------------- 
* ================================================================================= 
*/

public static function SendFiles($url, $tmpFile, $type, $name, $CaminhoPasta)
{

    //$url = 'http://localhost/curl/Receber.php';
    //$tmpFile = $_FILES['file']['tmp_name'];
    //$type = $_FILES['file']['type'];
    //$name = $_FILES['file']['name'];

    $curl = curl_init($url);

    $curl_file = new CURLFile($tmpFile, $type, $name);

    $file = array('file' => $curl_file, 'CaminhoPasta' => $CaminhoPasta);

    curl_setopt($curl, CURLOPT_POST,1);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $file);

    curl_exec($curl);

    /*
    if($errno = curl_errno($curl)) {
        $error_message = curl_strerror($errno);
        echo "cURL error ({$errno}):\n {$error_message}";
    }*/

    curl_close($curl);

}

} /* Fim Classe */