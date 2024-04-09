--
-- PostgreSQL database cluster dump
--

-- Started on 2024-02-05 23:51:44

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Roles
--

CREATE ROLE postgres;
ALTER ROLE postgres WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN REPLICATION BYPASSRLS;

--
-- User Configurations
--








--
-- Databases
--

--
-- Database "template1" dump
--

\connect template1

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.5
-- Dumped by pg_dump version 15.5

-- Started on 2024-02-05 23:51:44

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

-- Completed on 2024-02-05 23:51:44

--
-- PostgreSQL database dump complete
--

--
-- Database "eco_balance" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.5
-- Dumped by pg_dump version 15.5

-- Started on 2024-02-05 23:51:44

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 3806 (class 1262 OID 16926)
-- Name: eco_balance; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE eco_balance WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Portuguese_Brazil.1252';


ALTER DATABASE eco_balance OWNER TO postgres;

\connect eco_balance

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16384)
-- Name: adminpack; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS adminpack WITH SCHEMA pg_catalog;


--
-- TOC entry 3807 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION adminpack; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION adminpack IS 'administrative functions for PostgreSQL';


--
-- TOC entry 274 (class 1255 OID 17620)
-- Name: buscar_id_em_tabelas(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.buscar_id_em_tabelas(p_id integer) RETURNS TABLE(table_name text, result_set jsonb)
    LANGUAGE plpgsql
    AS $_$
DECLARE
    table_name_text TEXT;
BEGIN
    -- Loop através de todas as tabelas
    FOR table_name_text IN (SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = 'public') 
    LOOP
        -- Construir a consulta dinâmica
        RETURN QUERY EXECUTE 'SELECT * FROM ' || table_name_text || ' WHERE id = $1' USING p_id;
    END LOOP;
END;
$_$;


ALTER FUNCTION public.buscar_id_em_tabelas(p_id integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 267 (class 1259 OID 17622)
-- Name: entrada_bonificacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.entrada_bonificacao (
    id_entrada integer NOT NULL,
    dt_entrada date NOT NULL,
    nu_nota character varying,
    dt_emissao date,
    vl_reaistotal real NOT NULL,
    vl_ecototal real
);


ALTER TABLE public.entrada_bonificacao OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 17621)
-- Name: entrada_bonificacao_id_entrada_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.entrada_bonificacao_id_entrada_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.entrada_bonificacao_id_entrada_seq OWNER TO postgres;

--
-- TOC entry 3808 (class 0 OID 0)
-- Dependencies: 266
-- Name: entrada_bonificacao_id_entrada_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.entrada_bonificacao_id_entrada_seq OWNED BY public.entrada_bonificacao.id_entrada;


--
-- TOC entry 253 (class 1259 OID 17425)
-- Name: ficha_cadastral; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ficha_cadastral (
    id_fichacadastral integer NOT NULL,
    cpf character varying(11) NOT NULL,
    nome character varying(100) NOT NULL,
    dt_nascimento date NOT NULL,
    sexo character(1) NOT NULL,
    email character varying(100),
    fone character varying(20),
    cep character varying(8),
    logradouro character varying(255),
    numero character varying(10),
    complemento character varying(100),
    bairro character varying(100),
    estado character varying(2),
    cidade character varying(100)
);


ALTER TABLE public.ficha_cadastral OWNER TO postgres;

--
-- TOC entry 252 (class 1259 OID 17424)
-- Name: ficha_cadastral_id_fichacadastral_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ficha_cadastral_id_fichacadastral_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ficha_cadastral_id_fichacadastral_seq OWNER TO postgres;

--
-- TOC entry 3809 (class 0 OID 0)
-- Dependencies: 252
-- Name: ficha_cadastral_id_fichacadastral_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ficha_cadastral_id_fichacadastral_seq OWNED BY public.ficha_cadastral.id_fichacadastral;


--
-- TOC entry 271 (class 1259 OID 17763)
-- Name: item_entrada_bonificacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.item_entrada_bonificacao (
    id_itementradabonificacao integer NOT NULL,
    id_entrada integer,
    id_produto integer,
    qt_item integer,
    vl_reais real,
    vl_total real
);


ALTER TABLE public.item_entrada_bonificacao OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 17762)
-- Name: item_entrada_bonificacao_id_itementradabonificacao_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.item_entrada_bonificacao_id_itementradabonificacao_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.item_entrada_bonificacao_id_itementradabonificacao_seq OWNER TO postgres;

--
-- TOC entry 3810 (class 0 OID 0)
-- Dependencies: 270
-- Name: item_entrada_bonificacao_id_itementradabonificacao_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.item_entrada_bonificacao_id_itementradabonificacao_seq OWNED BY public.item_entrada_bonificacao.id_itementradabonificacao;


--
-- TOC entry 263 (class 1259 OID 17561)
-- Name: item_recebimento_material; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.item_recebimento_material (
    id_itemrecebimentomaterial integer NOT NULL,
    id_recebimentomaterial integer,
    id_materialresidual integer,
    qt_item real,
    vl_unidade real,
    vl_total real
);


ALTER TABLE public.item_recebimento_material OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 17560)
-- Name: item_recebimento_material_id_itemrecebimentomaterial_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.item_recebimento_material_id_itemrecebimentomaterial_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.item_recebimento_material_id_itemrecebimentomaterial_seq OWNER TO postgres;

--
-- TOC entry 3811 (class 0 OID 0)
-- Dependencies: 262
-- Name: item_recebimento_material_id_itemrecebimentomaterial_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.item_recebimento_material_id_itemrecebimentomaterial_seq OWNED BY public.item_recebimento_material.id_itemrecebimentomaterial;


--
-- TOC entry 273 (class 1259 OID 17770)
-- Name: item_saida_bonificacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.item_saida_bonificacao (
    id_itemsaidabonificacao integer NOT NULL,
    id_saida integer,
    id_produto integer,
    qt_item integer,
    vl_unitario numeric(10,2),
    vl_total numeric(10,2)
);


ALTER TABLE public.item_saida_bonificacao OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 17769)
-- Name: item_saida_bonificacao_id_itemsaidabonificacao_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.item_saida_bonificacao_id_itemsaidabonificacao_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.item_saida_bonificacao_id_itemsaidabonificacao_seq OWNER TO postgres;

--
-- TOC entry 3812 (class 0 OID 0)
-- Dependencies: 272
-- Name: item_saida_bonificacao_id_itemsaidabonificacao_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.item_saida_bonificacao_id_itemsaidabonificacao_seq OWNED BY public.item_saida_bonificacao.id_itemsaidabonificacao;


--
-- TOC entry 259 (class 1259 OID 17467)
-- Name: material_residuo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.material_residuo (
    id_materialresidual integer NOT NULL,
    nm_materialresidual character varying(100) NOT NULL,
    id_unidademedida integer,
    vl_bonificacao real NOT NULL,
    id_tiporesiduo integer
);


ALTER TABLE public.material_residuo OWNER TO postgres;

--
-- TOC entry 258 (class 1259 OID 17466)
-- Name: material_residuo_id_materialresidual_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.material_residuo_id_materialresidual_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.material_residuo_id_materialresidual_seq OWNER TO postgres;

--
-- TOC entry 3813 (class 0 OID 0)
-- Dependencies: 258
-- Name: material_residuo_id_materialresidual_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.material_residuo_id_materialresidual_seq OWNED BY public.material_residuo.id_materialresidual;


--
-- TOC entry 265 (class 1259 OID 17599)
-- Name: produto_bonificacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.produto_bonificacao (
    id_produto integer NOT NULL,
    nm_produto character varying(100) NOT NULL,
    ds_produto text,
    vl_eco real NOT NULL,
    vl_reais real
);


ALTER TABLE public.produto_bonificacao OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 17598)
-- Name: produto_bonificacao_id_produto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.produto_bonificacao_id_produto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.produto_bonificacao_id_produto_seq OWNER TO postgres;

--
-- TOC entry 3814 (class 0 OID 0)
-- Dependencies: 264
-- Name: produto_bonificacao_id_produto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.produto_bonificacao_id_produto_seq OWNED BY public.produto_bonificacao.id_produto;


--
-- TOC entry 261 (class 1259 OID 17484)
-- Name: recebimento_material; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recebimento_material (
    id_recebimentomaterial integer NOT NULL,
    id_fichacadastral integer,
    dt_recebimento date NOT NULL,
    local_entrega character varying(255),
    status_recebimento character varying(50),
    vl_recebimento real
);


ALTER TABLE public.recebimento_material OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 17483)
-- Name: recebimento_material_id_recebimentomaterial_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recebimento_material_id_recebimentomaterial_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.recebimento_material_id_recebimentomaterial_seq OWNER TO postgres;

--
-- TOC entry 3815 (class 0 OID 0)
-- Dependencies: 260
-- Name: recebimento_material_id_recebimentomaterial_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recebimento_material_id_recebimentomaterial_seq OWNED BY public.recebimento_material.id_recebimentomaterial;


--
-- TOC entry 269 (class 1259 OID 17662)
-- Name: saida_bonificacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.saida_bonificacao (
    id_saida integer NOT NULL,
    id_fichacadastral integer NOT NULL,
    dt_saida date NOT NULL,
    vl_ecototal real NOT NULL,
    vl_saldo real NOT NULL,
    status character varying(50),
    vl_reaistotal real
);


ALTER TABLE public.saida_bonificacao OWNER TO postgres;

--
-- TOC entry 268 (class 1259 OID 17661)
-- Name: saida_bonificacao_id_saida_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.saida_bonificacao_id_saida_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saida_bonificacao_id_saida_seq OWNER TO postgres;

--
-- TOC entry 3816 (class 0 OID 0)
-- Dependencies: 268
-- Name: saida_bonificacao_id_saida_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.saida_bonificacao_id_saida_seq OWNED BY public.saida_bonificacao.id_saida;


--
-- TOC entry 236 (class 1259 OID 17206)
-- Name: system_access_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_access_log (
    id integer NOT NULL,
    sessionid character varying(256),
    login character varying(256),
    login_time timestamp without time zone,
    login_year character varying(4),
    login_month character varying(2),
    login_day character varying(2),
    logout_time timestamp without time zone,
    impersonated character(1),
    access_ip character varying(45),
    impersonated_by character varying(200)
);


ALTER TABLE public.system_access_log OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 17220)
-- Name: system_access_notification_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_access_notification_log (
    id integer NOT NULL,
    login character varying(256),
    email character varying(256),
    ip_address character varying(256),
    login_time character varying(256)
);


ALTER TABLE public.system_access_notification_log OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 17192)
-- Name: system_change_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_change_log (
    id integer NOT NULL,
    logdate timestamp without time zone,
    login character varying(256),
    tablename character varying(256),
    primarykey character varying(256),
    pkvalue character varying(256),
    operation character varying(256),
    columnname character varying(256),
    oldvalue text,
    newvalue text,
    access_ip character varying(256),
    transaction_id character varying(256),
    log_trace text,
    session_id character varying(256),
    class_name character varying(256),
    php_sapi character varying(256),
    log_year character varying(4),
    log_month character varying(2),
    log_day character varying(2)
);


ALTER TABLE public.system_change_log OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 17021)
-- Name: system_document; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_document (
    id integer NOT NULL,
    system_user_id integer,
    title character varying(256),
    description character varying(4096),
    category_id integer,
    submission_date date,
    archive_date date,
    filename character varying(512),
    in_trash character(1),
    system_folder_id integer
);


ALTER TABLE public.system_document OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 17058)
-- Name: system_document_bookmark; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_document_bookmark (
    id integer NOT NULL,
    system_user_id integer,
    system_document_id integer
);


ALTER TABLE public.system_document_bookmark OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 16986)
-- Name: system_document_category; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_document_category (
    id integer NOT NULL,
    name character varying(256)
);


ALTER TABLE public.system_document_category OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 17048)
-- Name: system_document_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_document_group (
    id integer NOT NULL,
    document_id integer,
    system_group_id integer
);


ALTER TABLE public.system_document_group OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 17038)
-- Name: system_document_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_document_user (
    id integer NOT NULL,
    document_id integer,
    system_user_id integer
);


ALTER TABLE public.system_document_user OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16991)
-- Name: system_folder; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_folder (
    id integer NOT NULL,
    system_user_id integer,
    created_at date,
    name character varying(256) NOT NULL,
    in_trash character(1),
    system_folder_parent_id integer
);


ALTER TABLE public.system_folder OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 17068)
-- Name: system_folder_bookmark; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_folder_bookmark (
    id integer NOT NULL,
    system_user_id integer,
    system_folder_id integer
);


ALTER TABLE public.system_folder_bookmark OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 17011)
-- Name: system_folder_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_folder_group (
    id integer NOT NULL,
    system_folder_id integer,
    system_group_id integer
);


ALTER TABLE public.system_folder_group OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 17001)
-- Name: system_folder_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_folder_user (
    id integer NOT NULL,
    system_folder_id integer,
    system_user_id integer
);


ALTER TABLE public.system_folder_user OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 17253)
-- Name: system_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_group (
    id integer NOT NULL,
    name character varying(256)
);


ALTER TABLE public.system_group OWNER TO postgres;

--
-- TOC entry 248 (class 1259 OID 17346)
-- Name: system_group_program; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_group_program (
    id integer NOT NULL,
    system_group_id integer,
    system_program_id integer
);


ALTER TABLE public.system_group_program OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 16972)
-- Name: system_message; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_message (
    id integer NOT NULL,
    system_user_id integer,
    system_user_to_id integer,
    subject character varying(256),
    message text,
    dt_message character varying(256),
    checked character(1)
);


ALTER TABLE public.system_message OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16979)
-- Name: system_notification; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_notification (
    id integer NOT NULL,
    system_user_id integer,
    system_user_to_id integer,
    subject character varying(256),
    message text,
    dt_message character varying(256),
    action_url character varying(4096),
    action_label character varying(256),
    icon character varying(256),
    checked character(1)
);


ALTER TABLE public.system_notification OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 17078)
-- Name: system_post; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_post (
    id integer NOT NULL,
    system_user_id integer,
    title character varying(256) NOT NULL,
    content text NOT NULL,
    created_at timestamp without time zone NOT NULL,
    active character(1) DEFAULT 'Y'::bpchar NOT NULL
);


ALTER TABLE public.system_post OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 17106)
-- Name: system_post_comment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_post_comment (
    id integer NOT NULL,
    comment text NOT NULL,
    system_user_id integer NOT NULL,
    system_post_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL
);


ALTER TABLE public.system_post_comment OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 17118)
-- Name: system_post_like; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_post_like (
    id integer NOT NULL,
    system_user_id integer,
    system_post_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL
);


ALTER TABLE public.system_post_like OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 17086)
-- Name: system_post_share_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_post_share_group (
    id integer NOT NULL,
    system_group_id integer,
    system_post_id integer NOT NULL
);


ALTER TABLE public.system_post_share_group OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 17096)
-- Name: system_post_tag; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_post_tag (
    id integer NOT NULL,
    system_post_id integer NOT NULL,
    tag character varying(256) NOT NULL
);


ALTER TABLE public.system_post_tag OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 17279)
-- Name: system_preference; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_preference (
    id character varying(256),
    value text
);


ALTER TABLE public.system_preference OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 17258)
-- Name: system_program; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_program (
    id integer NOT NULL,
    name character varying(256),
    controller character varying(256)
);


ALTER TABLE public.system_program OWNER TO postgres;

--
-- TOC entry 251 (class 1259 OID 17386)
-- Name: system_program_method_role; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_program_method_role (
    id integer NOT NULL,
    system_program_id integer,
    system_role_id integer,
    method_name character varying(256)
);


ALTER TABLE public.system_program_method_role OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 17213)
-- Name: system_request_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_request_log (
    id integer NOT NULL,
    endpoint character varying(4096),
    logdate character varying(256),
    log_year character varying(4),
    log_month character varying(2),
    log_day character varying(2),
    session_id character varying(256),
    login character varying(256),
    access_ip character varying(256),
    class_name character varying(256),
    class_method character varying(256),
    http_host character varying(256),
    server_port character varying(256),
    request_uri text,
    request_method character varying(256),
    query_string text,
    request_headers text,
    request_body text,
    request_duration integer
);


ALTER TABLE public.system_request_log OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 17272)
-- Name: system_role; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_role (
    id integer NOT NULL,
    name character varying(256),
    custom_code character varying(256)
);


ALTER TABLE public.system_role OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 17199)
-- Name: system_sql_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_sql_log (
    id integer NOT NULL,
    logdate timestamp without time zone,
    login character varying(256),
    database_name character varying(256),
    sql_command text,
    statement_type character varying(256),
    access_ip character varying(45),
    transaction_id character varying(256),
    log_trace text,
    session_id character varying(256),
    class_name character varying(256),
    php_sapi character varying(256),
    request_id character varying(256),
    log_year character varying(4),
    log_month character varying(2),
    log_day character varying(2)
);


ALTER TABLE public.system_sql_log OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 17265)
-- Name: system_unit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_unit (
    id integer NOT NULL,
    name character varying(256),
    connection_name character varying(256),
    custom_code character varying(256)
);


ALTER TABLE public.system_unit OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 17316)
-- Name: system_user_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_user_group (
    id integer NOT NULL,
    system_user_id integer,
    system_group_id integer
);


ALTER TABLE public.system_user_group OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 17376)
-- Name: system_user_old_password; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_user_old_password (
    id integer NOT NULL,
    system_user_id integer,
    password character varying(256),
    created_at timestamp without time zone
);


ALTER TABLE public.system_user_old_password OWNER TO postgres;

--
-- TOC entry 249 (class 1259 OID 17361)
-- Name: system_user_program; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_user_program (
    id integer NOT NULL,
    system_user_id integer,
    system_program_id integer
);


ALTER TABLE public.system_user_program OWNER TO postgres;

--
-- TOC entry 247 (class 1259 OID 17331)
-- Name: system_user_role; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_user_role (
    id integer NOT NULL,
    system_user_id integer,
    system_role_id integer
);


ALTER TABLE public.system_user_role OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 17301)
-- Name: system_user_unit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_user_unit (
    id integer NOT NULL,
    system_user_id integer,
    system_unit_id integer
);


ALTER TABLE public.system_user_unit OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 17284)
-- Name: system_users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_users (
    id integer NOT NULL,
    name character varying(256),
    login character varying(256),
    password character varying(256),
    email character varying(256),
    accepted_term_policy character(1),
    phone character varying(256),
    address character varying(256),
    function_name character varying(256),
    about character varying(4096),
    accepted_term_policy_at character varying(256),
    accepted_term_policy_data text,
    frontpage_id integer,
    system_unit_id integer,
    active character(1),
    custom_code character varying(256),
    otp_secret character varying(256)
);


ALTER TABLE public.system_users OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 17128)
-- Name: system_wiki_page; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_wiki_page (
    id integer NOT NULL,
    system_user_id integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone,
    title character varying(256) NOT NULL,
    description character varying(4096) NOT NULL,
    content text NOT NULL,
    active character(1) DEFAULT 'Y'::bpchar NOT NULL,
    searchable character(1) DEFAULT 'Y'::bpchar NOT NULL
);


ALTER TABLE public.system_wiki_page OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 17147)
-- Name: system_wiki_share_group; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_wiki_share_group (
    id integer NOT NULL,
    system_group_id integer,
    system_wiki_page_id integer NOT NULL
);


ALTER TABLE public.system_wiki_share_group OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 17137)
-- Name: system_wiki_tag; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_wiki_tag (
    id integer NOT NULL,
    system_wiki_page_id integer NOT NULL,
    tag character varying(256) NOT NULL
);


ALTER TABLE public.system_wiki_tag OWNER TO postgres;

--
-- TOC entry 255 (class 1259 OID 17434)
-- Name: tipo_residuo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tipo_residuo (
    id_tiporesiduo integer NOT NULL,
    nm_tiporesiduo character varying(50) NOT NULL
);


ALTER TABLE public.tipo_residuo OWNER TO postgres;

--
-- TOC entry 254 (class 1259 OID 17433)
-- Name: tipo_residuo_id_tiporesiduo_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tipo_residuo_id_tiporesiduo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tipo_residuo_id_tiporesiduo_seq OWNER TO postgres;

--
-- TOC entry 3817 (class 0 OID 0)
-- Dependencies: 254
-- Name: tipo_residuo_id_tiporesiduo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tipo_residuo_id_tiporesiduo_seq OWNED BY public.tipo_residuo.id_tiporesiduo;


--
-- TOC entry 257 (class 1259 OID 17448)
-- Name: unidade_medida; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.unidade_medida (
    id_unidademedida integer NOT NULL,
    nm_unidademedida character varying(100) NOT NULL,
    sigla character varying(4) NOT NULL
);


ALTER TABLE public.unidade_medida OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 17447)
-- Name: unidade_medida_id_unidademedida_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.unidade_medida_id_unidademedida_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.unidade_medida_id_unidademedida_seq OWNER TO postgres;

--
-- TOC entry 3818 (class 0 OID 0)
-- Dependencies: 256
-- Name: unidade_medida_id_unidademedida_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.unidade_medida_id_unidademedida_seq OWNED BY public.unidade_medida.id_unidademedida;


--
-- TOC entry 3383 (class 2604 OID 17625)
-- Name: entrada_bonificacao id_entrada; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_bonificacao ALTER COLUMN id_entrada SET DEFAULT nextval('public.entrada_bonificacao_id_entrada_seq'::regclass);


--
-- TOC entry 3376 (class 2604 OID 17428)
-- Name: ficha_cadastral id_fichacadastral; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha_cadastral ALTER COLUMN id_fichacadastral SET DEFAULT nextval('public.ficha_cadastral_id_fichacadastral_seq'::regclass);


--
-- TOC entry 3385 (class 2604 OID 17766)
-- Name: item_entrada_bonificacao id_itementradabonificacao; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_entrada_bonificacao ALTER COLUMN id_itementradabonificacao SET DEFAULT nextval('public.item_entrada_bonificacao_id_itementradabonificacao_seq'::regclass);


--
-- TOC entry 3381 (class 2604 OID 17564)
-- Name: item_recebimento_material id_itemrecebimentomaterial; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_recebimento_material ALTER COLUMN id_itemrecebimentomaterial SET DEFAULT nextval('public.item_recebimento_material_id_itemrecebimentomaterial_seq'::regclass);


--
-- TOC entry 3386 (class 2604 OID 17773)
-- Name: item_saida_bonificacao id_itemsaidabonificacao; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_saida_bonificacao ALTER COLUMN id_itemsaidabonificacao SET DEFAULT nextval('public.item_saida_bonificacao_id_itemsaidabonificacao_seq'::regclass);


--
-- TOC entry 3379 (class 2604 OID 17470)
-- Name: material_residuo id_materialresidual; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_residuo ALTER COLUMN id_materialresidual SET DEFAULT nextval('public.material_residuo_id_materialresidual_seq'::regclass);


--
-- TOC entry 3382 (class 2604 OID 17602)
-- Name: produto_bonificacao id_produto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produto_bonificacao ALTER COLUMN id_produto SET DEFAULT nextval('public.produto_bonificacao_id_produto_seq'::regclass);


--
-- TOC entry 3380 (class 2604 OID 17487)
-- Name: recebimento_material id_recebimentomaterial; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recebimento_material ALTER COLUMN id_recebimentomaterial SET DEFAULT nextval('public.recebimento_material_id_recebimentomaterial_seq'::regclass);


--
-- TOC entry 3384 (class 2604 OID 17665)
-- Name: saida_bonificacao id_saida; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saida_bonificacao ALTER COLUMN id_saida SET DEFAULT nextval('public.saida_bonificacao_id_saida_seq'::regclass);


--
-- TOC entry 3377 (class 2604 OID 17437)
-- Name: tipo_residuo id_tiporesiduo; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipo_residuo ALTER COLUMN id_tiporesiduo SET DEFAULT nextval('public.tipo_residuo_id_tiporesiduo_seq'::regclass);


--
-- TOC entry 3378 (class 2604 OID 17451)
-- Name: unidade_medida id_unidademedida; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidade_medida ALTER COLUMN id_unidademedida SET DEFAULT nextval('public.unidade_medida_id_unidademedida_seq'::regclass);


--
-- TOC entry 3794 (class 0 OID 17622)
-- Dependencies: 267
-- Data for Name: entrada_bonificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.entrada_bonificacao (id_entrada, dt_entrada, nu_nota, dt_emissao, vl_reaistotal, vl_ecototal) FROM stdin;
1	2024-02-05	001	\N	43.75	12.5
2	2024-02-05	002	\N	700	200
\.


--
-- TOC entry 3780 (class 0 OID 17425)
-- Dependencies: 253
-- Data for Name: ficha_cadastral; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ficha_cadastral (id_fichacadastral, cpf, nome, dt_nascimento, sexo, email, fone, cep, logradouro, numero, complemento, bairro, estado, cidade) FROM stdin;
1	85778905548	Ramon Barbosa Souza	2002-09-04	M	\N	\N	45416000	\N	\N	\N	\N	BA	Presidente Tancredo Neves
\.


--
-- TOC entry 3798 (class 0 OID 17763)
-- Dependencies: 271
-- Data for Name: item_entrada_bonificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.item_entrada_bonificacao (id_itementradabonificacao, id_entrada, id_produto, qt_item, vl_reais, vl_total) FROM stdin;
1	1	2	5	8.75	43.75
2	2	1	10	70	700
\.


--
-- TOC entry 3790 (class 0 OID 17561)
-- Dependencies: 263
-- Data for Name: item_recebimento_material; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.item_recebimento_material (id_itemrecebimentomaterial, id_recebimentomaterial, id_materialresidual, qt_item, vl_unidade, vl_total) FROM stdin;
1	2	1	4	30	120
\.


--
-- TOC entry 3800 (class 0 OID 17770)
-- Dependencies: 273
-- Data for Name: item_saida_bonificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.item_saida_bonificacao (id_itemsaidabonificacao, id_saida, id_produto, qt_item, vl_unitario, vl_total) FROM stdin;
\.


--
-- TOC entry 3786 (class 0 OID 17467)
-- Dependencies: 259
-- Data for Name: material_residuo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.material_residuo (id_materialresidual, nm_materialresidual, id_unidademedida, vl_bonificacao, id_tiporesiduo) FROM stdin;
1	Zinco	2	30	1
2	Garrafa de Pet	3	15	2
\.


--
-- TOC entry 3792 (class 0 OID 17599)
-- Dependencies: 265
-- Data for Name: produto_bonificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.produto_bonificacao (id_produto, nm_produto, ds_produto, vl_eco, vl_reais) FROM stdin;
1	Relogio	Pulso	20	70
2	Lapiz	Personalizado	2.5	8.75
\.


--
-- TOC entry 3788 (class 0 OID 17484)
-- Dependencies: 261
-- Data for Name: recebimento_material; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.recebimento_material (id_recebimentomaterial, id_fichacadastral, dt_recebimento, local_entrega, status_recebimento, vl_recebimento) FROM stdin;
2	1	2024-02-05	\N	\N	120
\.


--
-- TOC entry 3796 (class 0 OID 17662)
-- Dependencies: 269
-- Data for Name: saida_bonificacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.saida_bonificacao (id_saida, id_fichacadastral, dt_saida, vl_ecototal, vl_saldo, status, vl_reaistotal) FROM stdin;
\.


--
-- TOC entry 3763 (class 0 OID 17206)
-- Dependencies: 236
-- Data for Name: system_access_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_access_log (id, sessionid, login, login_time, login_year, login_month, login_day, logout_time, impersonated, access_ip, impersonated_by) FROM stdin;
1	o5m1ctb0pb017255ld9ka965bc	admin	2024-01-31 22:19:30	2024	01	31	\N	N	::1	\N
2	blgiugbqh3kue6addvq7rsokqu	admin	2024-01-31 23:21:19	2024	01	31	\N	N	::1	\N
3	tv8idrvv5fv8eqv9c3voqlaelb	admin	2024-02-02 23:02:44	2024	02	02	\N	N	::1	\N
4	emepdlae75pojh1c9sdb8eg7dh	admin	2024-02-03 20:08:31	2024	02	03	\N	N	::1	\N
5	ofu2d64fr0gp3qmsv1iukv55sk	admin	2024-02-03 20:16:37	2024	02	03	\N	N	::1	\N
6	ugrtlt4rgh1llfg1542hnmmhht	admin	2024-02-03 20:19:01	2024	02	03	\N	N	::1	\N
7	g12ns4vrsupaa03m939f484et5	admin	2024-02-04 14:15:03	2024	02	04	\N	N	::1	\N
8	fp8srh7j4bph6dmlokjf1j7ted	admin	2024-02-05 19:52:46	2024	02	05	\N	N	::1	\N
\.


--
-- TOC entry 3765 (class 0 OID 17220)
-- Dependencies: 238
-- Data for Name: system_access_notification_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_access_notification_log (id, login, email, ip_address, login_time) FROM stdin;
\.


--
-- TOC entry 3761 (class 0 OID 17192)
-- Dependencies: 234
-- Data for Name: system_change_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_change_log (id, logdate, login, tablename, primarykey, pkvalue, operation, columnname, oldvalue, newvalue, access_ip, transaction_id, log_trace, session_id, class_name, php_sapi, log_year, log_month, log_day) FROM stdin;
\.


--
-- TOC entry 3748 (class 0 OID 17021)
-- Dependencies: 221
-- Data for Name: system_document; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_document (id, system_user_id, title, description, category_id, submission_date, archive_date, filename, in_trash, system_folder_id) FROM stdin;
\.


--
-- TOC entry 3751 (class 0 OID 17058)
-- Dependencies: 224
-- Data for Name: system_document_bookmark; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_document_bookmark (id, system_user_id, system_document_id) FROM stdin;
\.


--
-- TOC entry 3744 (class 0 OID 16986)
-- Dependencies: 217
-- Data for Name: system_document_category; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_document_category (id, name) FROM stdin;
1	Documentos
\.


--
-- TOC entry 3750 (class 0 OID 17048)
-- Dependencies: 223
-- Data for Name: system_document_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_document_group (id, document_id, system_group_id) FROM stdin;
\.


--
-- TOC entry 3749 (class 0 OID 17038)
-- Dependencies: 222
-- Data for Name: system_document_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_document_user (id, document_id, system_user_id) FROM stdin;
\.


--
-- TOC entry 3745 (class 0 OID 16991)
-- Dependencies: 218
-- Data for Name: system_folder; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_folder (id, system_user_id, created_at, name, in_trash, system_folder_parent_id) FROM stdin;
\.


--
-- TOC entry 3752 (class 0 OID 17068)
-- Dependencies: 225
-- Data for Name: system_folder_bookmark; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_folder_bookmark (id, system_user_id, system_folder_id) FROM stdin;
\.


--
-- TOC entry 3747 (class 0 OID 17011)
-- Dependencies: 220
-- Data for Name: system_folder_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_folder_group (id, system_folder_id, system_group_id) FROM stdin;
\.


--
-- TOC entry 3746 (class 0 OID 17001)
-- Dependencies: 219
-- Data for Name: system_folder_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_folder_user (id, system_folder_id, system_user_id) FROM stdin;
\.


--
-- TOC entry 3766 (class 0 OID 17253)
-- Dependencies: 239
-- Data for Name: system_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_group (id, name) FROM stdin;
1	Admin
2	Standard
\.


--
-- TOC entry 3775 (class 0 OID 17346)
-- Dependencies: 248
-- Data for Name: system_group_program; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_group_program (id, system_group_id, system_program_id) FROM stdin;
1	1	1
2	1	2
3	1	3
4	1	4
5	1	5
6	1	6
7	1	8
8	1	9
9	1	11
10	1	14
11	1	15
12	2	10
13	2	12
14	2	13
15	2	16
16	2	17
17	2	18
18	2	19
19	2	20
20	1	21
25	1	26
26	1	27
27	1	28
28	1	29
29	2	30
30	1	31
31	1	32
32	1	33
33	1	34
34	1	35
36	1	36
37	1	37
38	1	38
39	1	39
40	1	40
41	1	41
42	1	42
43	1	43
44	1	44
45	1	45
46	1	46
47	1	47
48	1	48
49	1	49
52	1	52
53	1	53
54	1	54
55	1	55
56	1	56
57	1	57
58	1	58
59	1	59
60	1	60
61	1	61
62	2	54
63	2	60
64	2	43
65	2	44
66	2	45
67	2	46
68	2	47
69	2	48
70	2	49
71	2	55
72	2	56
73	2	61
74	1	62
75	1	63
76	2	64
77	1	65
78	1	66
79	1	67
80	1	68
81	1	69
82	1	70
83	1	71
84	1	72
85	1	73
86	1	74
87	1	75
88	1	76
89	1	78
90	1	77
91	1	79
92	1	80
93	1	81
94	1	82
95	1	83
96	1	84
97	1	85
98	1	86
\.


--
-- TOC entry 3742 (class 0 OID 16972)
-- Dependencies: 215
-- Data for Name: system_message; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_message (id, system_user_id, system_user_to_id, subject, message, dt_message, checked) FROM stdin;
\.


--
-- TOC entry 3743 (class 0 OID 16979)
-- Dependencies: 216
-- Data for Name: system_notification; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_notification (id, system_user_id, system_user_to_id, subject, message, dt_message, action_url, action_label, icon, checked) FROM stdin;
\.


--
-- TOC entry 3753 (class 0 OID 17078)
-- Dependencies: 226
-- Data for Name: system_post; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_post (id, system_user_id, title, content, created_at, active) FROM stdin;
1	1	Primeira noticia	<p style="text-align: justify; "><span style="font-family: &quot;Source Sans Pro&quot;; font-size: 18px;">﻿</span><span style="font-family: &quot;Source Sans Pro&quot;; font-size: 18px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Id cursus metus aliquam eleifend mi in nulla posuere sollicitudin. Tincidunt nunc pulvinar sapien et ligula ullamcorper. Odio pellentesque diam volutpat commodo sed egestas egestas. Eget egestas purus viverra accumsan in nisl nisi scelerisque. Habitant morbi tristique senectus et netus et malesuada. Vitae ultricies leo integer malesuada nunc vel risus commodo viverra. Vehicula ipsum a arcu cursus. Rhoncus est pellentesque elit ullamcorper dignissim. Faucibus in ornare quam viverra orci sagittis eu. Nisi scelerisque eu ultrices vitae auctor. Tellus cras adipiscing enim eu turpis egestas. Eget lorem dolor sed viverra ipsum nunc aliquet. Neque convallis a cras semper auctor neque. Bibendum ut tristique et egestas. Amet nisl suscipit adipiscing bibendum.</span></p><p style="text-align: justify;"><span style="font-family: &quot;Source Sans Pro&quot;; font-size: 18px;">Mattis nunc sed blandit libero volutpat sed cras ornare. Leo duis ut diam quam nulla. Tempus imperdiet nulla malesuada pellentesque elit eget gravida cum sociis. Non quam lacus suspendisse faucibus. Enim nulla aliquet porttitor lacus luctus accumsan tortor posuere ac. Dignissim enim sit amet venenatis urna. Elit sed vulputate mi sit. Sit amet nisl suscipit adipiscing bibendum est. Maecenas accumsan lacus vel facilisis. Orci phasellus egestas tellus rutrum tellus pellentesque eu tincidunt tortor. Aenean pharetra magna ac placerat vestibulum lectus mauris ultrices eros. Augue lacus viverra vitae congue eu consequat ac felis. Bibendum neque egestas congue quisque egestas diam. Facilisis magna etiam tempor orci eu lobortis elementum. Rhoncus est pellentesque elit ullamcorper dignissim cras tincidunt lobortis. Pellentesque adipiscing commodo elit at imperdiet dui accumsan sit amet. Nullam eget felis eget nunc. Nec ullamcorper sit amet risus nullam eget felis. Lacus vel facilisis volutpat est velit egestas dui id.</span></p>	2022-11-03 14:59:39	Y
2	1	Segunda noticia	<p style="text-align: justify; "><span style="font-size: 18px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ac orci phasellus egestas tellus rutrum. Pretium nibh ipsum consequat nisl vel pretium lectus quam. Faucibus scelerisque eleifend donec pretium vulputate sapien. Mattis molestie a iaculis at erat pellentesque adipiscing commodo elit. Ultricies mi quis hendrerit dolor magna eget. Quam id leo in vitae turpis massa sed elementum tempus. Eget arcu dictum varius duis at consectetur lorem. Quis varius quam quisque id diam. Consequat interdum varius sit amet mattis vulputate. Purus non enim praesent elementum facilisis leo vel fringilla. Nulla facilisi nullam vehicula ipsum a arcu. Habitant morbi tristique senectus et netus et malesuada fames. Risus commodo viverra maecenas accumsan lacus. Mattis molestie a iaculis at erat pellentesque adipiscing commodo elit. Imperdiet proin fermentum leo vel orci porta non pulvinar neque. Massa massa ultricies mi quis hendrerit. Vel turpis nunc eget lorem dolor sed viverra ipsum nunc. Quisque egestas diam in arcu cursus euismod quis.</span></p><p style="text-align: justify; "><span style="font-size: 18px;">Posuere morbi leo urna molestie at elementum eu facilisis. Dolor morbi non arcu risus quis varius quam. Fermentum posuere urna nec tincidunt praesent semper feugiat nibh. Consectetur adipiscing elit ut aliquam purus sit. Gravida cum sociis natoque penatibus et magnis. Sollicitudin aliquam ultrices sagittis orci. Tortor consequat id porta nibh venenatis cras sed felis. Dictumst quisque sagittis purus sit amet volutpat consequat mauris nunc. Arcu dictum varius duis at consectetur. Mauris commodo quis imperdiet massa tincidunt nunc pulvinar. At tellus at urna condimentum mattis pellentesque. Tellus mauris a diam maecenas sed.</span></p>	2022-11-03 15:03:31	Y
\.


--
-- TOC entry 3756 (class 0 OID 17106)
-- Dependencies: 229
-- Data for Name: system_post_comment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_post_comment (id, comment, system_user_id, system_post_id, created_at) FROM stdin;
1	My first comment	1	2	2022-11-03 15:22:11
2	Another comment	1	2	2022-11-03 15:22:17
3	The best comment	2	2	2022-11-03 15:23:11
\.


--
-- TOC entry 3757 (class 0 OID 17118)
-- Dependencies: 230
-- Data for Name: system_post_like; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_post_like (id, system_user_id, system_post_id, created_at) FROM stdin;
\.


--
-- TOC entry 3754 (class 0 OID 17086)
-- Dependencies: 227
-- Data for Name: system_post_share_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_post_share_group (id, system_group_id, system_post_id) FROM stdin;
1	1	1
2	2	1
3	1	2
4	2	2
\.


--
-- TOC entry 3755 (class 0 OID 17096)
-- Dependencies: 228
-- Data for Name: system_post_tag; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_post_tag (id, system_post_id, tag) FROM stdin;
1	1	novidades
2	2	novidades
\.


--
-- TOC entry 3770 (class 0 OID 17279)
-- Dependencies: 243
-- Data for Name: system_preference; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_preference (id, value) FROM stdin;
\.


--
-- TOC entry 3767 (class 0 OID 17258)
-- Dependencies: 240
-- Data for Name: system_program; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_program (id, name, controller) FROM stdin;
1	System Group Form	SystemGroupForm
2	System Group List	SystemGroupList
3	System Program Form	SystemProgramForm
4	System Program List	SystemProgramList
5	System User Form	SystemUserForm
6	System User List	SystemUserList
7	Common Page	CommonPage
8	System PHP Info	SystemPHPInfoView
9	System ChangeLog View	SystemChangeLogView
10	Welcome View	WelcomeView
11	System Sql Log	SystemSqlLogList
12	System Profile View	SystemProfileView
13	System Profile Form	SystemProfileForm
14	System SQL Panel	SystemSQLPanel
15	System Access Log	SystemAccessLogList
16	System Message Form	SystemMessageForm
17	System Message List	SystemMessageList
18	System Message Form View	SystemMessageFormView
19	System Notification List	SystemNotificationList
20	System Notification Form View	SystemNotificationFormView
21	System Document Category List	SystemDocumentCategoryFormList
26	System Unit Form	SystemUnitForm
27	System Unit List	SystemUnitList
28	System Access stats	SystemAccessLogStats
29	System Preference form	SystemPreferenceForm
30	System Support form	SystemSupportForm
31	System PHP Error	SystemPHPErrorLogView
32	System Database Browser	SystemDatabaseExplorer
33	System Table List	SystemTableList
34	System Data Browser	SystemDataBrowser
35	System Menu Editor	SystemMenuEditor
36	System Request Log	SystemRequestLogList
37	System Request Log View	SystemRequestLogView
38	System Administration Dashboard	SystemAdministrationDashboard
39	System Log Dashboard	SystemLogDashboard
40	System Session vars	SystemSessionVarsView
41	System Information	SystemInformationView
42	System files diff	SystemFilesDiff
43	System Documents	SystemDriveList
44	System Folder form	SystemFolderForm
45	System Share folder	SystemFolderShareForm
46	System Share document	SystemDocumentShareForm
47	System Document properties	SystemDocumentFormWindow
48	System Folder properties	SystemFolderFormView
49	System Document upload	SystemDriveDocumentUploadForm
52	System Post list	SystemPostList
53	System Post form	SystemPostForm
54	Post View list	SystemPostFeedView
55	Post Comment form	SystemPostCommentForm
56	Post Comment list	SystemPostCommentList
57	System Contacts list	SystemContactsList
58	System Wiki list	SystemWikiList
59	System Wiki form	SystemWikiForm
60	System Wiki search	SystemWikiSearchList
61	System Wiki view	SystemWikiView
62	System Role List	SystemRoleList
63	System Role Form	SystemRoleForm
64	System Profile 2FA Form	SystemProfile2FAForm
65	Ficha Cadastral List	FichaCadastralList
66	Ficha Cadastral Form	FichaCadastralForm
67	Tipo Residuo List	TipoResiduoList
68	Tipo Residuo Form	TipoResiduoForm
69	Unidade Medida Form	UnidadeMedidaForm
70	Unidade Medida List	UnidadeMedidaList
71	Material Residuo Form	MaterialResiduoForm
72	Material Residuo List	MaterialResiduoList
73	Recebimento Material List	RecebimentoMaterialList
74	Recebimento Material Form	RecebimentoMaterialForm
75	Produto Bonificacao List	ProdutoBonificacaoList
76	Produto Bonificacao Form	ProdutoBonificacaoForm
78	Entrada Bonificacao Form	EntradaBonificacaoForm
77	Entrada Bonificacao List	EntradaBonificacaoList
79	Saida Bonificacao List	SaidaBonificacaoList
80	Saidabonificacao Form	SaidabonificacaoForm
81	Saida Bonificacao Form	SaidaBonificacaoForm
82	Recebimento Material Ficha Cadastral Seek	RecebimentoMaterialFichaCadastralSeek
83	Entrada Produto Bonificacao Seek	EntradaProdutoBonificacaoSeek
84	Saldo Produto View	SaldoProdutoView
85	Saldo Pessoa Material View	SaldoPessoaMaterialView
86	Saldo Acumulo Pessoa View	SaldoAcumuloPessoaView
\.


--
-- TOC entry 3778 (class 0 OID 17386)
-- Dependencies: 251
-- Data for Name: system_program_method_role; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_program_method_role (id, system_program_id, system_role_id, method_name) FROM stdin;
\.


--
-- TOC entry 3764 (class 0 OID 17213)
-- Dependencies: 237
-- Data for Name: system_request_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_request_log (id, endpoint, logdate, log_year, log_month, log_day, session_id, login, access_ip, class_name, class_method, http_host, server_port, request_uri, request_method, query_string, request_headers, request_body, request_duration) FROM stdin;
\.


--
-- TOC entry 3769 (class 0 OID 17272)
-- Dependencies: 242
-- Data for Name: system_role; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_role (id, name, custom_code) FROM stdin;
1	Role A	
2	Role B	
\.


--
-- TOC entry 3762 (class 0 OID 17199)
-- Dependencies: 235
-- Data for Name: system_sql_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_sql_log (id, logdate, login, database_name, sql_command, statement_type, access_ip, transaction_id, log_trace, session_id, class_name, php_sapi, request_id, log_year, log_month, log_day) FROM stdin;
\.


--
-- TOC entry 3768 (class 0 OID 17265)
-- Dependencies: 241
-- Data for Name: system_unit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_unit (id, name, connection_name, custom_code) FROM stdin;
1	Unit A	unit_a	
2	Unit B	unit_b	
\.


--
-- TOC entry 3773 (class 0 OID 17316)
-- Dependencies: 246
-- Data for Name: system_user_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_user_group (id, system_user_id, system_group_id) FROM stdin;
1	1	1
2	2	2
3	1	2
\.


--
-- TOC entry 3777 (class 0 OID 17376)
-- Dependencies: 250
-- Data for Name: system_user_old_password; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_user_old_password (id, system_user_id, password, created_at) FROM stdin;
\.


--
-- TOC entry 3776 (class 0 OID 17361)
-- Dependencies: 249
-- Data for Name: system_user_program; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_user_program (id, system_user_id, system_program_id) FROM stdin;
1	2	7
\.


--
-- TOC entry 3774 (class 0 OID 17331)
-- Dependencies: 247
-- Data for Name: system_user_role; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_user_role (id, system_user_id, system_role_id) FROM stdin;
\.


--
-- TOC entry 3772 (class 0 OID 17301)
-- Dependencies: 245
-- Data for Name: system_user_unit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_user_unit (id, system_user_id, system_unit_id) FROM stdin;
1	1	1
2	1	2
3	2	1
4	2	2
\.


--
-- TOC entry 3771 (class 0 OID 17284)
-- Dependencies: 244
-- Data for Name: system_users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_users (id, name, login, password, email, accepted_term_policy, phone, address, function_name, about, accepted_term_policy_at, accepted_term_policy_data, frontpage_id, system_unit_id, active, custom_code, otp_secret) FROM stdin;
1	Administrator	admin	$2y$10$xuR3XEc3J6tpv7myC9gPj.Ab5GacSeHSZoYUTYtOg.cEc22G.iBwa	admin@admin.net	Y	+123 456 789	Admin Street, 123	Administrator	I'm the administrator	\N	\N	10	\N	Y	\N	\N
2	User	user	$2y$10$MUYN29LOSHrCSGhrzvYG8O/PtAjbWvCubaUSTJGhVTJhm69WNFJs.	user@user.net	Y	+123 456 789	User Street, 123	End user	I'm the end user	\N	\N	7	\N	Y	\N	\N
\.


--
-- TOC entry 3758 (class 0 OID 17128)
-- Dependencies: 231
-- Data for Name: system_wiki_page; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_wiki_page (id, system_user_id, created_at, updated_at, title, description, content, active, searchable) FROM stdin;
1	1	2022-11-02 15:33:58	2022-11-02 15:35:10	Manual de operacoes	Este manual explica os procedimentos basicos de operacao	<p style="text-align: justify; "><span style="font-size: 18px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sapien nec sagittis aliquam malesuada bibendum arcu vitae. Quisque egestas diam in arcu cursus euismod quis. Risus nec feugiat in fermentum posuere urna nec tincidunt praesent. At imperdiet dui accumsan sit amet. Est pellentesque elit ullamcorper dignissim cras tincidunt lobortis. Elementum facilisis leo vel fringilla est ullamcorper. Id porta nibh venenatis cras. Viverra orci sagittis eu volutpat odio facilisis mauris sit. Senectus et netus et malesuada fames ac turpis. Sociis natoque penatibus et magnis dis parturient montes. Vel turpis nunc eget lorem dolor sed viverra ipsum nunc. Sed viverra tellus in hac habitasse. Tellus id interdum velit laoreet id donec ultrices tincidunt arcu. Pharetra et ultrices neque ornare aenean euismod elementum. Volutpat blandit aliquam etiam erat velit scelerisque in. Neque aliquam vestibulum morbi blandit cursus risus. Id consectetur purus ut faucibus pulvinar elementum.</span></p><p style="text-align: justify; "><br></p>	Y	Y
2	1	2022-11-02 15:35:04	2022-11-02 15:37:49	Instrucoes de lancamento	Este manual explica as instrucoes de lancamento de produto	<p><span style="font-size: 18px;">Non curabitur gravida arcu ac tortor dignissim convallis. Nunc scelerisque viverra mauris in aliquam sem fringilla ut morbi. Nunc eget lorem dolor sed viverra. Et odio pellentesque diam volutpat commodo sed egestas. Enim lobortis scelerisque fermentum dui faucibus in ornare quam viverra. Faucibus et molestie ac feugiat. Erat velit scelerisque in dictum non consectetur a erat nam. Quis risus sed vulputate odio ut enim blandit volutpat. Pharetra vel turpis nunc eget lorem dolor sed viverra. Nisl tincidunt eget nullam non nisi est sit. Orci phasellus egestas tellus rutrum tellus pellentesque eu. Et tortor at risus viverra adipiscing at in tellus integer. Risus ultricies tristique nulla aliquet enim. Ac felis donec et odio pellentesque diam volutpat commodo sed. Ut morbi tincidunt augue interdum. Morbi tempus iaculis urna id volutpat.</span></p><p><a href="index.php?class=SystemWikiView&amp;method=onLoad&amp;key=3" generator="adianti">Sub pagina de instrucoes 1</a></p><p><a href="index.php?class=SystemWikiView&amp;method=onLoad&amp;key=4" generator="adianti">Sub pagina de instrucoes 2</a><br><span style="font-size: 18px;"><br></span><br></p>	Y	Y
3	1	2022-11-02 15:36:59	2022-11-02 15:37:21	Instrucoes - sub pagina 1	Instrucoes - sub pagina 1	<p><span style="font-size: 18px;">Follow these steps:</span></p><ol><li><span style="font-size: 18px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></li><li><span style="font-size: 18px;">Sapien nec sagittis aliquam malesuada bibendum arcu vitae.</span></li><li><span style="font-size: 18px;">Quisque egestas diam in arcu cursus euismod quis.</span><br></li></ol>	Y	N
4	1	2022-11-02 15:37:17	2022-11-02 15:37:22	Instrucoes - sub pagina 2	Instrucoes - sub pagina 2	<p><span style="font-size: 18px;">Follow these steps:</span></p><ol><li><span style="font-size: 18px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></li><li><span style="font-size: 18px;">Sapien nec sagittis aliquam malesuada bibendum arcu vitae.</span></li><li><span style="font-size: 18px;">Quisque egestas diam in arcu cursus euismod quis.</span></li></ol>	Y	N
\.


--
-- TOC entry 3760 (class 0 OID 17147)
-- Dependencies: 233
-- Data for Name: system_wiki_share_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_wiki_share_group (id, system_group_id, system_wiki_page_id) FROM stdin;
1	1	1
2	2	1
3	1	2
4	2	2
5	1	3
6	2	3
7	1	4
8	2	4
\.


--
-- TOC entry 3759 (class 0 OID 17137)
-- Dependencies: 232
-- Data for Name: system_wiki_tag; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_wiki_tag (id, system_wiki_page_id, tag) FROM stdin;
3	1	manual
5	4	manual
6	3	manual
7	2	manual
\.


--
-- TOC entry 3782 (class 0 OID 17434)
-- Dependencies: 255
-- Data for Name: tipo_residuo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tipo_residuo (id_tiporesiduo, nm_tiporesiduo) FROM stdin;
1	Metal
2	Plastico
\.


--
-- TOC entry 3784 (class 0 OID 17448)
-- Dependencies: 257
-- Data for Name: unidade_medida; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.unidade_medida (id_unidademedida, nm_unidademedida, sigla) FROM stdin;
1	Centimetros	CM
2	Grama	G
3	Litro	L
\.


--
-- TOC entry 3819 (class 0 OID 0)
-- Dependencies: 266
-- Name: entrada_bonificacao_id_entrada_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.entrada_bonificacao_id_entrada_seq', 1, false);


--
-- TOC entry 3820 (class 0 OID 0)
-- Dependencies: 252
-- Name: ficha_cadastral_id_fichacadastral_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ficha_cadastral_id_fichacadastral_seq', 1, false);


--
-- TOC entry 3821 (class 0 OID 0)
-- Dependencies: 270
-- Name: item_entrada_bonificacao_id_itementradabonificacao_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.item_entrada_bonificacao_id_itementradabonificacao_seq', 1, false);


--
-- TOC entry 3822 (class 0 OID 0)
-- Dependencies: 262
-- Name: item_recebimento_material_id_itemrecebimentomaterial_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.item_recebimento_material_id_itemrecebimentomaterial_seq', 1, false);


--
-- TOC entry 3823 (class 0 OID 0)
-- Dependencies: 272
-- Name: item_saida_bonificacao_id_itemsaidabonificacao_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.item_saida_bonificacao_id_itemsaidabonificacao_seq', 1, false);


--
-- TOC entry 3824 (class 0 OID 0)
-- Dependencies: 258
-- Name: material_residuo_id_materialresidual_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.material_residuo_id_materialresidual_seq', 1, false);


--
-- TOC entry 3825 (class 0 OID 0)
-- Dependencies: 264
-- Name: produto_bonificacao_id_produto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.produto_bonificacao_id_produto_seq', 1, false);


--
-- TOC entry 3826 (class 0 OID 0)
-- Dependencies: 260
-- Name: recebimento_material_id_recebimentomaterial_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.recebimento_material_id_recebimentomaterial_seq', 1, false);


--
-- TOC entry 3827 (class 0 OID 0)
-- Dependencies: 268
-- Name: saida_bonificacao_id_saida_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.saida_bonificacao_id_saida_seq', 1, false);


--
-- TOC entry 3828 (class 0 OID 0)
-- Dependencies: 254
-- Name: tipo_residuo_id_tiporesiduo_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tipo_residuo_id_tiporesiduo_seq', 1, false);


--
-- TOC entry 3829 (class 0 OID 0)
-- Dependencies: 256
-- Name: unidade_medida_id_unidademedida_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.unidade_medida_id_unidademedida_seq', 1, false);


--
-- TOC entry 3557 (class 2606 OID 17627)
-- Name: entrada_bonificacao entrada_bonificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_bonificacao
    ADD CONSTRAINT entrada_bonificacao_pkey PRIMARY KEY (id_entrada);


--
-- TOC entry 3543 (class 2606 OID 17432)
-- Name: ficha_cadastral ficha_cadastral_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ficha_cadastral
    ADD CONSTRAINT ficha_cadastral_pkey PRIMARY KEY (id_fichacadastral);


--
-- TOC entry 3561 (class 2606 OID 17768)
-- Name: item_entrada_bonificacao item_entrada_bonificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_entrada_bonificacao
    ADD CONSTRAINT item_entrada_bonificacao_pkey PRIMARY KEY (id_itementradabonificacao);


--
-- TOC entry 3553 (class 2606 OID 17566)
-- Name: item_recebimento_material item_recebimento_material_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_recebimento_material
    ADD CONSTRAINT item_recebimento_material_pkey PRIMARY KEY (id_itemrecebimentomaterial);


--
-- TOC entry 3563 (class 2606 OID 17775)
-- Name: item_saida_bonificacao item_saida_bonificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_saida_bonificacao
    ADD CONSTRAINT item_saida_bonificacao_pkey PRIMARY KEY (id_itemsaidabonificacao);


--
-- TOC entry 3549 (class 2606 OID 17472)
-- Name: material_residuo material_residuo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_residuo
    ADD CONSTRAINT material_residuo_pkey PRIMARY KEY (id_materialresidual);


--
-- TOC entry 3555 (class 2606 OID 17606)
-- Name: produto_bonificacao produto_bonificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produto_bonificacao
    ADD CONSTRAINT produto_bonificacao_pkey PRIMARY KEY (id_produto);


--
-- TOC entry 3551 (class 2606 OID 17489)
-- Name: recebimento_material recebimento_material_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recebimento_material
    ADD CONSTRAINT recebimento_material_pkey PRIMARY KEY (id_recebimentomaterial);


--
-- TOC entry 3559 (class 2606 OID 17667)
-- Name: saida_bonificacao saida_bonificacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saida_bonificacao
    ADD CONSTRAINT saida_bonificacao_pkey PRIMARY KEY (id_saida);


--
-- TOC entry 3483 (class 2606 OID 17212)
-- Name: system_access_log system_access_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_access_log
    ADD CONSTRAINT system_access_log_pkey PRIMARY KEY (id);


--
-- TOC entry 3495 (class 2606 OID 17226)
-- Name: system_access_notification_log system_access_notification_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_access_notification_log
    ADD CONSTRAINT system_access_notification_log_pkey PRIMARY KEY (id);


--
-- TOC entry 3468 (class 2606 OID 17198)
-- Name: system_change_log system_change_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_change_log
    ADD CONSTRAINT system_change_log_pkey PRIMARY KEY (id);


--
-- TOC entry 3427 (class 2606 OID 17062)
-- Name: system_document_bookmark system_document_bookmark_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_bookmark
    ADD CONSTRAINT system_document_bookmark_pkey PRIMARY KEY (id);


--
-- TOC entry 3397 (class 2606 OID 16990)
-- Name: system_document_category system_document_category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_category
    ADD CONSTRAINT system_document_category_pkey PRIMARY KEY (id);


--
-- TOC entry 3423 (class 2606 OID 17052)
-- Name: system_document_group system_document_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_group
    ADD CONSTRAINT system_document_group_pkey PRIMARY KEY (id);


--
-- TOC entry 3415 (class 2606 OID 17027)
-- Name: system_document system_document_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document
    ADD CONSTRAINT system_document_pkey PRIMARY KEY (id);


--
-- TOC entry 3419 (class 2606 OID 17042)
-- Name: system_document_user system_document_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_user
    ADD CONSTRAINT system_document_user_pkey PRIMARY KEY (id);


--
-- TOC entry 3431 (class 2606 OID 17072)
-- Name: system_folder_bookmark system_folder_bookmark_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder_bookmark
    ADD CONSTRAINT system_folder_bookmark_pkey PRIMARY KEY (id);


--
-- TOC entry 3410 (class 2606 OID 17015)
-- Name: system_folder_group system_folder_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder_group
    ADD CONSTRAINT system_folder_group_pkey PRIMARY KEY (id);


--
-- TOC entry 3402 (class 2606 OID 16995)
-- Name: system_folder system_folder_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder
    ADD CONSTRAINT system_folder_pkey PRIMARY KEY (id);


--
-- TOC entry 3406 (class 2606 OID 17005)
-- Name: system_folder_user system_folder_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder_user
    ADD CONSTRAINT system_folder_user_pkey PRIMARY KEY (id);


--
-- TOC entry 3498 (class 2606 OID 17257)
-- Name: system_group system_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_group
    ADD CONSTRAINT system_group_pkey PRIMARY KEY (id);


--
-- TOC entry 3530 (class 2606 OID 17350)
-- Name: system_group_program system_group_program_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_group_program
    ADD CONSTRAINT system_group_program_pkey PRIMARY KEY (id);


--
-- TOC entry 3390 (class 2606 OID 16978)
-- Name: system_message system_message_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_message
    ADD CONSTRAINT system_message_pkey PRIMARY KEY (id);


--
-- TOC entry 3394 (class 2606 OID 16985)
-- Name: system_notification system_notification_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_notification
    ADD CONSTRAINT system_notification_pkey PRIMARY KEY (id);


--
-- TOC entry 3445 (class 2606 OID 17112)
-- Name: system_post_comment system_post_comment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_comment
    ADD CONSTRAINT system_post_comment_pkey PRIMARY KEY (id);


--
-- TOC entry 3449 (class 2606 OID 17122)
-- Name: system_post_like system_post_like_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_like
    ADD CONSTRAINT system_post_like_pkey PRIMARY KEY (id);


--
-- TOC entry 3434 (class 2606 OID 17085)
-- Name: system_post system_post_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post
    ADD CONSTRAINT system_post_pkey PRIMARY KEY (id);


--
-- TOC entry 3438 (class 2606 OID 17090)
-- Name: system_post_share_group system_post_share_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_share_group
    ADD CONSTRAINT system_post_share_group_pkey PRIMARY KEY (id);


--
-- TOC entry 3441 (class 2606 OID 17100)
-- Name: system_post_tag system_post_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_tag
    ADD CONSTRAINT system_post_tag_pkey PRIMARY KEY (id);


--
-- TOC entry 3541 (class 2606 OID 17390)
-- Name: system_program_method_role system_program_method_role_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_program_method_role
    ADD CONSTRAINT system_program_method_role_pkey PRIMARY KEY (id);


--
-- TOC entry 3502 (class 2606 OID 17264)
-- Name: system_program system_program_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_program
    ADD CONSTRAINT system_program_pkey PRIMARY KEY (id);


--
-- TOC entry 3492 (class 2606 OID 17219)
-- Name: system_request_log system_request_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_request_log
    ADD CONSTRAINT system_request_log_pkey PRIMARY KEY (id);


--
-- TOC entry 3508 (class 2606 OID 17278)
-- Name: system_role system_role_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_role
    ADD CONSTRAINT system_role_pkey PRIMARY KEY (id);


--
-- TOC entry 3477 (class 2606 OID 17205)
-- Name: system_sql_log system_sql_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_sql_log
    ADD CONSTRAINT system_sql_log_pkey PRIMARY KEY (id);


--
-- TOC entry 3505 (class 2606 OID 17271)
-- Name: system_unit system_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_unit
    ADD CONSTRAINT system_unit_pkey PRIMARY KEY (id);


--
-- TOC entry 3522 (class 2606 OID 17320)
-- Name: system_user_group system_user_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_group
    ADD CONSTRAINT system_user_group_pkey PRIMARY KEY (id);


--
-- TOC entry 3537 (class 2606 OID 17380)
-- Name: system_user_old_password system_user_old_password_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_old_password
    ADD CONSTRAINT system_user_old_password_pkey PRIMARY KEY (id);


--
-- TOC entry 3534 (class 2606 OID 17365)
-- Name: system_user_program system_user_program_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_program
    ADD CONSTRAINT system_user_program_pkey PRIMARY KEY (id);


--
-- TOC entry 3526 (class 2606 OID 17335)
-- Name: system_user_role system_user_role_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_role
    ADD CONSTRAINT system_user_role_pkey PRIMARY KEY (id);


--
-- TOC entry 3518 (class 2606 OID 17305)
-- Name: system_user_unit system_user_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_unit
    ADD CONSTRAINT system_user_unit_pkey PRIMARY KEY (id);


--
-- TOC entry 3514 (class 2606 OID 17290)
-- Name: system_users system_users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_users
    ADD CONSTRAINT system_users_pkey PRIMARY KEY (id);


--
-- TOC entry 3452 (class 2606 OID 17136)
-- Name: system_wiki_page system_wiki_page_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_wiki_page
    ADD CONSTRAINT system_wiki_page_pkey PRIMARY KEY (id);


--
-- TOC entry 3459 (class 2606 OID 17151)
-- Name: system_wiki_share_group system_wiki_share_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_wiki_share_group
    ADD CONSTRAINT system_wiki_share_group_pkey PRIMARY KEY (id);


--
-- TOC entry 3455 (class 2606 OID 17141)
-- Name: system_wiki_tag system_wiki_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_wiki_tag
    ADD CONSTRAINT system_wiki_tag_pkey PRIMARY KEY (id);


--
-- TOC entry 3545 (class 2606 OID 17439)
-- Name: tipo_residuo tipo_residuo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipo_residuo
    ADD CONSTRAINT tipo_residuo_pkey PRIMARY KEY (id_tiporesiduo);


--
-- TOC entry 3547 (class 2606 OID 17453)
-- Name: unidade_medida unidade_medida_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidade_medida
    ADD CONSTRAINT unidade_medida_pkey PRIMARY KEY (id_unidademedida);


--
-- TOC entry 3478 (class 1259 OID 17244)
-- Name: sys_access_log_day_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_access_log_day_idx ON public.system_access_log USING btree (login_day);


--
-- TOC entry 3479 (class 1259 OID 17241)
-- Name: sys_access_log_login_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_access_log_login_idx ON public.system_access_log USING btree (login);


--
-- TOC entry 3480 (class 1259 OID 17243)
-- Name: sys_access_log_month_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_access_log_month_idx ON public.system_access_log USING btree (login_month);


--
-- TOC entry 3481 (class 1259 OID 17242)
-- Name: sys_access_log_year_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_access_log_year_idx ON public.system_access_log USING btree (login_year);


--
-- TOC entry 3493 (class 1259 OID 17252)
-- Name: sys_access_notification_log_login_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_access_notification_log_login_idx ON public.system_access_notification_log USING btree (login);


--
-- TOC entry 3460 (class 1259 OID 17232)
-- Name: sys_change_log_class_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_class_idx ON public.system_change_log USING btree (class_name);


--
-- TOC entry 3461 (class 1259 OID 17228)
-- Name: sys_change_log_date_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_date_idx ON public.system_change_log USING btree (logdate);


--
-- TOC entry 3462 (class 1259 OID 17231)
-- Name: sys_change_log_day_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_day_idx ON public.system_change_log USING btree (log_day);


--
-- TOC entry 3463 (class 1259 OID 17227)
-- Name: sys_change_log_login_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_login_idx ON public.system_change_log USING btree (login);


--
-- TOC entry 3464 (class 1259 OID 17230)
-- Name: sys_change_log_month_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_month_idx ON public.system_change_log USING btree (log_month);


--
-- TOC entry 3465 (class 1259 OID 17233)
-- Name: sys_change_log_table_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_table_idx ON public.system_change_log USING btree (tablename);


--
-- TOC entry 3466 (class 1259 OID 17229)
-- Name: sys_change_log_year_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_change_log_year_idx ON public.system_change_log USING btree (log_year);


--
-- TOC entry 3424 (class 1259 OID 17177)
-- Name: sys_document_bookmark_document_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_bookmark_document_idx ON public.system_document_bookmark USING btree (system_document_id);


--
-- TOC entry 3425 (class 1259 OID 17176)
-- Name: sys_document_bookmark_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_bookmark_user_idx ON public.system_document_bookmark USING btree (system_user_id);


--
-- TOC entry 3411 (class 1259 OID 17170)
-- Name: sys_document_category_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_category_idx ON public.system_document USING btree (category_id);


--
-- TOC entry 3395 (class 1259 OID 17161)
-- Name: sys_document_category_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_category_name_idx ON public.system_document_category USING btree (name);


--
-- TOC entry 3412 (class 1259 OID 17171)
-- Name: sys_document_folder_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_folder_idx ON public.system_document USING btree (system_folder_id);


--
-- TOC entry 3420 (class 1259 OID 17174)
-- Name: sys_document_group_document_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_group_document_idx ON public.system_document_group USING btree (document_id);


--
-- TOC entry 3421 (class 1259 OID 17175)
-- Name: sys_document_group_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_group_group_idx ON public.system_document_group USING btree (system_group_id);


--
-- TOC entry 3416 (class 1259 OID 17172)
-- Name: sys_document_user_document_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_user_document_idx ON public.system_document_user USING btree (document_id);


--
-- TOC entry 3413 (class 1259 OID 17169)
-- Name: sys_document_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_user_idx ON public.system_document USING btree (system_user_id);


--
-- TOC entry 3417 (class 1259 OID 17173)
-- Name: sys_document_user_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_document_user_user_idx ON public.system_document_user USING btree (system_user_id);


--
-- TOC entry 3428 (class 1259 OID 17179)
-- Name: sys_folder_bookmark_folder_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_bookmark_folder_idx ON public.system_folder_bookmark USING btree (system_folder_id);


--
-- TOC entry 3429 (class 1259 OID 17178)
-- Name: sys_folder_bookmark_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_bookmark_user_idx ON public.system_folder_bookmark USING btree (system_user_id);


--
-- TOC entry 3407 (class 1259 OID 17167)
-- Name: sys_folder_group_folder_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_group_folder_idx ON public.system_folder_group USING btree (system_folder_id);


--
-- TOC entry 3408 (class 1259 OID 17168)
-- Name: sys_folder_group_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_group_group_idx ON public.system_folder_group USING btree (system_group_id);


--
-- TOC entry 3398 (class 1259 OID 17163)
-- Name: sys_folder_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_name_idx ON public.system_folder USING btree (name);


--
-- TOC entry 3399 (class 1259 OID 17164)
-- Name: sys_folder_parend_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_parend_id_idx ON public.system_folder USING btree (system_folder_parent_id);


--
-- TOC entry 3403 (class 1259 OID 17165)
-- Name: sys_folder_user_folder_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_user_folder_idx ON public.system_folder_user USING btree (system_folder_id);


--
-- TOC entry 3400 (class 1259 OID 17162)
-- Name: sys_folder_user_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_user_id_idx ON public.system_folder USING btree (system_user_id);


--
-- TOC entry 3404 (class 1259 OID 17166)
-- Name: sys_folder_user_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_folder_user_user_idx ON public.system_folder_user USING btree (system_user_id);


--
-- TOC entry 3496 (class 1259 OID 17409)
-- Name: sys_group_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_group_name_idx ON public.system_group USING btree (name);


--
-- TOC entry 3527 (class 1259 OID 17405)
-- Name: sys_group_program_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_group_program_group_idx ON public.system_group_program USING btree (system_group_id);


--
-- TOC entry 3528 (class 1259 OID 17404)
-- Name: sys_group_program_program_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_group_program_program_idx ON public.system_group_program USING btree (system_program_id);


--
-- TOC entry 3387 (class 1259 OID 17157)
-- Name: sys_message_user_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_message_user_id_idx ON public.system_message USING btree (system_user_id);


--
-- TOC entry 3388 (class 1259 OID 17158)
-- Name: sys_message_user_to_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_message_user_to_idx ON public.system_message USING btree (system_user_to_id);


--
-- TOC entry 3391 (class 1259 OID 17159)
-- Name: sys_notification_user_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_notification_user_id_idx ON public.system_notification USING btree (system_user_id);


--
-- TOC entry 3392 (class 1259 OID 17160)
-- Name: sys_notification_user_to_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_notification_user_to_idx ON public.system_notification USING btree (system_user_to_id);


--
-- TOC entry 3442 (class 1259 OID 17185)
-- Name: sys_post_comment_post_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_comment_post_idx ON public.system_post_comment USING btree (system_post_id);


--
-- TOC entry 3443 (class 1259 OID 17184)
-- Name: sys_post_comment_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_comment_user_idx ON public.system_post_comment USING btree (system_user_id);


--
-- TOC entry 3446 (class 1259 OID 17187)
-- Name: sys_post_like_post_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_like_post_idx ON public.system_post_like USING btree (system_post_id);


--
-- TOC entry 3447 (class 1259 OID 17186)
-- Name: sys_post_like_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_like_user_idx ON public.system_post_like USING btree (system_user_id);


--
-- TOC entry 3435 (class 1259 OID 17181)
-- Name: sys_post_share_group_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_share_group_group_idx ON public.system_post_share_group USING btree (system_group_id);


--
-- TOC entry 3436 (class 1259 OID 17182)
-- Name: sys_post_share_group_post_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_share_group_post_idx ON public.system_post_share_group USING btree (system_post_id);


--
-- TOC entry 3439 (class 1259 OID 17183)
-- Name: sys_post_tag_post_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_tag_post_idx ON public.system_post_tag USING btree (system_post_id);


--
-- TOC entry 3432 (class 1259 OID 17180)
-- Name: sys_post_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_post_user_idx ON public.system_post USING btree (system_user_id);


--
-- TOC entry 3509 (class 1259 OID 17414)
-- Name: sys_preference_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_preference_id_idx ON public.system_preference USING btree (id);


--
-- TOC entry 3510 (class 1259 OID 17415)
-- Name: sys_preference_value_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_preference_value_idx ON public.system_preference USING btree (value);


--
-- TOC entry 3499 (class 1259 OID 17411)
-- Name: sys_program_controller_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_program_controller_idx ON public.system_program USING btree (controller);


--
-- TOC entry 3538 (class 1259 OID 17421)
-- Name: sys_program_method_role_program_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_program_method_role_program_idx ON public.system_program_method_role USING btree (system_program_id);


--
-- TOC entry 3539 (class 1259 OID 17422)
-- Name: sys_program_method_role_role_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_program_method_role_role_idx ON public.system_program_method_role USING btree (system_role_id);


--
-- TOC entry 3500 (class 1259 OID 17410)
-- Name: sys_program_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_program_name_idx ON public.system_program USING btree (name);


--
-- TOC entry 3484 (class 1259 OID 17250)
-- Name: sys_request_log_class_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_class_idx ON public.system_request_log USING btree (class_name);


--
-- TOC entry 3485 (class 1259 OID 17246)
-- Name: sys_request_log_date_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_date_idx ON public.system_request_log USING btree (logdate);


--
-- TOC entry 3486 (class 1259 OID 17249)
-- Name: sys_request_log_day_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_day_idx ON public.system_request_log USING btree (log_day);


--
-- TOC entry 3487 (class 1259 OID 17245)
-- Name: sys_request_log_login_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_login_idx ON public.system_request_log USING btree (login);


--
-- TOC entry 3488 (class 1259 OID 17251)
-- Name: sys_request_log_method_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_method_idx ON public.system_request_log USING btree (class_method);


--
-- TOC entry 3489 (class 1259 OID 17248)
-- Name: sys_request_log_month_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_month_idx ON public.system_request_log USING btree (log_month);


--
-- TOC entry 3490 (class 1259 OID 17247)
-- Name: sys_request_log_year_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_request_log_year_idx ON public.system_request_log USING btree (log_year);


--
-- TOC entry 3506 (class 1259 OID 17413)
-- Name: sys_role_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_role_name_idx ON public.system_role USING btree (name);


--
-- TOC entry 3469 (class 1259 OID 17237)
-- Name: sys_sql_log_class_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_class_idx ON public.system_sql_log USING btree (class_name);


--
-- TOC entry 3470 (class 1259 OID 17236)
-- Name: sys_sql_log_database_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_database_idx ON public.system_sql_log USING btree (database_name);


--
-- TOC entry 3471 (class 1259 OID 17235)
-- Name: sys_sql_log_date_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_date_idx ON public.system_sql_log USING btree (logdate);


--
-- TOC entry 3472 (class 1259 OID 17240)
-- Name: sys_sql_log_day_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_day_idx ON public.system_sql_log USING btree (log_day);


--
-- TOC entry 3473 (class 1259 OID 17234)
-- Name: sys_sql_log_login_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_login_idx ON public.system_sql_log USING btree (login);


--
-- TOC entry 3474 (class 1259 OID 17239)
-- Name: sys_sql_log_month_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_month_idx ON public.system_sql_log USING btree (log_month);


--
-- TOC entry 3475 (class 1259 OID 17238)
-- Name: sys_sql_log_year_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_sql_log_year_idx ON public.system_sql_log USING btree (log_year);


--
-- TOC entry 3503 (class 1259 OID 17412)
-- Name: sys_unit_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_unit_name_idx ON public.system_unit USING btree (name);


--
-- TOC entry 3519 (class 1259 OID 17402)
-- Name: sys_user_group_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_group_group_idx ON public.system_user_group USING btree (system_group_id);


--
-- TOC entry 3520 (class 1259 OID 17403)
-- Name: sys_user_group_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_group_user_idx ON public.system_user_group USING btree (system_user_id);


--
-- TOC entry 3535 (class 1259 OID 17420)
-- Name: sys_user_old_password_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_old_password_user_idx ON public.system_user_old_password USING btree (system_user_id);


--
-- TOC entry 3511 (class 1259 OID 17401)
-- Name: sys_user_program_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_program_idx ON public.system_users USING btree (frontpage_id);


--
-- TOC entry 3531 (class 1259 OID 17406)
-- Name: sys_user_program_program_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_program_program_idx ON public.system_user_program USING btree (system_program_id);


--
-- TOC entry 3532 (class 1259 OID 17407)
-- Name: sys_user_program_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_program_user_idx ON public.system_user_program USING btree (system_user_id);


--
-- TOC entry 3523 (class 1259 OID 17419)
-- Name: sys_user_role_role_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_role_role_idx ON public.system_user_role USING btree (system_role_id);


--
-- TOC entry 3524 (class 1259 OID 17418)
-- Name: sys_user_role_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_role_user_idx ON public.system_user_role USING btree (system_user_id);


--
-- TOC entry 3515 (class 1259 OID 17417)
-- Name: sys_user_unit_unit_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_unit_unit_idx ON public.system_user_unit USING btree (system_unit_id);


--
-- TOC entry 3516 (class 1259 OID 17416)
-- Name: sys_user_unit_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_user_unit_user_idx ON public.system_user_unit USING btree (system_user_id);


--
-- TOC entry 3512 (class 1259 OID 17408)
-- Name: sys_users_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_users_name_idx ON public.system_users USING btree (name);


--
-- TOC entry 3450 (class 1259 OID 17188)
-- Name: sys_wiki_page_user_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_wiki_page_user_idx ON public.system_wiki_page USING btree (system_user_id);


--
-- TOC entry 3456 (class 1259 OID 17190)
-- Name: sys_wiki_share_group_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_wiki_share_group_group_idx ON public.system_wiki_share_group USING btree (system_group_id);


--
-- TOC entry 3457 (class 1259 OID 17191)
-- Name: sys_wiki_share_group_page_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_wiki_share_group_page_idx ON public.system_wiki_share_group USING btree (system_wiki_page_id);


--
-- TOC entry 3453 (class 1259 OID 17189)
-- Name: sys_wiki_tag_page_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sys_wiki_tag_page_idx ON public.system_wiki_tag USING btree (system_wiki_page_id);


--
-- TOC entry 3597 (class 2606 OID 17572)
-- Name: item_recebimento_material item_recebimento_material_id_materialresidual_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_recebimento_material
    ADD CONSTRAINT item_recebimento_material_id_materialresidual_fkey FOREIGN KEY (id_materialresidual) REFERENCES public.material_residuo(id_materialresidual);


--
-- TOC entry 3598 (class 2606 OID 17567)
-- Name: item_recebimento_material item_recebimento_material_id_recebimentomaterial_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_recebimento_material
    ADD CONSTRAINT item_recebimento_material_id_recebimentomaterial_fkey FOREIGN KEY (id_recebimentomaterial) REFERENCES public.recebimento_material(id_recebimentomaterial);


--
-- TOC entry 3594 (class 2606 OID 17478)
-- Name: material_residuo material_residuo_id_tiporesiduo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_residuo
    ADD CONSTRAINT material_residuo_id_tiporesiduo_fkey FOREIGN KEY (id_tiporesiduo) REFERENCES public.tipo_residuo(id_tiporesiduo) ON DELETE SET NULL;


--
-- TOC entry 3595 (class 2606 OID 17473)
-- Name: material_residuo material_residuo_id_unidademedida_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_residuo
    ADD CONSTRAINT material_residuo_id_unidademedida_fkey FOREIGN KEY (id_unidademedida) REFERENCES public.unidade_medida(id_unidademedida) ON DELETE SET NULL;


--
-- TOC entry 3596 (class 2606 OID 17490)
-- Name: recebimento_material recebimento_material_id_fichacadastral_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recebimento_material
    ADD CONSTRAINT recebimento_material_id_fichacadastral_fkey FOREIGN KEY (id_fichacadastral) REFERENCES public.ficha_cadastral(id_fichacadastral) ON DELETE CASCADE;


--
-- TOC entry 3599 (class 2606 OID 17668)
-- Name: saida_bonificacao saida_bonificacao_id_fichacadastral_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saida_bonificacao
    ADD CONSTRAINT saida_bonificacao_id_fichacadastral_fkey FOREIGN KEY (id_fichacadastral) REFERENCES public.ficha_cadastral(id_fichacadastral);


--
-- TOC entry 3571 (class 2606 OID 17063)
-- Name: system_document_bookmark system_document_bookmark_system_document_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_bookmark
    ADD CONSTRAINT system_document_bookmark_system_document_id_fkey FOREIGN KEY (system_document_id) REFERENCES public.system_document(id);


--
-- TOC entry 3567 (class 2606 OID 17028)
-- Name: system_document system_document_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document
    ADD CONSTRAINT system_document_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.system_document_category(id);


--
-- TOC entry 3570 (class 2606 OID 17053)
-- Name: system_document_group system_document_group_document_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_group
    ADD CONSTRAINT system_document_group_document_id_fkey FOREIGN KEY (document_id) REFERENCES public.system_document(id);


--
-- TOC entry 3568 (class 2606 OID 17033)
-- Name: system_document system_document_system_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document
    ADD CONSTRAINT system_document_system_folder_id_fkey FOREIGN KEY (system_folder_id) REFERENCES public.system_folder(id);


--
-- TOC entry 3569 (class 2606 OID 17043)
-- Name: system_document_user system_document_user_document_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_document_user
    ADD CONSTRAINT system_document_user_document_id_fkey FOREIGN KEY (document_id) REFERENCES public.system_document(id);


--
-- TOC entry 3572 (class 2606 OID 17073)
-- Name: system_folder_bookmark system_folder_bookmark_system_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder_bookmark
    ADD CONSTRAINT system_folder_bookmark_system_folder_id_fkey FOREIGN KEY (system_folder_id) REFERENCES public.system_folder(id);


--
-- TOC entry 3566 (class 2606 OID 17016)
-- Name: system_folder_group system_folder_group_system_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder_group
    ADD CONSTRAINT system_folder_group_system_folder_id_fkey FOREIGN KEY (system_folder_id) REFERENCES public.system_folder(id);


--
-- TOC entry 3564 (class 2606 OID 16996)
-- Name: system_folder system_folder_system_folder_parent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder
    ADD CONSTRAINT system_folder_system_folder_parent_id_fkey FOREIGN KEY (system_folder_parent_id) REFERENCES public.system_folder(id);


--
-- TOC entry 3565 (class 2606 OID 17006)
-- Name: system_folder_user system_folder_user_system_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_folder_user
    ADD CONSTRAINT system_folder_user_system_folder_id_fkey FOREIGN KEY (system_folder_id) REFERENCES public.system_folder(id);


--
-- TOC entry 3587 (class 2606 OID 17351)
-- Name: system_group_program system_group_program_system_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_group_program
    ADD CONSTRAINT system_group_program_system_group_id_fkey FOREIGN KEY (system_group_id) REFERENCES public.system_group(id);


--
-- TOC entry 3588 (class 2606 OID 17356)
-- Name: system_group_program system_group_program_system_program_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_group_program
    ADD CONSTRAINT system_group_program_system_program_id_fkey FOREIGN KEY (system_program_id) REFERENCES public.system_program(id);


--
-- TOC entry 3575 (class 2606 OID 17113)
-- Name: system_post_comment system_post_comment_system_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_comment
    ADD CONSTRAINT system_post_comment_system_post_id_fkey FOREIGN KEY (system_post_id) REFERENCES public.system_post(id);


--
-- TOC entry 3576 (class 2606 OID 17123)
-- Name: system_post_like system_post_like_system_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_like
    ADD CONSTRAINT system_post_like_system_post_id_fkey FOREIGN KEY (system_post_id) REFERENCES public.system_post(id);


--
-- TOC entry 3573 (class 2606 OID 17091)
-- Name: system_post_share_group system_post_share_group_system_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_share_group
    ADD CONSTRAINT system_post_share_group_system_post_id_fkey FOREIGN KEY (system_post_id) REFERENCES public.system_post(id);


--
-- TOC entry 3574 (class 2606 OID 17101)
-- Name: system_post_tag system_post_tag_system_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_post_tag
    ADD CONSTRAINT system_post_tag_system_post_id_fkey FOREIGN KEY (system_post_id) REFERENCES public.system_post(id);


--
-- TOC entry 3592 (class 2606 OID 17391)
-- Name: system_program_method_role system_program_method_role_system_program_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_program_method_role
    ADD CONSTRAINT system_program_method_role_system_program_id_fkey FOREIGN KEY (system_program_id) REFERENCES public.system_program(id);


--
-- TOC entry 3593 (class 2606 OID 17396)
-- Name: system_program_method_role system_program_method_role_system_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_program_method_role
    ADD CONSTRAINT system_program_method_role_system_role_id_fkey FOREIGN KEY (system_role_id) REFERENCES public.system_role(id);


--
-- TOC entry 3583 (class 2606 OID 17326)
-- Name: system_user_group system_user_group_system_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_group
    ADD CONSTRAINT system_user_group_system_group_id_fkey FOREIGN KEY (system_group_id) REFERENCES public.system_group(id);


--
-- TOC entry 3584 (class 2606 OID 17321)
-- Name: system_user_group system_user_group_system_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_group
    ADD CONSTRAINT system_user_group_system_user_id_fkey FOREIGN KEY (system_user_id) REFERENCES public.system_users(id);


--
-- TOC entry 3591 (class 2606 OID 17381)
-- Name: system_user_old_password system_user_old_password_system_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_old_password
    ADD CONSTRAINT system_user_old_password_system_user_id_fkey FOREIGN KEY (system_user_id) REFERENCES public.system_users(id);


--
-- TOC entry 3589 (class 2606 OID 17371)
-- Name: system_user_program system_user_program_system_program_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_program
    ADD CONSTRAINT system_user_program_system_program_id_fkey FOREIGN KEY (system_program_id) REFERENCES public.system_program(id);


--
-- TOC entry 3590 (class 2606 OID 17366)
-- Name: system_user_program system_user_program_system_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_program
    ADD CONSTRAINT system_user_program_system_user_id_fkey FOREIGN KEY (system_user_id) REFERENCES public.system_users(id);


--
-- TOC entry 3585 (class 2606 OID 17341)
-- Name: system_user_role system_user_role_system_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_role
    ADD CONSTRAINT system_user_role_system_role_id_fkey FOREIGN KEY (system_role_id) REFERENCES public.system_role(id);


--
-- TOC entry 3586 (class 2606 OID 17336)
-- Name: system_user_role system_user_role_system_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_role
    ADD CONSTRAINT system_user_role_system_user_id_fkey FOREIGN KEY (system_user_id) REFERENCES public.system_users(id);


--
-- TOC entry 3581 (class 2606 OID 17311)
-- Name: system_user_unit system_user_unit_system_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_unit
    ADD CONSTRAINT system_user_unit_system_unit_id_fkey FOREIGN KEY (system_unit_id) REFERENCES public.system_unit(id);


--
-- TOC entry 3582 (class 2606 OID 17306)
-- Name: system_user_unit system_user_unit_system_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_user_unit
    ADD CONSTRAINT system_user_unit_system_user_id_fkey FOREIGN KEY (system_user_id) REFERENCES public.system_users(id);


--
-- TOC entry 3579 (class 2606 OID 17296)
-- Name: system_users system_users_frontpage_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_users
    ADD CONSTRAINT system_users_frontpage_id_fkey FOREIGN KEY (frontpage_id) REFERENCES public.system_program(id);


--
-- TOC entry 3580 (class 2606 OID 17291)
-- Name: system_users system_users_system_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_users
    ADD CONSTRAINT system_users_system_unit_id_fkey FOREIGN KEY (system_unit_id) REFERENCES public.system_unit(id);


--
-- TOC entry 3578 (class 2606 OID 17152)
-- Name: system_wiki_share_group system_wiki_share_group_system_wiki_page_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_wiki_share_group
    ADD CONSTRAINT system_wiki_share_group_system_wiki_page_id_fkey FOREIGN KEY (system_wiki_page_id) REFERENCES public.system_wiki_page(id);


--
-- TOC entry 3577 (class 2606 OID 17142)
-- Name: system_wiki_tag system_wiki_tag_system_wiki_page_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_wiki_tag
    ADD CONSTRAINT system_wiki_tag_system_wiki_page_id_fkey FOREIGN KEY (system_wiki_page_id) REFERENCES public.system_wiki_page(id);


-- Completed on 2024-02-05 23:51:44

--
-- PostgreSQL database dump complete
--

--
-- Database "postgres" dump
--

\connect postgres

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.5
-- Dumped by pg_dump version 15.5

-- Started on 2024-02-05 23:51:44

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16384)
-- Name: adminpack; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS adminpack WITH SCHEMA pg_catalog;


--
-- TOC entry 3316 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION adminpack; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION adminpack IS 'administrative functions for PostgreSQL';


-- Completed on 2024-02-05 23:51:44

--
-- PostgreSQL database dump complete
--

-- Completed on 2024-02-05 23:51:44

--
-- PostgreSQL database cluster dump complete
--

