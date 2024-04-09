<?php
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;

class EntradaBonificacaoService
{


    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Calcular valor total
    */
    public static function onCalculoTotal($formName,$param)
    {
        $quantidade = (float) str_replace(',', '.', str_replace('.', '', $param['qt_item']));
        $unitario = (float) str_replace(',', '.', str_replace('.', '', $param['vl_reais']));
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
    public static function carregarInfoMaterialResidual($data_base,$formName,$param)
    {
        TTransaction::open($data_base);
                $produto   = new ProdutoBonificacao($param['id_produto']);
                
                // Certifique-se de que os campos não são nulos
                $nm_produto = $produto->nm_produto ?? '';
                $vl_reais = number_format($produto->vl_reais, 2, ',', '.') ?? '';

                // Consolidar as atualizações em uma única chamada
                TForm::sendData($formName, (object) [
                    'nm_produto' => $nm_produto,
                    'vl_reais' => $vl_reais,
                ]);

        TTransaction::close();
    }
        /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Validações ao incluir um item a data grid
    */
    public static function validacaoItemProduto($param, $data)
    {
        $edicao = TSession::getValue('status_edit');
        if (!empty($param['item_entrada_bonificacao_list_id_produto'])) {
            foreach ($param['item_entrada_bonificacao_list_id_produto'] as $item) {

                if ($data->id_produto == $item && $edicao != 1) {
                    throw new Exception('Produto ja adicionado a lista!');
                }
            }
        }
    }
     /*
    @author: Ramon
    @created: 03/01/2024
    @summary: Exclui o registro de recebimento material, fazendo as validações necessárias
    */
    public static function excluir($tabela, $item_entrada, $conn)
    {
        foreach($item_entrada as $item)
        {
            $existente = $conn->query("select cast(1 as bool) as fl_existe_limite 
            from item_saida_bonificacao sb 
            join item_entrada_bonificacao eb  on sb.id_produto  = eb.id_produto 
            where sb.id_produto  = $item->id_produto
            limit 1")->fetchObject();
            
           
            
        }
    
        if($existente == true)
        {
            throw new Exception('Não é possivel excluir, já existe vinculo!');
        }
        else{
            $tabela->delete();
        } 
        
       
       
    }
}
