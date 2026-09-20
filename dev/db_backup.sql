--
-- PostgreSQL database dump
--

\restrict wfqcMfG0HkXmahMaZT2BcFopkr41oNxE2DlU71Ocf2eiKqhGNc6jFYhFfhpEwv6

-- Dumped from database version 18.2 (Postgres.app)
-- Dumped by pg_dump version 18.0

-- Started on 2026-09-20 14:29:51 CEST

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
-- TOC entry 221 (class 1259 OID 16738)
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
-- TOC entry 223 (class 1259 OID 16747)
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
-- TOC entry 222 (class 1259 OID 16746)
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
-- TOC entry 3849 (class 0 OID 0)
-- Dependencies: 222
-- Name: goal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.goal_id_seq OWNED BY public.goal.id;


--
-- TOC entry 220 (class 1259 OID 16728)
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
-- TOC entry 219 (class 1259 OID 16727)
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
-- TOC entry 3850 (class 0 OID 0)
-- Dependencies: 219
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- TOC entry 3682 (class 2604 OID 16750)
-- Name: goal id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.goal ALTER COLUMN id SET DEFAULT nextval('public.goal_id_seq'::regclass);


--
-- TOC entry 3679 (class 2604 OID 16731)
-- Name: user id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- TOC entry 3841 (class 0 OID 16738)
-- Dependencies: 221
-- Data for Name: category; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.category VALUES (1, 16, 'Gym', false);
INSERT INTO public.category VALUES (1, 13, 'Gym', false);
INSERT INTO public.category VALUES (1, 14, 'Gym', false);
INSERT INTO public.category VALUES (1, 15, 'Gym', false);
INSERT INTO public.category VALUES (2, 9, 'Gym', false);
INSERT INTO public.category VALUES (2, 10, 'Gym', false);
INSERT INTO public.category VALUES (2, 11, 'Gym', false);
INSERT INTO public.category VALUES (2, 12, 'Gym', false);
INSERT INTO public.category VALUES (1, 5, 'Calestenics', false);
INSERT INTO public.category VALUES (1, 6, 'Calestenics', false);
INSERT INTO public.category VALUES (1, 7, 'Calestenics', false);
INSERT INTO public.category VALUES (1, 8, 'Calestenics', false);


--
-- TOC entry 3843 (class 0 OID 16747)
-- Dependencies: 223
-- Data for Name: goal; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.goal VALUES (10, 'backfull', 1, false);
INSERT INTO public.goal VALUES (6, 'HSPU coude 90°', 3, false);
INSERT INTO public.goal VALUES (5, 'HSPU semi-ampli', 4, false);
INSERT INTO public.goal VALUES (8, 'l-sit en HS', 1, false);
INSERT INTO public.goal VALUES (7, 'HS en elbow', 2, false);
INSERT INTO public.goal VALUES (14, 'side sol', 3, false);
INSERT INTO public.goal VALUES (15, 'double front', 2, false);
INSERT INTO public.goal VALUES (16, 'double back', 1, false);
INSERT INTO public.goal VALUES (13, 'side', 4, false);
INSERT INTO public.goal VALUES (9, '1/2 backfull', 2, false);
INSERT INTO public.goal VALUES (11, 'double side', 3, false);
INSERT INTO public.goal VALUES (12, 'double front', 4, false);


--
-- TOC entry 3840 (class 0 OID 16728)
-- Dependencies: 220
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public."user" VALUES (2, 'Quentyn', 'fdaa1733a7999f41e41878fe887843476a512b39b1da20323b221ec7ed4c6ed6', 0);
INSERT INTO public."user" VALUES (1, 'Mathys', '174dd44f9d8cbed784d676260f13aecd0e04e29f49ca9420645bc45deb649ea7', 0);


--
-- TOC entry 3851 (class 0 OID 0)
-- Dependencies: 222
-- Name: goal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.goal_id_seq', 16, true);


--
-- TOC entry 3852 (class 0 OID 0)
-- Dependencies: 219
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_id_seq', 2, true);


--
-- TOC entry 3687 (class 2606 OID 16745)
-- Name: category category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT category_pkey PRIMARY KEY (user_id, goal_id);


--
-- TOC entry 3689 (class 2606 OID 16756)
-- Name: goal goal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.goal
    ADD CONSTRAINT goal_pkey PRIMARY KEY (id);


--
-- TOC entry 3685 (class 2606 OID 16737)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- TOC entry 3690 (class 2606 OID 16762)
-- Name: category fk_category_goal; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT fk_category_goal FOREIGN KEY (goal_id) REFERENCES public.goal(id) NOT VALID;


--
-- TOC entry 3691 (class 2606 OID 16757)
-- Name: category fk_category_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT fk_category_user FOREIGN KEY (user_id) REFERENCES public."user"(id);


-- Completed on 2026-09-20 14:29:52 CEST

--
-- PostgreSQL database dump complete
--

\unrestrict wfqcMfG0HkXmahMaZT2BcFopkr41oNxE2DlU71Ocf2eiKqhGNc6jFYhFfhpEwv6

