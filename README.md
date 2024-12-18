
# EloquentSQL

EloquentSQL is a Laravel package that generates raw SQL insert queries from Eloquent model records. This package makes it easy to convert your Eloquent models into raw SQL insert statements, which can be useful for debugging, logging, exporting data, or batch insert operations.

## 🚀 Features

- Converts Eloquent model data into raw SQL insert statements
- Ability to exclude / include specific columns
- Easy to use with any Laravel Eloquent model
- Ideal for debugging, logging, or data migration

## 📦 Requirements

- PHP 7.3 or higher
- Laravel 8.x or higher

## 🛠 Installation

You can install the package via Composer:

```bash
composer require khalidmh/eloquent-sql
```

## 📦 Usage

### Generating Insert Query for a Model

```PHP

use KhalidMh\EloquentSQL\EloquentSQL;
use App\Models\User;

$user = User::find(1);
$sql = EloquentSQL::set($user)->toQuery();

// Output: INSERT INTO `users` (`id`, `name`, `email`, ...) VALUES (1, 'John Doe', 'john@example.com', ...);
````

### Excluding Columns

### Specify which columns to be excluded from the insert query

```PHP

use KhalidMh\EloquentSQL\EloquentSQL;
use App\Models\User;

$user = User::find(1);

$sql = EloquentSQL::set($user)
        ->except(['id', 'created_at', 'updated_at'])
        ->toQuery();


// Output: INSERT INTO `users` (`name`, `email`, ...) VALUES ('John Doe', 'john@example.com', ...);
```

### Including Columns

### Specify which columns to be in the insert query

```PHP

use KhalidMh\EloquentSQL\EloquentSQL;
use App\Models\User;

$user = User::find(1);

$sql = EloquentSQL::set($user)
        ->only(['name', 'email'])
        ->toQuery();


// Output: INSERT INTO `users` (`name`, `email`) VALUES ('John Doe', 'johnjohn@example.com');
```

### Including hidden model attributes

### By default Laravel removes hidden attributes from the Model based on the $hidden property, you can use the includeHidden() to add them to the insert query without updating the Model's $hidden property globaly.

```PHP

use KhalidMh\EloquentSQL\EloquentSQL;
use App\Models\User;

$user = User::find(1);

$sql = EloquentSQL::set($user)
        ->includeHidden()
        ->only(['password', 'remember_token'])
        ->toQuery();

// Output: INSERT INTO `users` (`password`, `remember_token`) VALUES ('password', '8zfuGf0f....');
```

## ⚙️ Configuration

No additional configuration is required. EloquentSQL works out-of-the-box with your existing Eloquent models.

## 📃 License

EloquentSQL is open-source software licensed under the [MIT license](LICENSE).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.
