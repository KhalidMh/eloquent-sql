
# EloquentSQL

EloquentSQL is a Laravel package that generates raw SQL insert queries from Eloquent model records. This package makes it easy to convert your Eloquent models into raw SQL insert statements, which can be useful for debugging, logging, exporting data, or batch insert operations.

## 🚀 Features

- Converts Eloquent model data into raw SQL insert statements
- Supports single or multiple records
- Easy to use with any Laravel Eloquent model
- Ideal for debugging, logging, or data migration

## 🛠 Installation

You can install the package via Composer:

```bash
composer require your-vendor/EloquentSQL
```

## 📦 Usage

### Generating Insert Query for a Single Model Record

```PHP

use YourVendor\EloquentSQL\Facades\EloquentSQL;
use App\Models\User;

$user = User::find(1);
$sql = EloquentSQL::generate($user);

echo $sql; 
// Output: INSERT INTO `users` (`id`, `name`, `email`, ...) VALUES (1, 'John Doe', 'john@example.com', ...);
````

### Generating Insert Query for Multiple Model Records

```PHP
$users = User::where('status', 'active')->get();
$sql = EloquentSQL::generate($users);

echo $sql;
/*
Output: 
INSERT INTO `users` (`id`, `name`, `email`, ...) VALUES (1, 'John Doe', 'john@example.com', ...),
                                           (2, 'Jane Doe', 'jane@example.com', ...);
*/
```

## ⚙️ Configuration

No additional configuration is required. EloquentSQL works out-of-the-box with your existing Eloquent models.

## 📚 Advanced Usage

### Customizing Columns

If you need to generate an insert query with specific columns, you can pass an array of columns as a second parameter:

```PHP
$sql = EloquentSQL::generate($user, ['id', 'name', 'email']);
```

### Ignoring Timestamps

By default, `created_at` and `updated_at` timestamps will be included if they exist in your model. You can ignore these columns by passing a third parameter:

```PHP
$sql = EloquentSQL::generate($user, ['id', 'name', 'email'], true);
```

## 📃 License

EloquentSQL is open-source software licensed under the [MIT license](LICENSE).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.

## 🙏 Acknowledgements

Inspired by the need to streamline database operations and simplify data migration tasks in Laravel.
