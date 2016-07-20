#Email Newsletter Plugin for Enact CMS
Plugin for [Enact CMS](https://enactcms.com).

##Functionality
The Email Newsletter plugin provides support for:
- Allowing email news letter subscription via front end forms of your site.
- Viewing/searching/sorting/deleting subscribed emails.
- Exporting subscribed emails to CSV for use in 3rd party services.
- Creating campaigns to be sent to subscribers.
- Testing campaigns.
- Viewing statistics for each campaign to deterimine open and click through rates.

###Subscriptions
You can use the pre-provided form to get started with collecting email subscriptions super quickly via your twig templates:

```
{{ enact.plugin.EmailNewsletter.emailSubscribeForm }}
```

Or:

```
{% include "@emailnewsletter/subscribe-form %}
```


You can also opt to build your own form:

```
{% if enact.session.hasFlash('email-newsletter-subscribe-success') %}
    <div class='success'>{{ enact.session.getFlash('email-newsletter-subscribe-success') }}</div>
{% else %}
    <form method='post' action='/email-newsletter-subscribe'>
        {{ enact.csrfFormInput }}
        <input type='hidden' name='submit-uri' value='{{ enact.request.uri }}'/>
        <input type='email' name='email' required placeholder='Enter your email address...'/>
        <input type='submit' value='Subscribe'/>
        {% if enact.session.hasFlash('email-newsletter-subscribe-error') %}
            <div class='error'>{{ enact.session.getFlash('email-newsletter-subscribe-error') }}</div>
        {% endif %}
    </form>
{% endif %}
```

The form must be POSTED to `email-newsletter-subscribe`.

In the POST data you must provide:
- `email` : The email to add to the subscribers list.
- `submit-uri` : The URI to redirect to after the action, whether it fails or not.

Determine success or error by checking the session flash data:
- `email-newsletter-subscribe-error` : Set to a string providing information about the error.
- `email-newsletter-subscribe-success` : Set to `Thanks for subscribing!` if email was successfully subscribed.

You can do AJAX by defining the POST variable `ajax`, which will return a JSON response which will contain either:
- `success` - Set if successful with the message `Thanks for subscribing!`.
- `error` - Set if the email could not be subscribed, with a message describing the reason.

###Campaigns
Your campaign templates are stored by default in your template directory in `enact/template/_emailnewsletter` which
you will need to create. You can change the directory your campaign templates are stored in by creating a configuration file for the plugin at
`enact/config/EmailNewsletter.php` which defines the setting `emailTemplatesDir`, for example:
```
return Array(
    'emailTemplatesDir' => '_custom_email_dir/'
);
```

Once you have your campaign template directory created you are ready to create your first campaign, but before you 
can create your first campaign you need to create a twig template that will serve as the body of the
campaign.

There are two files/components to a campaign:
- The twig template which servers as the body of the email for the campaign.
- (optional) The variable definition file which defines dynamic user defined variables for use in the campaign body
  template and has the exact name as the twig template but with a `php` extension and returns an array of arrays
  (see below).

Lets say for example we want to create a campaign for weekend special sale:

Create a template at `enact/template/_emailnewsletter/weekend-special.html`
```
<p>To : {{ email }}</p>

<h1>{{ subject }}</h1>

<p>Get %{{ percentOff }} off all {{ productCategory }} all day Saturday & Sunday</p>

<a href='{{ emailLink('/') }}'>View {{ productCateogry }} specials</a>

<p>{{ message }}</p>

<hr>

<a href='{{ emailUnSubLink() }}'>Unsubscribe from future emails</a>

{{ emailViewedOnImg() }}
```

The variables `{{ subject }}` and `{{ email }}` are reserved and always populated for every campaign and you should
NOT use them in your custom variables definition.

You'll notice that `{{ percentOff }}`, `{{ productCategory }}`, and `{{ message }}` are variables that are going to be defined by the
user creating the campaign. 

Define the custom variables at `enact/template/_emailnewsletter/weekend-special.php`
```
<?php
return Array(
    Array(
        'name'      => 'Percent Off',
        'handle'    => 'percentOff',
        'tip'       => 'The percentage off for products in the category, do not include a % symbol!',
        'type'      => 'number',
        'default'   => 15,
        'required'  => true
    ),
    Array(
        'name'      => 'Product Category',
        'handle'    => 'productCategory',
        'tip'       => 'The product category that is on sale.',
        'type'      => 'text',
        'required'  => true
    ),
    Array(
        'name'      => 'Message',
        'handle'    => 'message',
        'tip'       => 'The primary message being conveyed.',
        'type'      => 'textarea',
    )

);```

Notice that the custom variable definition file returns an array of arrays, where each child array can specify:
- `name` - (required) The name of the field as presented to the user.
- `handle` - (required) The hanlde of the field as used/accessed in the campaign template.
- `tip` - (optional) A tip presented to the user about what the field is and how it is used.
- `type` - (required) A value of:
    - `text` - Short text.
    - `textarea` - Long text which can contain linebreaks or user inputted HTML, `\n` are automatically
      convered to `<br>`.
    - `number` - A numeric value.
- `default` - (optional) A default value to use in the field.
- `required` - (optional) Whether the field must be given a value before the campaign can be created.


You will also notice that three functions are available which are vitale for collecting statistics about the
campaign:
- `emailViewedOnImg()` - Embed a hidden 1x1 png image that is used to determine that the email was opened by the
  subscriber.
- `emailUnSubLink()` - Returns the appropriate FQD link that allows a subscriber to unsubscribe from future
  campaigns.
- `emailLink(string)` - Pass a string to generate a link which is used to determine that the subscriber has clicked
  the link, the user will be redirected to the appropriate link after the link click stat has been collected.
