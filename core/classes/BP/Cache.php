<?php

namespace BP;

class Cache extends Theme
{

    protected $expire_time = 60 * 60;

    /**
     * Cache an HTML response on the filesystem
     * so Boilerplate doesn't have to run next time
     *
     * @param string $path Currently loaded path
     * @param string $html HTML to cache
     */
    public function cache($path, $html)
    {
        $cache_path = sprintf('_cache/%s.html', $this->get_template_name($path));
        return @file_put_contents($cache_path, $html);
    }

    /**
     * Get Cached HTML
     *
     * @param string $path Currently loaded path
     */
    public function get_cached_html($path)
    {
        // Template file
        $template_path = sprintf('%s/%s', '_templates', $this->load($path));

        $template = $this->get_template_name($path);
        $static_path = sprintf('%s/%s.html', '_cache', $template);

        // Found a static file
        if (is_readable($static_path)) {
            $static_created_at = filemtime($static_path);
            $template_modified_at = filemtime($template_path);
            if (time() - $static_created_at < $this->expire_time && $static_created_at > $template_modified_at) {
                exit(file_get_contents($static_path));
            }
        }
    }

    public function get_template_name($path)
    {
        $template = $this->load($path);
        return str_replace($this->get_ext(), '', $template);
    }
}
