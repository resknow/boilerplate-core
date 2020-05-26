<?php

namespace BP;

use Exception;

class Filters
{

    /**
     * Instance
     *
     * Instance of Filters
     */
    private static $instance;

    /**
     * Hooks
     *
     * Array of active hooks
     */
    private $hooks = [];

    /**
     * Filters
     *
     * Array of filters
     */
    private $filters = [];

    /**
     * Construct
     */
    private function __construct()
    {
    }

    /**
     * Clone
     */
    private function __clone()
    {
    }

    /**
     * Add Hook
     */
    public function add_hook($name)
    {
        if (!$this->hook_exists($name)) {
            $this->hooks[] = $name;
        }
    }

    /**
     * Hook Exists
     */
    public function hook_exists($name)
    {
        return in_array($name, $this->hooks);
    }

    /**
     * Add Filter
     */
    public function add_filter($hook, $callback, $priority = 10)
    {

        // Check Hook Exists
        if (!$this->hook_exists($hook)) {
            $this->add_hook($hook);
        }

        // Check $priority is a integer
        if (!is_int($priority)) {
            throw new Exception('<code>$priority</code> must be a valid integer.');
        }

        ############################

        // Create Filter Array
        $this->filters[$hook][] = [
            'priority'  => $priority,
            'callback'  => $callback
        ];
    }

    /**
     * Filter Exists
     */
    public function filter_exists($hook, $name)
    {
        return isset($this->filters[$hook][$name]);
    }

    /**
     * Apply Filters
     */
    public function apply_filters($hook, $input = null)
    {

        // Check for active filters
        if (empty($this->filters[$hook])) {
            return $input;
        }

        // Get Filters
        $filters = $this->filters[$hook];

        // Sort Filters by Priority
        $filters = $this->sort_filters($filters);

        // Apply Each Filter
        foreach ($filters as $filter) {
            $input = $this->apply_filter(
                $hook,
                $filter['callback'],
                $input
            );
        }

        // Return Filtered Result
        return $input;
    }

    /**
     * Apply Filter
     */
    public function apply_filter($hook, $callback, $input)
    {

        /**
         * Helper to allow filters to automatically
         * return a boolean value instead of having
         * to write a function to return the value
         *
         * e.g. add_filter( 'render', false );
         */
        if (is_bool($callback)) {
            return $callback;
        }

        // Check Filter Function Exists
        if (!is_callable($callback)) {
            throw new Exception('Invalid callback.');
        }

        ############################

        $input = call_user_func($callback, $input);
        return $input;
    }

    /**
     * Sort Filters
     */
    protected function sort_filters(array $filters)
    {
        usort($filters, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? -1 : 1;
        });

        return $filters;
    }

    /**
     * Get Instance
     */
    public static function get_instance()
    {

        if (self::$instance === null) {
            self::$instance = new Filters();
        }

        return self::$instance;
    }

    /**
     * Add Filter [Static]
     *
     * @since 1.5.4
     */
    public static function add($hook, $name, $priority = 10)
    {
        $i = self::get_instance();
        $i->add_filter($hook, $name, $priority);
    }

    /**
     * Apply Filters [Static]
     *
     * @since 1.5.4
     */
    public static function apply($hook, $name)
    {
        $i = self::get_instance();
        return $i->apply_filters($hook, $name);
    }
}
