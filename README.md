# easyswoole-demo
基于easyswoole框架，集成文档中部分内容的demo。

## 目前集成功能
1. mysqli（使用pool的demo）
2. redis
3. redis-pool
4. queue
5. orm 
6. http-client（需要swoole支持openssl）

## NOTE

### nginx配置

```
server {
listen 80;
server_name SERVER_NAME;
return 307 https://$server_name$request_uri;
}

server {
listen 443;
server_name SERVER_NAME;

    ssl on;
    ssl_certificate ssl/CRT.crt;
    ssl_certificate_key ssl/KEY.key;
    ssl_ciphers "EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH";
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;

    location / {
        # 将客户端host及ip信息转发到对应节点
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

        # 转发Cookie，设置 SameSite
        proxy_cookie_path / "/;";

        # 代理访问真实服务器
        proxy_pass http://127.0.0.1:PORT;
    }

    access_log LOG_DIR access;
}
```

