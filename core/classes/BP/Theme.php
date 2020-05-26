<?php

namespace BP;

use Exception;

class Theme
{

    /**
     * Template directory
     */
    protected $dir;

    /**
     * Template file extension
     */
    protected $ext;

    /**
     * Default file
     * For templates would be a 404
     * or for logic, could be a simple
     * file for setting default
     * code for every page
     */
    protected $default;

    /**
     * Index
     *
     * Splits up path names by / so we can search
     * directories to matching files
     */
    protected $index;

    /**
     * Loaded Template
     *
     * Stores the path of the last loaded
     * template file - helpful for debugging
     */
    protected $loaded_template;

    /**
     * Render Function
     *
     * Custom render function, only used if callable
     */
    protected $render_function = null;

    /**
     * Construct
     *
     * @param string $dir Theme directory
     */
    public function __construct($dir, $ext = '.php', $default = '404')
    {

        // Default config
        $this->dir          = $dir;         # Template directory
        $this->ext          = $ext;         # Template file extension
        $this->default      = $default;     # Default template name

    }

    /**
     * Render
     *
     * @param string $template Template to render
     * @param array $variables Variables to pass to the template
     * @return string Rendered template
     */
    public function render($template, array $variables)
    {

        // Check for a custom render function
        if (is_callable($this->render_function)) {

            /**
             * Call the custom render function
             *
             * Passed to the function:
             * @param array $variables Variables in this scope
             * @param string $template The loaded template
             */

            // Remove the file extension
            $template = str_replace($this->ext, '', $template);

            return call_user_func_array($this->render_function, [$variables, $template, $this]);
        }

        // Start Output Buffering
        ob_start();

        // Include Template File
        include $this->dir . '/' . $template;

        // Render Output
        return ob_get_clean();
    }

    /**
     * Load Template
     *
     * @param string $name Template to search for
     * @param bool $file Is a full filename
     * @return string Matched template name
     */
    public function load($name, $file = false)
    {

        // Setup Index
        $this->index  = explode('/', $name);

        /**
         * Find required template
         */
        switch (true) {

                /**
             * If specific file is
             * entered, attempt
             * to load it.
             */
            case $file !== false && file_exists($this->dir . '/' . $file):
                $template = $file;
                break;

                /**
                 * If template with
                 * specified name exists,
                 * attempt to load it.
                 */
            case $file === false && file_exists($this->dir . '/' . $name . $this->ext):
                $template = $name . $this->ext;
                break;

                /**
                 * If we find an index.php
                 * in a sub folder, load it.
                 */
            case $file === false && file_exists($this->dir . '/' . $name . '/index' . $this->ext):
                $template = $name . '/index' . $this->ext;
                break;

                /**
                 * If template
                 * where dashes replace
                 * slashes exists, attempt
                 * to load it.
                 */
            case $file === false && file_exists($this->dir . '/' . str_replace('/', '-', $name) . $this->ext):
                $template = str_replace('/', '-', $name) . $this->ext;
                break;

                /**
                 * Look for parent
                 * templates in real
                 * folders
                 */
            case $file === false && !file_exists($this->dir . '/' . $name . $this->ext):
                $template = $this->find_template();
                break;

                /**
                 * Finally, look for
                 * parent templates
                 * within naming convention
                 *
                 * e.g.: find-this-file.php
                 */
            case $file === false && !file_exists($this->dir . '/' . str_replace('/', '-', $name) . $this->ext):
                $template = $this->find_template(true);
                break;
        }

        /**
         * Return it
         */
        if ($template !== false && is_readable($this->dir . '/' . $template)) {
            $this->loaded_template = $template;
            return $template;
        }
    }

    /**
     * Find Template
     *
     * @param bool $replace Whether to replace / with -
     * @return string Matched template
     */
    protected function find_template($replace = false)
    {

        $implode = ($replace === false ? '/' : '-');

        $index_count = count($this->index) - 1;
        $index = $this->index;

        while ($index) {

            // Remove last index
            unset($index[$index_count]);

            // Generate filename
            $file = implode($implode, $index) . $this->ext;

            // If we found a match, use it
            if (file_exists($this->dir . '/' . $file)) {
                $template = $file;
                break;
            }

            // If not, move on to the next one
            $index_count--;
        }

        // Return template
        if (isset($template)) {
            return $template;
        }

        return $this->default . $this->ext;
    }

    /**
     * Get Partial
     *
     * @param string $part Name of partial
     * @param mixed $context (optional) Context
     */
    public function get_partial($part, $context = false)
    {

        // Get Partial
        $partial = 'partials/' . $part . $this->ext;

        // Return false if not found
        if (!is_readable($this->dir . '/' . $partial)) {
            return false;
        }

        include $this->dir . '/' . $partial;
    }

    /**
     * Get Dir
     *
     * @return string Theme directory
     */
    public function get_dir()
    {
        return $this->dir;
    }

    /**
     * Get Ext
     *
     * @return string Theme file extension
     */
    public function get_ext()
    {
        return $this->ext;
    }

    /**
     * Get Loaded Template
     *
     * @return string Path to the loaded template
     */
    public function get_loaded_template()
    {
        return $this->loaded_template;
    }

    /**
     * Register Render Function
     *
     * @param closure $function
     */
    public function register_render_function($function)
    {

        // Make sure if callable
        if (!is_callable($function)) {
            throw new Exception('Custom render function must be callable.');
        }

        // Register it!
        $this->render_function = $function;
    }

    /**
     * Set Dir
     *
     * @param string $dir Theme directory
     */
    public function set_dir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * Set Ext
     *
     * @param string $ext Theme file extension
     */
    public function set_ext($ext)
    {
        $this->ext = $ext;
    }
}
