# Run it with Docker

Assuming you have access to a web server with support for Docker, you can use this command to run the service:

```sh
docker run -d \
  --name=post-to-email \
  -p 8080:80 \
  -e DSN="smtp://user:password@smtp.my-domain.com:port" \
  -e RECIPIENT="Matthias Mullie <my-email@example.com>" \
  matthiasmullie/post-to-email
```

This will pull the [`matthiasmullie/post-to-email` image from Docker Hub](https://hub.docker.com/r/matthiasmullie/post-to-email) and run it exposed on port 8080 with a hard-coded `DSN` and `RECIPIENT`.

- `DSN` should be your SMTP connection string
  - This usually takes the form of `smtp://<username>:<password>@<smtp-server>:<port>`
  - For more information, check with your email provider. Here's a link to [Gmail](https://support.google.com/mail/answer/7126229?hl=en#zippy=%2Cstep-change-smtp-other-settings-in-your-email-client) and [Outlook](https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-8361e398-8af4-4e97-b147-6c6c4ac95353)'s documentation
    - Note: If your email account is secured with multi-factor authentication, you may need to create an app password. Here's some documentation for [Gmail](https://support.google.com/accounts/answer/185833?hl=en) and [Outlook](https://support.microsoft.com/en-us/account-billing/manage-app-passwords-for-two-step-verification-d6dc8c6d-4bf7-4851-ad95-6d07799387e9)
- `RECIPIENT` is the email address you want to receive emails at
  - This can take either `my-email@example.com` or `My Name <my-email@example.com>` format

While it's possible to include these variables in the data your form will submit, it makes sense to hard-code some of these in the web service, to prevent others from being able to (ab)use your service.

*Note: don't forget to replace below DSN & RECIPIENT variables with your own. Check [configuration](#configuration) to see what other environment variables are available.*
