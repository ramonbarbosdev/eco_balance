<?php

//require_once (PATH . '/app/utils/W5iSequencia.php');

use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;



class ControleSaldoService
{

    /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Saldo por produto
    */
    public static function consultaSaldoProduto($conn)
    {

        $consulta = $conn->query("
                            SELECT 
                            pb.id_produto,
                            pb.nm_produto,
                            COALESCE(SUM(ieb.qt_item), 0) - COALESCE(SUM(isb.qt_item_saida), 0) AS saldo_quantidade,
                            COALESCE(SUM(ieb_total.vl_total), 0) - COALESCE(SUM(isb.vl_total), 0) AS saldo_valor_reais,
                            COALESCE((SUM(ieb_total.vl_total) - COALESCE(SUM(isb.vl_total), 0)) / 3.50, 0) AS valor_eco
                        FROM produto_bonificacao pb
                        LEFT JOIN (
                            SELECT id_produto, SUM(qt_item) as qt_item
                            FROM item_entrada_bonificacao
                            GROUP BY id_produto
                        ) ieb ON pb.id_produto = ieb.id_produto
                        LEFT JOIN (
                            SELECT id_produto, SUM(qt_item) as qt_item_saida, SUM(vl_total) as vl_total
                            FROM item_saida_bonificacao
                            GROUP BY id_produto
                        ) isb ON pb.id_produto = isb.id_produto
                        LEFT JOIN (
                            SELECT id_produto, SUM(vl_total) as vl_total
                            FROM item_entrada_bonificacao
                            GROUP BY id_produto
                        ) ieb_total ON pb.id_produto = ieb_total.id_produto
                        GROUP BY pb.id_produto, pb.nm_produto;

                                ");

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Saldo por pessoa
    */
    public static function consultaSaldoPessoa($conn)
    {

        $consulta = $conn->query("
                                select 
                                fc.cpf 
                                ,nome
                                ,COALESCE(SUM(irm.qt_item), 0)as qt_materiaisresiduais 
                                ,COALESCE(SUM(irm.vl_total), 0)as vl_eco 
                                from ficha_cadastral fc 
                                join recebimento_material rm on fc.id_fichacadastral = rm.id_fichacadastral
                                join item_recebimento_material irm on rm.id_recebimentomaterial = irm.id_recebimentomaterial 
                                GROUP BY fc.cpf , fc.nome

                                ");

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    /*
    @author: Ramon
    @created: 04/01/2024
    @summary: Saldo por pessoa
    */
    public static function consultaAcumuloPessoa($conn)
    {

        $consulta = $conn->query("
                            select 
                            fc.id_fichacadastral 
                            ,fc.cpf 
                            ,nome
                            ,rm.vl_recebimento as vl_entradas_eco 
                            ,COALESCE(SUM(isb.vl_total), 0) / 3.50 as vl_saidas_eco 
                            ,COALESCE(SUM(irm.vl_total), 0) - COALESCE(SUM(isb.vl_total), 0) / 3.50 as saldo_eco 
                            from ficha_cadastral fc 
                            inner join recebimento_material rm on fc.id_fichacadastral = rm.id_fichacadastral
                            inner join item_recebimento_material irm on rm.id_recebimentomaterial = irm.id_recebimentomaterial 
                            left join saida_bonificacao sb ON fc.id_fichacadastral = sb.id_fichacadastral
                            left join item_saida_bonificacao isb on sb.id_saida = isb.id_saida
                            GROUP BY fc.id_fichacadastral ,fc.cpf , fc.nome,rm.vl_recebimento 

                                ");

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
}
