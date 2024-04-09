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
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TAlert;
use Adianti\Widget\Dialog\TMessage;
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
use Adianti\Widget\Form\TText;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class ProdutoBonificacaoForm extends TPage
{
    private $form;
    private static $data_base = 'sample';
    private static $active_Record = 'ProdutoBonificacao';
    private static $primaryKey = 'id_produto';
    private static $formName = 'form_ProdutoBonificacaoForm';


    use Adianti\base\AdiantiStandardFormTrait;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['ProdutoBonificacaoList', 'onReload'], ['register_state' => 'true']));

        $this->setDatabase('sample');
        $this->setActiveRecord('ProdutoBonificacao');


        // Criação do formulário
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle('Cadastro de Produto');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(3, ['col-sm-4', 'col-sm-4', 'col-sm-4']);

        // Criação de fields
        $id = new THidden('id_produto');
        $nm_produto = new TEntry('nm_produto');
        $ds_produto = new  TText('ds_produto');
        $vl_eco = new TNumeric('vl_eco', 2, ',', '.');
        $vl_reais = new TNumeric('vl_reais', 2, ',', '.');

        // Validação do campo 
        $nm_produto->addValidation('Nome', new TRequiredValidator);
        $ds_produto->addValidation('Descrição', new TRequiredValidator);
        $vl_eco->addValidation('Valor em Eco', new TRequiredValidator);
        $vl_reais->addValidation('Valor em Reais', new TRequiredValidator);

        // Liberar Edição
        $id->setEditable(false);
        $vl_reais->setEditable(false);

        // Tamanho Campo
        $id->setSize('100%');
        $nm_produto->setSize('100%');
        $ds_produto->setSize('100%');
        $vl_eco->setSize('100%');
        $vl_reais->setSize('100%');

        // Mascaras

        //Placeholders
        $vl_eco->setId('id_vl_eco');
        TScript::create("document.getElementById('id_vl_eco').placeholder = '0,00';");
        $vl_reais->setId('id_vl_reais');
        TScript::create("document.getElementById('id_vl_reais').placeholder = '0,00';");

        //Propriedades

        //Ações
        $vl_eco_action = new TAction(array($this, 'onChangeValorEco'));
        $vl_eco->setExitAction($vl_eco_action);


        //Fieldes

        $row1 =  $this->form->addFields([new TLabel(''), $id,]);
        $row1->layout = ['col-sm-6'];

        $row2 =  $this->form->addFields([new TLabel('Nome do Produto (*)', '#ff0000'), $nm_produto]);
        $row2->layout = ['col-sm-12'];

        $row3 =  $this->form->addFields([new TLabel('Descrição (*)', '#ff0000'), $ds_produto]);
        $row3->layout = ['col-sm-12'];

        $row4 =  $this->form->addFields([new TLabel('Valor em Eco (*)', '#ff0000'), $vl_eco], [new TLabel('Valor em Reais'), $vl_reais]);
        $row4->layout = ['col-sm-6', 'col-sm-6'];

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
    @created: 04/02/2024
    @summary:Carregar ações quando o vl_eco for informada
    */
    public static function onChangeValorEco($param)
    {
        if (!empty($param['vl_eco'])) {
            try {

                ProdutoBonificacaoService::converterReais(self::$formName, $param);
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        } else {
        }
    }
    // Método fechar
    public function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
