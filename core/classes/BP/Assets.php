<?php

namespace BP;

use Exception;

class Assets {

    protected $config = [];
    protected $assets = [];
    protected $protected; // Whether assets are protected
    protected $locked; // Whether assets are locked
    protected $path;
    protected $library = []; // Library of assets

    /**
     * Construct
     */
    public function __construct( array $args = array() ) {

        // Set defaults
        $defaults = [
            'protected' => false,
            'locked' => false,
            'load_theme_assets' => true,
            'append_version_tag' => true
        ];

        // Merge $args
        $this->config = array_merge($defaults, $args);

        /**
         * When the instance is protected, it
         * means that if an asset is added
         * with an existing ID, an Exception
         * will be thrown.
         */
        $this->protect($this->config['protected']);

        /**
         * When the instance is locked, assets
         * cannot be removed or added, so any
         * calls to add or remove will throw
         * an Exception
         */
        $this->lock($this->config['locked']);

        // Get current path
        $this->path = get('page.path');

        // Load assets from theme config
        if ( $this->config['load_theme_assets'] === true ) {
            $this->load_theme_assets();
        }

    }

    /**
     * Get
     *
     * Use property names to access assets arrays
     *
     * @param $type (string) Asset type
     */
    public function __get( $type ) {
        if ( ! array_key_exists($type, $this->assets) ) {
            return $this->assets[$type];
        }
    }

    /**
     * Load Site Assets
     * @changed 2.6.0
     */
    protected function load_theme_assets() {

        // Get Stylesheets
        if ( $stylesheets = get('site.stylesheets') ) {
            foreach ( $stylesheets as $name => $path ) {
                $this->add_asset( 'stylesheet', $name, $this->get_assets_dir($path) );
            }
        }

        // Get Scripts
        if ( $scripts = get('site.scripts') ) {
            foreach ( $scripts as $name => $path ) {
                $this->add_asset( 'script', $name, $this->get_assets_dir($path) );
            }
        }

        // Get Library Stylesheets
        if ( $library = get('site.library.stylesheets') ) {
            foreach ( $library as $name => $path ) {
                $this->add_library_asset( 'stylesheet', $name, $path );
            }
        }

        // Get Library scripts
        if ( $library = get('site.library.scripts') ) {
            foreach ( $library as $name => $path ) {
                $this->add_library_asset( 'script', $name, $path );
            }
        }

    }

    /**
     * Get Assets Dir
     *
     * Replace ~ with assets directory location
     * when loading theme assets.
     *
     * @param $path (string) Path to replace
     */
    protected function get_assets_dir( $path ) {

        if ( substr($path, 0, 1) === '~' ) {
            return substr_replace($path, assets_dir(), 0, 1);
        }

        return $path;

    }

    /**
     * Protected
     *
     * Put this instance in to a protected state
     *
     * @param $state (bool) true/false
     */
    public function protect( $state ) {

        if ( !is_bool($state) ) {
            throw new Exception('Assets: Invalid protection state.');
        }

        $this->protected = $state;
    }

    /**
     * Is Protected
     */
    public function is_protected() {
        return $this->protected;
    }

    /**
     * Lock
     *
     * Put this instance in to a locked state
     *
     * @param $state (bool) true/false
     */
    public function lock( $state ) {

        if ( !is_bool($state) ) {
            throw new Exception('Assets: Invalid lock state.');
        }

        $this->locked = $state;

    }

    /**
     * Is Locked
     */
    public function is_locked() {
        return $this->locked;
    }

    /**
     * Add Asset
     *
     * @param $type (string) Asset type (script/stylesheet)
     * @param $id (string) Asset ID
     * @param $path (string) Path to asset
     * @param $paths (array) Array of paths to load this asset on
     */
    public function add_asset( $type, $id, $path, array $paths = array() ) {

        // Check $paths array
        if ( !empty($paths) ) {
            if ( in_array($this->path, $paths) ) {
                return $this->queue_asset( $type, $id, $path );
            } else {
                return false;
            }
        }

        // Queue for all paths
        return $this->queue_asset( $type, $id, $path );

    }

    /**
     * Add Assets
     *
     * Add an array of assets
     *
     * @param $type (string) Asset type
     * @param $assets (array) Array of assets to load
     */
    public function add_assets( $type, array $assets ) {

        // Check it's not empty
        if ( empty($assets) ) {
            return false;
        }

        // Add Assets
        foreach ( $assets as $id => $path ) {
            $this->add_asset( $type, $id, $path );
        }

    }

    /**
     * Queue Asset
     *
     * Takes an asset from add_asset() and adds
     * it to the respective queue
     */
    protected function queue_asset( $type, $id, $path ) {

        // Check if this instance is locked
        if ( $this->is_locked() ) {
            throw new Exception('Unable to add asset, Assets instance is locked.');
        }

        // If protected, check ID does not exist
        if ( $this->is_protected() && array_key_exists($id, $this->$type) ) {
            throw new Exception('Unable to overwrite existing asset, Assets instance is protected.');
        }

        // Get asset
        $asset = $this->get_assets_dir($path);

        // Append Version Tag
        if ( $this->config['append_version_tag'] === true ) {
            $asset = $this->append_version_tag($asset);
        }

        // Queue asset
        $this->assets[$type][$id] = $asset;

    }

    /**
     * Append Version Tag
     *
     * Adds a version tag to the asset path
     */
    private function append_version_tag( $asset ) {

        // Check for existing params
        $params = strstr($asset, '?');
        $filename = strstr($asset, '?', true) ?: $asset;
        $paramsep = ( $params ? '&' : '?' );
        $filepath = ROOT_DIR . $filename;

        // Get last modified timestamp
        $tag = ( file_exists($filepath) ? filemtime($filepath) : false );

        // If we have timestamp, add a tag
        if ( $tag ) {
            return sprintf('%s%s_bpv=%s', $asset, $paramsep, $tag);
        }

        // Or pass it back as it was
        return $asset;

    }

    /**
     * Remove Asset
     *
     * Remove an asset from a queue
     */
    public function remove_asset( $type, $id, array $paths = array() ) {

        // Check if this instance is locked
        if ( $this->is_locked() ) {
            throw new Exception('Unable to remove asset, Assets instance is locked.');
        }

        // Conditionally remove based on path
        if ( !empty($paths) ) {
            if ( in_array($this->path, $paths) ) {
                return $this->unset_asset( $type, $id );
            } else {
                return false;
            }
        }

        // Remove Asset
        return $this->unset_asset( $type, $id );

    }

    /**
     * Unset Asset
     */
    protected function unset_asset( $type, $id ) {

        if ( $this->asset_exists($type, $id) ) {
            unset($this->assets[$type][$id]);
        }

    }

    /**
     * Get Assets
     *
     * @param $type (string) Asset type
     */
    public function get_assets( $type ) {

        // Check if type exists
        if ( !$this->type_exists($type) ) {
            return false;
        }

        return $this->assets[$type];
    }

    /**
     * Type Exists
     *
     * @param $type (string) Asset type to check
     */
    public function type_exists( $type ) {
        return array_key_exists($type, $this->assets);
    }

    /**
     * Asset Exists
     *
     * @param $type (string) Asset type
     * @param $id (string) Asset ID
     */
    public function asset_exists( $type, $id ) {

        // Check Type exists
        if ( !$this->type_exists($type) ) {
            return false;
        }

        return array_key_exists( $id, $this->assets[$type] );

    }

    /**
     * Add Library Asset
     *
     * @since 2.6.0
     * @param string $type script or stylesheet
     * @param string $id Asset ID
     * @param string $path Path to asset
     */
    public function add_library_asset( $type, $id, $path ) {

        // Validate
        if ( !is_string($type) ) {
            throw new Exception( 'Type must be a string' );
        }

        if ( !is_string($id) ) {
            throw new Exception( 'ID must be a string' );
        }

        if ( !is_string($path) ) {
            throw new Exception( 'Path must be a string' );
        }

        // Add to library
        $this->library[$type][$id] = $path;

    }

    /**
     * Use Library Asset
     *
     * Add a library asset to the queue
     * @since 2.6.0
     * @param string $type
     * @param string $id
     */
    public function use_library_asset( $type, $id ) {

        // Validate
        if ( !is_string($id) ) {
            throw new Exception( 'ID must be a string' );
        }

        // Check the asset exists
        if ( $this->library_asset_exists( $type, $id ) ) {
            $this->add_asset( $type, $id, $this->library[$type][$id] );
        }

    }

    /**
     * Library Asset Exists
     *
     * @param $type (string) Asset type
     * @param $id (string) Asset ID
     */
    public function library_asset_exists( $type, $id ) {

        // Check the type exists
        if ( !array_key_exists( $type, $this->library ) ) {
            return false;
        }

        return array_key_exists( $id, $this->library[$type] );
    }

}
