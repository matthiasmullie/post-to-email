# Form to email: contact forms for static websites

This project accepts POST data and turns it into an email, which enables contact forms to be implemented on otherwise static websites.

Given the marginal footprint of this project, the free or cheapest plan on your Paas of choice (e.g. heroku.com, render.com, fly.io, ...) should suffice to host this.


## Installation

1. Clone this repo and deploy it to any PaaS capable of serving Docker images
2. Set environment variables on the PaaS (optional - see [configuration](#configuration))
3. Submit POST request (with remaining variables - see [configuration](#configuration))
4. Receive email


## Example

Instead of deploying to a PaaS, let's build & run this locally:

*Note: don't forget to replace below DSN & RECIPIENT variables with your own.*

```sh
docker build -t post-to-email .
docker run -d -p 8080:80 -e DSN="smtp://user:pass@smtp.example.com:port" -e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" post-to-email
```

Next: build a simple form like this. Submit it & receive an email!

Or add some JavaScript to submit the form without leaving the page.

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


## Configuration

Email configuration can be set globally on the server hosting this script (via environment variables), or per request on the client calling this (via POST or GET params)

If set, environment variables always get precedence over POST, which gets precendence over GET params.
This can be useful to lock down certain settings (e.g. `RECIPIENT` or `DSN`) within your hosting environment to prevent abuse.

Available params:

* `DSN`: DSN string for a transport (e.g. SMTP) to send emails with
* `SENDER`: sender email address
* `RECIPIENT`: recipient email address
* `REPLY_TO` *(optional, defaults to `SENDER`)*: reply-to email address
* `SUBJECT` *(optional, defaults to "Form to email")*: email subject
* `REDIRECT` *(optional, defaults to referrer)*: location to redirect back to after handing submission, or explicitly blank for no redirect
* `ALLOW_ORIGIN` **[ENV only]** *(optional, defaults to `*`)*: allowed CORS domain
