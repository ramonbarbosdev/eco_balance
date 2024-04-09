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
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

require_once(PATH . '/app/service/cadastro/EntradaBonificacaoService.php');

class EntradaBonificacaoList extends TPage
{
  private $form;
  private $datagrid;
  private $pageNavigation;
  private $formgrid;
  private $deleteButton;
  private static $data_base = 'sample';
  private static $active_Record = 'EntradaBonificacao';
  private static $primaryKey = 'id_entrada';
  private static $formName = 'form_EntradaBonificacaoList';

  use Adianti\base\AdiantiStandardListTrait;

  public function __construct()
  {

    parent::__construct();


    //Conexão com a tabela
    $this->setdatabase('sample');
    $this->setactiveRecord('EntradaBonificacao');
    $this->setDefaultOrder('id_entrada', 'asc');
    $this->setLimit(10);

    $this->addFilterField('nu_nota', 'ilike', 'nu_nota');
    $this->addFilterField('dt_entrada', '=', 'dt_entrada');

    //Criação do formulario 
    $this->form = new BootstrapFormBuilder(self::$formName);
    $this->form->setFormTitle('Entrada de Bonificações');
    // expand button
    $this->form->addExpandButton();

    //Criação de fields
   
    $campo1 = new TDBUniqueSearch('nu_nota', 'sample', 'EntradaBonificacao', 'nu_nota', 'nu_nota');
    $campo2 = new TDate('dt_entrada');



    $this->form->addFields([new TLabel('Nota Fiscal')], [$campo1]);
    $this->form->addFields([new TLabel('Data da entrada')], [$campo2]);

    //Tamanho dos fields
    $campo1->setSize('100%');
    $campo2->setSize('100%');


    //Propriedades
    $campo1->setMinLength(0);

    $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

    //Adicionar field de busca
    $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
    $btn->class = 'btn btn-sm btn-primary';
    $this->form->addActionLink(_t('New'), new TAction(['EntradaBonificacaoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');

    //Criando a data grid
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->style = 'width: 100%';

    //Criando colunas da datagrid
    $column_1 = new TDataGridColumn('id_entrada', 'Codigo', 'center');
    $column_2 = new TDataGridColumn('nu_nota', 'Nota Fiscal', 'center',);
    $column_3 = new TDataGridColumn('dt_entrada', 'Data da Entrada', 'center',);
    $column_4 = new TDataGridColumn('vl_reaistotal', 'Valor', 'center',);
    // $column_5 = new TDataGridColumn('status_recebimento', 'Status', 'left',);

    //Transformações
    $column_3->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
      return date('d/m/Y', strtotime($value));
    });

    $column_4->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
      if (is_numeric($value)) {
        return 'R$ ' . number_format($value, 2, ',', '.');
      }
      return $value;
    });




    //add coluna da datagrid
    $this->datagrid->addColumn($column_1);
    $this->datagrid->addColumn($column_2);
    $this->datagrid->addColumn($column_3);
    $this->datagrid->addColumn($column_4);
    //$this->datagrid->addColumn($column_5);

    //Criando ações para o datagrid
    $column_1->setAction(new TAction([$this, 'onReload']), ['order' => 'id_entrada']);
    $column_2->setAction(new TAction([$this, 'onReload']), ['order' => 'id_fichacadastral']);
    $column_2->setAction(new TAction([$this, 'onReload']), ['order' => 'id_materialresidual']);


    $action1 = new TDataGridAction(['EntradaBonificacaoForm', 'onEdit'], ['id' => '{id_entrada}', 'register_state' => 'false']);
    $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id_entrada}']);

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

        $object = new EntradaBonificacao($key, FALSE);
        $item_entrada = ItemEntradaBonificacao::where('id_entrada', '=',$object->id_entrada )->load();

        //ControleSaldoService::movimentoExcluirEntrada($item_entrada, $conn);

        EntradaBonificacaoService::excluir($object, $item_entrada,  $conn);

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
    AdiantiCoreApplication::loadPage('EntradaBonificacaoList', 'onShow', $loadPageParam);
  }
}
