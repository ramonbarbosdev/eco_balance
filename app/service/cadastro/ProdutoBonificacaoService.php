<?php

//require_once (PATH . '/app/utils/W5iSequencia.php');

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;

class ProdutoBonificacaoService
{ 
     /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Converter Eco em Reais

    */
    public static function  converterReais($formName, $param)
    {
        $vl_eco =  (float) str_replace(',', '.', str_replace('.', '', $param['vl_eco']));
        $taxaReal = 3.5;

        $vl_reais = $vl_eco * $taxaReal;
        $vl = number_format($vl_reais, 2, ',', '.') ?? '';
        TForm::sendData($formName, (object) ['vl_reais' => $vl]);
      
    }
    /*
    @author: Ramon
    @created: 03/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias

    */
    public static function excluir($tabela, $conn)
    {

        $existente = $conn->query("select cast(1 as bool) as fl_existe_limite 
                                    from item_entrada_bonificacao ieb
                                    join produto_bonificacao pb  on ieb.id_produto  = pb.id_produto 
                                    where ieb.id_produto  = $tabela->id_produto
                                    limit 1")->fetchObject();




        if ($existente == true) {
            throw new Exception('Não é possivel excluir, já existe vinculo!');
        } else {
            $tabela->delete();
        }
    }
}
