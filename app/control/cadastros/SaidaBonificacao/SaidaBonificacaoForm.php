<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TNumericValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TAlert;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCheckList;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFieldList;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TFormSeparator;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;


require_once(PATH . '/app/service/cadastro/SaidaBonificacaoService.php');


class SaidaBonificacaoForm extends TPage
{
    private $form;
    private $item_saida_bonificacao_list;
    private static $data_base = 'sample';
    private static $active_Record = 'SaidaBonificacao';
    private static $primaryKey = 'id_saida';
    private static $formName = 'form_SaidaBonificacaoForm';

    use Adianti\base\AdiantiStandardFormTrait;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['SaidaBonificacaoList', 'onReload'], ['register_state' => 'true']));

        $this->setDatabase('sample');
        $this->setActiveRecord('SaidaBonificacao');


        // Criação do formulário
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Saida de Bonificação');
        $this->form->setClientValidation(true);
        // $this->form->setColumnClasses(3, ['col-sm-4', 'col-sm-4', 'col-sm-4']);

        // Criação de fields
        $id = new TEntry('id_saida');
        $dt_saida = new TDate('dt_saida');
        $id_fichacadastral = new TSeekButton('id_fichacadastral');
        $fichacadastral_nome = new TEntry('nm_fichacadastral');
        $vl_ecototal = new TNumeric('vl_ecototal', 2, ',', '.');
        $vl_saldo = new TNumeric('vl_saldo', 2, ',', '.');
        $vl_reaistotal = new TNumeric('vl_reaistotal', 2, ',', '.');
        $status = new TEntry('status');





        //Datagrid fields
        $uniqid      = new THidden('uniqid');
        $detail_id         = new THidden('detail_id');
        $id_produto = new TSeekButton('id_produto');
        $nm_produto = new TEntry('nm_produto');
        $qt_item = new TEntry('qt_item');
        $vl_unitario = new TEntry('vl_unitario');
        $vl_total =  new TEntry('vl_total');


        //Ações
        $id_fichacadastral->setAction(new TAction(['RecebimentoMaterialFichaCadastralSeek', 'onReload']));
        $id_fichacadastral->setAuxiliar($fichacadastral_nome);
        $id_produto->setAction(new TAction(['EntradaProdutoBonificacaoSeek', 'onReload']));
        $id_produto->setAuxiliar($nm_produto);
        $qt_item_action = new TAction(array($this, 'onChangeQuantidade'));
        $qt_item->setExitAction($qt_item_action);
        $qt_vl_unitario = new TAction(array($this, 'onChangeUnidade'));
        $vl_unitario->setExitAction($qt_vl_unitario);

        // Validação do campo 
        $dt_saida->addValidation('Data Saida', new TRequiredValidator);
        $id_fichacadastral->addValidation('Pessoa', new TRequiredValidator);
        // $vl_nota->addValidation('Valor Do Recebimento', new TRequiredValidator);

        // Liberar Edição
        $id->setEditable(false);
        $fichacadastral_nome->setEditable(false);
        $nm_produto->setEditable(false);
        $vl_saldo->setEditable(false);
        $vl_ecototal->setEditable(false);
        $vl_reaistotal->setEditable(false);
        $vl_total->setEditable(false);

        // Tamanho Campo
        $id->setSize('100%');
        $id_produto->setSize('27%');
        $nm_produto->setSize('70%');
        $qt_item->setSize('100%');
        $vl_unitario->setSize('100%');
        $dt_saida->setSize('100%');
        $vl_ecototal->setSize('100%');
        $vl_reaistotal->setSize('100%');
        $vl_total->setSize('100%');
        $vl_saldo->setSize('100%');
        $id_fichacadastral->setSize('15%');
        $fichacadastral_nome->setSize('79%');

        // Placeholcer
        $qt_item->setId('id_qt_item');
        $vl_total->setId('id_vl_total');
        $vl_unitario->setId('id_vl_unitario');
        TScript::create("document.getElementById('id_qt_item').placeholder = '0,00';");
        TScript::create("document.getElementById('id_vl_total').placeholder = '0,00';");
        TScript::create("document.getElementById('id_vl_unitario').placeholder = '0,00';");

        //Mascaras
        $dt_saida->setMask('dd/mm/yyyy');
        $dt_saida->setDatabaseMask('yyyy-mm-dd');
        $qt_item->setNumericMask(2, ',', '.');
        $vl_saldo->setNumericMask(2, ',', '.');

        //Propriedades
        $id_produto->setMinLength(0);
        $id_fichacadastral->setMinLength(0);



        //Fieldes
        $row1 =  $this->form->addFields([new TLabel('Codigo'), $id,]);
        $row1->layout = ['col-sm-6'];

        $row2 =  $this->form->addFields([new TLabel('Pessoa'), $id_fichacadastral], [new TLabel('Data da Saida (*)', '#ff0000'), $dt_saida],);
        $row2->layout = ['col-sm-6', 'col-sm-6'];

        $row3 =  $this->form->addFields([new TLabel('Valor R$'), $vl_reaistotal], [new TLabel('Valor em Eco'), $vl_ecototal], [new TLabel('Saldo Disponivel $Eco'), $vl_saldo]);
        $row3->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        //Tab
        $subform = new BootstrapFormBuilder;
        $subform->setFieldSizes('100%');
        $subform->setProperty('style', 'border:none');
        //Tap Itens
        $subform->appendPage('Itens');
        $row5 = $subform->addFields([$uniqid], [$detail_id]);
        $row5->layout = ['col-sm-4', 'col-sm-8'];
        $row5 = $subform->addFields([new TLabel('Produto'), $id_produto]);
        $row5->layout = ['col-sm-12'];
        $row6 = $subform->addFields([new TLabel('Quantidade'), $qt_item], [new TLabel('Valor'), $vl_unitario], [new TLabel('Total'), $vl_total],);
        $row6->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $this->form->addContent([$subform]);

        $add_product = TButton::create('add_product', [$this, 'onAddDetailItem'], 'Adicionar', 'fa:plus-circle green');
        $add_product->getAction()->setParameter('static', '1');

        $clear_product = TButton::create('clear_product', [$this, 'onclearProduto'], 'Limpar Itens', 'fa:ban red');
        $clear_product->getAction()->setParameter('static', '1');


        $this->form->addFields([$add_product], [$clear_product]);




        //Criação de DataGrid
        $this->item_saida_bonificacao_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->item_saida_bonificacao_list->setHeight(150);
        $this->item_saida_bonificacao_list->makeScrollable();
        $this->item_saida_bonificacao_list->setId('item_saida_bonificacao_list');
        $this->item_saida_bonificacao_list->generateHiddenFields();
        $this->item_saida_bonificacao_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->item_saida_bonificacao_list->setMutationAction(new TAction([$this, 'onMutationAction']));

        $col_uniqid   = new TDataGridColumn('uniqid', 'Uniqid', 'center', '10%');
        $col_id        = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_produto  = new TDataGridColumn('id_produto', 'Produto', 'center', '10%');
        $col_nome = new TDataGridColumn('nm_produto', 'Nome', 'center', '30%');
        $col_qt_item = new TDataGridColumn('qt_item', 'Quantidade', 'center', '15%');
        $col_vl_unitario = new TDataGridColumn('vl_unitario', 'Valor.', 'center', '15%');
        $col_vl_total  = new TDataGridColumn('vl_total', 'Total', 'center', '20%');

        $this->item_saida_bonificacao_list->addColumn($col_uniqid);
        $this->item_saida_bonificacao_list->addColumn($col_id);
        $this->item_saida_bonificacao_list->addColumn($col_produto);
        $this->item_saida_bonificacao_list->addColumn($col_nome);
        $this->item_saida_bonificacao_list->addColumn($col_qt_item);
        $this->item_saida_bonificacao_list->addColumn($col_vl_unitario);
        $this->item_saida_bonificacao_list->addColumn($col_vl_total);


        $col_uniqid->setVisibility(false);
        $col_id->setVisibility(false);
        //Botçoes da Datagrid
        $action1 = new TDataGridAction([$this, 'onEditItemSaida']);
        $action1->setFields(['uniqid', '*']);

        $action2 = new TDataGridAction([$this, 'onDeleteItemSaida']);
        $action2->setField('uniqid');

        // Botoes da Datagrid
        $this->item_saida_bonificacao_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->item_saida_bonificacao_list->addAction($action2, _t('Delete'), 'far:trash-alt red');



        //Criar datagrid na janela
        $this->item_saida_bonificacao_list->createModel();

        $panel = new TPanelGroup();
        $panel->add($this->item_saida_bonificacao_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent([$panel]);

        // Adicionar botão de salvar
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:plus green');
        $btn->class = 'btn btn-sm btn-primary';
      

        // Adicionar link para criar um novo registro
        $this->form->addActionLink(_t('New'), new TAction([$this, 'onEdit']), 'fa:eraser red');

        // Adicionar link para fechar o formulário
        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        // Vertical container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }


    public static function onProdutoChange($param)
    {
        if (!empty($param['id_produto'])) {
            try {

                SaidaBonificacaoService::carregarInfoItem(self::$data_base, self::$formName, $param);
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Tornar valor padrão
            TForm::sendData(self::$formName, (object) [
                'nm_produto' => '',
                'qt_item' => '0,00',
                'vl_unitario' => '0,00',
                'vl_total' => '0,00',
            ]);
        }
    }
    /*
    @author: Ramon
    @created: 03/02/2024
    @summary:Carregar ações quando a quantidade for informada
    */
    public static function onChangeQuantidade($param)
    {
        if (!empty($param['qt_item'])) {
            try {

                SaidaBonificacaoService::onCalculoTotal(self::$formName, $param);
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
        }
    }
    /*
    @author: Ramon
    @created: 03/02/2024
    @summary:Carregar ações quando a valor unidade for informada
    */
    public static function onChangeUnidade($param)
    {
        if (!empty($param['vl_unitario'])) {
            try {

                SaidaBonificacaoService::onCalculoTotal(self::$formName, $param);
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
        }
    }
    /*
    @author: Ramon
    @created: 02/02/2024
    @summary: Adicionar na datagrid
    */
    public function onAddDetailItem($param)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();

            if ((!$data->id_produto) || (!$data->qt_item) || (!$data->vl_total)) {
                throw new Exception('Não é possivel incluir, verifique os campos!');
            }


            $conn = TTransaction::open(self::$data_base);
            SaidaBonificacaoService::validacaoItemSaldo(self::$formName, $data, $conn);
           

            $produto = new ProdutoBonificacao($param['id_produto']);

            $uniqid = !empty($data->uniqid) ? $data->uniqid : uniqid();

            $grid_data = [
                'uniqid'      => $uniqid,
                'id'      => $data->detail_id,
                'id_produto'  => $data->id_produto,
                'nm_produto'      => $produto->nm_produto,
                'qt_item'      => $data->qt_item,
                'vl_unitario'      => $data->vl_unitario,
                'vl_total'      => $data->vl_total
            ];
            TTransaction::close();
            //inserir na linha 
            $row = $this->item_saida_bonificacao_list->addItem((object) $grid_data);
            $row->id = $uniqid;

         
            SaidaBonificacaoService::validacaoItemProduto($param, $data);

            TDataGrid::replaceRowById('item_saida_bonificacao_list', $uniqid, $row);


            // limpar datagrid
            $data->uniqid     = '';
            $data->detail_id     = '';
            $data->id_produto = '';
            $data->nm_produto       = '';
            $data->qt_item       = '';
            $data->vl_unitario     = '';
            $data->vl_total     = '';

            TForm::sendData(self::$formName, $data, false, false);
            TSession::delValue('status_edit');

            TTransaction::close();
        } catch (Exception $e) {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    /*
    @author: Ramon
    @created: 02/02/2024
    @summary: Editar row da datagrid
    */
    public static function onEditItemSaida($param)
    {
        try {
            TTransaction::open(self::$data_base);

            TSession::setValue('status_edit', 1);

            $produto = new ProdutoBonificacao($param['id_produto']);
            $data = new stdClass;
            $data->uniqid     = $param['uniqid'];
            $data->detail_id      = $param['uniqid'];
            $data->id_produto = $param['id_produto'];
            $data->nm_produto       = $produto->nm_produto;
            $data->qt_item       = $param['qt_item'];
            $data->vl_unitario     = $param['vl_unitario'];
            $data->vl_total     = $param['vl_total'];

            TForm::sendData(self::$formName, $data, false, false);

            TTransaction::close();
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    /*
    @author: Ramon
    @created: 02/02/2024
    @summary: Excluir row da datagrid
    */
    public static function onDeleteItemSaida($param)
    {

        $data = new stdClass;
        $data->uniqid     = '';
        $data->detail_id     = '';
        $data->id_produto = '';
        $data->nm_produto = '';
        $data->qt_item       = '';
        $data->vl_unitario     = '';
        $data->vl_total     = '';

        // send data, do not fire change/exit events
        TForm::sendData(self::$formName, $data, false, false);

        // remove row
        TDataGrid::removeRowById('item_saida_bonificacao_list', $param['uniqid']);
    }
    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Exibir valor total do recebimento
    */
    public static function onMutationAction($param)
    {
        // Form data: $param['form_data']
        // List data: $param['list_data']

        $conn = TTransaction::open(self::$data_base);

        $total = 0;
        $total_eco = 0;
        if (!empty($param['list_data'])) {
            foreach ($param['list_data'] as $row) {
                $produto =  new ProdutoBonificacao($row['id_produto']);
                $total +=  (float) str_replace(',', '.', str_replace('.', '', $row['vl_total']));
                $total_eco += (floatval($produto->vl_eco)) *  floatval($row['qt_item']);
            }
        }
      
        TForm::sendData(self::$formName, (object) [
            'vl_reaistotal' => number_format($total, 2, ',', '.'),
            'vl_ecototal' => number_format($total_eco, 2, ',', '.'),
        ]);
       
    }
    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Exibir data atual sempre que abrir o Form 
    */
    public static function retornarData($param = null)
    {
        try {
            $hoje = date('d/m/Y');

            if (empty($param['dt_saida'])) {
                TForm::sendData(self::$formName, ['dt_saida' => $hoje]);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onShow($param = null)
    {

        self::retornarData($param);
    }


    public function onSave($param)
    {
        try {
            TTransaction::open(self::$data_base);

            $data = $this->form->getData();
            //$this->form->validate();

            $saida_bonificacao = new SaidaBonificacao();
            $saida_bonificacao->fromArray((array) $data);


            $saida_bonificacao->store();

            ItemSaidaBonificacao::where('id_saida', '=', $saida_bonificacao->id_saida)->delete();

            if (!empty($param['item_saida_bonificacao_list_id_produto'])) {
                foreach ($param['item_saida_bonificacao_list_id_produto'] as $key => $item_id) {
                    $item = new ItemSaidaBonificacao;
        
                    $item->id_produto  = $item_id;
                    $item->qt_item  = (float)    str_replace(',', '.', str_replace('.', '', $param['item_saida_bonificacao_list_qt_item'][$key]));
                    $item->vl_unitario      = (float)   str_replace(',', '.', str_replace('.', '', $param['item_saida_bonificacao_list_vl_unitario'][$key]));;
                    $item->vl_total    = (float)  str_replace(',', '.', str_replace('.', '', $param['item_saida_bonificacao_list_vl_total'][$key]));
                   
                    $item->id_saida  = $saida_bonificacao->id_saida;
                    $item->store();
                }
            }

            $saida_bonificacao->store(); // stores the object

            TForm::sendData(self::$formName, (object) ['id_saida' => $saida_bonificacao->id_saida]);

            $loadPageParam = [];

            if (!empty($param['target_container'])) {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            TTransaction::close(); // close the transaction
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            AdiantiCoreApplication::loadPage('SaidaBonificacaoList', 'onShow', $loadPageParam);
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData()); // keep form data
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            TTransaction::open(self::$data_base);

            if (isset($param['key'])) {
                $key = $param['key'];


                $object = new SaidaBonificacao($key);
                $item = ItemSaidaBonificacao::where('id_saida', '=', $object->id_saida)->load();
                $ficha_cadastral = new FichaCadastral($object->id_fichacadastral);
                //Desabilitar campos
                //$this->form->getField('tp_folha')->setEditable(false);

                foreach ($item as $itens) {
                    $produto_bonificacao = ProdutoBonificacao::where('id_produto', '=', $itens->id_produto)->first();

                    $itens->uniqid = uniqid();
                    $itens->nm_produto = $produto_bonificacao->nm_produto;
                    $row = $this->item_saida_bonificacao_list->addItem($itens);
                    $row->id = $itens->uniqid;
                }

                TForm::sendData(self::$formName, (object) ['nm_fichacadastral' => $ficha_cadastral->nome]);
                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear();
                self::onShow($param);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

     /*
    @author: Ramon
    @created: 04/02/2024
    @summary: Função para limpar os campos da datagrid
    */

    public function onclearProduto($param)
    {
        TForm::sendData(self::$formName, (object) ['id_produto' => '']);
        TForm::sendData(self::$formName, (object) ['nm_produto' => '']);
        TForm::sendData(self::$formName, (object) ['qt_item' => '']);
        TForm::sendData(self::$formName, (object) ['vl_unitario' => '']);
        TForm::sendData(self::$formName, (object) ['vl_total' => '']);
        
    }
    /*
    @author: Ramon
    @created: 04/02/2024
    @summary: Função para limpar as linhas do datagrid
    */

    public function oncleanDataGrid($param)
    {
        //Limpar DataGrid Completa
        $tabela = 'item_saida_bonificacao_list';
        TScript::create("
                 function ttable_remove_all_rows(table_id) {
                     $('#' + table_id + ' tbody').empty();
                 }
                 ttable_remove_all_rows('$tabela');
           ");
    }
    // Método fechar
    public function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
