FROM zabbix/zabbix-agent

ARG ZBX_SERVER_HOST=127.0.0.1

COPY ./development/zabbix-agent/zabbix_agentd.conf /etc/zabbix/zabbix_agentd.conf

USER root

RUN mkdir -p /var/log/zabbix && chown -R zabbix:zabbix /var/log/zabbix

USER zabbix

RUN sed -i "s/Server=127.0.0.1/Server=$ZBX_SERVER_HOST/g" /etc/zabbix/zabbix_agentd.conf

CMD ["zabbix_agentd", "-f"]
