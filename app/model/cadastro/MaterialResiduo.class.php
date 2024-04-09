<?php

use Adianti\Database\TRecord;


class MaterialResiduo extends TRecord
{
    const TABLENAME = 'material_residuo';
    const PRIMARYKEY= 'id_materialresidual';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nm_materialresidual');
        parent::addAttribute('id_unidademedida');
        parent::addAttribute('vl_bonificacao');
        parent::addAttribute('id_tiporesiduo');
    }

    public function get_unidade_medida()
    {
        return UnidadeMedida::find($this->id_unidademedida);
    }
    public function get_tipo_residuo()
    {
        return TipoResiduo::find($this->id_tiporesiduo);
    }
  
}
