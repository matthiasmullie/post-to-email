# Validate input

Another alternative, depending on what information your form requests, would be to validate the input. This only really works with content that is expected to follow a very strict format, though.

Since this is a general-purpose project, no such validation is built into the receiving end, which means doing it in JavaScript prior to actually sending the request. This means that you should already have implemented (some of) the [other JavaScript steps](2-spam-javascript.md) to ensure that bots can't simply read all form information in the first place, and already have to simulate actual interaction.

But moving on to an example: let's say I want to know a user's postal code, and I only intend to cater to Belgians. To the best of my knowledge, all Belgian postal codes are a sequence of 4 digits, so we could do something like this:

```html
<script src="script.js"></script>
<form action="?SUBJECT=Contact%20form">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <input type="text" name="postal_code" placeholder="Your postal code" required="required" />
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

    if (!/^\d{4}$/.test(this.querySelector('input[name="postal_code"]').value)) {
        // the postal code entered is not 4 digits, therefore the input is invalid
        alert('The postal code entered is invalid; 4 digits are expected.');
        return;
    }

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

Another popular one would be to reject anything that contains a URL in the text if you're not
expecting such input.

You can take this as far as you want, but remain careful not to end up with validation that is too
tight and reject valid input, like users mistakenly submitting ill-formatted input (e.g. a stray
space at the end of the postal code), or your failure to realize that in certain edge cases, valid
input may be different after all (e.g. a user might be trying to contact you with a question and
include a link to the page they're struggling to understand)
