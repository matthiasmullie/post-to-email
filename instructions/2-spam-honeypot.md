# Add a honeypot to your form

The honeypot technique works by leveraging the difference between how users and bots "see" a form.

Bots will parse your website until they come across a form, then fill out all available fields.  Users will do the same, unless instructed not to, or unless they're not even aware of certain fields.

So, we could add a simple field that we expect to remain empty and visually hide it. Users will not see it (and leave it blank), but most bots will be more naive and fill it out. After all, they're usually crude enough tools to not be able to process the rest of your code in order to be able to figure out that this field is not visible: that kind of complex processing is simply not worth it for generic spam bots.

Now how do we implement this?


## 1. Add the honeypot field to your form

In-between your other (valid) form input fields, add the honeypot field.

*Note: don't pick an obvious field name like "honeypot"; instead, pick a realistic-looking name that you don't intend to use. In this example, I'll go with `username`.*

```html
<input type="text" name="username" placeholder="Your username" tabindex="-1" autocomplete="new-password">
```

*Note: I've also added `tabindex="-1"` to prevent users from accidentally tabbing into this field, and I've added `autocomplete="new-password"` to prevent password managers from accidentally filling it out.*


## 2. Add some CSS to visually hide the input field

```css
input[name="username"] {
    position: absolute;
    left: -999999999px;
}
```

*Note: you could also embed these right into the html via e.g. the `style` attribute, but moving it into CSS adds one more hurdle to overcome for bots, who are less likely to go fetch and process additional resources.*


*Note: you could also use e.g. `display: none`, `visibility: hidden` or `opacity: 0`, although those
are more easily identifiable as "intentionally hidden" by bots. It isn't exactly rocket science to
figure out that this element is positioned off-screen and probably not intended to be visible, so
you could certainly consider other techniques if your overall project structure allows for it;
e.g. leaving it on-screen, but positioning other content over it.*


## 3. Set environment variable `HONEYPOT`

Bots will try to guess at what content your form fields expects (i.e. a field named "name" will be
populated with a name, and that big textarea will have their spam pitch), fill them out and submit
the form.

Genuine users will do the same, but since they weren't aware of your honeypot field's existence,
that one will remain blank.
We can use this discrepancy to filter out unwanted submissions.

Just like you've set other environment variable like `ALLOW_ORIGIN` or `DSN`, you can supply an environment variable with the name `HONEYPOT`.

The value for this variable should be the name of the input field that you want to act as a honeypot, i.e. the field where you don't expect any input from. This allows us to filter out such unwanted spam submissions.

With these 3 simple things in place, bots are likely to fall into the trap of filling out this honeypot field and these will simply be rejected.
