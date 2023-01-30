# Use additional services

Below are some generic and widely used services to further help rid the world of bots.

Because they are so widely used, they can leverage vast amounts of data across their installation base in order to detect and prevent bots from taking their shot on your website; although their success also means that bots will more actively attempt to adapt in order to try to sneak past them.


### Akismet

[Akismet](https://akismet.com) is a third party service originally created to block spam on [WordPress](https://wordpress.org) websites, but you can easily integrate it into your own website after signing up for an API key.

It works by crowd-sourcing form submissions across all participating websites and comparing new submissions against its vast database, rejecting any that match known spam.


### Firewall

Firewalls can help you prevent a bot from being able to submit forms on your website, assuming you know what that kind of "visitor" look like.

Cloudflare, an internet service provider reverse proxying a massive amount of website globally, employs an array of heuristics on their network in order to detect improper traffic.

They can help you fight off bots with their [Cloudflare Bot Management](https://www.cloudflare.com/en-gb/products/bot-management).


### Captcha


I would not recommend captchas unless all other options have already been exhausted and spam continues to roll in. After all, captchas add some friction for genuine users as well.

In essence, captchas add yet another barrier to the process by requiring users to prove that they're human, often in the form of solving visual riddles (that are hard to execute by bots), although some have implemented additional heuristics to confirm that you're a human without too much friction.

Note that some bots are able to solve certain captcha implementations already, so this too is not necessarily a silver bullet. Nothing will ever be: bots adapt and all that we can do is add more hurdles for them to just over, ideally without too much impact on actual human users.

If you want to pursue implementing captchas in your form, you may want to look into [reCAPTCHA](https://www.google.com/recaptcha/about/).
