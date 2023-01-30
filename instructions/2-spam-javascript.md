# Rely on JavaScript to submit your form

Much like the honeypot technique, this leverages a difference in interaction between bots and users.
Most bots simply scrape and process your HTML, and anything that relies on JavaScript is going to throw them off.

*Note: since some browse the web without JavaScript enabled, relying on JavaScript is also going to exclude a (minor) subset of valid users.*

Let's walk through how we could improve a simple form like this, where all the info required for successful submission is right there for everyone to read:

```html
<form action="https://post-to-form.my-server.com/?SUBJECT=Contact%20form" method="post">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>
```


## 1. Move the form endpoint out of the HTML

### Simple implementation

Instead of including the form `action` in the HTML, we'll let JavaScript fill that out:

```html
<form action="#" method="post">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>

<script>
    document.querySelector('form').setAttribute('action', 'https://post-to-form.my-server.com/?SUBJECT=Contact%20form');
</script>
```

It is now impossible for bots to simply submit the form based on the data available in the HTML. They'll also need to process a script.


### Step up: separate resource

A step up from here would be to move the script into a separate file, also requiring bots to download and process additional resources:

```html
<script src="script.js"></script>
<form action="#" method="post">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>
```

**script.js**
```js
document.querySelector('form').setAttribute('action', 'https://post-to-form.my-server.com/?SUBJECT=Contact%20form');
```


## 2. Obfuscate required information

Alright, by now, we've moved some key piece of information around to make it unlikely for bots to be able to work with it, but it's still there, and sufficiently advanced bots may still find it. Let's make that a little harder by obfuscating that data:

```html
<script src="script.js"></script>
<form action="?SUBJECT=Contact%20form" method="post">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>
```

**script.js**
```js
var url = atob('aHR0cHM6Ly9wb3N0LXRvLWZvcm0ubXktc2VydmVyLmNvbQ==')
var query = document.querySelector('form').getAttribute('action');
document.querySelector('form').setAttribute('action', `${url}/${query}`);
```

The above code snippet does 2 things:

- it breaks the required information and spreads it across 2 places
  - the querystring is still part of the form's `action`, and looks to be a valid action (the form would submit to the same page) so there is no reason for a bot to expect this not to be valid
  - the rest of the path has been moved to JavaScript
- part of the information (the non-querystring part of the URL) is base64-encoded; any bot looking for a value that matches a URL would not be able to locate it
  - `aHR0cHM6Ly9wb3N0LXRvLWZvcm0ubXktc2VydmVyLmNvbQ==` is the result of `btoa('https://post-to-form.my-server.com')`

The rest of the JavaScript will then simply re-assemble the action by base64-decoding the encoded URL part (`atob('aHR0cHM6Ly9wb3N0LXRvLWZvcm0ubXktc2VydmVyLmNvbQ==')`) and gluing both pieces back together.


## 3. Require interaction

### Simple implementation

While very unlikely, it's still possible for fully equipped bots to let the page load as intended and read the post-script-execution DOM.

So, let's not fill out that form `action`, but let JavaScript submit the form after having interacted with the "submit" button:

```html
<script src="script.js"></script>
<form action="?SUBJECT=Contact%20form">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>
```

**script.js**
```js
var url = atob('aHR0cHM6Ly9wb3N0LXRvLWZvcm0ubXktc2VydmVyLmNvbQ==')
var query = document.querySelector('form').getAttribute('action');
document.querySelector('form').addEventListener('submit', function (event) {
    event.preventDefault();
    fetch(`${url}/${query}`, {
        method: 'POST',
        body: new URLSearchParams(new FormData(event.target))
    }).then(this.reset.bind(this));
});
```


### Step up: require realistic interaction

While unrealistic, it's still not entirely impossible that some bot loaded the page, load & execute all scripts, filled out the input fields and simulated a click on the submit button.

Let's up the ante one last time, by making assumptions that are likely to be true for human beings.
We can simply assume that your input field will take a normal person a certain amount of time to complete, and not allow submissions before that.
We can also check for any keyboard or mouse activity to have happened, to ensure the form hasn't been filled out programmatically.

Let's do both:

```html
<script src="script.js"></script>
<form action="?SUBJECT=Contact%20form">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>
```

**script.js**
```js
var interacted = false;
addEventListener('mousemove', () => interacted = true );
addEventListener('keypress', () => interacted = true );

var timeout = 30;
var timer = setTimeout( function () {
    timer = null;
}, timeout * 1000);

var url = atob('aHR0cHM6Ly9wb3N0LXRvLWZvcm0ubXktc2VydmVyLmNvbQ==')
var query = document.querySelector('form').getAttribute('action');
document.querySelector('form').addEventListener('submit', function (event) {
    event.preventDefault();

    if (interacted === false) {
        // no keyboard or mouse interaction was detected, so any data present must have been filled out programmatically
        alert("This form was submitted without keyboard or mouse interaction, which is rather suspicious!");
        return;
    }

    if (timer !== null) {
        // the timer has not yet run out, this was submitted so rapidly that it's likely a bot
        alert(`This form was submitted so rapidly that it made you look like a bot! Please try again after {timeout} seconds.`);
        return;
    }

    fetch(`${url}/${query}`, {
        method: 'POST',
        body: new URLSearchParams(new FormData(event.target))
    }).then(this.reset.bind(this));
});
```

No bot is going to stick around for 30 seconds; it just wouldn't be worth it anymore.
If any bot is going to make it past this point, I will very much welcome their spam!
