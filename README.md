# Form to email: contact forms for static websites

This project accepts POST data and turns it into an email, which enables contact forms to be implemented on otherwise static websites.

Given the marginal footprint of this project, the free or cheapest plan on your Paas of choice (e.g. heroku.com, render.com, fly.io, ...) should suffice to host this.


## Installation

1. Clone this repo and deploy it to any PaaS capable of serving Docker images
2. Set environment variables on the PaaS (optional - see [configuration](#configuration))
3. Submit POST requests (with remaining variables - see [configuration](#configuration))
4. Receive emails


## Configuration

Email configuration can be set globally on the server hosting this script (via environment variables), or per request on the client calling this (via GET params)

If set, environment variables always get precedence over GET params. This can be useful to lock down certain settings (e.g. `RECIPIENT` or `SMTP_DSN`) to prevent abuse.

Available params:

* `ALLOW_ORIGIN` [ENV only] (optional, defaults to `*`): Allowed CORS domain
* `SMTP_DSN`: DSN string for an SMTP server to send emails with
* `SUBJECT`: email subject
* `SENDER`: sender email address
* `RECIPIENT`: recipient email address
* `REPLY_TO` (optional, defaults to `SENDER`): reply-to email address
