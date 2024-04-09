<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

require_once(PATH . '/app/service/cadastro/RecebimentoMaterialService.php');

class RecebimentoMaterialList extends TPage
{
  private $form;
  private $datagrid;
  private $pageNavigation;
  private $formgrid;
  private $deleteButton;
  private static $data_base = 'sample';
  private static $active_Record = 'RecebimentoMaterial';
  private static $primaryKey = 'id_recebimentomaterial';
  private static $formName = 'form_RecebimentoMaterialList';

  use Adianti\base\AdiantiStandardListTrait;

  public function __construct()
  {

    parent::__construct();


    //Conexão com a tabela
    $this->setdatabase('sample');
    $this->setactiveRecord('RecebimentoMaterial');
    $this->setDefaultOrder('id_recebimentomaterial', 'asc');
    $this->setLimit(10);


    $this->addFilterField('id_fichacadastral', '=', 'id_fichacadastral');

    //Criação do formulario 
    $this->form = new BootstrapFormBuilder('form_RecebimentoMaterialList');
    $this->form->setFormTitle('Recebimento de Materias');
    // expand button
    $this->form->addExpandButton();

    //Criação de fields
   
    $campo1 = new TDBUniqueSearch('id_fichacadastral', 'sample', 'FichaCadastral', 'id_fichacadastral', 'nome');



    $this->form->addFields([new TLabel('Pessoa')], [$campo1]);

    //Tamanho dos fields
    $campo1->setSize('100%');


    //Propriedades
    $campo1->setMinLength(0);


    $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

    //Adicionar field de busca
    $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
    $btn->class = 'btn btn-sm btn-primary';
    $this->form->addActionLink(_t('New'), new TAction(['RecebimentoMaterialForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');

    //Criando a data grid
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->style = 'width: 100%';

    //Criando colunas da datagrid
    $column_1 = new TDataGridColumn('id_recebimentomaterial', 'Codigo', 'center');
    $column_2 = new TDataGridColumn('ficha_cadastral->nome', 'Pessoa', 'center',);
    $column_3 = new TDataGridColumn('vl_recebimento', 'Valor Recebimento', 'center',);
    $column_4 = new TDataGridColumn('dt_recebimento', 'Data de Recebimento', 'center',);
    // $column_5 = new TDataGridColumn('status_recebimento', 'Status', 'left',);

    //Transformações
    $column_3->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
      if (is_numeric($value)) {
        return 'R$ ' . number_format($value, 2, ',', '.');
      }
      return $value;
    });

    $column_4->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
      return date('d/m/Y', strtotime($value));
    });




    //add coluna da datagrid
    $this->datagrid->addColumn($column_1);
    $this->datagrid->addColumn($column_2);
    $this->datagrid->addColumn($column_3);
    $this->datagrid->addColumn($column_4);
    //$this->datagrid->addColumn($column_5);

    //Criando ações para o datagrid
    $column_1->setAction(new TAction([$this, 'onReload']), ['order' => 'id_recebimentomaterial']);
    $column_2->setAction(new TAction([$this, 'onReload']), ['order' => 'id_fichacadastral']);
    $column_2->setAction(new TAction([$this, 'onReload']), ['order' => 'id_materialresidual']);


    $action1 = new TDataGridAction(['RecebimentoMaterialForm', 'onEdit'], ['id' => '{id_recebimentomaterial}', 'register_state' => 'false']);
    $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id_recebimentomaterial}']);

    //Adicionando a ação na tela
    $this->datagrid->addAction($action1, _t('Edit'), 'fa:edit blue');
    $this->datagrid->addAction($action2, _t('Delete'), 'fa:trash-alt red');


    //Criar datagrid 
    $this->datagrid->createModel();

    //Criação de paginador
    $this->pageNavigation = new TPageNavigation;
    $this->pageNavigation->setAction(new TAction([$this, 'onReload']));



    //Enviar para tela
    $panel = new TPanelGroup('', 'white');
    $panel->add($this->datagrid);
    $panel->addFooter($this->pageNavigation);

    //Exportar
    $drodown = new TDropDown('Exportar', 'fa:list');
    $drodown->setPullSide('right');
    $drodown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
    $drodown->addAction('Salvar como CSV', new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table green');
    $drodown->addAction('Salvar como PDF', new TAction([$this, 'onExportPDF'], ['register_state' => 'false',  'static' => '1']), 'fa:file-pdf red');
    $panel->addHeaderWidget($drodown);

    //Vertical container
    $container = new TVBox;
    $container->style = 'width: 100%';
    $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
    $container->add($this->form);
    $container->add($panel);

    parent::add($container);
  }

  public function onDelete($param = null)
  {
    $loadPageParam = [];

    if (!empty($param['target_container'])) {
      $loadPageParam['target_container'] = $param['target_container'];
    }

    if (isset($param['delete']) && $param['delete'] == 1) {
      try {

        $key = $param['key'];

        $conn = TTransaction::open(self::$data_base);

        $object = new RecebimentoMaterial($key, FALSE);

        RecebimentoMaterialService::excluir($object,$conn);

        TTransaction::close();

        TToast::show('success', AdiantiCoreTranslator::translate('Record deleted'), 'topRight', 'far:check-circle');
      } catch (Exception $e) // in case of exception
      {
        // shows the exception error message
        new TMessage('error', $e->getMessage());
        // undo all pending operations
        TTransaction::rollback();
      }
    } else {
      // define the delete action
      $action = new TAction(array($this, 'onDelete'));
      $action->setParameters($param); // pass the key paramseter ahead
      $action->setParameter('delete', 1);
      // shows a dialog to the user
      new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    AdiantiCoreApplication::loadPage('RecebimentoMaterialList', 'onShow', $loadPageParam);
  }
}
