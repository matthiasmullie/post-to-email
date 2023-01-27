# Build a simple form & submit it

Now that we've deployed this project, we can build our form.

A simple form could look something like this:

```html
<form action="https://post-to-form.my-server.com/?SUBJECT=Contact%20form" method="post">
    <input type="email" name="SENDER" placeholder="Your email" required="required" />
    <input type="text" name="name" placeholder="Your name" required="required" />
    <textarea name="message" cols="30" rows="5" required="required"></textarea>
    <input type="submit" value="Submit" />
</form>
```

Let's break this down:

- The form has an action that points to your service. In this case, it's `https://post-to-form.example.com`, but you should substitute that with the location of your service
- We're appending a simple querystring (`?SUBJECT=Contact%20form`) so that every submission of this form will end up sending an email with "Contact form" as subject
  - Note: `SUBJECT` could also have been an input field (either available for user input or hidden) just like `SENDER` (see below)
- We have an `<input name="SENDER">` where the user can enter their email address. This will be the `From` address of the email being sent. `SENDER` is one of the required [configuration options](../README.md#configuration). While you could also set it as an environment variable on your service, we hadn't done so in the examples, so we need this to be included in the request
  - Note: we can add other [configuration options](../README.md#configuration) if we wish (hidden or otherwise, or as part of the form action, like `SUBJECT` above); e.g. `REDIRECT` for a link to redirect to after successfully having processed the form submission
- `<input name="name">` and `<textarea name="message">` are custom inputs. You can add as many inputs as you wish. All data that is input in those fields will be included in the body of the email that will be sent

*We could also submit the form with JavaScript without even leaving the page. Here's a quick snippet that takes over the above form's "submit" button and sends it asynchronously:*

```html
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

Our service is up and running. Our form is ready. We're done here!

Unless we want to curb spam - [read on for some quick pointers](2-spam.md)!
