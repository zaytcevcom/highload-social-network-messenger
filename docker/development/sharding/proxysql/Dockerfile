#FROM proxysql/proxysql:2.4.7
FROM proxysql/proxysql:latest

# Update packages
RUN apt update -y --fix-missing

# Install common packages
RUN apt install -y mariadb-client

# Set custom config
ADD ./development/sharding/proxysql/proxysql.cnf /etc/
