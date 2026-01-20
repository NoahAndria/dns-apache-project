$TTL    604800
@       IN      SOA     ns1.sixseven.com. admin.sixseven.com. (
                              2         ; Serial
                         604800         ; Refresh
                          86400         ; Retry
                        2419200         ; Expire
                         604800 )       ; Negative Cache TTL

@	IN	NS	ns1.sixseven.com.
@	IN	A	127.0.0.1
ns1	IN	A	127.0.0.1
www	IN	A	127.0.0.1
@	3600	IN	CNAME	127.0.0.1
