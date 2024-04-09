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
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;


require_once(PATH . '/app/service/cadastro/EntradaBonificacaoService.php');


class EntradaBonificacaoForm extends TPage
{
    private $form;
    private $item_entrada_bonificacao_list;
    private static $data_base = 'sample';
    private static $active_Record = 'EntradaBonificacao';
    private static $primaryKey = 'id_entrada';
    private static $formName = 'form_EntradaBonificacaoForm';

    use Adianti\base\AdiantiStandardFormTrait;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['EntradaBonificacaoList', 'onReload'], ['register_state' => 'true']));

        $this->setDatabase('sample');
        $this->setActiveRecord('EntradaBonificacao');


        // Criação do formulário
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Entrada de Bonificação');
        $this->form->setClientValidation(true);
        // $this->form->setColumnClasses(3, ['col-sm-4', 'col-sm-4', 'col-sm-4']);

        // Criação de fields
        $id = new THidden('id_entrada');
        $dt_entrada = new TDate('dt_entrada');
        $nu_nota = new TEntry('nu_nota');
        $dt_emissao = new TDate('dt_emissao');
        $local_entrega = new TEntry('local_entrega');
        $vl_reaistotal = new TNumeric('vl_reaistotal', 2, ',', '.',true);
        $vl_ecototal = new TNumeric('vl_ecototal', 2, ',', '.',true);

        //Datagrid fields
        $uniqid      = new THidden('uniqid');
        $detail_id         = new THidden('detail_id');
        $id_produto = new TDBUniqueSearch('id_produto', 'sample', 'ProdutoBonificacao', 'id_produto', 'nm_produto');
        $nm_produto = new TEntry('nm_produto');
        $qt_item = new TEntry('qt_item');
        $vl_reais = new TEntry('vl_reais');
        $vl_total =  new TEntry('vl_total');
        //TNumeric('vl_total', 2, ',', '.',true);

        // Validação do campo 
        $dt_entrada->addValidation('Data Entrada', new TRequiredValidator);
        // $vl_reaistotal->addValidation('Valor Do Recebimento', new TRequiredValidator);

        // Liberar Edição
        $id->setEditable(false);
        $nm_produto->setEditable(false);
        $vl_total->setEditable(false);
        $vl_reaistotal->setEditable(false);
        $vl_ecototal->setEditable(false);

        // Tamanho Campo
        $id->setSize('100%');
        $id_produto->setSize('100%');
        $qt_item->setSize('100%');
        $vl_reais->setSize('100%');
        $dt_entrada->setSize('100%');
        $dt_emissao->setSize('100%');
        $vl_reaistotal->setSize('100%');
        $nu_nota->setSize('100%');

        // Placeholcer
        $qt_item->setId('id_qt_item');
        $vl_total->setId('id_vl_total');
        $vl_reais->setId('id_vl_reais');
        TScript::create("document.getElementById('id_qt_item').placeholder = '0,00';");
        TScript::create("document.getElementById('id_vl_total').placeholder = '0,00';");
        TScript::create("document.getElementById('id_vl_reais').placeholder = '0,00';");

        //Mascaras
        $dt_entrada->setMask('dd/mm/yyyy');
        $dt_entrada->setDatabaseMask('yyyy-mm-dd');
        $dt_emissao->setMask('dd/mm/yyyy');
        $dt_emissao->setDatabaseMask('yyyy-mm-dd');
        $qt_item->setNumericMask(2, ',', '.');

        //Propriedades
        $id_produto->setMinLength(0);

        //Ações
        $id_produto->setChangeAction(new TAction([$this, 'onMaterialChange']));
        $qt_item_action = new TAction(array($this, 'onChangeQuantidade'));
        $qt_item->setExitAction($qt_item_action);
        $qt_vl_reais = new TAction(array($this, 'onChangeUnidade'));
        $vl_reais->setExitAction($qt_vl_reais);

        //Fieldes
        $row1 =  $this->form->addFields([new TLabel(''), $id,]);
        $row1->layout = ['col-sm-6'];

        $row2 =  $this->form->addFields([new TLabel('Data da Entrada (*)', '#ff0000'), $dt_entrada], [new TLabel('Nota Fiscal'), $nu_nota]);
        $row2->layout = ['col-sm-6', ' col-sm-6'];

        $row3 =  $this->form->addFields([new TLabel('Data da Emissão'), $dt_emissao], [new TLabel('Valor R$'), $vl_reaistotal],[new TLabel('Valor $Eco'), $vl_ecototal]);
        $row3->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        //Tab
        $subform = new BootstrapFormBuilder;
        $subform->setFieldSizes('100%');
        $subform->setProperty('style', 'border:none');
        //Tap Itens
        $subform->appendPage('Itens');
        $row5 = $subform->addFields([$uniqid], [$detail_id]);
        $row5->layout = ['col-sm-4', 'col-sm-8'];
        $row5 = $subform->addFields([new TLabel('Produto'), $id_produto], [new TLabel(''), $nm_produto]);
        $row5->layout = ['col-sm-4', 'col-sm-8'];
        $row6 = $subform->addFields([new TLabel('Quantidade'), $qt_item], [new TLabel('Valor R$'), $vl_reais], [new TLabel('Total'), $vl_total],);
        $row6->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $this->form->addContent([$subform]);

        $add_product = TButton::create('add_product', [$this, 'onAddDetailItemRecebimento'], 'Register', 'fa:plus-circle green');
        $add_product->getAction()->setParameter('static', '1');
        $this->form->addFields([], [$add_product]);




        //Criação de DataGrid
        $this->item_entrada_bonificacao_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->item_entrada_bonificacao_list->setHeight(150);
        $this->item_entrada_bonificacao_list->makeScrollable();
        $this->item_entrada_bonificacao_list->setId('item_entrada_bonificacao_list');
        $this->item_entrada_bonificacao_list->generateHiddenFields();
        $this->item_entrada_bonificacao_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->item_entrada_bonificacao_list->setMutationAction(new TAction([$this, 'onMutationAction']));

        $col_uniqid   = new TDataGridColumn('uniqid', 'Uniqid', 'center', '10%');
        $col_id        = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_produto  = new TDataGridColumn('id_produto', 'Produto', 'center', '10%');
        $col_nome = new TDataGridColumn('nm_produto', 'Nome', 'center', '30%');
        $col_qt_item = new TDataGridColumn('qt_item', 'Quantidade', 'center', '15%');
        $col_vl_reais = new TDataGridColumn('vl_reais', 'Valor.', 'center', '15%');
        $col_vl_total  = new TDataGridColumn('vl_total', 'Total', 'center', '20%');

        $col_vl_reais->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
                    if(is_numeric($value))
                    {
                       $valor = number_format($value, 2, ",", ".");
                        return "<span'>R$ $valor </span>";
                    }
                    else
                    {
                        return $value;
                    }
        });
        $col_vl_total->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
                    if(is_numeric($value))
                    {
                       $valor = number_format($value, 2, ",", ".");
                        return "<span'>R$ $valor </span>";
                    }
                    else
                    {
                        return $value;
                    }
        });


        $this->item_entrada_bonificacao_list->addColumn($col_uniqid);
        $this->item_entrada_bonificacao_list->addColumn($col_id);
        $this->item_entrada_bonificacao_list->addColumn($col_produto);
        $this->item_entrada_bonificacao_list->addColumn($col_nome);
        $this->item_entrada_bonificacao_list->addColumn($col_qt_item);
        $this->item_entrada_bonificacao_list->addColumn($col_vl_reais);
        $this->item_entrada_bonificacao_list->addColumn($col_vl_total);

       

        $col_uniqid->setVisibility(false);
        $col_id->setVisibility(false);
        //Botçoes da Datagrid
        $action1 = new TDataGridAction([$this, 'onEditItemEntrada']);
        $action1->setFields(['uniqid', '*']);

        $action2 = new TDataGridAction([$this, 'onDeleteItemEntrada']);
        $action2->setField('uniqid');

        // Botoes da Datagrid
        $this->item_entrada_bonificacao_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->item_entrada_bonificacao_list->addAction($action2, _t('Delete'), 'far:trash-alt red');



        //Criar datagrid na janela
        $this->item_entrada_bonificacao_list->createModel();

        $panel = new TPanelGroup();
        $panel->add($this->item_entrada_bonificacao_list);
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

    /*
    @author: Ramon
    @created: 03/02/2024
    @summary: Carregar ações quando a quantidade for informada
    */
    public static function onMaterialChange($param)
    {
        if (!empty($param['id_produto'])) {
            try {

                EntradaBonificacaoService::carregarInfoMaterialResidual(self::$data_base, self::$formName, $param);
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Tornar valor padrão
            TForm::sendData(self::$formName, (object) [
                'nm_produto' => '',
                'qt_item' => '0,00',
                'vl_reais' => '0,00',
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

                EntradaBonificacaoService::onCalculoTotal(self::$formName, $param);
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
        if (!empty($param['vl_reais'])) {
            try {

                EntradaBonificacaoService::onCalculoTotal(self::$formName, $param);
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
    public function onAddDetailItemRecebimento($param)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();

            if ((!$data->id_produto) || (!$data->qt_item) || (!$data->vl_total)) {
                throw new Exception('Não é possivel incluir, verifique os campos!');
            }



            $uniqid = !empty($data->uniqid) ? $data->uniqid : uniqid();

            $grid_data = [
                'uniqid'      => $uniqid,
                'id'      => $data->detail_id,
                'id_produto'  => $data->id_produto,
                'nm_produto'      => $data->nm_produto,
                'qt_item'      => $data->qt_item,
                'vl_reais'      => $data->vl_reais,
                'vl_total'      => $data->vl_total
            ];

            //inserir na linha 
            $row = $this->item_entrada_bonificacao_list->addItem((object) $grid_data);
            $row->id = $uniqid;

            EntradaBonificacaoService::validacaoItemProduto($param, $data);

            TDataGrid::replaceRowById('item_entrada_bonificacao_list', $uniqid, $row);


            // limpar datagrid
            $data->uniqid     = '';
            $data->detail_id     = '';
            $data->id_produto = '';
            $data->nm_produto       = '';
            $data->qt_item       = '';
            $data->vl_reais     = '';
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
    public static function onEditItemEntrada($param)
    {
        try {
            TSession::setValue('status_edit', 1);
            $data = new stdClass;
            $data->uniqid     = $param['uniqid'];
            $data->detail_id      = $param['uniqid'];
            $data->id_produto = $param['id_produto'];
            $data->nm_produto       = $param['nm_produto'];
            $data->qt_item       = $param['qt_item'];
            $data->vl_reais     = $param['vl_reais'];
            $data->vl_total     = $param['vl_total'];

            TForm::sendData(self::$formName, $data, false, false);
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
    public static function onDeleteItemEntrada($param)
    {

        $data = new stdClass;
        $data->uniqid     = '';
        $data->detail_id     = '';
        $data->id_produto = '';
        $data->nm_produto = '';
        $data->qt_item       = '';
        $data->vl_reais     = '';
        $data->vl_total     = '';

        // send data, do not fire change/exit events
        TForm::sendData(self::$formName, $data, false, false);

        // remove row
        TDataGrid::removeRowById('item_entrada_bonificacao_list', $param['uniqid']);
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
        $conn = TTransaction::open(self::$data_base);;

        $total = 0;
        $total_eco = 0;
        if (!empty($param['list_data'])) {
            foreach ($param['list_data'] as $row) {
                $produto =  new ProdutoBonificacao($row['id_produto']);
                $total += (floatval($produto->vl_reais)) *  floatval($row['qt_item']);
                $total_eco += (floatval($produto->vl_eco)) *  floatval($row['qt_item']);
            }
        }

        TForm::sendData(self::$formName, (object) [
            'vl_reaistotal' => number_format($total, 2, ',', '.'),
            'vl_ecototal' => number_format($total_eco, 2, ',', '.'),
        ]);
        TTransaction::close(); 
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

            if (empty($param['dt_entrada'])) {
                TForm::sendData(self::$formName, ['dt_entrada' => $hoje]);
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
            $conn = TTransaction::open(self::$data_base);

            $data = $this->form->getData();
            //$this->form->validate();

            $entrada_bonificacao = new EntradaBonificacao();
            $entrada_bonificacao->fromArray((array) $data);

           
            $entrada_bonificacao->store();

            ItemEntradaBonificacao::where('id_entrada', '=', $entrada_bonificacao->id_entrada)->delete();

            if (!empty($param['item_entrada_bonificacao_list_id_produto'])) {
                foreach ($param['item_entrada_bonificacao_list_id_produto'] as $key => $item_id) {
                   
                    $item = new ItemEntradaBonificacao;
                    $item->id_produto  = $item_id;
                    $item->qt_item  = (float)    str_replace(',', '.', str_replace('.', '', $param['item_entrada_bonificacao_list_qt_item'][$key]));
                    $item->vl_reais      = (float)   str_replace(',', '.', str_replace('.', '', $param['item_entrada_bonificacao_list_vl_reais'][$key]));;
                    $item->vl_total    = (float)  str_replace(',', '.', str_replace('.', '', $param['item_entrada_bonificacao_list_vl_total'][$key]));
                   
                    $item->id_entrada  = $entrada_bonificacao->id_entrada;

                    $item->store();
                }
            }

            $entrada_bonificacao->store(); // stores the object

            TForm::sendData(self::$formName, (object) ['id_entrada' => $entrada_bonificacao->id_entrada]);

            $loadPageParam = [];

            if (!empty($param['target_container'])) {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            TTransaction::close(); // close the transaction
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            AdiantiCoreApplication::loadPage('EntradaBonificacaoList', 'onShow', $loadPageParam);
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


                $object = new EntradaBonificacao($key);
                $item = ItemEntradaBonificacao::where('id_entrada', '=', $object->id_entrada)->load();

                //Desabilitar campos
                //$this->form->getField('tp_folha')->setEditable(false);

                foreach ($item as $itens) {
                    $produto_bonificacao = ProdutoBonificacao::where('id_produto', '=', $itens->id_produto)->first();

                    $itens->uniqid = uniqid();
                    $itens->nm_produto = $produto_bonificacao->nm_produto;
                    $row = $this->item_entrada_bonificacao_list->addItem($itens);
                    $row->id = $itens->uniqid;
                }
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

    // Método fechar
    public function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
