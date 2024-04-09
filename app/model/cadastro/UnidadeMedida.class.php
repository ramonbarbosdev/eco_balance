<?php

use Adianti\Database\TRecord;


class UnidadeMedida extends TRecord
{
    const TABLENAME = 'unidade_medida';
    const PRIMARYKEY= 'id_unidademedida';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nm_unidademedida');
        parent::addAttribute('sigla');
    }
}
