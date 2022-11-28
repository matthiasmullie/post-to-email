# Form to email: contact forms for static websites

Turn POST requests into email, enabling contact forms etc. on otherwise static websites.


## Installation

1. Run the `matthiasmullie/post-to-email` [Docker image](https://hub.docker.com/r/matthiasmullie/post-to-email) in whatever [configuration](#configuration) makes sense
2. Submit a POST request (with remaining variables - see [configuration](#configuration))
3. Receive email

Note: given the minimal resource requirements for this project, you could also choose to host this container on the free or cheapest plan on your Paas of choice (e.g. heroku.com, render.com, fly.io, ...)


## Example

### 1. Run the container

#### Run it with Docker

*Note: don't forget to replace below DSN & RECIPIENT variables with your own.*

*Check [configuration](#configuration) to see what other environment variables are available.*

```sh
docker run -d \
  --name=post-to-email
  -p 8080:80 \
  -e DSN="smtp://user:pass@smtp.example.com:port" \
  -e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
  matthiasmullie/post-to-email
```

#### Or with docker-compose

```yaml
version: '3.8'
services:
  post-to-email:
    restart: unless-stopped
    image: matthiasmullie/post-to-email
    container_name: post-to-email
    environment:
      - DSN=smtp://user:pass@smtp.example.com:port
      - RECIPIENT=Matthias Mullie <post-to-email@mullie.eu>
    ports:
      - '8080:80'
    healthcheck:
      test: 'curl --fail http://localhost:80/test/?SENDER=test@example.com'
      interval: 1m
      timeout: 10s
      retries: 3
      start_period: 20s
```

#### Or clone the repo and build it yourself

...if you want to make changes to this project (e.g. alter the email templates)

```sh
git clone https://github.com/matthiasmullie/post-to-email.git
cd post-to-email
docker build -t post-to-email .
docker run -d \
  --name=post-to-email
  -p 8080:80 \
  -e DSN="smtp://user:pass@smtp.example.com:port" \
  -e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
  post-to-email
```

### 2. Build a simple form & submit it

Like this:

*Or even add some JavaScript to submit the form without leaving the page.*

```html
<form action="http://localhost:8080/?SUBJECT=Contact%20form" method="post">
    <input type="text" name="name" placeholder="Your name" required="required" />
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>

<script>
    document.querySelector('form').addEventListener('submit', function (event) {
        event.preventDefault();
        fetch(this.getAttribute('action'), {
            method: 'POST',
            body: new URLSearchParams(new FormData(event.target))
        }).then(this.reset.bind(this));
    });
</script>
```

### 3. Receive an email

You've got this! We're done here.


## Configuration

Email configuration can be set globally on the server hosting this script (via environment variables), or per request on the client calling this (via POST or GET params)

If set, environment variables always get precedence over POST, which gets precedence over GET params.
This can be useful to lock down certain settings (e.g. `RECIPIENT` or `DSN`) within your hosting environment to prevent abuse.

Available params:

* `DSN`: DSN string for a transport (e.g. SMTP) to send emails with
* `SENDER`: sender email address
* `RECIPIENT`: recipient email address
* `REPLY_TO` *(optional, defaults to `SENDER`)*: reply-to email address
* `SUBJECT` *(optional, defaults to "Form to email")*: email subject
* `REDIRECT` *(optional, defaults to referrer)*: location to redirect back to after handing submission, or explicitly blank for no redirect
* `ALLOW_ORIGIN` **[ENV only]** *(optional, defaults to `*`)*: allowed CORS domain
