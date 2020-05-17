# Statamic Field Tag

The field tag provides you with a way of accessing form field data in a way that works with partials.

## Why?

Out of the box, it is impossible to create reusable partials for forms with Statamic. This is because key data like errors and old values are only accesible through a tag method for the field name. e.g. a field called name would access its errors like `{{ name:error }}` and its old value like `{{ name:old }}`.

The field tag gives you a way of accessing these values through a parameter, instead of a method name. This means you can create a partial for an input like so.
```html
<label class="flex flex-col">
  <span class="flex mb-1">{{ label | sanitize }}</span>
  <input
    type="{{ type or 'text' }}"
    name="{{ name }}"
    id="{{ input_id or name }}"
    value="{{ field:old :src='name' }}"
    class="
      border border-gray-300 rounded leading-none py-3 px-4
      {{ if class }} {{ class }}{{ endif }}
      {{ if { field:hasError :src='name' } }}bg-red-100 border-red-400{{ endif }}
      "
    >
    {{ if { field:hasError :src='name' } == true }}
      <span class="mt-2 text-sm text-red-700">{{ field:error :src="name" }}</span>
    {{ endif }}
</label>
```
You could then call it with just the label and name fields, like so:
```html
{{ partial:input
  label="Your Name"
  name="name"
}}
```

## Installation

To install this addon, download the files from the Statamic Marketplace and place the `Field` folder in your `site/addons` directory.

## How to Use

The addon introduces a number of methods for use with Statamic's form functionality.

### Old

The `{{ field:old }}` tag allows you to return the old value associated with the field.

#### Parameters

##### src

`src` is used to define the form field you want to get the data back for. In a partial - as above with a name attribute – you could use it like so:
**partials/input.html**  
```html
<input
  ...
  :name="name"
  value="{{ field:old }}"
  ...
>
```
**Template**
```
{{ partial:input
  name="email"
}}
```

### Error

The `{{ field:error }}` tag allows you to return the error associated with the field.

#### Parameters

##### src

`src` is used to define the form field you want to get the data back for. In a partial - as above with a name attribute – you could use it like so:
**partials/input.html**  
```html
<input
  ...
>
<span class="error">{{ field:error :src="name" }}</span>
```
**Template**
```
{{ partial:input
  name="email"
}}
```

### HasError

Has error allows you to conditionally check whether the form field has an error and apply conditional logic – such as a class or the markup for the error container – to the template.

E.g.
```html
{{ if { field:hasError :src='name' } == true }}
  <span class="mt-2 text-sm text-red-700">{{ field:error :src="name" }}</span>
{{ endif }}
```

## Authors
Ben Furfie
Max Solomon

## Copyright

&copy; 2020 IMImobile PLC | All Rights Reserved

## License

MIT