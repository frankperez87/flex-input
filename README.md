# Flex Input
Flexible input handler for request inputs (POST, GET, PUT, DELETE, REQUEST, FILES, COOKIE) using filter_var().


##Install
Via composer
> { "require": { "rgasch/flex-input": "1.0.0" } }


### Example Use
> <?php
>
>use rgasch\FlexInput\Input;
>

Retrieving the entire GET array: 

> $value = Input::get ();

Retrieving a specific item from GET: 

> $value = Input::get ('key');

Retrieving a specific item from GET while specifying a default value to be returned if the requested item is not found: 

> $value = Input::get ('key', $defaultValue);

Retrieving a specific item from POST while specifying default, filter and args

> $value = Input::post ('key', $defaultValue, FILTER_SANITIZE_STRING, array(FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));


Internally the PHP function filter_var() is used; consult the PHP docs for the various options which are supported.


