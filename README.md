# Form to email

Accepts POST data and turns it into an email.

## Configuration

Email configuration can be set globally on the server hosting this script (via ENV variables), or per request on the client calling this (via GET params)

If set, ENV variables always get precedence over GET params.

Available params:

* ALLOW_ORIGIN (ENV only): Allowed CORS domain (defaults to * if not set)
* SMTP_DSN: DSN string for an SMTP server to send emails with
* SUBJECT: email subject
* SENDER: sender email address
* RECIPIENT: recipient email address
* REPLY_TO (optional): reply-to email address (defaults to SENDER)
