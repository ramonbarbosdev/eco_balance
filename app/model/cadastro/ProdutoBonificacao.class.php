<?php

use Adianti\Database\TRecord;


class ProdutoBonificacao extends TRecord
{
    const TABLENAME = 'produto_bonificacao';
    const PRIMARYKEY= 'id_produto';
    const IDPOLICY =  'max'; // {max, serial}
    
    

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nm_produto');
        parent::addAttribute('ds_produto');
        parent::addAttribute('vl_eco');
        parent::addAttribute('vl_reais');
    }

   
}
