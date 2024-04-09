
<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TSqlSelect;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TincidenciaValidator;
use Adianti\Validator\TMinLengthValidator;
use Adianti\Validator\TNumericValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TAlert;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCheckList;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\Tdata;
use Adianti\Widget\Form\Tvl_entradas_eco;
use Adianti\Widget\Form\Tdt_despesa;
use Adianti\Widget\Form\Tdt_despesaTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFieldList;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TFormSeparator;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBvl_entradas_eco;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class SaldoAcumuloPessoaView  extends TPage
{
    private $datagrid;
    private $pdf;

    use Adianti\base\AdiantiStandardListTrait;
    public function __construct()
    {
        parent::__construct();



        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // create the datagrid columns
        $cpf    = new TDataGridColumn('cpf',  'CPF',       'center',  '20%');
        $nome    = new TDataGridColumn('nome',  'Nome',       'center',  '20%');
        $vl_entradas_eco   = new TDataGridColumn('vl_entradas_eco',   'Entradas $Eco',      'center',  '20%');
        $vl_saidas_eco  = new TDataGridColumn('vl_saidas_eco',   'Saidas $Eco',     'center', '20%');
        $saldo_eco   = new TDataGridColumn('saldo_eco',          'Saldo $Eco',      'center', '20%');
       

        // add the columns to the datagrid
        $this->datagrid->addColumn($cpf);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($vl_entradas_eco);
        $this->datagrid->addColumn($vl_saidas_eco);
        $this->datagrid->addColumn($saldo_eco);

        $vl_entradas_eco->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
            if (is_numeric($value)) {
              return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
          });

          
        $vl_saidas_eco->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
            if (is_numeric($value)) {
              return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
          });

          $saldo_eco->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
            if (is_numeric($value)) {
              return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
          });

        // creates the datagrid model
        $this->datagrid->createModel();

 

        $panel = new TPanelGroup('Acumulo por Pessoa');
        $panel->add($this->datagrid);

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);


        parent::add($vbox);
    }

   



    public function onReload($param)
{
    try {
        $conn = TTransaction::open('sample');

        $consulta = ControleSaldoService::consultaAcumuloPessoa($conn);
        
        // Certifique-se de que $consulta é um array de objetos antes de tentar iterar
        if ($consulta && is_array($consulta)) {
            foreach ($consulta as $row) {
                $item = new StdClass;
                $item->cpf           = $row->cpf;
                $item->nome           = $row->nome;
                $item->vl_entradas_eco     = $row->vl_entradas_eco;
                $item->vl_saidas_eco = $row->vl_saidas_eco;
                $item->saldo_eco            = $row->saldo_eco;
        
                $this->datagrid->addItem($item);
            }
        }
        
        TTransaction::close();
        
    } catch (Exception $e) {
        new TMessage('error', $e->getMessage(), $this->afterSaveAction);
        TTransaction::rollback();
    }
}

    /**
     * shows the page
     */
    public function show()
    {
        $this->onReload([]);
        parent::show();
    }

   
}