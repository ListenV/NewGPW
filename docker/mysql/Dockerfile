FROM mariadb:10.3

COPY ./mysqld_sql_mode.cnf /etc/mysql/conf.d/mysqld_sql_mode.cnf

COPY ./gazelle.sql docker-entrypoint-initdb.d/gazelle.sql