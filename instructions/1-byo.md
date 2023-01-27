# Clone the repo and build it yourself

If you want to make changes to this project (e.g. alter the email templates), or don't trust me to not sneak malicious things in the image published on Docker Hub, you can simply fork this project and build your own image:

```sh
git clone https://github.com/matthiasmullie/post-to-email.git # or your own fork
cd post-to-email
# optional: make your changes here
```

You can now build & run your own Docker image:

```sh
docker build -t post-to-email .
docker run -d \
  --name=post-to-email \
  -p 8080:80 \
  -e ALLOW_ORIGIN="*" \
  -e DSN="smtp://user:password@smtp.my-domain.com:port" \
  -e RECIPIENT="Matthias Mullie <my-email@example.com>" \
  post-to-email
```

Or run it with a **docker-compose.yml** that looks something like this:

```yaml
version: '3.4'
services:
  post-to-email:
    restart: unless-stopped
    build: .
    container_name: post-to-email
    environment:
      - ALLOW_ORIGIN=*
      - DSN=smtp://user:password@smtp.my-domain.com:port
      - RECIPIENT=Matthias Mullie <my-email@example.com>
    ports:
      - '8080:80'
    healthcheck:
      test: 'curl --fail http://localhost:80/?SENDER=test@example.com'
      interval: 1m
      timeout: 10s
      retries: 3
      start_period: 20s
```

Or deploy your own repo [on a PaaS](1-paas.md).
