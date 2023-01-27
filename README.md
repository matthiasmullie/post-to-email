# Post to email

![Image by fullvector on Freepik](instructions/assets/logo.png)


## Contact forms for static websites

Here's a solution for those with static websites wanting a contact form, but find themselves in a situation where they can't provide a script to support it (e.g. your platform doesn't allow you to host such script, or you simply don't know how)

This project provides a simple endpoint to submit your forms to, turning those form fields into an email to you.

All you need is:

- An SMTP server, for outgoing mails (e.g. [Gmail](https://support.google.com/mail/answer/7126229?hl=en#zippy=%2Cstep-change-smtp-other-settings-in-your-email-client) or [Outlook](https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-8361e398-8af4-4e97-b147-6c6c4ac95353))
- A platform that supports deploying Docker containers (e.g. [render](instructions/1-paas.md))
- Your static website with a form, hosted anywhere

For your convenience, this project is wrapped into a Docker container that can easily be deployed on any Paas - some of which will allow you to deploy this for free.
Read on!


## Instructions

### 1. Deploy the container

Run the `matthiasmullie/post-to-email` Docker container in whatever [configuration](#configuration) makes sense.

Below are detailed instructions to run this in a couple of different ways - pick whichever makes most sense for your use-case:

- [Deploy it on a PaaS](instructions/1-paas.md)
- [Run it with Docker](instructions/1-docker.md)
- [Run it with Docker Compose](instructions/1-docker-compose.md)
- [Clone the repo and build it yourself](instructions/1-byo.md)


### 2. Build a simple form & submit it

Submit a POST request (with remaining variables - see [configuration](#configuration))

Check out [this simple example to build a form that submits forms](instructions/2-form.md) to the service we deployed in the first step.

And here's [information about mitigating spam](instructions/2-spam.md) in case you were worried about that!


### 3. Receive email

You've got this! We're done here.


## Configuration

Email configuration can be set globally on the server hosting this script (via environment variables), or per request on the client calling this (via POST or GET params)

If set, environment variables always get precedence over POST, which gets precedence over GET params.
This can be useful to lock down certain settings (e.g. `RECIPIENT` or `DSN`) within your hosting environment to prevent abuse.

Available params:

* `ALLOW_ORIGIN` **[ENV only]**: allowed CORS domain
* `DSN` **[ENV only]**: DSN string for a transport (e.g. SMTP) to send emails with
* `SENDER`: sender email address
* `RECIPIENT`: recipient email address
* `REPLY_TO` *(optional, defaults to `SENDER`)*: reply-to email address
* `SUBJECT` *(optional, defaults to "Form to email")*: email subject
* `REDIRECT` *(optional, defaults to referrer)*: location to redirect back to after handing submission, or explicitly blank for no redirect
* `HONEYPOT` *(optional, defaults to none)*: Name of a form field to use as [honeypot](instructions/2-spam-honeypot.md) to filter out [unwanted (spam) submissions](instructions/2-spam.md)
