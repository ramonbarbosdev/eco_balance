<?php

//require_once (PATH . '/app/utils/W5iSequencia.php');

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;

class RecebimentoMaterialService
{

    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Calcular valor total
    */
    public static function onCalculoTotal($formName, $param)
    {
        $qt_item = (float) str_replace(',', '.', str_replace('.', '', $param['qt_item']));
        $vl_unidade = (float) str_replace(',', '.', str_replace('.', '', $param['vl_unidade']));
        $calculo = $qt_item * $vl_unidade;
        $calculo_formatado = number_format($calculo, 2, ',', '.');
        // Enviar o resultado para o formulário, se necessário
        TForm::sendData($formName, (object) ['vl_total' => $calculo_formatado]);
    }
    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Carregar informações ao selecionar o material
    */
    public static function carregarInfoMaterialResidual($data_base, $formName, $param)
    {
        TTransaction::open($data_base);
        $material_residual   = new MaterialResiduo($param['id_materialresidual']);

        // Certifique-se de que os campos não são nulos
        $nm_materialresidual = $material_residual->nm_materialresidual ?? '';
        $vl_unidade = number_format($material_residual->vl_bonificacao, 2, ',', '.') ?? '';

        // Consolidar as atualizações em uma única chamada
        TForm::sendData($formName, (object) [
            'nm_materialresidual' => $nm_materialresidual,
            'vl_unidade' => $vl_unidade,
        ]);

        TTransaction::close();
    }

    /*
    @author: Ramon
    @created: 03/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias

    */
    public static function excluir($tabela,$conn)
    {
        $existente = $conn->query("select cast(1 as bool) as fl_existe_limite 
                                    from saida_bonificacao sb
                                    join ficha_cadastral fc  on sb.id_fichacadastral  = fc.id_fichacadastral 
                                    where sb.id_fichacadastral  = $tabela->id_fichacadastral
                                    limit 1")->fetchObject();
         




        if ($existente == true) {
            throw new Exception('Não é possivel excluir, já existe vinculo!');
        } else {
            $tabela->delete();
        }
    }
}
