<?php

namespace KhalidMh\EloquentSql;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class EloquentSQL
{
    /**
     * @var string The name of the database table associated with the Eloquent model.
     */
    private $table;

    /**
     * @var array
     *
     * This property holds the columns to be selected in the SQL query.
     * By default, it selects all columns ('*').
     */
    private $columns = ['*'];
    /**
     * @var array
     *
     * This property holds the original attributes of the Eloquent model.
     * It is used to keep track of the initial state of the model's attributes
     * before any changes are made.
     */
    private $original;

    /**
     * Set the model instance and initialize the EloquentSQL instance with the model's attributes and table.
     *
     * @param Model $model The Eloquent model instance to set.
     * @return self Returns an instance of the EloquentSQL class.
     */
    public static function setModel(Model $model): self
    {
        $instance = new self();

        $instance->original = $model->getOriginal();
        $instance->table = $model->getTable();

        return $instance;
    }

    /**
     * Converts the current state of the object to a SQL query string.
     *
     * This method builds a SQL query string using the table name, attribute names,
     * and attribute values of the current object.
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
            return array_keys($this->original);
        }

        return $this->columns;
    }

    /**
     * Retrieve the values of the model's attributes.
     *
     * @return array An array of column values.
     */
    private function getAttributesValues(): array
    {
        $filtred_values = array_filter(
            $this->original,
            function ($value, $name) { return in_array($name, $this->getAttributesNames()); },
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
     * @param string $table The name of the table into which data will be inserted.
     * @param array $names An array of column names to be included in the query.
     * @param array $values An array of values corresponding to the column names.
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
     * Format an array of values for SQL queries.
     *
     * This method takes an array of values and formats them into a string
     * suitable for SQL queries. It handles different types of values:
     * - NULL values are converted to the string 'NULL'.
     * - String values are escaped and enclosed in double quotes.
     * - DateTime objects are formatted to 'Y-m-d H:i:s'.
     * - Other values are returned as-is.
     *
     * @param array $values The array of values to format.
     * @return string The formatted string of values.
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

            if ($value instanceof DateTime) {
                return $value->toDateTimeString();
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
}
