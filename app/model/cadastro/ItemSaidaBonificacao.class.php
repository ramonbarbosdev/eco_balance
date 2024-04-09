<?php

use Adianti\Database\TRecord;


class ItemSaidaBonificacao extends TRecord
{
    const TABLENAME = 'item_saida_bonificacao';
    const PRIMARYKEY= 'id_itemsaidabonificacao';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('id_saida');
        parent::addAttribute('id_produto');
        parent::addAttribute('qt_item');
        parent::addAttribute('vl_unitario');
        parent::addAttribute('vl_total');

    }

    public function get_produto_bonificacao()
    {
        return ProdutoBonificacao::find($this->id_produto);
    }

  
}
