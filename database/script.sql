create database englishware;

use englishware;

create table niveis
( niv_codigo     int(5)       not null auto_increment
, niv_descricao  varchar(50)  not null
, constraint pk_niveis  primary key (niv_codigo)
) ;

insert into niveis (niv_descricao) values ('1ª ed. Starter: Verde');
insert into niveis (niv_descricao) values ('1ª ed. Livro 1: Vermelho');
insert into niveis (niv_descricao) values ('1ª ed. Livro 2: Amarelo');
insert into niveis (niv_descricao) values ('1ª ed. Livro 3: Azul');
insert into niveis (niv_descricao) values ('1ª ed. Livro 4: Roxo');
insert into niveis (niv_descricao) values ('2ª ed. Starter: Verde');
insert into niveis (niv_descricao) values ('2ª ed. Livro 1: Vermelho');
insert into niveis (niv_descricao) values ('2ª ed. Livro 2: Amarelo');
insert into niveis (niv_descricao) values ('2ª ed. Livro 3: Azul');
insert into niveis (niv_descricao) values ('2ª ed. Livro 4: Roxo');
insert into niveis (niv_descricao) values ('2ª ed. Livro 5: Cinza');
insert into niveis (niv_descricao) values ('Aulas Diversas');

create table alunos
( alu_codigo     int(5)       not null auto_increment
, alu_nome       varchar(50)  not null
, alu_email      varchar(50) 
, alu_fone       varchar(50)
, alu_nivel      int(5)       not null
, constraint pk_alunos  primary key (alu_codigo)
, constraint fk1_alunos foreign key (alu_nivel)  references niveis (niv_codigo)
) ;

create table aulas
( aul_aluno      int (5)      not null
, aul_dia        int (1)      not null default 2
, aul_hor_ini    time         not null default '00:00'
, aul_hor_fim    time         not null default '00:00'
, aul_preco      numeric(5,2) not null default 0
, constraint pk_aulas  primary key (aul_aluno, aul_dia, aul_hor_ini)
, constraint fk1_aulas foreign key (aul_aluno) references alunos (alu_codigo)
) ;

create table projecao_mensal
( pjm_ano         int(4)       not null
, pjm_mes         int(2)       not null
, pjm_aluno       int(5)       not null
, pjm_aluno_nome  varchar(50)  not null
, pjm_aluno_email varchar(50)  
, pjm_aluno_nivel varchar(50)  not null
, pjm_data_aula   date         not null
, pjm_dia_semana  varchar(50)  not null
, pjm_hor_ini     time         not null default '00:00'
, pjm_hor_fim     time         not null default '00:00'
, pjm_vlr_aula    numeric(5,2) not null default 0
, pjm_revisado    int(1)       not null default 0
, constraint pk_projecoes primary key (pjm_ano, pjm_mes, pjm_aluno, pjm_data_aula)
) ;

create index idx0_projecao_mensal on projecao_mensal (pjm_ano, pjm_mes, pjm_aluno, pjm_data_aula);
create index idx1_projecao_mensal on projecao_mensal (pjm_ano, pjm_mes);
create index idx2_projecao_mensal on projecao_mensal (pjm_aluno);
