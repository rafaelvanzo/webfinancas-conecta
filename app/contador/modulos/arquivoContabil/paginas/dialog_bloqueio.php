<!-- Bloqueio de movimento do cliente -->

<div id="dialog-bloqueio" style="height: auto; padding:20px; text-align: center; display:none; overflow:hidden;" title="Bloqeuio de Movimento do Cliente">


<select class="anoBloqueio" data-clienteid="" style="max-width: 80px;">
            <?php
                $dateIni = 2014;
                $dateEnd = date('Y');
                $data;

                while($dateIni <= $dateEnd)
                {
                    
                    if($dateIni == $dateEnd)
                    $selected = 'selected="selected"';

                    $data .= '<option '.$selected.'>'.$dateIni.'</option>';
                    
                    $dateIni++;
                }

                echo $data;
            ?>
        <?php 
        
        
        ?>
        </select>



<ul id="lista-bloquear">
    <li>
        JAN: <button class="button btn-bloquear jan" data-mes="1" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        FEV: <button class="button btn-bloquear fev" data-mes="2" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        MAR: <button class="button btn-bloquear mar" data-mes="3" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        ABR: <button class="button btn-bloquear abr" data-mes="4" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        MAI: <button class="button btn-bloquear mai" data-mes="5" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        JUN: <button class="button btn-bloquear jun" data-mes="6" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        JUL: <button class="button btn-bloquear jul" data-mes="7" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        AGO: <button class="button btn-bloquear ago" data-mes="8" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        SET: <button class="button btn-bloquear set" data-mes="9" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        OUT: <button class="button btn-bloquear out" data-mes="10" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        NOV: <button class="button btn-bloquear nov" data-mes="11" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
    <li> 
        DEZ: <button class="button btn-bloquear dez" data-mes="12" data-bloqueado="0" data-clienteid="">Liberado</button>
    </li>
</ul>


</div><!-- Fim dialog --> 

<!-- Bloqueio de movimento do cliente -->

