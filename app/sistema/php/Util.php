<?php

/**
 * Util short summary.
 *
 * Util description.
 *
 * @version 1.0
 * @author Fabio
 */
class Util
{
    
    /**
     * Summary of array_to_json
     * @param mixed $array 
     * @return bool|string
     */
    function array_to_json( $array ){

        if( !is_array( $array ) ){
            return false;
        }

        $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
        if( $associative ){

            $construct = array();
            foreach( $array as $key => $value ){

                // We first copy each key/value pair into a staging array,
                // formatting each key and value properly as we go.

                // Format the key:
                if( is_numeric($key) ){
                    $key = "key_$key";
                }
                $key = "\"".addslashes($key)."\"";

                // Format the value:
                if( is_array( $value )){
                    $value = self::array_to_json( $value );
                } else if( !is_numeric( $value ) || is_string( $value ) ){
                    $value = "\"".addslashes($value)."\"";
                }

                // Add to staging array:
                $construct[] = "$key: $value";
            }

            // Then we collapse the staging array into the JSON form:
            $result = "{ " . implode( ", ", $construct ) . " }";

        } else { // If the array is a vector (not associative):

            $construct = array();
            foreach( $array as $value ){

                // Format the value:
                if( is_array( $value )){
                    $value = self::array_to_json( $value );
                } else if( !is_numeric( $value ) || is_string( $value ) ){
                    $value = "'".addslashes($value)."'";
                }

                // Add to staging array:
                $construct[] = $value;
            }

            // Then we collapse the staging array into the JSON form:
            $result = "[ " . implode( ", ", $construct ) . " ]";
        }

        return $result;
    }

    static function CurlRequest($url,$dados = null){

        $cURL = curl_init($url);

        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

        if($dados != null)
        {
            curl_setopt($cURL, CURLOPT_POST, true);
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $dados);
        }

        // O site só permite requisições vindas do próprio site:
        // Definimos então o REFERER como sendo a página do formulário de contato
        curl_setopt($cURL, CURLOPT_REFERER, $url);

        $resultado = curl_exec($cURL);

        curl_close($cURL);

        return $resultado;
    }
}
