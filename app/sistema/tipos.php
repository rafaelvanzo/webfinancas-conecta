<?php
require("php/db_conexao.php");
	
			
			
				$pos = 3;
				while($pos <= 3){
				$array_documentos = $db->fetch_assoc("select * from documentos where id =".$pos);

				$array_documento[nome] = utf8_encode($array_documentos[nome]);

				$db->query_update('documentos',$array_documento,'id = '.$array_documentos[id]);
				
 			++ $pos;
			}
			
	
				
				

?>