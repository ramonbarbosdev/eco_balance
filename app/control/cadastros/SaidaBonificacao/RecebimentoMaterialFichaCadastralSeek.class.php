<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class RecebimentoMaterialFichaCadastralSeek extends TWindow
{

    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    private static $data_base = 'sample';
    private static $formName = 'form_search_Recebimento';

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {

        parent::__construct();

        $this->setDatabase('sample');                // defines the database
        $this->setActiveRecord('RecebimentoMaterial');            // defines the active record
        $this->setDefaultOrder('id_recebimentomaterial', 'asc');          // defines the default order
        $this->addFilterField('id_fichacadastral', 'like'); // add a filter field
        // $this->addFilterField('unity', '='); 

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Ficha Cadastral de Pessoas');

        $campo1 = new TDBUniqueSearch('id_fichacadastral', 'sample', 'FichaCadastral', 'id_fichacadastral', 'nome');

        //Tamanho dos fields
        $campo1->setSize('100%');
        //Propriedades
        $campo1->setMinLength(0);

        $this->form->addFields([new TLabel('Pessoa')], [$campo1]);
        $this->form->setData(TSession::getValue('RecebimentoMaterialFichaCadastralSeek_filter_data'));

        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('New',  new TAction(['RecebimentoMaterialForm', 'onEdit']), 'fa:plus green');

        $this->form->addExpandButton();

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        // $this->datagrid->enablePopover('Image', "<img style='max-height: 300px' src='{photo_path}'>");

        $col_id          = new TDataGridColumn('id_fichacadastral', 'Codigo', 'center', '10%');
        $col_nome = new TDataGridColumn('ficha_cadastral->nome', 'Nome', 'center', '45%');
        $col_cpf       = new TDataGridColumn('ficha_cadastral->cpf', 'CPF', 'center', '15%');
        $col_valor       = new TDataGridColumn('vl_recebimento', 'Valor do Recebimento', 'center', '15%');

        $col_valor->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
            if (is_numeric($value)) {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });


        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_cpf);
        $this->datagrid->addColumn($col_valor);

        $action1 = new TDataGridAction([$this, 'onSelect'], ['id' => '{id_recebimentomaterial}']);
        $this->datagrid->addAction($action1, 'Select', 'far:hand-pointer blue');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        $panel->getBody()->style = 'overflow-x:auto';
        parent::add($container);
    }


    public function onSelect($param)
    {
        try {
            $key = $param['key'];
            TTransaction::open(self::$data_base);

            // load the active record
            $reebimento_material = new RecebimentoMaterial($key);
            $ficha_cadastral = new FichaCadastral($reebimento_material->id_fichacadastral);
            // closes the transaction
            TTransaction::close();

            $object = new StdClass;
            $object->id_fichacadastral   = $reebimento_material->id_fichacadastral;
            $object->nm_fichacadastral = $ficha_cadastral->nome;

            $object->vl_saldo = (double) str_replace(',', '.', str_replace('.', '', $reebimento_material->vl_recebimento)) ;
            TForm::sendData('form_SaidaBonificacaoForm', $object);

         
            parent::closeWindow(); // close the window


        } catch (Exception $e) // em caso de exceção
        {
            // clear fields
            $object = new StdClass;
            $object->id_fichacadastral   = '';
            $object->nm_fichacadastral = '';
            $object->vl_saldo = '';
            TForm::sendData('form_SaidaBonificacaoForm', $object);

            // undo pending operations
            TTransaction::rollback();
        }
    }
}
