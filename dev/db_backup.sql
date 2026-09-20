--
-- PostgreSQL database dump
--

\restrict QLWm64kuxOpceuK5tVg5wefk1cLZ8SIO49Y9yjoo9cQO3IjXGCrjXKC01MbVFCu

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

-- Started on 2026-09-20 03:35:18

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
-- TOC entry 219 (class 1259 OID 24577)
-- Name: category; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.category (
    user_id integer NOT NULL,
    goal_id integer NOT NULL,
    nom character varying(50) NOT NULL,
    is_complete boolean DEFAULT false NOT NULL
);


ALTER TABLE public.category OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 24583)
-- Name: goal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.goal (
    id integer NOT NULL,
    nom character varying(50) NOT NULL,
    type integer NOT NULL,
    is_complete boolean DEFAULT false NOT NULL
);


ALTER TABLE public.goal OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 24591)
-- Name: goal_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.goal_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.goal_id_seq OWNER TO postgres;

--
-- TOC entry 5035 (class 0 OID 0)
-- Dependencies: 221
-- Name: goal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.goal_id_seq OWNED BY public.goal.id;


--
-- TOC entry 222 (class 1259 OID 24592)
-- Name: user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password_hash character varying(256) NOT NULL,
    nbr_point integer DEFAULT 0 NOT NULL
);


ALTER TABLE public."user" OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 24600)
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_id_seq OWNER TO postgres;

--
-- TOC entry 5036 (class 0 OID 0)
-- Dependencies: 223
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- TOC entry 4866 (class 2604 OID 24601)
-- Name: goal id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.goal ALTER COLUMN id SET DEFAULT nextval('public.goal_id_seq'::regclass);


--
-- TOC entry 4868 (class 2604 OID 24602)
-- Name: user id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- TOC entry 5025 (class 0 OID 24577)
-- Dependencies: 219
-- Data for Name: category; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.category (user_id, goal_id, nom, is_complete) FROM stdin;
1	5	Calestenics	f
1	6	Calestenics	f
1	7	Calestenics	f
1	8	Calestenics	f
2	9	Gym	f
2	10	Gym	f
2	11	Gym	f
2	12	Gym	f
1	16	Gym	f
1	13	Gym	f
1	14	Gym	f
1	15	Gym	f
2	17	Test	t
\.


--
-- TOC entry 5026 (class 0 OID 24583)
-- Dependencies: 220
-- Data for Name: goal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.goal (id, nom, type, is_complete) FROM stdin;
5	HSPU semi-ampli	4	f
6	HSPU coude 90°	3	f
7	HS en elbow	2	f
8	l-sit en HS	1	f
13	side	4	f
14	side sol	3	f
15	double front	2	f
16	double back	1	f
10	backfull	1	t
9	1/2 backfull	2	t
11	double side	3	t
12	double front	4	t
17	Test Goal id 17	4	t
\.


--
-- TOC entry 5028 (class 0 OID 24592)
-- Dependencies: 222
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."user" (id, username, password_hash, nbr_point) FROM stdin;
1	Mathys	174dd44f9d8cbed784d676260f13aecd0e04e29f49ca9420645bc45deb649ea7	0
2	Quentyn	ef260e9aa3c673af240d17a2660480361a8e081d1ffeca2a5ed0e3219fc18567	0
\.


--
-- TOC entry 5037 (class 0 OID 0)
-- Dependencies: 221
-- Name: goal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.goal_id_seq', 17, true);


--
-- TOC entry 5038 (class 0 OID 0)
-- Dependencies: 223
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_id_seq', 2, true);


--
-- TOC entry 4871 (class 2606 OID 24604)
-- Name: category category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT category_pkey PRIMARY KEY (user_id, goal_id);


--
-- TOC entry 4873 (class 2606 OID 24606)
-- Name: goal goal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.goal
    ADD CONSTRAINT goal_pkey PRIMARY KEY (id);


--
-- TOC entry 4875 (class 2606 OID 24608)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- TOC entry 4876 (class 2606 OID 24609)
-- Name: category fk_category_goal; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT fk_category_goal FOREIGN KEY (goal_id) REFERENCES public.goal(id) NOT VALID;


--
-- TOC entry 4877 (class 2606 OID 24614)
-- Name: category fk_category_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT fk_category_user FOREIGN KEY (user_id) REFERENCES public."user"(id);


-- Completed on 2026-09-20 03:35:19

--
-- PostgreSQL database dump complete
--

\unrestrict QLWm64kuxOpceuK5tVg5wefk1cLZ8SIO49Y9yjoo9cQO3IjXGCrjXKC01MbVFCu

