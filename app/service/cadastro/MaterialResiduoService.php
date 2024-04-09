<?php

//require_once (PATH . '/app/utils/W5iSequencia.php');

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;



class MaterialResiduoService
{

     /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias
    */
    public static function excluir($tabela, $conn)
    {
    
        $existente = $conn->query("select cast(1 as bool) as fl_existe_limite 
                                from item_recebimento_material irm 
                                join material_residuo mr   on mr.id_materialresidual  = mr.id_materialresidual 
                                where irm.id_materialresidual  = $tabela->id_materialresidual
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
