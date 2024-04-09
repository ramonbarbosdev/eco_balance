<?php

use Adianti\Database\TRecord;


class ItemEntradaBonificacao extends TRecord
{
    const TABLENAME = 'item_entrada_bonificacao';
    const PRIMARYKEY= 'id_itementradabonificacao';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('id_entrada');
        parent::addAttribute('id_produto');
        parent::addAttribute('qt_item');
        parent::addAttribute('vl_reais');
        parent::addAttribute('vl_total');

    }

    public function get_produto_bonificacao()
    {
        return ProdutoBonificacao::find($this->id_produto);
    }

  
}
