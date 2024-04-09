<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
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


require_once(PATH . '/app/service/cadastro/RecebimentoMaterialService.php');
require_once(PATH . '/app/service/cadastro/SaidaBonificacaoService.php');

class RecebimentoMaterialForm extends TPage
{
    private $form;
    private $item_recebimento_material_list;
    private static $data_base = 'sample';
    private static $active_Record = 'RecebimentoMaterial';
    private static $primaryKey = 'id_recebimentomaterial';
    private static $formName = 'form_RecebimentoMaterialForm';

    use Adianti\base\AdiantiStandardFormTrait;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['RecebimentoMaterialList', 'onReload'], ['register_state' => 'true']));

        $this->setDatabase('sample');
        $this->setActiveRecord('RecebimentoMaterial');


        // Criação do formulário
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro Recebimento de Material');
        $this->form->setClientValidation(true);
        // $this->form->setColumnClasses(3, ['col-sm-4', 'col-sm-4', 'col-sm-4']);

        // Criação de fields
        $id = new TEntry('id_recebimentomaterial');
        $id_fichacadastral = new TDBUniqueSearch('id_fichacadastral', 'sample', 'FichaCadastral', 'id_fichacadastral', 'nome');
        $dt_recebimento = new TDate('dt_recebimento');
        $local_entrega = new TEntry('local_entrega');
        $status_recebimento = new THidden('status_recebimento');
        $vl_recebimento = new TNumeric('vl_recebimento', 2, ',', '.');

        //Datagrid fields
        $uniqid      = new THidden('uniqid');
        $detail_id         = new THidden('detail_id');
        $id_materialresidual = new TDBUniqueSearch('id_materialresidual', 'sample', 'MaterialResiduo', 'id_materialresidual', 'nm_materialresidual');
        $nm_materialresidual = new TEntry('nm_materialresidual');
        $qt_item = new TEntry('qt_item');
        $vl_unidade = new TEntry('vl_unidade');
        $vl_total =  new TEntry('vl_total');

        // Validação do campo 
        $id_fichacadastral->addValidation('Pessoa', new TRequiredValidator);
        $dt_recebimento->addValidation('Data Recebimento', new TRequiredValidator);
        // $vl_recebimento->addValidation('Valor Do Recebimento', new TRequiredValidator);

        // Liberar Edição
        $id->setEditable(false);
        $status_recebimento->setEditable(false);
        $nm_materialresidual->setEditable(false);
        $vl_total->setEditable(false);
        $vl_recebimento->setEditable(false);

        // Tamanho Campo
        $id->setSize('100%');
        $id_fichacadastral->setSize('100%');
        $id_materialresidual->setSize('100%');
        $qt_item->setSize('100%');
        $vl_unidade->setSize('100%');
        $dt_recebimento->setSize('100%');
        $local_entrega->setSize('100%');
        $status_recebimento->setSize('100%');
        $vl_recebimento->setSize('100%');

        // Placeholcer
        $qt_item->setId('id_qt_item');
        $vl_total->setId('id_vl_total');
        $vl_unidade->setId('id_vl_unidade');
        TScript::create("document.getElementById('id_qt_item').placeholder = '0,00';");
        TScript::create("document.getElementById('id_vl_total').placeholder = '0,00';");
        TScript::create("document.getElementById('id_vl_unidade').placeholder = '0,00';");

        //Mascaras
        $dt_recebimento->setMask('dd/mm/yyyy');
        $dt_recebimento->setDatabaseMask('yyyy-mm-dd');
        $qt_item->setNumericMask(2, ',', '.');

        //Propriedades
        $id_fichacadastral->setMinLength(0);
        $id_materialresidual->setMinLength(0);

        //Ações
        $id_materialresidual->setChangeAction(new TAction([$this, 'onMaterialChange']));
        $qt_item_action = new TAction(array($this, 'onChangeQuantidade'));
        $qt_item->setExitAction($qt_item_action);
        $qt_vl_unidade = new TAction(array($this, 'onChangeUnidade'));
        $vl_unidade->setExitAction($qt_vl_unidade);

        //Fieldes
        $row1 =  $this->form->addFields([new TLabel('Codigo'), $id,]);
        $row1->layout = ['col-sm-6'];

        $row2 =  $this->form->addFields([new TLabel('Pessoa (*)', '#ff0000'), $id_fichacadastral], [new TLabel('Data de Recebimento (*)', '#ff0000'), $dt_recebimento]);
        $row2->layout = ['col-sm-6', ' col-sm-6'];

        $row3 =  $this->form->addFields([new TLabel('Local da Entrega'), $local_entrega], [new TLabel('Valor Recebimento $Eco (*)', '#ff0000'), $vl_recebimento]);
        $row3->layout = ['col-sm-6', 'col-sm-6'];

        //Tab
        $subform = new BootstrapFormBuilder;
        $subform->setFieldSizes('100%');
        $subform->setProperty('style', 'border:none');
        //Tap Itens
        $subform->appendPage('Itens');
        $row5 = $subform->addFields([$uniqid], [$detail_id]);
        $row5->layout = ['col-sm-4', 'col-sm-8'];
        $row5 = $subform->addFields([new TLabel('Material'), $id_materialresidual], [new TLabel(''), $nm_materialresidual]);
        $row5->layout = ['col-sm-4', 'col-sm-8'];
        $row6 = $subform->addFields([new TLabel('Quantidade'), $qt_item], [new TLabel('Valor Unidade'), $vl_unidade], [new TLabel('Total $Eco'), $vl_total],);
        $row6->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $this->form->addContent([$subform]);

        $add_product = TButton::create('add_product', [$this, 'onAddDetailItemRecebimento'], 'Register', 'fa:plus-circle green');
        $add_product->getAction()->setParameter('static', '1');
        $this->form->addFields([], [$add_product]);




        //Criação de DataGrid
        $this->item_recebimento_material_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->item_recebimento_material_list->setHeight(150);
        $this->item_recebimento_material_list->makeScrollable();
        $this->item_recebimento_material_list->setId('item_recebimento_material_list');
        $this->item_recebimento_material_list->generateHiddenFields();
        $this->item_recebimento_material_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->item_recebimento_material_list->setMutationAction(new TAction([$this, 'onMutationAction']));

        $col_uniqid   = new TDataGridColumn('uniqid', 'Uniqid', 'center', '10%');
        $col_id        = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_material  = new TDataGridColumn('id_materialresidual', 'Material', 'center', '10%');
        $col_nome = new TDataGridColumn('nm_materialresidual', 'Nome', 'center', '30%');
        $col_qt_item = new TDataGridColumn('qt_item', 'Quantidade', 'center', '15%');
        $col_vl_unidade = new TDataGridColumn('vl_unidade', 'Valor Unid.', 'center', '15%');
        $col_vl_total  = new TDataGridColumn('vl_total', 'Total $Eco', 'center', '20%');

        $this->item_recebimento_material_list->addColumn($col_uniqid);
        $this->item_recebimento_material_list->addColumn($col_id);
        $this->item_recebimento_material_list->addColumn($col_material);
        $this->item_recebimento_material_list->addColumn($col_nome);
        $this->item_recebimento_material_list->addColumn($col_qt_item);
        $this->item_recebimento_material_list->addColumn($col_vl_unidade);
        $this->item_recebimento_material_list->addColumn($col_vl_total);


        $col_uniqid->setVisibility(false);
        $col_id->setVisibility(false);
        //Botçoes da Datagrid
        $action1 = new TDataGridAction([$this, 'onEditItemRecebimento']);
        $action1->setFields(['uniqid', '*']);

        $action2 = new TDataGridAction([$this, 'onDeleteItemRecebimento']);
        $action2->setField('uniqid');

        // Botoes da Datagrid
        $this->item_recebimento_material_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->item_recebimento_material_list->addAction($action2, _t('Delete'), 'far:trash-alt red');



        //Criar datagrid na janela
        $this->item_recebimento_material_list->createModel();

        $panel = new TPanelGroup();
        $panel->add($this->item_recebimento_material_list);
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
        if (!empty($param['id_materialresidual'])) {
            try {

                RecebimentoMaterialService::carregarInfoMaterialResidual(self::$data_base, self::$formName, $param);
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
            // Tornar valor padrão
            TForm::sendData(self::$formName, (object) [
                'nm_materialresidual' => '',
                'qt_item' => '0,00',
                'vl_unidade' => '0,00',
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

                RecebimentoMaterialService::onCalculoTotal(self::$formName, $param);
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
        if (!empty($param['vl_unidade'])) {
            try {

                RecebimentoMaterialService::onCalculoTotal(self::$formName, $param);
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

            if ((!$data->id_materialresidual) || (!$data->qt_item) || (!$data->vl_total)) {
                throw new Exception('Não é possivel incluir, verifique os campos!');
            }



            $uniqid = !empty($data->uniqid) ? $data->uniqid : uniqid();

            $grid_data = [
                'uniqid'      => $uniqid,
                'id'      => $data->detail_id,
                'id_materialresidual'  => $data->id_materialresidual,
                'nm_materialresidual'      => $data->nm_materialresidual,
                'qt_item'      => $data->qt_item,
                'vl_unidade'      => $data->vl_unidade,
                'vl_total'      => $data->vl_total
            ];

            //inserir na linha 
            $row = $this->item_recebimento_material_list->addItem((object) $grid_data);
            $row->id = $uniqid;

            TDataGrid::replaceRowById('item_recebimento_material_list', $uniqid, $row);


            // limpar datagrid
            $data->uniqid     = '';
            $data->detail_id     = '';
            $data->id_materialresidual = '';
            $data->nm_materialresidual       = '';
            $data->qt_item       = '';
            $data->vl_unidade     = '';
            $data->vl_total     = '';

            TForm::sendData(self::$formName, $data, false, false);

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
    public static function onEditItemRecebimento($param)
    {
        try {

            $data = new stdClass;
            $data->uniqid     = $param['uniqid'];
            $data->detail_id      = $param['uniqid'];
            $data->id_materialresidual = $param['id_materialresidual'];
            $data->nm_materialresidual       = $param['nm_materialresidual'];
            $data->qt_item       = $param['qt_item'];
            $data->vl_unidade     = $param['vl_unidade'];
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
    public static function onDeleteItemRecebimento($param)
    {

        $data = new stdClass;
        $data->uniqid     = '';
        $data->detail_id     = '';
        $data->id_materialresidual = '';
        $data->nm_materialresidual = '';
        $data->qt_item       = '';
        $data->vl_unidade     = '';
        $data->vl_total     = '';

        // send data, do not fire change/exit events
        TForm::sendData(self::$formName, $data, false, false);

        // remove row
        TDataGrid::removeRowById('item_recebimento_material_list', $param['uniqid']);
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


        $total = 0;
        if ($param['list_data']) {
            foreach ($param['list_data'] as $row) {
                $total += (floatval($row['vl_unidade'])) *  floatval($row['qt_item']);
            }
        }
        TForm::sendData(self::$formName, (object) [
            'vl_recebimento' => number_format($total, 2, ',', '.'),
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

            if (empty($param['dt_recebimento'])) {
                TForm::sendData(self::$formName, ['dt_recebimento' => $hoje]);
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

            $recebimento_material = new RecebimentoMaterial();
            $recebimento_material->fromArray((array) $data);


            $recebimento_material->store();

            SaidaBonificacaoService::atualizarSaldo($recebimento_material ,$conn);

            ItemRecebimentoMaterial::where('id_recebimentomaterial', '=', $recebimento_material->id_recebimentomaterial)->delete();

            if (!empty($param['item_recebimento_material_list_id_materialresidual'])) {
                foreach ($param['item_recebimento_material_list_id_materialresidual'] as $key => $item_id) {
                    $item = new ItemRecebimentoMaterial;
                    $item->id_materialresidual  = $item_id;
                    $item->qt_item  = (float) $param['item_recebimento_material_list_qt_item'][$key];
                    $item->vl_unidade      = (float) $param['item_recebimento_material_list_vl_unidade'][$key];
                    $item->vl_total    = (float) $param['item_recebimento_material_list_vl_total'][$key];

                    $item->id_recebimentomaterial  = $recebimento_material->id_recebimentomaterial;
                    $item->store();
                }
            }

            $recebimento_material->store(); // stores the object

            TForm::sendData(self::$formName, (object) ['id_recebimentomaterial' => $recebimento_material->id_recebimentomaterial]);

            $loadPageParam = [];

            if (!empty($param['target_container'])) {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            TTransaction::close(); // close the transaction
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            AdiantiCoreApplication::loadPage('RecebimentoMaterialList', 'onShow', $loadPageParam);
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
                

                $object = new RecebimentoMaterial($key);
                $item = ItemRecebimentoMaterial::where('id_recebimentomaterial', '=', $object->id_recebimentomaterial)->load();

                //Desabilitar campos
                //$this->form->getField('tp_folha')->setEditable(false);

                foreach ($item as $itens) {
                    $material_residuo = MaterialResiduo::where('id_materialresidual', '=', $itens->id_materialresidual)->first();

                    $itens->uniqid = uniqid();
                    $itens->nm_materialresidual = $material_residuo->nm_materialresidual;
                    $row = $this->item_recebimento_material_list->addItem($itens);
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
