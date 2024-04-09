<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TForm;

class SaidaBonificacaoService
{


    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Calcular valor total
    */
    public static function onCalculoTotal($formName, $param)
    {
        $quantidade = (float) str_replace(',', '.', str_replace('.', '', $param['qt_item']));
        $unitario = (float) str_replace(',', '.', str_replace('.', '', $param['vl_unitario']));
        $calculo = $quantidade * $unitario;
        $calculo_formatado = number_format($calculo, 2, ',', '.');
        // Enviar o resultado para o formulário, se necessário
        TForm::sendData($formName, (object) ['vl_total' => $calculo_formatado]);
    }
    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Carregar informações ao selecionar o material
    */
    public static function carregarInfoItem($data_base, $formName, $param)
    {
        TTransaction::open($data_base);
        $produto   = new ProdutoBonificacao($param['id_produto']);

        // Certifique-se de que os campos não são nulos
        $nm_produto = $produto->nm_produto ?? '';
        $vl_unitario = number_format($produto->vl_eco, 2, ',', '.') ?? '';

        // Consolidar as atualizações em uma única chamada
        TForm::sendData($formName, (object) [
            'nm_produto' => $nm_produto,
            'vl_unitario' => $vl_unitario,
        ]);

        TTransaction::close();
    }
    /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Validações ao incluir um item a data grid
    */
    public static function validacaoItemSaldo($formName, $data, $conn)
    {
        $id_fichacadastral = $data->id_fichacadastral;
        $vl_saldo = (float) str_replace(',', '.', str_replace('.', '', $data->vl_saldo));
        $vl_total = (float) str_replace(',', '.', str_replace('.', '', $data->vl_total));
        $vl_eco = (float) $data->vl_ecototal;

        //converção
        $vl_total_Eco = $vl_total / 3.50;

        $soma_valor_total = $vl_eco + $vl_total_Eco;

        $edicao = TSession::getValue('status_edit');

        $sumSaldo = $conn->query("SELECT SUM(vl_recebimento) AS total_recebimento
                            FROM recebimento_material;")->fetchObject();
        $sumProdutoItem = $conn->query("SELECT SUM(qt_item) AS total_item
                                        FROM item_entrada_bonificacao ieb 
                                        where id_produto = $data->id_produto")->fetchObject();

        $vl_recebimento = $sumSaldo->total_recebimento;

        if ($vl_recebimento < $vl_total_Eco) {
            throw new Exception('Saldo insuficiente!');
        }

        if ($vl_saldo < $vl_total_Eco) {
            throw new Exception('Saldo insuficiente!');
        }

        if ($vl_eco >= $vl_saldo && $edicao != 1) {
            throw new Exception('O limite do saldo já foi atingido!');
        }

        if ($soma_valor_total > $vl_saldo) {
            throw new Exception('Com essa adição o saldo ficará negativo!');
        }

        if ($sumProdutoItem <  $data->qt_item) {
            throw new Exception('A quantidade ultrapassa o estoque disponivel!');
        }
    }
    /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Validações ao incluir um item a data grid
    */
    public static function validacaoItemProduto($param, $data)
    {
        $edicao = TSession::getValue('status_edit');
        if (!empty($param['item_saida_bonificacao_list_id_produto'])) {
            foreach ($param['item_saida_bonificacao_list_id_produto'] as $item) {

                if ($data->id_produto == $item && $edicao != 1) {
                    throw new Exception('Produto ja adicionado a lista!');
                }
            }
        }
    }
    /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Atualizar Saldo Disponivel
    */
    public static function atualizarSaldo($tabela, $conn)
    {
        $existe_saida = SaidaBonificacao::where('id_fichacadastral', '=', $tabela->id_fichacadastral)->first();


        if (!empty($existe_saida)) {

            $sum = $conn->query("SELECT SUM(vl_recebimento) AS total_recebimento
                                FROM recebimento_material;")->fetchObject();
            $vl_recebimento = $sum->total_recebimento;

            $update = $conn->query("UPDATE saida_bonificacao
                                    SET  vl_saldo  = $vl_recebimento
                                    where id_fichacadastral = $tabela->id_fichacadastral;");
        }
    }

    /*
    @author: Ramon
    @created: 03/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias
    */
    public static function excluir($tabela, $conn)
    {

        $tabela->delete();
    }
}
