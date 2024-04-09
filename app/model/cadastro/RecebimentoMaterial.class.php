<?php

use Adianti\Database\TRecord;


class RecebimentoMaterial extends TRecord
{
    const TABLENAME = 'recebimento_material';
    const PRIMARYKEY= 'id_recebimentomaterial';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('id_fichacadastral');
        parent::addAttribute('dt_recebimento');
        parent::addAttribute('local_entrega');
        parent::addAttribute('status_recebimento');
        parent::addAttribute('vl_recebimento');

    }

    public function get_ficha_cadastral()
    {
        return FichaCadastral::find($this->id_fichacadastral);
    }
    
    //Excluir os filhos a essa tablea
    public function onBeforeDelete()
    {
        ItemRecebimentoMaterial::where("id_recebimentomaterial", "=", $this->id_recebimentomaterial)
                ->delete(); 
    }
}
