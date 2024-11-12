<?php

namespace KhalidMh\EloquentSql;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

class EloquentSQL
{
    private string $table;
    private array $columns = ['*'];
    private array $originalAttributes = [];

    /**
     * Create a new instance of the EloquentSQL class.
     *
     * This method initializes a new instance of the EloquentSQL class with the provided Eloquent model.
     *
     * @param Model $model The Eloquent model instance.
     *
     * @return self A new instance of the EloquentSQL class.
     */
    public static function setModel(Model $model): self
    {
        $instance = new self();
        $instance->originalAttributes = $model->getAttributes();
        $instance->table = $model->getTable();

        return $instance;
    }

    /**
     * Generates an SQL insert query string for the model.
     *
     * @return string The generated SQL insert query string.
     */
    public function toQuery(): string
    {
        return $this->buildQuery(
            table: $this->table,
            columns: $this->getAttributesNames(),
            values: $this->getAttributesValues()
        );
    }

    /**
     * Retrieve attributes names that should be in the query.
     *
     * @return array An array of attributes names.
     */
    public function getAttributesNames(): array
    {
        $attributes = array_keys($this->originalAttributes);

        if ($this->columns[0] === '*') {
            return $attributes;
        }

        return $this->columns;
    }

    /**
     * Retrieve the values of the model's attributes.
     *
     * @return array An array of column values.
     */
    public function getAttributesValues(): array
    {
        return collect($this->originalAttributes)
            ->filter(fn ($value, $key) => in_array($key, $this->getAttributesNames()))
            ->toArray();
    }

    /**
     * Generate the SQL query.
     *
     * @param string $table The table name.
     * @param array $columns The columns to include in the query.
     * @param array $values The values to insert into the table.
     *
     * @return string The generated SQL query.
     */
    private function buildQuery(string $table, array $columns, array $values): string
    {
        $columns = $this->formatColumns($columns);
        $values = $this->formatValues($values);

        return "INSERT INTO `$table` ($columns) VALUES ($values);";
    }

    /**
     * Format columns values to be inserted to the database.
     *
     * @param array $values The array of values to format.
     * @return string The formatted string of values.
     */
    private function formatValues(array $values): string
    {
        return collect($values)->map(function ($value) {
            if (is_null($value)) {
                return 'NULL';
            }

            if (is_string($value)) {
                return '"' . addslashes($value) . '"';
            }

            if ($value instanceof \DateTime) {
                return $value->format('Y-m-d H:i:s');
            }

            return $value;
        })->implode(', ');
    }

    /**
     * Formats an array of column names into a string suitable for SQL queries.
     *
     * This method takes an array of column names and converts it into a single string
     * where each column name is enclosed in backticks and separated by commas.
     *
     * @param array $columns An array of column names to be formatted.
     * @return string A formatted string of column names for SQL queries.
     */
    private function formatColumns(array $columns): string
    {
        $columns = implode(', ', $columns);
        $columns = "`" . str_replace(", ", "`, `", $columns) . "`";

        return $columns;
    }

    /**
     * @param array $columns
     * @return $this
     *
     * Specify the columns that you want to include in the query.
     */
    public function only(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     *
     * Specify the columns that you want to exclude from the query.
     */
    public function except(array $excluded): self
    {
        $original = array_keys($this->originalAttributes);
        $diff = array_diff($original, $excluded);

        $this->columns = array_values($diff);

        return $this;
    }
}
