<?php

use Adianti\Database\TRecord;


class SaidaBonificacao extends TRecord
{
    const TABLENAME = 'saida_bonificacao';
    const PRIMARYKEY= 'id_saida';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('id_fichacadastral');
        parent::addAttribute('dt_saida');
        parent::addAttribute('vl_ecototal');
        parent::addAttribute('vl_saldo');
        parent::addAttribute('status');
        parent::addAttribute('vl_reaistotal');

    }

    public function get_ficha_cadastral()
    {
        return FichaCadastral::find($this->id_fichacadastral);
    }
    
    //Excluir os filhos a essa tablea
    public function onBeforeDelete()
    {
        ItemSaidaBonificacao::where("id_saida", "=", $this->id_saida)
                ->delete(); 
    }
}
