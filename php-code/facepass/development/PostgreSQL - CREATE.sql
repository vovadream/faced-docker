/*
Дата: 15.06.2017

Нужно создать: 
+ Таблицу филиалов (filial)

+ Таблицу пользователей (users)
+ (связь пользователей с филиалом)

+ Таблицу работников (workers)
+ Таблицу прав доступа работника (permissions)
+ (связь работника с пользователем)
+ (связь работника с правами доступа)

+ Таблицу документов пользователя (user_documents)
+ Таблицу типов документов пользователя (user_document_type)
+ (связь документов пользователя с пользователем)
+ (связь документов пользователя с типом)

+ Таблица типов пользователей (users_type)
+ (В таблицу пользователей добавить поле тип пользователя) (users.user_type_id)
+ (Связь пользователь - тип пользователя)

+ Таблица диалогов (workers_dialogs)
+ (Связь диалогов с сотрудником)

+ Таблица участников диалога (workers_dialog_users)
+ (Связь участников с сотрудником)

+ Таблица сообщений (workers_dialog_messages)
+ (Связь сообщений с диалогом)
+ (Связь сообщений с сотрудником)

+ Таблица оповещений работника (workers_notifications)
+ (связь оповещения с работником)

+ Таблица интерфейсов (interfaces)
+ Таблица доступа работника к интерфейсам permissions_to_interfaces)
+ (связь доступа работника к интерфейсам с интерфейсом)
+ (связь доступа работника к интерфейсам с работником)

+ Таблица стандартных доступов к интерфейсам (permissions_def_interfaces)
+ (Связь стандартного доступа с интерфесами)
+ (Связь стандартного доступа с правами доступа)

+ Таблица оборудования (equipment)
+ Таблица типов оборудования (equipment_type)
+ Таблица помещений филиала (rooms)
+ (Связь помещения с филиалом)
+ (Связь помещения с ответственным сотрудником)
+ (Связь оборудования с типом оборудования)
+ (Связь оборудования с филиалом)
+ (Связь оборудования с помещением)

+ Таблица доступа сотрудника к камерам филиала (workers_videocam_access)
+ (Связь доступа сотрудника к камерам с оборудованием)
+ (Связь доступа сотрудника к камерам с сотрудником)

+ Таблица доступа сотрудника на территорию филиала (permissions_access)
+ (Связь доступа на территорию филиала с пользователем)
+ (Связь доступа на территорию филиала с помещением)

+ Создание таблицы каетегории доступа (permissions_access_category)
+ Создание таблицы доступа к помещениям по категории (permissions_access_category_rooms)
+ (Связь категории доступа с филиалом)
+ (Связь доступа к помещениям по категории с категорией)
+ (Связь доступа к помещениям по категории с помещением)

+ Таблица слушаний (hearing)
+ Таблица доступных помещений для слушаний (hearing_rooms)
+ (Связь слушания с филиалом)
+ (Связь слушания с сотрудником)
+ (Связь доуступных помещений для слушания со слушанием)
+ (Связь доуступных помещений для слушания с помещениями)

+ Таблица меток пользователей
+ Таблица меток
+ (Связь меток пользователей с меткой)
+ (Связь меток пользователей с пользователем)
+ (Связь меток пользователей с сотрудником)

+ Таблица логирования действий
+ (Связь логирования с пользователем)
+ (Связь логирования с филиалом)
+ (Связь логирования с оборудованием)
+ (Связь логирования с помещением)

+ Таблица задач системы
+ Таблица типов отслеживаемых действий
+ (Связь задач с филиалом)
+ (Связь задач с типом действия)

В таблицу метод добавить дату статус, закрытия метки, время закрытия, кто закрыл.
(связь кто закрыл с сотрудником)
*/


-- Таблица филиалов
CREATE TABLE filial (
	id bigserial PRIMARY KEY,
	name text,
	city text,
	street text
);

-- Таблица пользователей
CREATE TABLE users (
	id bigserial PRIMARY KEY,
	email text,
	phone text,
	first_name text,
	last_name text,
	surname text,
	birthday date,
	user_photo text,
	reg_date date,
	filial_id bigint,
	video_identify_id integer
);

-- Создание связи пользователь - филиал
ALTER TABLE users ADD CONSTRAINT "users_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- Создание таблицы работников
CREATE TABLE workers (
	id bigserial PRIMARY KEY,
	user_id bigint,
	permission_id bigint,
	login text,
	password text,
	code text
);

-- Создание таблицы прав доступа
CREATE TABLE permissions (
	id bigserial PRIMARY KEY,
	name text UNIQUE
);

-- Связь работники - пользователи
ALTER TABLE workers ADD CONSTRAINT "workers_user_id" FOREIGN KEY (user_id) REFERENCES users(id);

-- Связь работники - права доступа
ALTER TABLE workers ADD CONSTRAINT "workers_permissions_id" FOREIGN KEY (permission_id) REFERENCES permissions(id);

-- Создание таблицы документов пользователя
CREATE TABLE user_documents (
	id bigserial PRIMARY KEY,
	user_id bigint,
	type_id bigint,
	info text
);

-- Создание таблицы типов документов
CREATE TABLE document_type (
	id bigserial PRIMARY KEY,
	name text UNIQUE
);

-- Связь документы - пользователи
ALTER TABLE user_documents ADD CONSTRAINT "user_documetns_user_id" FOREIGN KEY (user_id) REFERENCES users(id);

-- Связь документы - тип документа
ALTER TABLE user_documents ADD CONSTRAINT "user_documetns_document_type" FOREIGN KEY (type_id) REFERENCES document_type(id);


-- Таблица типов пользователей
CREATE TABLE user_types (
	id bigserial PRIMARY KEY,
	name text
);

-- Добавление в таблицу пользователей столбца с типом пользователей
ALTER TABLE users ADD COLUMN user_type_id bigint;

-- Связь пользователей с типом пользователя
ALTER TABLE users ADD CONSTRAINT "user_type_id_to_user_type" FOREIGN KEY (user_type_id) REFERENCES user_types(id);

-- Таблица диалогов сотрудников
CREATE TABLE workers_dialogs (                                                                                     
	id bigserial PRIMARY KEY,
	name text,
	worker_id bigint
);

-- Связь диалога с сотрудником-создателем
ALTER TABLE workers_dialog ADD CONSTRAINT "workers_dialog_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);  

-- Таблица участники диалога
CREATE TABLE workers_dialog_users (                                                                                        
	id bigserial PRIMARY KEY,
	dialog_id bigint,
	worker_id bigint
);

-- Связь участников с диалогом
ALTER TABLE workers_dialog_users ADD CONSTRAINT "workers_dialog_users_dialog_id" FOREIGN KEY (dialog_id) REFERENCES workers_dialogs(id);

-- Таблица сообщений диалогов
CREATE TABLE workers_dialog_messages (
	id bigserial PRIMARY KEY,
	message text,
	mdate date,
	mtime time,
	dialog_id bigint,
	worker_id bigint
);

-- Таблица оповещений работника (workers_notifications)
CREATE TABLE workers_notifications (
	id bigserial PRIMARY KEY,
	worker_id bigint,
	action_text text,
	adate date,
	atime time
);

-- (связь оповещения с работником)
ALTER TABLE workers_notifications ADD CONSTRAINT "workers_notifications_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- Таблица интерфейсов (interfaces)
CREATE TABLE interfaces (
	id bigserial PRIMARY KEY,
	name text
);

-- Таблица доступа работника к интерфейсам permissions_to_interfaces)
CREATE TABLE permissions_to_interfaces (
	id bigserial PRIMARY KEY,
	worker_id bigint,
	interface_id bigint,
	status boolean
);

-- (связь доступа работника к интерфейсам с интерфейсом)
ALTER TABLE permissions_to_interfaces ADD CONSTRAINT "permissions_to_interfaces_interface_id" FOREIGN KEY (interface_id) REFERENCES interfaces(id);

-- (связь доступа работника к интерфейсам с работником)
ALTER TABLE permissions_to_interfaces ADD CONSTRAINT "permissions_to_interfaces_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- Таблица стандартных доступов к интерфейсам (permissions_def_interfaces)
CREATE TABLE permissions_def_interfaces (
	id bigserial PRIMARY KEY,
	permission_id bigint,
	interface_id bigint,
	status boolean
);

-- (Связь стандартного доступа с интерфесами)
ALTER TABLE permissions_def_interfaces ADD CONSTRAINT "permissions_def_interfaces_interface_id" FOREIGN KEY (interface_id) REFERENCES interfaces(id);

-- (Связь стандартного доступа с правами доступа)
ALTER TABLE permissions_def_interfaces ADD CONSTRAINT "permissions_def_interfaces_permission_id" FOREIGN KEY (permission_id) REFERENCES permissions(id);

-- Таблица оборудования (equipment)
CREATE TABLE equipment (
	id bigserial PRIMARY KEY,
	ip_adress text,
	mac_adress text,
	filial_id text,
	type_id bigint,
	room_id bigint,
	name text
);

-- Таблица типов оборудования (equipment_type)
CREATE TABLE equipment_types (
	id bigserial PRIMARY KEY,
	name text
);

-- Таблица помещений филиала (rooms)
CREATE TABLE rooms (
	id bigserial PRIMARY KEY,
	name text,
	floor integer,
	number text,
	work_time integer,
	worker_id bigint,
	filial_id bigint
);

-- (Связь помещения с филиалом)
ALTER TABLE rooms ADD CONSTRAINT "rooms_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- (Связь помещения с ответственным сотрудником)
ALTER TABLE rooms ADD CONSTRAINT "rooms_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- (Связь оборудования с типом оборудования)
ALTER TABLE equipment ADD CONSTRAINT "equipment_type_id" FOREIGN KEY (type_id) REFERENCES equipment_types(id);

-- (Связь оборудования с филиалом)
ALTER TABLE equipment ADD CONSTRAINT "equipment_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- (Связь оборудования с помещением)
ALTER TABLE equipment ADD CONSTRAINT "equipment_room_id" FOREIGN KEY (room_id) REFERENCES rooms(id);

-- Таблица доступа сотрудника к камерам филиала (workers_videocam_access)
CREATE TABLE workers_videocam_access (
	id bigserial PRIMARY KEY,
	worker_id bigint,
	equipment_id bigint
);

-- (Связь доступа сотрудника к камерам с оборудованием)
ALTER TABLE workers_videocam_access ADD CONSTRAINT "workers_videocam_access_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- (Связь доступа сотрудника к камерам с сотрудником)
ALTER TABLE workers_videocam_access ADD CONSTRAINT "workers_videocam_access_equipment_id" FOREIGN KEY (equipment_id) REFERENCES equipment(id);

-- Таблица доступа сотрудника на территорию филиала (permissions_access)
CREATE TABLE workers_permissions_access (
	id bigserial PRIMARY KEY,
	worker_id bigint,
	room_id bigint,
	access_from_time time,
	access_to_time time
);

-- (Связь доступа на территорию филиала с пользователем)
ALTER TABLE workers_permissions_access ADD CONSTRAINT "workers_permissions_access_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- (Связь доступа на территорию филиала с помещением)
ALTER TABLE workers_permissions_access ADD CONSTRAINT "workers_permissions_access_room_id" FOREIGN KEY (room_id) REFERENCES rooms(id);

-- Создание таблицы категории доступа (workers_permissions_access_category)
CREATE TABLE workers_permissions_access_category (
	id bigserial PRIMARY KEY,
	filial_id bigint,
	name text,
	system boolean
);

-- Создание таблицы доступа к помещениям по категории (workers_permissions_access_category_rooms)
CREATE TABLE workers_permissions_access_category_rooms (
	id bigserial PRIMARY KEY,
	category_id bigint,
	room_id bigint,
	access_from_time time,
	access_to_time time
);

-- (Связь категории доступа с филиалом)
ALTER TABLE workers_permissions_access_category ADD CONSTRAINT "workers_permissions_access_category_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- (Связь доступа к помещениям по категории с категорией)
ALTER TABLE workers_permissions_access_category_rooms ADD CONSTRAINT "workers_permissions_access_category_rooms_category_id" FOREIGN KEY (category_id) REFERENCES workers_permissions_access_category(id);

-- (Связь доступа к помещениям по категории с помещением)
ALTER TABLE workers_permissions_access_category_rooms ADD CONSTRAINT "workers_permissions_access_category_rooms_room_id" FOREIGN KEY (room_id) REFERENCES rooms(id);

-- Таблица слушаний (hearing)
CREATE TABLE hearing (
	id bigserial PRIMARY KEY,
	room_id bigint,
	name text,
	code text,
	hdate date,
	worker_id bigint,
	filial_id bigint
);

-- Таблица доступных помещений для слушаний (hearing_rooms)
CREATE TABLE hearing_rooms (
	id bigserial PRIMARY KEY,
	hearing_id bigint,
	room_id bigint
);

-- (Связь слушания с филиалом)
ALTER TABLE hearing ADD CONSTRAINT "hearing_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- (Связь слушания с сотрудником)
ALTER TABLE hearing ADD CONSTRAINT "hearing_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- (Связь доуступных помещений для слушания со слушанием)
ALTER TABLE hearing_rooms ADD CONSTRAINT "hearing_rooms_hearing_id" FOREIGN KEY (hearing_id) REFERENCES hearing(id);

-- (Связь доуступных помещений для слушания с помещениями)
ALTER TABLE hearing_rooms ADD CONSTRAINT "hearing_rooms_room_id" FOREIGN KEY (room_id) REFERENCES rooms(id);

-- Таблица меток пользователей (user_marks)
CREATE TABLE user_marks (
	id bigserial PRIMARY KEY,
	mark_id bigint,
	user_id bigint,
	mdate date,
	mtime time,
	worker_id bigint
);

-- Таблица меток (marks)
CREATE TABLE marks (
	id bigserial PRIMARY KEY,
	name text
);

-- (Связь меток пользователей с меткой)
ALTER TABLE user_marks ADD CONSTRAINT "user_marks_mark_id" FOREIGN KEY (mark_id) REFERENCES marks(id);

-- (Связь меток пользователей с пользователем)
ALTER TABLE user_marks ADD CONSTRAINT "user_marks_user_id" FOREIGN KEY (user_id) REFERENCES users(id);

-- (Связь меток пользователей с сотрудником)
ALTER TABLE user_marks ADD CONSTRAINT "user_marks_worker_id" FOREIGN KEY (worker_id) REFERENCES workers(id);

-- Таблица логирования действий
CREATE TABLE logs (
	id bigserial PRIMARY KEY,
	message text,
	ldate date,
	ltime time,
	user_id bigint,
	filial_id bigint,
	equipment_id bigint,
	room_id bigint
);

-- (Связь логирования с пользователем)
ALTER TABLE logs ADD CONSTRAINT "logs_user_id" FOREIGN KEY (user_id) REFERENCES users(id);

-- (Связь логирования с филиалом)
ALTER TABLE logs ADD CONSTRAINT "logs_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- (Связь логирования с оборудованием)
ALTER TABLE logs ADD CONSTRAINT "logs_equipment_id" FOREIGN KEY (equipment_id) REFERENCES equipment(id);

-- (Связь логирования с помещением)
ALTER TABLE logs ADD CONSTRAINT "logs_room_id" FOREIGN KEY (room_id) REFERENCES rooms(id);

-- Таблица задач системы
CREATE TABLE system_actions (
	id bigserial PRIMARY KEY,
	active boolean,
	date_start date,
	time_start time,
	filial_id bigint,
	type_id bigint,
	param text
);

-- Таблица типов отслеживаемых действий
CREATE TABLE system_actions_type (
	id bigserial PRIMARY KEY,
	name text
);

-- (Связь задач с филиалом)
ALTER TABLE system_actions ADD CONSTRAINT "system_actions_filial_id" FOREIGN KEY (filial_id) REFERENCES filial(id);

-- (Связь задач с типом действия)
ALTER TABLE system_actions ADD CONSTRAINT "system_actions_type_id" FOREIGN KEY (type_id) REFERENCES system_actions_type(id);

-- Таблица терминалов
CREATE TABLE filial_terminal (
	id bigserial PRIMARY KEY,
	equipment_id bigint,
	camera_id bigint,
	stream_ff_pid int,
	stream_http_pid int,
	stream_js_pid int,
	stream_wsl_port int,
	stream_wsr_port int,
	stream_http_port int
);

-- Связь терминал - equipment
ALTER TABLE filial_terminal ADD CONSTRAINT "filial_terminal_equipment_id" FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id);

-- Связь терминал.камера - equipment
ALTER TABLE filial_terminal ADD CONSTRAINT "filial_terminal_camera_id" FOREIGN KEY (camera_id) REFERENCES filial_equipment(id);

-- Таблица камер
CREATE TABLE filial_camera (
	id bigserial PRIMARY KEY,
	equipment_id bigint,
	stream_ff_pid int,
	stream_http_pid int,
	stream_js_pid int,
	stream_wsl_port int,
	stream_wsr_port int,
	stream_http_port int,
	user text,
	pass text,
);

-- Связь камера - equipment
ALTER TABLE filial_camera ADD CONSTRAINT "filial_camera_equipment_id" FOREIGN KEY (equipment_id) REFERENCES filial_equipment(id);


CREATE TABLE public.fake_scans
(
	id SERIAL PRIMARY KEY,
	id_person INT,
	surname TEXT,
	first_name TEXT,
	patronymic TEXT,
	series_number TEXT,
	date_birth DATE,
	gender INTEGER,
	birthplace TEXT,
	passport_date DATE,
	passport_code TEXT,
	passport_place TEXT,
	registration_place TEXT,
	verify BOOLEAN DEFAULT true
);
CREATE UNIQUE INDEX fake_scans_id_person_uindex ON public.fake_scans (id_person);