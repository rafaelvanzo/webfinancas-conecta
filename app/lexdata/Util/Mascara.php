<?php
class Mascara{

    static function Formatar($val, $mask)
    {
	    $maskared = '';
	    $k = 0;
	    for($i = 0; $i<strlen($mask); $i++)
	    {
		    if($mask[$i] == '#')
			    $maskared .= $val[$k++];
		    else
			    $maskared .= $mask[$i];
	    }
	    return $maskared;
    }
}
?>