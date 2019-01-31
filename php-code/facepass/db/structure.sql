--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.5
-- Dumped by pg_dump version 10.0

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: document_foreign_names; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE document_foreign_names (
    id integer NOT NULL,
    name character varying(25) NOT NULL,
    gender character varying(6) NOT NULL
);


ALTER TABLE document_foreign_names OWNER TO root;

--
-- Name: document_passport_rf; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE document_passport_rf (
    id bigint NOT NULL,
    surname text,
    first_name text,
    patronymic text,
    series_number text,
    date_birth date,
    gender integer,
    birthplace text,
    passport_date date,
    passport_code text,
    passport_place text,
    registration_place text,
    page_one text,
    page_two text,
    page_three text,
    verify boolean DEFAULT true
);


ALTER TABLE document_passport_rf OWNER TO root;

--
-- Name: document_passport_rf_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE document_passport_rf_id_seq
    START WITH 43
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE document_passport_rf_id_seq OWNER TO root;

--
-- Name: document_passport_rf_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE document_passport_rf_id_seq OWNED BY document_passport_rf.id;


--
-- Name: document_russian_names; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE document_russian_names (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    sex character varying(1)
);


ALTER TABLE document_russian_names OWNER TO root;

--
-- Name: document_russian_surnames; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE document_russian_surnames (
    id integer NOT NULL,
    surname character varying(100) NOT NULL,
    sex character varying(1)
);


ALTER TABLE document_russian_surnames OWNER TO root;

--
-- Name: document_type; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE document_type (
    id bigint NOT NULL,
    name text
);


ALTER TABLE document_type OWNER TO root;

--
-- Name: document_type_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE document_type_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE document_type_id_seq OWNER TO root;

--
-- Name: document_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE document_type_id_seq OWNED BY document_type.id;


--
-- Name: filial_equipment; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_equipment (
    id bigint NOT NULL,
    ip_adress text,
    mac_adress text,
    filial_id bigint,
    type_id bigint,
    room_id bigint,
    name text,
    active boolean DEFAULT true,
    debug boolean,
    compare_debug text,
    compareface_debug text,
    scan_debug text,
    series_number_debug text,
    passport_code_debug text,
    passport_date_debug text,
    passport_place_debug text,
    surname_debug text,
    first_name_debug text,
    patronymic_debug text,
    gender_debug text,
    date_birth_debug text,
    birthplace_debug text,
    id_person_debug text,
    registration_place_debug text,
    verify_debug boolean DEFAULT true
);


ALTER TABLE filial_equipment OWNER TO root;

--
-- Name: equipment_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE equipment_id_seq
    START WITH 38
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE equipment_id_seq OWNER TO root;

--
-- Name: equipment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE equipment_id_seq OWNED BY filial_equipment.id;


--
-- Name: equipment_types; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE equipment_types (
    id bigint NOT NULL,
    name text
);


ALTER TABLE equipment_types OWNER TO root;

--
-- Name: equpment_types_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE equpment_types_id_seq
    START WITH 8
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE equpment_types_id_seq OWNER TO root;

--
-- Name: equpment_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE equpment_types_id_seq OWNED BY equipment_types.id;


--
-- Name: fake_scans; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE fake_scans (
    id integer NOT NULL,
    id_person integer,
    surname text,
    first_name text,
    patronymic text,
    series_number text,
    date_birth date,
    gender integer,
    birthplace text,
    passport_date date,
    passport_code text,
    passport_place text,
    registration_place text,
    verify boolean DEFAULT true
);


ALTER TABLE fake_scans OWNER TO root;

--
-- Name: fake_scans_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE fake_scans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE fake_scans_id_seq OWNER TO root;

--
-- Name: fake_scans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE fake_scans_id_seq OWNED BY fake_scans.id;


--
-- Name: filial; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial (
    id bigint NOT NULL,
    name text,
    city text,
    street text
);


ALTER TABLE filial OWNER TO root;

--
-- Name: filial_camera; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_camera (
    id bigint NOT NULL,
    equipment_id bigint,
    stream_ff_pid integer,
    stream_http_pid integer,
    stream_js_pid integer,
    stream_ws_port integer,
    stream_http_port integer,
    username text,
    pass text,
    stream_url text,
    rtsp_port integer,
    face_min_width integer,
    face_min_height integer,
    ff_cam_id text,
    ff_person_id bigint,
    stream_ff_image_pid integer
);


ALTER TABLE filial_camera OWNER TO root;

--
-- Name: filial_camera_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_camera_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_camera_id_seq OWNER TO root;

--
-- Name: filial_camera_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_camera_id_seq OWNED BY filial_camera.id;


--
-- Name: filial_departament; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_departament (
    id bigint NOT NULL,
    name text,
    filial_id bigint,
    public boolean,
    parent_id bigint,
    "group" boolean,
    image text,
    info text,
    delete boolean DEFAULT false,
    date_create date,
    date_delete date
);


ALTER TABLE filial_departament OWNER TO root;

--
-- Name: TABLE filial_departament; Type: COMMENT; Schema: public; Owner: root
--

COMMENT ON TABLE filial_departament IS 'parent_id (integer) - родительский ID, используется для опеределения того, к какому департаменту принадлежит отдел;
public (boolean) - параметр для отображения департамента/отдела в терминале;
group (boolean) - параметр для определения того, что это отдел.
';


--
-- Name: filial_departament_floor; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_departament_floor (
    id bigint NOT NULL,
    departament_id bigint,
    floor_id bigint
);


ALTER TABLE filial_departament_floor OWNER TO root;

--
-- Name: filial_departament_floor_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_departament_floor_id_seq
    START WITH 56
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_departament_floor_id_seq OWNER TO root;

--
-- Name: filial_departament_floor_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_departament_floor_id_seq OWNED BY filial_departament_floor.id;


--
-- Name: filial_departament_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_departament_id_seq
    START WITH 99
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_departament_id_seq OWNER TO root;

--
-- Name: filial_departament_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_departament_id_seq OWNED BY filial_departament.id;


--
-- Name: filial_departament_rooms; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_departament_rooms (
    id bigint NOT NULL,
    departament_id bigint,
    room_id bigint,
    status boolean
);


ALTER TABLE filial_departament_rooms OWNER TO root;

--
-- Name: filial_departament_rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_departament_rooms_id_seq
    START WITH 19
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_departament_rooms_id_seq OWNER TO root;

--
-- Name: filial_departament_rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_departament_rooms_id_seq OWNED BY filial_departament_rooms.id;


--
-- Name: filial_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_id_seq OWNER TO root;

--
-- Name: filial_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_id_seq OWNED BY filial.id;


--
-- Name: filial_rooms; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_rooms (
    id bigint NOT NULL,
    name text,
    number text,
    worker_id bigint,
    filial_id bigint,
    parent_id bigint,
    room boolean,
    department_id bigint,
    step_in integer DEFAULT 0,
    step_out integer DEFAULT 0,
    delete boolean DEFAULT false,
    date_create date,
    date_delete date
);


ALTER TABLE filial_rooms OWNER TO root;

--
-- Name: TABLE filial_rooms; Type: COMMENT; Schema: public; Owner: root
--

COMMENT ON TABLE filial_rooms IS 'name - название категории/кабинета;
number - номер кабинета;
worker_id - ответственный/начальник кабинета;
parent_id - используется для определения категории помещения и категории подкатегории. (Кабинет №55 -> Этаж №2), (Этаж №2 -> Правое крыло);
room - параметр определния того, что это кабинет.';


--
-- Name: filial_rooms_hearing; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_rooms_hearing (
    id bigint NOT NULL,
    departament_id bigint,
    room_id bigint,
    worker_id bigint,
    name text,
    public boolean,
    pass_in_work_day time(6) without time zone,
    pass_out_work_day time(6) without time zone,
    dinner_start_work_day time(6) without time zone,
    dinner_end_work_day time(6) without time zone,
    pass_before_work_day integer,
    pass_after_work_day integer,
    stop_print_work_day integer,
    free_pass_work_day boolean,
    pass_in_short_day time(6) without time zone,
    pass_out_short_day time(6) without time zone,
    dinner_start_short_day time(6) without time zone,
    dinner_end_short_day time(6) without time zone,
    pass_before_short_day integer,
    pass_after_short_day integer,
    stop_print_short_day integer,
    free_pass_short_day boolean,
    monday_day_type integer,
    tuesday_day_type integer,
    wednesday_day_type integer,
    thursday_day_type integer,
    friday_day_type integer,
    saturday_day_type integer,
    sunday_day_type integer,
    system_settings boolean DEFAULT true
);


ALTER TABLE filial_rooms_hearing OWNER TO root;

--
-- Name: filial_rooms_hearing_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_rooms_hearing_id_seq
    START WITH 30
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_rooms_hearing_id_seq OWNER TO root;

--
-- Name: filial_rooms_hearing_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_rooms_hearing_id_seq OWNED BY filial_rooms_hearing.id;


--
-- Name: filial_terminal; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_terminal (
    id bigint NOT NULL,
    equipment_id bigint,
    camera_id bigint,
    webrtc_room text
);


ALTER TABLE filial_terminal OWNER TO root;

--
-- Name: filial_terminal_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_terminal_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_terminal_id_seq OWNER TO root;

--
-- Name: filial_terminal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_terminal_id_seq OWNED BY filial_terminal.id;


--
-- Name: filial_turnstiles; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE filial_turnstiles (
    id bigint NOT NULL,
    filial_id bigint,
    camera_in_id bigint,
    camera_out_id bigint,
    num integer,
    equipment_id bigint
);


ALTER TABLE filial_turnstiles OWNER TO root;

--
-- Name: filial_turnstiles_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE filial_turnstiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE filial_turnstiles_id_seq OWNER TO root;

--
-- Name: filial_turnstiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE filial_turnstiles_id_seq OWNED BY filial_turnstiles.id;


--
-- Name: hearing; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE hearing (
    id bigint NOT NULL,
    room_id bigint,
    name text,
    code text,
    hdate date,
    worker_id bigint,
    filial_id bigint,
    date date,
    "time" time(6) without time zone,
    departament_id integer,
    pass_in time(6) without time zone,
    pass_out time(6) without time zone,
    dinner_start time(6) without time zone,
    dinner_end time(6) without time zone,
    pause_from time(6) without time zone,
    pause_duration time(6) without time zone,
    pause_interval time(6) without time zone,
    freepass boolean,
    day_type integer,
    filial_rooms_hearing_id bigint,
    pass_before bigint,
    pass_after bigint,
    stop_print bigint
);


ALTER TABLE hearing OWNER TO root;

--
-- Name: hearing_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE hearing_id_seq
    START WITH 6527
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE hearing_id_seq OWNER TO root;

--
-- Name: hearing_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE hearing_id_seq OWNED BY hearing.id;


--
-- Name: hearing_rooms; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE hearing_rooms (
    id bigint NOT NULL,
    hearing_id bigint,
    room_id bigint,
    status boolean
);


ALTER TABLE hearing_rooms OWNER TO root;

--
-- Name: hearing_rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE hearing_rooms_id_seq
    START WITH 112
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE hearing_rooms_id_seq OWNER TO root;

--
-- Name: hearing_rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE hearing_rooms_id_seq OWNED BY hearing_rooms.id;


--
-- Name: interfaces; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE interfaces (
    id bigint NOT NULL,
    name text,
    url text,
    active_icon text,
    num integer,
    passive_icon text
);


ALTER TABLE interfaces OWNER TO root;

--
-- Name: interfaces_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE interfaces_id_seq
    START WITH 72
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE interfaces_id_seq OWNER TO root;

--
-- Name: interfaces_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE interfaces_id_seq OWNED BY interfaces.id;


--
-- Name: logs; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE logs (
    id bigint NOT NULL,
    message text,
    ldate date,
    ltime time(6) without time zone,
    user_id bigint,
    filial_id bigint,
    equipment_id bigint,
    room_id bigint,
    debug_info text
);


ALTER TABLE logs OWNER TO root;

--
-- Name: logs_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE logs_id_seq
    START WITH 9002
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE logs_id_seq OWNER TO root;

--
-- Name: logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE logs_id_seq OWNED BY logs.id;


--
-- Name: marks; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE marks (
    id bigint NOT NULL,
    name text,
    quite_alert boolean DEFAULT false
);


ALTER TABLE marks OWNER TO root;

--
-- Name: marks_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE marks_id_seq
    START WITH 19
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE marks_id_seq OWNER TO root;

--
-- Name: marks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE marks_id_seq OWNED BY marks.id;


--
-- Name: notification_types; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE notification_types (
    id integer NOT NULL,
    name text
);


ALTER TABLE notification_types OWNER TO root;

--
-- Name: notification_types_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE notification_types_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE notification_types_id_seq OWNER TO root;

--
-- Name: notification_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE notification_types_id_seq OWNED BY notification_types.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE permissions (
    id bigint NOT NULL,
    name text
);


ALTER TABLE permissions OWNER TO root;

--
-- Name: permissions_def_interfaces; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE permissions_def_interfaces (
    id bigint NOT NULL,
    permission_id bigint,
    interface_id bigint,
    status boolean
);


ALTER TABLE permissions_def_interfaces OWNER TO root;

--
-- Name: permissions_def_interfaces_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE permissions_def_interfaces_id_seq
    START WITH 33
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE permissions_def_interfaces_id_seq OWNER TO root;

--
-- Name: permissions_def_interfaces_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE permissions_def_interfaces_id_seq OWNED BY permissions_def_interfaces.id;


--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE permissions_id_seq OWNER TO root;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE permissions_id_seq OWNED BY permissions.id;


--
-- Name: permissions_to_interfaces; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE permissions_to_interfaces (
    id bigint NOT NULL,
    worker_id bigint,
    interface_id bigint,
    status boolean
);


ALTER TABLE permissions_to_interfaces OWNER TO root;

--
-- Name: permissions_to_interfaces_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE permissions_to_interfaces_id_seq
    START WITH 115
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE permissions_to_interfaces_id_seq OWNER TO root;

--
-- Name: permissions_to_interfaces_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE permissions_to_interfaces_id_seq OWNED BY permissions_to_interfaces.id;


--
-- Name: phinxlog; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE phinxlog (
    version bigint NOT NULL,
    migration_name character varying(100),
    start_time timestamp without time zone,
    end_time timestamp without time zone,
    breakpoint boolean DEFAULT false NOT NULL
);


ALTER TABLE phinxlog OWNER TO postgres;

--
-- Name: rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE rooms_id_seq
    START WITH 79
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE rooms_id_seq OWNER TO root;

--
-- Name: rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE rooms_id_seq OWNED BY filial_rooms.id;


--
-- Name: system_actions; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE system_actions (
    id bigint NOT NULL,
    active boolean,
    date_start date,
    time_start time(6) without time zone,
    filial_id bigint,
    type_id bigint,
    param text
);


ALTER TABLE system_actions OWNER TO root;

--
-- Name: system_actions_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE system_actions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system_actions_id_seq OWNER TO root;

--
-- Name: system_actions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE system_actions_id_seq OWNED BY system_actions.id;


--
-- Name: system_actions_type; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE system_actions_type (
    id bigint NOT NULL,
    name text
);


ALTER TABLE system_actions_type OWNER TO root;

--
-- Name: system_actions_type_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE system_actions_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system_actions_type_id_seq OWNER TO root;

--
-- Name: system_actions_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE system_actions_type_id_seq OWNED BY system_actions_type.id;


--
-- Name: turnstile_audio; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE turnstile_audio (
    id integer NOT NULL,
    text text
);


ALTER TABLE turnstile_audio OWNER TO root;

--
-- Name: turnstile_audio_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE turnstile_audio_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE turnstile_audio_id_seq OWNER TO root;

--
-- Name: turnstile_audio_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE turnstile_audio_id_seq OWNED BY turnstile_audio.id;


--
-- Name: user_acces_rooms; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_acces_rooms (
    id bigint NOT NULL,
    access_id bigint,
    room_id bigint,
    status boolean
);


ALTER TABLE user_acces_rooms OWNER TO root;

--
-- Name: user_acces_rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_acces_rooms_id_seq
    START WITH 45
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_acces_rooms_id_seq OWNER TO root;

--
-- Name: user_acces_rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_acces_rooms_id_seq OWNED BY user_acces_rooms.id;


--
-- Name: user_access; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_access (
    id bigint NOT NULL,
    user_id bigint,
    hearing_id bigint,
    code text,
    status integer,
    info text,
    creating_time bigint
);


ALTER TABLE user_access OWNER TO root;

--
-- Name: user_access_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_access_id_seq
    START WITH 122
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_access_id_seq OWNER TO root;

--
-- Name: user_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_access_id_seq OWNED BY user_access.id;


--
-- Name: user_documents; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_documents (
    id bigint NOT NULL,
    user_id bigint,
    type_id bigint,
    document_id bigint
);


ALTER TABLE user_documents OWNER TO root;

--
-- Name: user_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_documents_id_seq
    START WITH 43
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_documents_id_seq OWNER TO root;

--
-- Name: user_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_documents_id_seq OWNED BY user_documents.id;


--
-- Name: user_invitees; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_invitees (
    id integer NOT NULL,
    user_id integer,
    hearing_id integer,
    t_date date,
    t_time time(6) without time zone,
    active boolean DEFAULT true
);


ALTER TABLE user_invitees OWNER TO root;

--
-- Name: user_invitees_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_invitees_id_seq
    START WITH 31
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_invitees_id_seq OWNER TO root;

--
-- Name: user_invitees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_invitees_id_seq OWNED BY user_invitees.id;


--
-- Name: user_marks; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_marks (
    id bigint NOT NULL,
    mark_id bigint,
    user_id bigint,
    mdate date,
    mtime time(6) without time zone,
    worker_id bigint,
    status boolean,
    worker_id_close bigint,
    date_close date,
    time_close time(6) without time zone
);


ALTER TABLE user_marks OWNER TO root;

--
-- Name: user_marks_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_marks_id_seq
    START WITH 89
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_marks_id_seq OWNER TO root;

--
-- Name: user_marks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_marks_id_seq OWNED BY user_marks.id;


--
-- Name: user_pass; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_pass (
    id bigint NOT NULL,
    user_id bigint,
    access_id bigint,
    date_in date,
    time_in time(6) without time zone,
    date_out date,
    time_out time(6) without time zone,
    info text,
    mark_id bigint,
    metal_detector text,
    "x-ray" text
);


ALTER TABLE user_pass OWNER TO root;

--
-- Name: user_pass_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_pass_id_seq
    START WITH 81
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_pass_id_seq OWNER TO root;

--
-- Name: user_pass_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_pass_id_seq OWNED BY user_pass.id;


--
-- Name: users_search; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE users_search (
    id bigint NOT NULL,
    person_id bigint,
    worker_id bigint,
    filial_id bigint,
    status boolean DEFAULT false
);


ALTER TABLE users_search OWNER TO root;

--
-- Name: user_search_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_search_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_search_id_seq OWNER TO root;

--
-- Name: user_search_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_search_id_seq OWNED BY users_search.id;


--
-- Name: user_types; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE user_types (
    id bigint NOT NULL,
    name text,
    parent_id bigint,
    filial_id bigint,
    main_class bigint
);


ALTER TABLE user_types OWNER TO root;

--
-- Name: user_types_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE user_types_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_types_id_seq OWNER TO root;

--
-- Name: user_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE user_types_id_seq OWNED BY user_types.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE users (
    id bigint NOT NULL,
    email text,
    phone text,
    first_name text,
    patronymic text,
    surname text,
    birthday date,
    user_photo text,
    reg_date date,
    filial_id bigint,
    user_type_id bigint,
    passport text,
    ff_person_id bigint NOT NULL,
    work_place text,
    work_position text
);


ALTER TABLE users OWNER TO root;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE users_id_seq
    START WITH 141
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE users_id_seq OWNER TO root;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: workers; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers (
    id bigint NOT NULL,
    user_id bigint,
    permission_id bigint,
    login text,
    password text,
    code text,
    filial_id bigint,
    room_id bigint,
    department_id bigint,
    public boolean
);


ALTER TABLE workers OWNER TO root;

--
-- Name: workers_departamet_access; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_departamet_access (
    id bigint NOT NULL,
    worker_id bigint,
    departament_id bigint,
    status boolean
);


ALTER TABLE workers_departamet_access OWNER TO root;

--
-- Name: workers_departamet_access_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_departamet_access_id_seq
    START WITH 139
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_departamet_access_id_seq OWNER TO root;

--
-- Name: workers_departamet_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_departamet_access_id_seq OWNED BY workers_departamet_access.id;


--
-- Name: workers_dialogs; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_dialogs (
    id bigint NOT NULL,
    name text,
    worker_id bigint
);


ALTER TABLE workers_dialogs OWNER TO root;

--
-- Name: workers_dialog_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_dialog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_dialog_id_seq OWNER TO root;

--
-- Name: workers_dialog_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_dialog_id_seq OWNED BY workers_dialogs.id;


--
-- Name: workers_dialog_messages; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_dialog_messages (
    id bigint NOT NULL,
    message text,
    mdate date,
    mtime time(6) without time zone,
    dialog_id bigint,
    worker_id bigint
);


ALTER TABLE workers_dialog_messages OWNER TO root;

--
-- Name: workers_dialog_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_dialog_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_dialog_messages_id_seq OWNER TO root;

--
-- Name: workers_dialog_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_dialog_messages_id_seq OWNED BY workers_dialog_messages.id;


--
-- Name: workers_dialog_users; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_dialog_users (
    id bigint NOT NULL,
    dialog_id bigint,
    worker_id bigint
);


ALTER TABLE workers_dialog_users OWNER TO root;

--
-- Name: workers_dialog_users_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_dialog_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_dialog_users_id_seq OWNER TO root;

--
-- Name: workers_dialog_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_dialog_users_id_seq OWNED BY workers_dialog_users.id;


--
-- Name: workers_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_id_seq
    START WITH 60
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_id_seq OWNER TO root;

--
-- Name: workers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_id_seq OWNED BY workers.id;


--
-- Name: workers_notifications; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_notifications (
    id bigint NOT NULL,
    worker_id bigint,
    action_text text,
    adate date,
    atime time(6) without time zone,
    type integer DEFAULT 1,
    equipment_id integer,
    read boolean DEFAULT false,
    reply boolean DEFAULT false
);


ALTER TABLE workers_notifications OWNER TO root;

--
-- Name: workers_notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_notifications_id_seq
    START WITH 323
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_notifications_id_seq OWNER TO root;

--
-- Name: workers_notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_notifications_id_seq OWNED BY workers_notifications.id;


--
-- Name: workers_permissions_access; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_permissions_access (
    id bigint NOT NULL,
    worker_id bigint,
    room_id bigint,
    status boolean DEFAULT false,
    security_mode boolean,
    acces_from_time time(6) without time zone,
    acces_to_time time(6) without time zone
);


ALTER TABLE workers_permissions_access OWNER TO root;

--
-- Name: workers_permissions_access_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_permissions_access_id_seq
    START WITH 22
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_permissions_access_id_seq OWNER TO root;

--
-- Name: workers_permissions_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_permissions_access_id_seq OWNED BY workers_permissions_access.id;


--
-- Name: workers_videocam_access; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE workers_videocam_access (
    id bigint NOT NULL,
    worker_id bigint,
    equipment_id bigint
);


ALTER TABLE workers_videocam_access OWNER TO root;

--
-- Name: workers_videocam_access_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE workers_videocam_access_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE workers_videocam_access_id_seq OWNER TO root;

--
-- Name: workers_videocam_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE workers_videocam_access_id_seq OWNED BY workers_videocam_access.id;


--
-- Name: document_passport_rf id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY document_passport_rf ALTER COLUMN id SET DEFAULT nextval('document_passport_rf_id_seq'::regclass);


--
-- Name: document_type id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY document_type ALTER COLUMN id SET DEFAULT nextval('document_type_id_seq'::regclass);


--
-- Name: equipment_types id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY equipment_types ALTER COLUMN id SET DEFAULT nextval('equpment_types_id_seq'::regclass);


--
-- Name: fake_scans id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY fake_scans ALTER COLUMN id SET DEFAULT nextval('fake_scans_id_seq'::regclass);


--
-- Name: filial id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial ALTER COLUMN id SET DEFAULT nextval('filial_id_seq'::regclass);


--
-- Name: filial_camera id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_camera ALTER COLUMN id SET DEFAULT nextval('filial_camera_id_seq'::regclass);


--
-- Name: filial_departament id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament ALTER COLUMN id SET DEFAULT nextval('filial_departament_id_seq'::regclass);


--
-- Name: filial_departament_floor id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_floor ALTER COLUMN id SET DEFAULT nextval('filial_departament_floor_id_seq'::regclass);


--
-- Name: filial_departament_rooms id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_rooms ALTER COLUMN id SET DEFAULT nextval('filial_departament_rooms_id_seq'::regclass);


--
-- Name: filial_equipment id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_equipment ALTER COLUMN id SET DEFAULT nextval('equipment_id_seq'::regclass);


--
-- Name: filial_rooms id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms ALTER COLUMN id SET DEFAULT nextval('rooms_id_seq'::regclass);


--
-- Name: filial_rooms_hearing id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms_hearing ALTER COLUMN id SET DEFAULT nextval('filial_rooms_hearing_id_seq'::regclass);


--
-- Name: filial_terminal id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_terminal ALTER COLUMN id SET DEFAULT nextval('filial_terminal_id_seq'::regclass);


--
-- Name: filial_turnstiles id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_turnstiles ALTER COLUMN id SET DEFAULT nextval('filial_turnstiles_id_seq'::regclass);


--
-- Name: hearing id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing ALTER COLUMN id SET DEFAULT nextval('hearing_id_seq'::regclass);


--
-- Name: hearing_rooms id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing_rooms ALTER COLUMN id SET DEFAULT nextval('hearing_rooms_id_seq'::regclass);


--
-- Name: interfaces id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY interfaces ALTER COLUMN id SET DEFAULT nextval('interfaces_id_seq'::regclass);


--
-- Name: logs id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY logs ALTER COLUMN id SET DEFAULT nextval('logs_id_seq'::regclass);


--
-- Name: marks id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY marks ALTER COLUMN id SET DEFAULT nextval('marks_id_seq'::regclass);


--
-- Name: notification_types id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY notification_types ALTER COLUMN id SET DEFAULT nextval('notification_types_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions ALTER COLUMN id SET DEFAULT nextval('permissions_id_seq'::regclass);


--
-- Name: permissions_def_interfaces id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_def_interfaces ALTER COLUMN id SET DEFAULT nextval('permissions_def_interfaces_id_seq'::regclass);


--
-- Name: permissions_to_interfaces id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_to_interfaces ALTER COLUMN id SET DEFAULT nextval('permissions_to_interfaces_id_seq'::regclass);


--
-- Name: system_actions id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY system_actions ALTER COLUMN id SET DEFAULT nextval('system_actions_id_seq'::regclass);


--
-- Name: system_actions_type id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY system_actions_type ALTER COLUMN id SET DEFAULT nextval('system_actions_type_id_seq'::regclass);


--
-- Name: turnstile_audio id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY turnstile_audio ALTER COLUMN id SET DEFAULT nextval('turnstile_audio_id_seq'::regclass);


--
-- Name: user_acces_rooms id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_acces_rooms ALTER COLUMN id SET DEFAULT nextval('user_acces_rooms_id_seq'::regclass);


--
-- Name: user_access id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_access ALTER COLUMN id SET DEFAULT nextval('user_access_id_seq'::regclass);


--
-- Name: user_documents id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_documents ALTER COLUMN id SET DEFAULT nextval('user_documents_id_seq'::regclass);


--
-- Name: user_invitees id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_invitees ALTER COLUMN id SET DEFAULT nextval('user_invitees_id_seq'::regclass);


--
-- Name: user_marks id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_marks ALTER COLUMN id SET DEFAULT nextval('user_marks_id_seq'::regclass);


--
-- Name: user_pass id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_pass ALTER COLUMN id SET DEFAULT nextval('user_pass_id_seq'::regclass);


--
-- Name: user_types id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_types ALTER COLUMN id SET DEFAULT nextval('user_types_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: users_search id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY users_search ALTER COLUMN id SET DEFAULT nextval('user_search_id_seq'::regclass);


--
-- Name: workers id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers ALTER COLUMN id SET DEFAULT nextval('workers_id_seq'::regclass);


--
-- Name: workers_departamet_access id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_departamet_access ALTER COLUMN id SET DEFAULT nextval('workers_departamet_access_id_seq'::regclass);


--
-- Name: workers_dialog_messages id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialog_messages ALTER COLUMN id SET DEFAULT nextval('workers_dialog_messages_id_seq'::regclass);


--
-- Name: workers_dialog_users id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialog_users ALTER COLUMN id SET DEFAULT nextval('workers_dialog_users_id_seq'::regclass);


--
-- Name: workers_dialogs id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialogs ALTER COLUMN id SET DEFAULT nextval('workers_dialog_id_seq'::regclass);


--
-- Name: workers_notifications id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_notifications ALTER COLUMN id SET DEFAULT nextval('workers_notifications_id_seq'::regclass);


--
-- Name: workers_permissions_access id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_permissions_access ALTER COLUMN id SET DEFAULT nextval('workers_permissions_access_id_seq'::regclass);


--
-- Name: workers_videocam_access id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_videocam_access ALTER COLUMN id SET DEFAULT nextval('workers_videocam_access_id_seq'::regclass);


--
-- Name: document_passport_rf document_passport_rf_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY document_passport_rf
    ADD CONSTRAINT document_passport_rf_pkey PRIMARY KEY (id);


--
-- Name: document_type document_type_name_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY document_type
    ADD CONSTRAINT document_type_name_key UNIQUE (name);


--
-- Name: document_type document_type_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY document_type
    ADD CONSTRAINT document_type_pkey PRIMARY KEY (id);


--
-- Name: equipment_types equipment_types_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY equipment_types
    ADD CONSTRAINT equipment_types_pkey PRIMARY KEY (id);


--
-- Name: fake_scans fake_scans_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY fake_scans
    ADD CONSTRAINT fake_scans_pkey PRIMARY KEY (id);


--
-- Name: filial_camera filial_camera_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_camera
    ADD CONSTRAINT filial_camera_pkey PRIMARY KEY (id);


--
-- Name: filial_departament_floor filial_departament_floor_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_floor
    ADD CONSTRAINT filial_departament_floor_pkey PRIMARY KEY (id);


--
-- Name: filial_departament filial_departament_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament
    ADD CONSTRAINT filial_departament_pkey PRIMARY KEY (id);


--
-- Name: filial_departament_rooms filial_departament_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_rooms
    ADD CONSTRAINT filial_departament_rooms_pkey PRIMARY KEY (id);


--
-- Name: filial_equipment filial_equipment_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_equipment
    ADD CONSTRAINT filial_equipment_pkey PRIMARY KEY (id);


--
-- Name: filial filial_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial
    ADD CONSTRAINT filial_pkey PRIMARY KEY (id);


--
-- Name: filial_rooms_hearing filial_rooms_hearing_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms_hearing
    ADD CONSTRAINT filial_rooms_hearing_pkey PRIMARY KEY (id);


--
-- Name: filial_rooms filial_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms
    ADD CONSTRAINT filial_rooms_pkey PRIMARY KEY (id);


--
-- Name: filial_terminal filial_terminal_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_terminal
    ADD CONSTRAINT filial_terminal_pkey PRIMARY KEY (id);


--
-- Name: filial_turnstiles filial_turnstiles_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_turnstiles
    ADD CONSTRAINT filial_turnstiles_pkey PRIMARY KEY (id);


--
-- Name: hearing hearing_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing
    ADD CONSTRAINT hearing_pkey PRIMARY KEY (id);


--
-- Name: hearing_rooms hearing_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing_rooms
    ADD CONSTRAINT hearing_rooms_pkey PRIMARY KEY (id);


--
-- Name: interfaces interfaces_active_icon_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY interfaces
    ADD CONSTRAINT interfaces_active_icon_key UNIQUE (active_icon);


--
-- Name: interfaces interfaces_num_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY interfaces
    ADD CONSTRAINT interfaces_num_key UNIQUE (num);


--
-- Name: interfaces interfaces_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY interfaces
    ADD CONSTRAINT interfaces_pkey PRIMARY KEY (id);


--
-- Name: interfaces interfaces_url_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY interfaces
    ADD CONSTRAINT interfaces_url_key UNIQUE (url);


--
-- Name: logs logs_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_pkey PRIMARY KEY (id);


--
-- Name: marks marks_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY marks
    ADD CONSTRAINT marks_pkey PRIMARY KEY (id);


--
-- Name: notification_types notification_types_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY notification_types
    ADD CONSTRAINT notification_types_pkey PRIMARY KEY (id);


--
-- Name: permissions_def_interfaces permissions_def_interfaces_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_def_interfaces
    ADD CONSTRAINT permissions_def_interfaces_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_name_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissions_name_key UNIQUE (name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: permissions_to_interfaces permissions_to_interfaces_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_to_interfaces
    ADD CONSTRAINT permissions_to_interfaces_pkey PRIMARY KEY (id);


--
-- Name: phinxlog phinxlog_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY phinxlog
    ADD CONSTRAINT phinxlog_pkey PRIMARY KEY (version);


--
-- Name: system_actions system_actions_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY system_actions
    ADD CONSTRAINT system_actions_pkey PRIMARY KEY (id);


--
-- Name: system_actions_type system_actions_type_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY system_actions_type
    ADD CONSTRAINT system_actions_type_pkey PRIMARY KEY (id);


--
-- Name: user_acces_rooms user_acces_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_acces_rooms
    ADD CONSTRAINT user_acces_rooms_pkey PRIMARY KEY (id);


--
-- Name: user_access user_access_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_access
    ADD CONSTRAINT user_access_pkey PRIMARY KEY (id);


--
-- Name: user_documents user_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_documents
    ADD CONSTRAINT user_documents_pkey PRIMARY KEY (id);


--
-- Name: user_marks user_marks_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_marks
    ADD CONSTRAINT user_marks_pkey PRIMARY KEY (id);


--
-- Name: user_pass user_pass_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_pass
    ADD CONSTRAINT user_pass_pkey PRIMARY KEY (id);


--
-- Name: user_types user_types_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_types
    ADD CONSTRAINT user_types_pkey PRIMARY KEY (id);


--
-- Name: users users_ff_person_id_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_ff_person_id_key UNIQUE (ff_person_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users_search users_search_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users_search
    ADD CONSTRAINT users_search_pkey PRIMARY KEY (id);


--
-- Name: workers_departamet_access workers_departamet_access_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_departamet_access
    ADD CONSTRAINT workers_departamet_access_pkey PRIMARY KEY (id);


--
-- Name: workers_dialog_messages workers_dialog_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialog_messages
    ADD CONSTRAINT workers_dialog_messages_pkey PRIMARY KEY (id);


--
-- Name: workers_dialog_users workers_dialog_users_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialog_users
    ADD CONSTRAINT workers_dialog_users_pkey PRIMARY KEY (id);


--
-- Name: workers_dialogs workers_dialogs_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialogs
    ADD CONSTRAINT workers_dialogs_pkey PRIMARY KEY (id);


--
-- Name: workers_notifications workers_notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_notifications
    ADD CONSTRAINT workers_notifications_pkey PRIMARY KEY (id);


--
-- Name: workers_permissions_access workers_permissions_access_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_permissions_access
    ADD CONSTRAINT workers_permissions_access_pkey PRIMARY KEY (id);


--
-- Name: workers workers_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers
    ADD CONSTRAINT workers_pkey PRIMARY KEY (id);


--
-- Name: workers_videocam_access workers_videocam_access_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_videocam_access
    ADD CONSTRAINT workers_videocam_access_pkey PRIMARY KEY (id);


--
-- Name: fake_scans_id_person_uindex; Type: INDEX; Schema: public; Owner: root
--

CREATE UNIQUE INDEX fake_scans_id_person_uindex ON fake_scans USING btree (id_person);


--
-- Name: filial_camera filial_camera_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_camera
    ADD CONSTRAINT filial_camera_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id);


--
-- Name: filial_departament filial_departament_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament
    ADD CONSTRAINT filial_departament_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_departament_floor filial_departament_floor_departament_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_floor
    ADD CONSTRAINT filial_departament_floor_departament_id_fkey FOREIGN KEY (departament_id) REFERENCES filial_departament(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_departament_floor filial_departament_floor_floor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_floor
    ADD CONSTRAINT filial_departament_floor_floor_id_fkey FOREIGN KEY (floor_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_departament_rooms filial_departament_rooms_departament_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_rooms
    ADD CONSTRAINT filial_departament_rooms_departament_id_fkey FOREIGN KEY (departament_id) REFERENCES filial_departament(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_departament_rooms filial_departament_rooms_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_departament_rooms
    ADD CONSTRAINT filial_departament_rooms_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_equipment filial_equipment_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_equipment
    ADD CONSTRAINT filial_equipment_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_equipment filial_equipment_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_equipment
    ADD CONSTRAINT filial_equipment_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_equipment filial_equipment_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_equipment
    ADD CONSTRAINT filial_equipment_type_id_fkey FOREIGN KEY (type_id) REFERENCES equipment_types(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_rooms filial_rooms_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms
    ADD CONSTRAINT filial_rooms_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_rooms_hearing filial_rooms_hearing_departament_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms_hearing
    ADD CONSTRAINT filial_rooms_hearing_departament_id_fkey FOREIGN KEY (departament_id) REFERENCES filial_departament(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_rooms_hearing filial_rooms_hearing_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms_hearing
    ADD CONSTRAINT filial_rooms_hearing_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_rooms_hearing filial_rooms_hearing_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_rooms_hearing
    ADD CONSTRAINT filial_rooms_hearing_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_terminal filial_terminal_camera_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_terminal
    ADD CONSTRAINT filial_terminal_camera_id_fkey FOREIGN KEY (camera_id) REFERENCES filial_equipment(id);


--
-- Name: filial_terminal filial_terminal_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_terminal
    ADD CONSTRAINT filial_terminal_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id);


--
-- Name: filial_turnstiles filial_turnstiles_camera_in_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_turnstiles
    ADD CONSTRAINT filial_turnstiles_camera_in_id_fkey FOREIGN KEY (camera_in_id) REFERENCES filial_equipment(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_turnstiles filial_turnstiles_camera_out_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_turnstiles
    ADD CONSTRAINT filial_turnstiles_camera_out_id_fkey FOREIGN KEY (camera_out_id) REFERENCES filial_equipment(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_turnstiles filial_turnstiles_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_turnstiles
    ADD CONSTRAINT filial_turnstiles_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: filial_turnstiles filial_turnstiles_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY filial_turnstiles
    ADD CONSTRAINT filial_turnstiles_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hearing hearing_departament_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing
    ADD CONSTRAINT hearing_departament_id_fkey FOREIGN KEY (departament_id) REFERENCES filial_departament(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hearing hearing_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing
    ADD CONSTRAINT hearing_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hearing_rooms hearing_rooms_hearing_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing_rooms
    ADD CONSTRAINT hearing_rooms_hearing_id_fkey FOREIGN KEY (hearing_id) REFERENCES hearing(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hearing_rooms hearing_rooms_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing_rooms
    ADD CONSTRAINT hearing_rooms_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hearing hearing_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY hearing
    ADD CONSTRAINT hearing_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: logs logs_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: logs logs_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: logs logs_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: logs logs_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: permissions_def_interfaces permissions_def_interfaces_interface_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_def_interfaces
    ADD CONSTRAINT permissions_def_interfaces_interface_id_fkey FOREIGN KEY (interface_id) REFERENCES interfaces(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: permissions_def_interfaces permissions_def_interfaces_permission_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_def_interfaces
    ADD CONSTRAINT permissions_def_interfaces_permission_id_fkey FOREIGN KEY (permission_id) REFERENCES permissions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: permissions_to_interfaces permissions_to_interfaces_interface_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_to_interfaces
    ADD CONSTRAINT permissions_to_interfaces_interface_id_fkey FOREIGN KEY (interface_id) REFERENCES interfaces(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: permissions_to_interfaces permissions_to_interfaces_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY permissions_to_interfaces
    ADD CONSTRAINT permissions_to_interfaces_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: system_actions system_actions_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY system_actions
    ADD CONSTRAINT system_actions_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: system_actions system_actions_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY system_actions
    ADD CONSTRAINT system_actions_type_id_fkey FOREIGN KEY (type_id) REFERENCES system_actions_type(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_acces_rooms user_acces_rooms_access_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_acces_rooms
    ADD CONSTRAINT user_acces_rooms_access_id_fkey FOREIGN KEY (access_id) REFERENCES user_access(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_acces_rooms user_acces_rooms_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_acces_rooms
    ADD CONSTRAINT user_acces_rooms_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_access user_access_hearing_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_access
    ADD CONSTRAINT user_access_hearing_id_fkey FOREIGN KEY (hearing_id) REFERENCES hearing(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_access user_access_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_access
    ADD CONSTRAINT user_access_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_documents user_documents_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_documents
    ADD CONSTRAINT user_documents_type_id_fkey FOREIGN KEY (type_id) REFERENCES document_type(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_documents user_documents_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_documents
    ADD CONSTRAINT user_documents_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_invitees user_invitees_hearing_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_invitees
    ADD CONSTRAINT user_invitees_hearing_id_fkey FOREIGN KEY (hearing_id) REFERENCES hearing(id);


--
-- Name: user_invitees user_invitees_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_invitees
    ADD CONSTRAINT user_invitees_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: user_marks user_marks_mark_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_marks
    ADD CONSTRAINT user_marks_mark_id_fkey FOREIGN KEY (mark_id) REFERENCES marks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_marks user_marks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_marks
    ADD CONSTRAINT user_marks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_marks user_marks_worker_id_close_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_marks
    ADD CONSTRAINT user_marks_worker_id_close_fkey FOREIGN KEY (worker_id_close) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_marks user_marks_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_marks
    ADD CONSTRAINT user_marks_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_pass user_pass_access_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_pass
    ADD CONSTRAINT user_pass_access_id_fkey FOREIGN KEY (access_id) REFERENCES user_access(id);


--
-- Name: user_pass user_pass_mark_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_pass
    ADD CONSTRAINT user_pass_mark_id_fkey FOREIGN KEY (mark_id) REFERENCES marks(id);


--
-- Name: user_pass user_pass_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY user_pass
    ADD CONSTRAINT user_pass_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: users users_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_search users_search_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users_search
    ADD CONSTRAINT users_search_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_search users_search_person_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users_search
    ADD CONSTRAINT users_search_person_id_fkey FOREIGN KEY (person_id) REFERENCES users(ff_person_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_search users_search_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users_search
    ADD CONSTRAINT users_search_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users users_user_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_user_type_id_fkey FOREIGN KEY (user_type_id) REFERENCES user_types(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_departamet_access workers_departamet_access_departament_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_departamet_access
    ADD CONSTRAINT workers_departamet_access_departament_id_fkey FOREIGN KEY (departament_id) REFERENCES filial_departament(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_departamet_access workers_departamet_access_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_departamet_access
    ADD CONSTRAINT workers_departamet_access_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_dialog_users workers_dialog_users_dialog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialog_users
    ADD CONSTRAINT workers_dialog_users_dialog_id_fkey FOREIGN KEY (dialog_id) REFERENCES workers_dialogs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_dialogs workers_dialogs_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_dialogs
    ADD CONSTRAINT workers_dialogs_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers workers_filial_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers
    ADD CONSTRAINT workers_filial_id_fkey FOREIGN KEY (filial_id) REFERENCES filial(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_notifications workers_notifications_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_notifications
    ADD CONSTRAINT workers_notifications_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id);


--
-- Name: workers_notifications workers_notifications_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_notifications
    ADD CONSTRAINT workers_notifications_type_fkey FOREIGN KEY (type) REFERENCES notification_types(id);


--
-- Name: workers_notifications workers_notifications_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_notifications
    ADD CONSTRAINT workers_notifications_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers workers_permission_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers
    ADD CONSTRAINT workers_permission_id_fkey FOREIGN KEY (permission_id) REFERENCES permissions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_permissions_access workers_permissions_access_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_permissions_access
    ADD CONSTRAINT workers_permissions_access_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_permissions_access workers_permissions_access_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_permissions_access
    ADD CONSTRAINT workers_permissions_access_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers workers_room_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers
    ADD CONSTRAINT workers_room_id_fkey FOREIGN KEY (room_id) REFERENCES filial_rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers workers_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers
    ADD CONSTRAINT workers_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_videocam_access workers_videocam_access_equipment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_videocam_access
    ADD CONSTRAINT workers_videocam_access_equipment_id_fkey FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: workers_videocam_access workers_videocam_access_worker_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY workers_videocam_access
    ADD CONSTRAINT workers_videocam_access_worker_id_fkey FOREIGN KEY (worker_id) REFERENCES workers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

