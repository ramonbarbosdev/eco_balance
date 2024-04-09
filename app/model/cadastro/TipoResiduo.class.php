<?php

use Adianti\Database\TRecord;


class TipoResiduo extends TRecord
{
    const TABLENAME = 'tipo_residuo';
    const PRIMARYKEY= 'id_tiporesiduo';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nm_tiporesiduo');
     
    }
}
