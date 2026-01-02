--
-- PostgreSQL database dump
--

\restrict iRDJgNoG0oVI9QNazs8nU5xltJGGUFnsxnUXWojSaE3pfmyY8Ujug39C73ac0us

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-01-01 01:26:01

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 41012)
-- Name: nomina; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nomina (
    id_recibo integer NOT NULL,
    cedula_trabajador character varying(20) NOT NULL,
    dias_trabajados integer NOT NULL,
    valor_dia_usd numeric(10,2) NOT NULL,
    horas_extras integer DEFAULT 0,
    valor_hora_extra_usd numeric(10,2) DEFAULT 0,
    bonus_usd numeric(10,2) DEFAULT 0,
    seguro_social_usd numeric(10,2) DEFAULT 0,
    prestamos_usd numeric(10,2) DEFAULT 0,
    total_usd numeric(10,2) NOT NULL,
    total_bs numeric(15,2) NOT NULL,
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    tasa_cambio_bs numeric(15,2) DEFAULT 0,
    observaciones text
);


ALTER TABLE public.nomina OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 41011)
-- Name: nomina_id_recibo_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.nomina_id_recibo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.nomina_id_recibo_seq OWNER TO postgres;

--
-- TOC entry 5000 (class 0 OID 0)
-- Dependencies: 223
-- Name: nomina_id_recibo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.nomina_id_recibo_seq OWNED BY public.nomina.id_recibo;


--
-- TOC entry 222 (class 1259 OID 32820)
-- Name: registro; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.registro (
    id_registro integer NOT NULL,
    nombres character varying(100) NOT NULL,
    apellidos character varying(100) NOT NULL,
    cedula character varying(20) NOT NULL,
    cargo character varying(100),
    fecha_ingreso date NOT NULL,
    direccion text,
    telefono character varying(20),
    observaciones text,
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.registro OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 32819)
-- Name: registro_id_registro_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.registro_id_registro_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.registro_id_registro_seq OWNER TO postgres;

--
-- TOC entry 5001 (class 0 OID 0)
-- Dependencies: 221
-- Name: registro_id_registro_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.registro_id_registro_seq OWNED BY public.registro.id_registro;


--
-- TOC entry 220 (class 1259 OID 32774)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 32773)
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- TOC entry 5002 (class 0 OID 0)
-- Dependencies: 219
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- TOC entry 4822 (class 2604 OID 41015)
-- Name: nomina id_recibo; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nomina ALTER COLUMN id_recibo SET DEFAULT nextval('public.nomina_id_recibo_seq'::regclass);


--
-- TOC entry 4820 (class 2604 OID 32823)
-- Name: registro id_registro; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registro ALTER COLUMN id_registro SET DEFAULT nextval('public.registro_id_registro_seq'::regclass);


--
-- TOC entry 4819 (class 2604 OID 32777)
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- TOC entry 4994 (class 0 OID 41012)
-- Dependencies: 224
-- Data for Name: nomina; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nomina (id_recibo, cedula_trabajador, dias_trabajados, valor_dia_usd, horas_extras, valor_hora_extra_usd, bonus_usd, seguro_social_usd, prestamos_usd, total_usd, total_bs, fecha_registro, tasa_cambio_bs, observaciones) FROM stdin;
1	17135693	7	40.00	20	3.00	20.00	0.00	0.00	360.00	108000.00	2025-12-30 01:09:33.776194	0.00	\N
2	17135693	20	50.00	50	5.00	80.00	50.00	20.00	1260.00	378000.00	2025-12-30 01:10:59.825697	0.00	\N
10	17135693	20	20.00	20	20.00	20.00	5.00	5.00	810.00	243000.00	2025-12-30 02:13:46.64865	300.00	adiconal
11	17135693	20	20.00	20	20.00	20.00	20.00	20.00	780.00	234000.00	2025-12-30 02:39:07.93448	300.00	aaaaa
12		20	20.00	20	20.00	20.00	20.00	2.00	798.00	239400.00	2025-12-30 04:16:04.035132	300.00	qasdas
13	17135693	20	20.00	20	20.00	20.00	20.00	2.00	798.00	239400.00	2025-12-30 04:16:52.144923	300.00	qasdas
14	17135693	20	20.00	20	20.00	20.00	20.00	20.00	780.00	234000.00	2025-12-30 14:29:20.348507	300.00	adasdas
15	17135693	50	50.00	50	50.00	50.00	50.00	50.00	4950.00	1485000.00	2025-12-30 14:35:23.72517	300.00	dasdasd
16	17135693	20	20.00	20	20.00	20.00	2.00	2.00	816.00	244800.00	2025-12-30 15:38:32.207205	300.00	adsasdas
17	17135693	30	30.00	30	30.00	30.00	5.00	5.00	1820.00	546000.00	2025-12-30 15:55:05.282934	300.00	adsasda
18	17135693	50	50.00	50	50.00	50.00	5.00	5.00	5040.00	1512000.00	2025-12-30 16:49:32.506249	300.00	dasdasda
19	123456	20	20.00	20	20.00	20.00	5.00	5.00	810.00	243000.00	2025-12-30 23:19:40.24375	300.00	adicional
20	17135693	25	25.00	25	25.00	25.00	2.00	2.00	1271.00	381300.00	2025-12-31 03:04:20.858864	300.00	adicional
21	17135693	25	25.00	25	25.00	25.00	2.00	2.00	1271.00	381300.00	2025-12-31 03:05:32.003801	300.00	adicional
22	17135693	20	20.00	20	20.00	20.00	20.00	20.00	780.00	234000.00	2025-12-31 03:06:22.29819	300.00	adiconal
23	17135693	20	20.00	20	20.00	20.00	2.00	2.00	816.00	244800.00	2025-12-31 03:12:58.313114	300.00	adiconal muchos
24	17135693	20	20.00	20	20.00	20.00	2.00	2.00	816.00	244800.00	2025-12-31 03:16:01.909332	300.00	adiconal muchos
25	17135693	30	30.00	30	30.00	30.00	5.00	5.00	1820.00	546000.00	2025-12-31 03:19:24.633561	300.00	seguimos 
26	17135693	30	30.00	30	30.00	30.00	5.00	5.00	1820.00	546000.00	2025-12-31 03:20:22.803972	300.00	seguimos 
27	17135693	35	35.00	35	35.00	35.00	5.00	5.00	2475.00	742500.00	2025-12-31 03:43:24.50571	300.00	seguimos 
28	17135693	35	35.00	35	35.00	35.00	5.00	5.00	2475.00	742500.00	2025-12-31 03:45:31.584902	300.00	seguimos 
29	17135693	35	35.00	35	35.00	35.00	5.00	5.00	2475.00	742500.00	2025-12-31 03:51:21.69739	300.00	seguimos 
30	17135693	35	35.00	35	35.00	35.00	5.00	5.00	2475.00	742500.00	2025-12-31 03:54:29.896161	300.00	seguimos 
31	17135693	30	30.00	30	30.00	30.00	5.00	5.00	1820.00	546000.00	2025-12-31 03:56:55.43248	300.00	aaaaaaaaa
32	17135693	30	30.00	30	30.00	30.00	5.00	5.00	1820.00	546000.00	2025-12-31 03:57:38.675363	300.00	aaaaaaaaa
33	17135693	50	50.00	50	50.00	50.00	50.00	2.00	4998.00	1499400.00	2025-12-31 03:58:28.519911	300.00	aaaaaa
34	17135693	25	25.00	25	25.00	25.00	5.00	5.00	1265.00	379500.00	2025-12-31 04:16:27.001788	300.00	aaaaa
35	17135693	25	25.00	25	25.00	25.00	5.00	5.00	1265.00	379500.00	2025-12-31 04:17:02.887494	300.00	aaaaa
36	17135693	50	50.00	50	50.00	50.00	5.00	5.00	5040.00	1512000.00	2025-12-31 04:49:05.104172	300.00	aaaaaaa
37	17135693	50	50.00	50	50.00	50.00	5.00	5.00	5040.00	1512000.00	2025-12-31 12:15:38.60449	300.00	asdasdasfasf
38	17135693	20	20.00	20	20.00	20.00	5.00	5.00	810.00	243000.00	2025-12-31 16:45:59.912198	300.00	asdasd
39	17135693	50	20.00	10	5.00	100.00	5.00	10.00	1135.00	340500.00	2026-01-01 00:19:25.276711	300.00	grghjsjfjf
40	17135693	7	25.00	14	2.50	0.00	0.00	0.00	210.00	63000.00	2026-01-01 00:28:03.506489	300.00	
41	17135693	20	20.00	20	20.00	20.00	20.00	10.00	790.00	237000.00	2026-01-01 00:38:14.426347	300.00	afdasf
\.


--
-- TOC entry 4992 (class 0 OID 32820)
-- Dependencies: 222
-- Data for Name: registro; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.registro (id_registro, nombres, apellidos, cedula, cargo, fecha_ingreso, direccion, telefono, observaciones, fecha_registro) FROM stdin;
1	ivan	manaure	19945802	ingeniero	2025-12-28	as	04127877402	asd	2025-12-28 23:32:37.447953
6	tata	tato	987456321	adada	2025-12-28	fadsfafa	154125412	fgasggfcxb	2025-12-29 01:25:25.761789
8	pafilo	folio	123456	civil	2025-12-30	aaasa	5555555555	asdasda	2025-12-30 16:52:25.389185
9	cele	cale	123	obrero	2025-12-31	0000000	0000000	000000	2025-12-31 04:52:06.492433
12	ccccc	sssss	12345678	obrero	2025-12-31	asdasda	2222	22222	2025-12-31 04:54:09.968672
18	petro	cares	202020	obrero	2025-12-31	asfdasda	5555555555	adsasd	2025-12-31 05:01:00.12619
19	teken	teke	1231231243242	obrero	2025-12-31	fgasgagasgasf	545346323	fgasgfdghfd	2025-12-31 12:17:49.25379
3	jose	cayama manaure	17135693	ingeniero	2025-12-28	fgdfg	04127877402	dfgdfg	2025-12-28 23:38:40.334801
\.


--
-- TOC entry 4990 (class 0 OID 32774)
-- Dependencies: 220
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, username, password) FROM stdin;
1	adm	123456789
\.


--
-- TOC entry 5003 (class 0 OID 0)
-- Dependencies: 223
-- Name: nomina_id_recibo_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.nomina_id_recibo_seq', 41, true);


--
-- TOC entry 5004 (class 0 OID 0)
-- Dependencies: 221
-- Name: registro_id_registro_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.registro_id_registro_seq', 20, true);


--
-- TOC entry 5005 (class 0 OID 0)
-- Dependencies: 219
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 1, true);


--
-- TOC entry 4841 (class 2606 OID 41030)
-- Name: nomina nomina_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nomina
    ADD CONSTRAINT nomina_pkey PRIMARY KEY (id_recibo);


--
-- TOC entry 4836 (class 2606 OID 32835)
-- Name: registro registro_cedula_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registro
    ADD CONSTRAINT registro_cedula_key UNIQUE (cedula);


--
-- TOC entry 4838 (class 2606 OID 32833)
-- Name: registro registro_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registro
    ADD CONSTRAINT registro_pkey PRIMARY KEY (id_registro);


--
-- TOC entry 4831 (class 2606 OID 32782)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 4833 (class 2606 OID 32784)
-- Name: usuarios usuarios_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_username_key UNIQUE (username);


--
-- TOC entry 4834 (class 1259 OID 32836)
-- Name: idx_consulta_cedula; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_consulta_cedula ON public.registro USING btree (cedula);


--
-- TOC entry 4839 (class 1259 OID 41031)
-- Name: idx_nomina_cedula; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_nomina_cedula ON public.nomina USING btree (cedula_trabajador);


-- Completed on 2026-01-01 01:26:01

--
-- PostgreSQL database dump complete
--

\unrestrict iRDJgNoG0oVI9QNazs8nU5xltJGGUFnsxnUXWojSaE3pfmyY8Ujug39C73ac0us

