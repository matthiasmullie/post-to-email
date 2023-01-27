# Post to email

![Image by fullvector on Freepik](instructions/assets/logo.png)

## Contact forms for static websites

Turn POST requests into email, enabling contact forms etc. on otherwise static websites.


## Instructions

### 1. Deploy the container

Run the `matthiasmullie/post-to-email` Docker container in whatever [configuration](#configuration) makes sense.

Below are detailed instructions to run this in a couple of different ways - pick whichever makes most sense for your usecase.

- [Deploy it on a PaaS](instructions/1-paas.md)
- [Run it with Docker](instructions/1-docker.md)
- [Run it with Docker Compose](instructions/1-docker-compose.md)
- [Clone the repo and build it yourself](instructions/1-byo.md)


### 2. Build a simple form & submit it

Submit a POST request (with remaining variables - see [configuration](#configuration))

Check out [this simple example to build a form that submits forms](instructions/2-form.md) to the service we deployed in the first step.


### 3. Receive email

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
