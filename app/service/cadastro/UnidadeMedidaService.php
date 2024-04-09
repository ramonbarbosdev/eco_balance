<?php

//require_once (PATH . '/app/utils/W5iSequencia.php');

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;



class UnidadeMedidaService
{

     /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias
    */
    public static function excluir($tabela, $conn)
    {
    
        $existente = $conn->query("select cast(1 as bool) as fl_existe_limite 
                                from material_residuo mr
                                join unidade_medida um on mr.id_unidademedida  = um.id_unidademedida 
                                where mr.id_unidademedida  = $tabela->id_unidademedida
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
