# Run it with Docker Compose

Assuming you have access to a web server with support for Docker Compose, you can use this yml config to run the service:

**docker-compose.yml**

```yaml
version: '3.4'
services:
  post-to-email:
    restart: unless-stopped
    image: matthiasmullie/post-to-email
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

Now execute `docker compose up` (or `docker-compose up`) to run the container. This will pull the [`matthiasmullie/post-to-email` image from Docker Hub](https://hub.docker.com/r/matthiasmullie/post-to-email) and run it exposed on port 8080 with a hard-coded `DSN` and `RECIPIENT`.

- For `ALLOW_ORIGIN`, enter the domain you want to accept requests from (e.g. `https://my-website.com`), or `*` to allow requests from anywhere
  - To prevent abuse, I'd recommend locking things down to a specific domain unless there's a good reason not to
  - Read up on [Access-Control-Allow-Origin](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin) if you want to learn more
- `DSN` should be your SMTP connection string
  - This usually takes the form of `smtp://<username>:<password>@<smtp-server>:<port>`
  - For more information, check with your email provider. Here's a link to [Gmail](https://support.google.com/mail/answer/7126229?hl=en#zippy=%2Cstep-change-smtp-other-settings-in-your-email-client) and [Outlook](https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-8361e398-8af4-4e97-b147-6c6c4ac95353)'s documentation
    - Note: If your email account is secured with multi-factor authentication, you may need to create an app password. Here's some documentation for [Gmail](https://support.google.com/accounts/answer/185833?hl=en) and [Outlook](https://support.microsoft.com/en-us/account-billing/manage-app-passwords-for-two-step-verification-d6dc8c6d-4bf7-4851-ad95-6d07799387e9)
- `RECIPIENT` is the email address you want to receive emails at
  - This can take either `my-email@example.com` or `My Name <my-email@example.com>` format

While it's possible to include these variables in the data your form will submit, it makes sense to hard-code some of these in the web service, to prevent others from being able to (ab)use your service.

*Note: don't forget to replace below DSN & RECIPIENT variables with your own. Check [configuration](../README.md#configuration) to see what other environment variables are available.*

*Note: the `healthcheck` config is not required, but can be useful to monitor that the service remains up & healthy.*
