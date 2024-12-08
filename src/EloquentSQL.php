<?php

namespace KhalidMh\EloquentSQL;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class EloquentSQL
{
    /**
     * @var string The name of the database table associated with the model.
     */
    private $table;

    /**
     * @var array
     *
     * This property holds the columns to be selected in the SQL query.
     */
    private $columns = ['*'];

    /**
     * @var array
     *
     * This property indicates whether hidden columns should be included in the query.
     */
    private $includeHidden = false;

    /**
     * @var array
     *
     * The original attributes of the Eloquent model.
     */
    private $original;

    /**
     * @var array
     *
     * The hidden attributes of the Eloquent model.
     */
    private $hidden;

    /**
     * Set the model instance and initialize the EloquentSQL instance with the model's attributes and table.
     *
     * @param Model $model The Eloquent model instance to set.
     * @return self Returns an instance of the EloquentSQL class.
     */
    public static function set(Model $model): self
    {
        $instance = new self();

        $instance->original = $model->withoutRelations()->getOriginal();
        $instance->hidden = $model->getHidden();
        $instance->table = $model->getTable();

        return $instance;
    }

    /**
     * Converts the current state of the object to a SQL insert query string.
     *
     * This method builds a SQL query string using the table name,
     * attribute names, and attribute values of the current object.
     *
     * @return string The generated SQL query string.
     */
    public function toQuery(): string
    {
        return $this->buildQuery(
            $this->table,
            $this->getAttributesNames(),
            $this->getAttributesValues()
        );
    }

    /**
     * Retrieve the names of the model's attributes.
     *
     * @return array The names of the attributes.
     */
    private function getAttributesNames(): array
    {
        if ($this->columns[0] === '*') {
            $this->columns = array_keys($this->original);
        }

        if (! $this->includeHidden) {
            $this->columns = array_diff($this->columns, $this->hidden);
        }

        return $this->columns;
    }

    /**
     * Retrieve the values of the model's attributes.
     *
     * @return array The values of attributes.
     */
    private function getAttributesValues(): array
    {
        $filtred_values = array_filter(
            $this->original,
            function ($value, $name) {
                return in_array($name, $this->getAttributesNames());
            },
            ARRAY_FILTER_USE_BOTH
        );

        return array_values($filtred_values);
    }

    /**
     * Builds an SQL INSERT query string.
     *
     * This method constructs an SQL query for inserting data into a specified table.
     * It formats the column names and values before embedding them into the query string.
     *
     * @param string $table The name of the table.
     * @param array $names An array of attributes names.
     * @param array $values An array of attributes values.
     * @return string The constructed SQL INSERT query string.
     */
    private function buildQuery(string $table, array $names, array $values): string
    {
        $names = $this->formatNames($names);
        $values = $this->formatValues($values);

        return "INSERT INTO `$table` ($names) VALUES ($values);";
    }

    /**
     * Formats an array of attributes names into a comma-separated string with each name wrapped in backticks.
     *
     * @param array $columns An array of column names to be formatted.
     * @return string A string of column names separated by commas and wrapped in backticks.
     */
    private function formatNames(array $names): string
    {
        $names_string = implode(', ', $names);

        return "`" . str_replace(", ", "`, `", $names_string) . "`";
    }

    /**
     * Format an array of attributes values for SQL queries.
     *
     * @param array $values The array of attributes values to format.
     * @return string The formatted string of attributes values.
     */
    private function formatValues(array $values): string
    {
        return implode(', ', array_map(function ($value) {
            if (is_null($value)) {
                return 'NULL';
            }

            if (is_string($value)) {
                return '"' . addslashes($value) . '"';
            }

            if ($value instanceof Carbon) {
                return '"' . $value->toDateTimeString() .'"';
            }

            return $value;
        }, $values));
    }

    /**
     * Specify the columns to include in the query.
     *
     * This method allows you to specify an array of column names that should be included in the query.
     *
     * @param array $columns An array of column names to include in the query.
     * @return $this
     */
    public function only(array $columns): self
    {
        if (! empty($columns)) {
            $this->columns = $columns;
        }

        return $this;
    }

    /**
     * Exclude specific columns from the query.
     *
     * This method allows you to specify an array of column names that should be excluded from the query.
     *
     * @param array $excluded An array of column names to exclude from the query.
     * @return $this
     */
    public function except(array $excluded): self
    {
        if (! empty($excluded)) {
            $original = array_keys($this->original);
            $diff = array_diff($original, $excluded);
            $this->columns = array_values($diff);
        }

        return $this;
    }

    public function includeHidden(): self
    {
        $this->includeHidden = true;

        return $this;
    }
}
