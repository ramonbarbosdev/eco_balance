<?php

use Adianti\Database\TRecord;


class ItemRecebimentoMaterial extends TRecord
{
    const TABLENAME = 'item_recebimento_material';
    const PRIMARYKEY= 'id_itemrecebimentomaterial';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('id_recebimentomaterial');
        parent::addAttribute('id_materialresidual');
        parent::addAttribute('qt_item');
        parent::addAttribute('vl_unidade');
        parent::addAttribute('vl_total');

    }

    public function get_recebimento_material()
    {
        return RecebimentoMaterial::find($this->id_recebimentomaterial);
    }
    public function get_material_residuo()
    {
        return MaterialResiduo::find($this->id_materialresidual);
    }
  
}
