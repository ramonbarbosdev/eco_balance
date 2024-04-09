<?php

use Adianti\Database\TRecord;


class EntradaBonificacao extends TRecord
{
    const TABLENAME = 'entrada_bonificacao';
    const PRIMARYKEY= 'id_entrada';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('dt_entrada');
        parent::addAttribute('nu_nota');
        parent::addAttribute('dt_emissao');
        parent::addAttribute('vl_reaistotal');
        parent::addAttribute('vl_ecototal');

    }

    
    //Excluir os filhos a essa tablea
    public function onBeforeDelete()
    {
        ItemEntradaBonificacao::where("id_entrada", "=", $this->id_entrada)
                ->delete(); 
    }
}
