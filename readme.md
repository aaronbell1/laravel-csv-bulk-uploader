# Laravel CSV Bulk Uploader

The bulk uploader is intended to allow users to have a simple way to validate and upload records from a CSV directly to database.

The package makes use of Laravel validators to validate the data, and Laravel DB object to commit records to the Database.

## Table of Contents  
[Installation](#installation)  
[Creating bulk uploader](#creating-bulk-uploader) 
[Usage](#usage) 

## Installation

You can install the package via composer:

```bash
composer require aaronbell1/laravel-csv-bulk-uploader
```

If you are using Laravel < 5.5, you will need to add the following to your `'providers'` array in `config/app.php`:

```php
    Aaronbell1/LaravelCsvBulkUploader/LaravelCsvBulkUploaderServiceProvider::class,
```

## Creating bulk uploader

To create a new uploader, from the command line enter:

```bash
php artisan make:uploader {name}
```

Where `{name}` is the class name e.g. 'UserBulkUploader'

This will generate the class within `app/Uploaders` folder.

There are 3 methods that are required to implement in your class:

- [`rules`](#rules)
- [`messages`](#messages)
- [`saveRow`](#saverow)

### rules

The `rules()` method represents validation rules as per a [Laravel Form Request](https://laravel.com/docs/5.8/validation#form-request-validation).

This must return an array with the rules required for a single row of your CSV data.
The field names are represented as a lowercase and underscored version of your column header value e.g. `User first name` would be `user_first_name`.

For example:

```php
return [
    'firstname' => 'required',
    'email'     => 'required|email',
    'age'       => 'integer|min:18'
];
```

### messages

The `messages()` method represents validation rule messages as per [Laravel Form Request Error Messages](https://laravel.com/docs/5.8/validation#customizing-the-error-messages).

For example, you could overwrite one of the above messages using:

```php
return [
    'firstname.required' => 'User first name is required.',
    'age.min'            => 'User age must be at least 18.'
];
```

### saveRow

The `saveRow()` method is used to define how you process each row of data returned from the CSV.

The data is passed through as an array with the keys defined as the column headers converted to lowercase with underscores.

For example using the validation from above, you can access each row of data with:

```php
$name = $row['firstname'];
$email = $row['email'];
```

## Usage

In order to make use of the package you must ensure that you are using a `.csv` file with a header row that includes a unique name for each column.

In the controller where you need to use the bulk uploader, you can either inject an instance of the class: 

```php
protected $userUploader;

public function __construct(UserBulkUploader $userUploader)
{
    $this->userUploader = $userUploader;
}
```

or create a new instance of it:

```php
public function store()
{
    $userUploader = new UserBulkUploader;
}
```

The uploader instance has the following methods:

- [`load`](#load)
- [`isValid`](#isvalid)
- [`save`](#save)
- [`redirectWithErrors`](#redirectwitherrors)

### load

The `load()` method accepts the path to the CSV file that you are working from.

```php
public function store()
{
    $userUploader = new UserBulkUploader;
    $userUploader->load('/path/to/file.csv'); 
}
```

### isValid

The `isValid()` method will use the rules as defined on the bulk uploader to check whether the CSV contains valid data.

This will return a boolean value.

```php
public function store()
{
    $userUploader = new UserBulkUploader;
    $userUploader->load('/path/to/file.csv');
    $isValid = $userUploader->isValid();
    
    if($isValid) {
      // success
    } else {
      // failure
    }
}
```

If the data is not valid, you can easily [redirect back with the errors](#redirectwitherrors) to display them to the user as you require.

### save

The `save()` method will make use of the `saveRow()` method defined on the bulk uploader to process your data.

This works as a database transaction so if it encounters any errors it will rollback the transaction and throw an appropriate exception, otherwise if successful it will be committed.

```php
public function store()
{
    $userUploader = new UserBulkUploader;
    $userUploader->load('/path/to/file.csv');
    $isValid = $userUploader->isValid();
    
    if($isValid) {
      $userUploader->save();
    } else {
      // failure
    }
}
```

### redirectWithErrors

The `redirectWithErrors()` method will make use of [Laravel Redirects](https://laravel.com/docs/5.8/redirects) to redirect the user back to the previous page with any errors stored in the session.

The method accepts the name of the data array which by default is `data`.

You can make use of these errors in your blade file using the session helper:

```php
// Controller.php
public function store()
{
    $userUploader = new UserBulkUploader;
    $userUploader->load('/path/to/file.csv');
    $isValid = $userUploader->isValid();
    
    if($isValid) {
      $userUploader->save();
    } else {
      return $userUplader->redirectWithErrors('users');
    }
}
```
```blade
// View.blade.php

// ERROR MESSAGES
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->unique() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </div>
@endif

// USERS WITH ERRORS
@foreach(session('users') as $key => $user)
    <li>{{ $user['firstname'] }}</li>
@endforeach
```