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
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class UnidadeMedidaForm extends TPage
{
    private $form;
    private static $data_base = 'sample';
    private static $active_Record = 'UnidadeMedida';
    private static $primary_Key = 'id_unidademedida';
    private static $form_Name = 'form_UnidadeMedidaForm';

    use Adianti\base\AdiantiStandardFormTrait;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['UnidadeMedidaList', 'onReload'], ['register_state' => 'true']));

        $this->setDatabase('sample');
        $this->setActiveRecord('UnidadeMedida');


        // Criação do formulário
        $this->form = new BootstrapFormBuilder('form_UnidadeMedidaForm');
        $this->form->setFormTitle('Nova Unidade de Medida');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(3, ['col-sm-4', 'col-sm-4', 'col-sm-4']);

        // Criação de fields
        $id = new TEntry('id_unidademedida');
        $nm_unidademedida = new TEntry('nm_unidademedida');
        $sigla = new TEntry('sigla');
        // Validação do campo 
        $nm_unidademedida->addValidation('Nome', new TRequiredValidator);
        $sigla->addValidation('Sigla', new TRequiredValidator);
        // Liberar Edição
        $id->setEditable(false);

        // Tamanho Campo
        $id->setSize('100%');
        $nm_unidademedida->setSize('100%');
        $sigla->setSize('100%');
        // Mascaras

        //Propriedades
   
        //Fieldes
        $this->form->addFields([new TLabel('Codigo')], [$id],);
        $this->form->addFields([new TLabel('Unidade Medida (*)', '#ff0000')], [$nm_unidademedida]);
        $this->form->addFields([new TLabel('Sigla (*)', '#ff0000')], [$sigla]);
        

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


    // Método fechar
    public function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
