<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
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
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class EntradaProdutoBonificacaoSeek extends TWindow
{

    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    private static $data_base = 'sample';
    private static $formName = 'form_search_entrada';

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {

        parent::__construct();

        $this->setDatabase('sample');                // defines the database
        $this->setActiveRecord('ItemEntradaBonificacao');            // defines the active record
        $this->setDefaultOrder('id_itementradabonificacao', 'asc');          // defines the default order
        $this->addFilterField('id_produto', '='); // add a filter field
        // $this->addFilterField('unity', '='); 

        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Produtos Disponiveis');

        $campo1 = new TDBUniqueSearch('id_produto', 'sample', 'ProdutoBonificacao', 'id_produto', 'nm_produto');

        //Tamanho dos fields
        $campo1->setSize('100%');
        //Propriedades
        $campo1->setMinLength(0);

        $this->form->addFields([new TLabel('Produto')], [$campo1]);
        $this->form->setData(TSession::getValue('EntradaProdutoBonificacaoSeek_filter_data'));

        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('New',  new TAction(['EntradaBonificacaoForm', 'onEdit']), 'fa:plus green');

        $this->form->addExpandButton();

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        // $this->datagrid->enablePopover('Image', "<img style='max-height: 300px' src='{photo_path}'>");

   
        $col_id          = new TDataGridColumn('id_produto', 'Codigo', 'center', '10%');
        $col_nome = new TDataGridColumn('nm_produto', 'Produto', 'center', '60%');
        $col_valor       = new TDataGridColumn('vl_reais', 'Valor', 'center', '30%');


        $col_valor->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
            if (is_numeric($value)) {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });


        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_valor);

        $action1 = new TDataGridAction([$this, 'onSelect'], ['id' => '{id_entrada}']);
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

            $item_entrada = new ItemEntradaBonificacao($key);

            $produto = new ProdutoBonificacao($item_entrada->id_produto);
            $object = new StdClass;
            $object->id_produto = $item_entrada->id_produto;
            $object->nm_produto = $produto->nm_produto;
            $object->vl_unitario = number_format($item_entrada->vl_reais, 2, ',', '.') ?? '';

            TTransaction::close();


            TForm::sendData('form_SaidaBonificacaoForm', $object);


            parent::closeWindow(); // close the window


        } catch (Exception $e) // em caso de exceção
        {
            // clear fields
            $object = new StdClass;
            $object->id_produto   = '';
            $object->nm_produto = '';
            $object->vl_unitario = '';
            TForm::sendData('form_SaidaBonificacaoForm', $object);

            // undo pending operations
            TTransaction::rollback();
        }
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open(self::$data_base);

            // Ajuste a consulta para agrupar por id_produto e selecionar a entrada mais recente
            $repository = new TRepository('ItemEntradaBonificacao');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('id_itementradabonificacao', 'IN', "(SELECT MAX(id_itementradabonificacao) FROM item_entrada_bonificacao GROUP BY id_produto)"));
            $criteria->setProperties(['order' => 'id_itementradabonificacao ASC']);
            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $produto =  ProdutoBonificacao::where('id_produto', '=', $object->id_produto)->first();
                    $item = new stdClass;
                    $item->id_entrada = $object->id_entrada;
                    $item->id_produto = $object->id_produto;
                    $item->nm_produto = $produto->nm_produto;
                    $item->vl_reais = $object->vl_reais;

                    $this->datagrid->addItem($item);
                }
            }

            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}
