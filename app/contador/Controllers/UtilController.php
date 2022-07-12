<?php

/**
 * Util short summary.
 *
 * Util description.
 *
 * @version 1.0
 * @author Fabio
 */
class UtilController
{
    /**
     * Registra id e nome do usuário selecionados no auto completar da contabilidade
     * @param mixed $params 
     */
    function SetSessionCliente($params){
        $_SESSION['contador_cliente'] = array('id'=>$params['cliente_id'],'nome'=>$params['nome']);
        echo json_encode($_SESSION['contador_cliente']);
    }
    
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

    /**
     * Summary of retirar_acento
     * @param mixed $str 
     * @return string
     */
    static function retirar_acento($str){

        $arrConv = array(
        "ç" => "c",
        "Ç" => "C",
        "ã" => "a",
        "Ã" => "A",
        "á" => "a",
        "Á" => "A",
        "à" => "a",
        "À" => "A",
        "â" => "a",
        "Â" => "A",
        "é" => "e",
        "É" => "E",
        "ê" => "e",
        "Ê" => "E",
        "è" => "e",
        "È" => "E",
        "í" => "i",
        "Í" => "I",
        "î" => "i",
        "Î" => "I",
        "ì" => "i",
        "Ì" => "I",
        "ó" => "o",
        "Ó" => "O",
        "ô" => "o",
        "Ô" => "O",
        "õ" => "o",
        "Õ" => "O",
        "ò" => "o",
        "Ò" => "O",
        "ú" => "u",
        "Ú" => "U",
        "ù" => "u",
        "Ù" => "U",
        "ü" => "u",
        "Ü" => "U",
        "ñ" => "n",
        "Ñ" => "N",
        "ý" => "y",
        "Ý" => "Y",
        "\"" => "",
        "'" => "",
        "," => "",
        //" " => "",
        "<" => "",
        ">" => "",
        );
        return strtr($str, $arrConv);
    }
}
