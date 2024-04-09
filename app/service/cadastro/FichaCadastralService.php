<?php

//require_once (PATH . '/app/utils/W5iSequencia.php');

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;

class FichaCadastralService
{

     /*
    @author: Ramon
    @created: 03/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias

    */
    public static function excluir($tabela, $conn)
    {
    
        $existente = $conn->query("select cast(1 as bool) as fl_existe_limite 
                                    from recebimento_material rm
                                    join ficha_cadastral fc  on rm.id_fichacadastral  = fc.id_fichacadastral 
                                    where rm.id_fichacadastral  = $tabela->id_fichacadastral
                                    limit 1")->fetchObject();
                                     
        
        if($existente == true)
        {
            throw new Exception('Não é possivel excluir, já existe vinculo!');
        }
        else{
            $tabela->delete();
        } 
       
    }
}
