#nohup /usr/local/bin/node /srv/www/htdocs/hc/node/srv.js&
homegear -d
nohup ntpd -q&
sleep 10
cd /srv/www/htdocs/hc/node/
nohup node srv.js >> ../log/node.log&
