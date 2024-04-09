
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
use Adianti\Widget\Form\Tqt_materiaisresiduais;
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
use Adianti\Widget\Wrapper\TDBqt_materiaisresiduais;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class SaldoPessoaMaterialView  extends TPage
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
        $qt_materiaisresiduais   = new TDataGridColumn('qt_materiaisresiduais',          'Quantidade de Materias',      'center',  '20%');
        $vl_eco   = new TDataGridColumn('vl_eco',          'Total em $Eco',      'center', '20%');
       

        // add the columns to the datagrid
        $this->datagrid->addColumn($cpf);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($qt_materiaisresiduais);
        $this->datagrid->addColumn($vl_eco);

        $vl_eco->setTransformer(function ($value, $object, $row, $cell = null, $last_row = null) {
            if (is_numeric($value)) {
              return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
          });

        // creates the datagrid model
        $this->datagrid->createModel();

 

        $panel = new TPanelGroup('Saldo Material por Pessoa');
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

        $consulta = ControleSaldoService::consultaSaldoPessoa($conn);
        
        // Certifique-se de que $consulta é um array de objetos antes de tentar iterar
        if ($consulta && is_array($consulta)) {
            foreach ($consulta as $row) {
                $item = new StdClass;
                $item->cpf           = $row->cpf;
                $item->nome           = $row->nome;
                $item->qt_materiaisresiduais     = $row->qt_materiaisresiduais;
                $item->vl_eco            = $row->vl_eco;
        
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