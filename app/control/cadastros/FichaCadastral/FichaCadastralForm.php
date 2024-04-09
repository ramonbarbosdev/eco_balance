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

class FichaCadastralForm extends TPage
{
    private $form;
    private static $data_base = 'sample';
    private static $active_Record = 'FichaCadastral';
    private static $primary_Key = 'id_fichacadastral';
    private static $form_Name = 'form_FichaCadastralForm';

    use Adianti\base\AdiantiStandardFormTrait;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['FichaCadastralList', 'onReload'], ['register_state' => 'true']));

        $this->setDatabase('sample');
        $this->setActiveRecord('FichaCadastral');


        // Criação do formulário
        $this->form = new BootstrapFormBuilder('form_FichaCadastralForm');
        $this->form->setFormTitle('Cadastro de Pessoa');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(3, ['col-sm-4', 'col-sm-4', 'col-sm-4']);

        // Criação de fields
        $id = new TEntry('id_fichacadastral');
        $cpf = new TEntry('cpf');
        $nome = new TEntry('nome');
        $dt_nascimento = new TDate('dt_nascimento');
        $sexo = new TCombo('sexo');
        $email = new TEntry('email');
        $fone = new TEntry('fone');
        $cep =  new TEntry('cep');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $estado = new TEntry('estado');
        $cidade = new TEntry('cidade');


        // Validação do campo 
        $dt_nascimento->addValidation('Nascimento', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        $cep->addValidation('cep', new TRequiredValidator);
        $cpf->addValidation('CPF', new TRequiredValidator);

        // Liberar Edição
        $id->setEditable(false);

        // Tamanho Campo
        $id->setSize('100%');
        $dt_nascimento->setSize('100%');
        $cep->setSize('100%');
        $cpf->setSize('100%');

        // Mascaras
        $dt_nascimento->setMask('dd/mm/yyyy');
        $dt_nascimento->setDatabaseMask('yyyy-mm-dd');
        $cpf->setMask('999.999.999-99', true);
        $fone->setMask('(99)99999-99999', true);


        //Propriedades
        $sexo->addItems(['M' => 'Masculino', 'F' => 'Feminino']);
        $cep->setExitAction( new TAction([ $this, 'onExitCEP']) );

        //Fieldes
        $this->form->addFields([new TLabel('Codigo')], [$id],);
        $this->form->addFields([new TLabel('CPF (*)', '#ff0000')], [$cpf], [new TLabel('Nome (*)', '#ff0000')], [$nome]);
        $this->form->addFields([new TLabel('Idade (*)', '#ff0000')], [$dt_nascimento], [new TLabel('Sexo')], [$sexo]);
        //Tab
        $subform = new BootstrapFormBuilder;
        $subform->setFieldSizes('100%');
        $subform->setProperty('style', 'border:none');
        //Tap Endereço
        $subform->appendPage('Endereço');
        $subform->addFields([new TLabel('CEP')], [$cep]);
        $subform->addFields([new TLabel('Logradouro')], [$logradouro], [new TLabel('Numero')], [$numero]);
        $subform->addFields([new TLabel('Complemento')], [$complemento], [new TLabel('Bairro')], [$bairro]);
        $subform->addFields([new TLabel('Estado')], [$estado], [new TLabel('Cidade')], [$cidade]);
        //Tap Contato
        $subform->appendPage('Contato');
        $subform->addFields([new TLabel('Email')], [$email], [new TLabel('fone')], [$fone]);
        $this->form->addContent([$subform]);


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

    public static function onExitCEP($param)
    {
        session_write_close();
        
        try
        {
            $cep = preg_replace('/[^0-9]/', '', $param['cep']);
            $url = 'https://viacep.com.br/ws/'.$cep.'/json/';
            
            $content = @file_get_contents($url);
            
            if ($content !== false)
            {
                $cep_data = json_decode($content);
                
                $data = new stdClass;
                if (is_object($cep_data) && empty($cep_data->erro))
                {
                  
                    
                    $data->logradouro  = $cep_data->logradouro;
                    $data->complemento = $cep_data->complemento;
                    $data->bairro      = $cep_data->bairro;
                    $data->estado      = $cep_data->uf;
                    $data->cidade      = $cep_data->localidade;
                    TForm::sendData(self::$form_Name, $data, false, true);
                    
                }
                else
                {
                    $data->logradouro  = '';
                    $data->complemento = '';
                    $data->bairro      = '';
                    $data->estado   = '';
                    $data->cidade   = '';
                    TForm::sendData(self::$form_Name, $data, false, true);
                    
                }
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }


    // Método fechar
    public function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
