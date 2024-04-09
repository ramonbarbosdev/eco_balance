<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
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

require_once(PATH . '/app/service/cadastro/ProdutoBonificacaoService.php');


class ProdutoBonificacaoList extends TPage
{
  private $form;
  private $datagrid;
  private $pageNavigation;
  private $formgrid;
  private $deleteButton;
  private static $data_base = 'sample';
  private static $active_Record = 'ProdutoBonificacao';
  private static $primaryKey = 'id_produto';
  private static $formName = 'form_ProdutoBonificacaoList';

  use Adianti\base\AdiantiStandardListTrait;

  public function __construct()
  {

    parent::__construct();


    //Conexão com a tabela
    $this->setDatabase('sample');
    $this->setActiveRecord('ProdutoBonificacao');
    $this->setDefaultOrder('id_produto', 'asc');
    $this->setLimit(10);

    $this->addFilterField('nm_produto', 'ilike', 'nm_produto');

    //Criação do formulario 
    $this->form = new BootstrapFormBuilder(self::$formName);
    $this->form->setFormTitle('Produtos');

    // expand button
    $this->form->addExpandButton();

    //Criação de fields
    $campo1 = new TEntry('nm_produto');


    $this->form->addFields([new TLabel('Produto')], [$campo1]);

    //Tamanho dos fields
    $campo1->setSize('100%');

    //Propriedades


    $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

    //Adicionar field de busca
    $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
    $btn->class = 'btn btn-sm btn-primary';
    $this->form->addActionLink(_t('New'), new TAction(['ProdutoBonificacaoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');

    //Criando a data grid
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->style = 'width: 100%';

    //Criando colunas da datagrid
    $column_1 = new TDataGridColumn('id_produto', 'Codigo', 'center');
    $column_2 = new TDataGridColumn('nm_produto', 'Produto', 'center',);
    $column_3 = new TDataGridColumn('vl_eco', 'Valor $Eco', 'center',);
    $column_4 = new TDataGridColumn('vl_reais', 'Valor R$', 'center',);

    //Transformação
    $column_3->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
      if (is_numeric($value)) {
        return 'R$ ' . number_format($value, 2, ',', '.');
      }
      return $value;
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


    //Criando ações para o datagrid
    $column_1->setAction(new TAction([$this, 'onReload']), ['order' => 'id_produto']);
    $column_2->setAction(new TAction([$this, 'onReload']), ['order' => 'nm_produto']);


    $action1 = new TDataGridAction(['ProdutoBonificacaoForm', 'onEdit'], ['id' => '{id_produto}', 'register_state' => 'false']);
    $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id_produto}']);

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

        $conn= TTransaction::open(self::$data_base);

        $object = new ProdutoBonificacao($key, FALSE);

        ProdutoBonificacaoService::excluir($object,$conn);

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
    AdiantiCoreApplication::loadPage('ProdutoBonificacaoList', 'onShow', $loadPageParam);
  }
}
