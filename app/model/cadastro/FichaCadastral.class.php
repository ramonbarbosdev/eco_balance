<?php

use Adianti\Database\TRecord;


class FichaCadastral extends TRecord
{
    const TABLENAME = 'ficha_cadastral';
    const PRIMARYKEY= 'id_fichacadastral';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('cpf');
        parent::addAttribute('nome');
        parent::addAttribute('dt_nascimento');
        parent::addAttribute('sexo');
        parent::addAttribute('email');
        parent::addAttribute('fone');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('estado');
        parent::addAttribute('cidade');
    }
}
