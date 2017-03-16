<?php

define('SBF_POST_SCRAPPER_OPTION_DEFAULT_POSTS_NUM', 20);
define('SBF_POST_SCRAPPER_OPTION_DEFAULT_SCRAPPING_URL', 'https://www.theguardian.com/world/natural-disasters/');

class SBF_PostScrapper_Options
{
    private $options;
    public $option_group;
    public $slug;

    public function __construct()
    {
        $this->slug = 'sbf-post-scrapper';
        $this->option_group = 'sbf-post-scrapper-option';
        $this->default_options = array(
            'scrapping_url' => SBF_POST_SCRAPPER_OPTION_DEFAULT_SCRAPPING_URL,
            'posts_num' => SBF_POST_SCRAPPER_OPTION_DEFAULT_POSTS_NUM,
        );
        $this->pull_options();
    }

    private function create_options()
    {
        update_option($this->slug, $this->default_options);

        return $this->default_options;
    }

    public function get_option($key = '', $default_value = false)
    {
        if (empty($key)) {
            return $this->options;
        }

        return isset($this->options[ $key ]) ? $this->options[ $key ] : $default_value;
    }

    public function set_option($key, $value)
    {
        $this->$options[$key] = $value();
        if ($options_updated) {
            $this->data = $options;
        }
    }

    public function get_options()
    {
        return $this->options;
    }

    public function set_options($new_options)
    {
        foreach ($new_options as $key => $value) {
            $options[$key] = $value;
        }
    }

    public function update_options()
    {
        $this->push_options();
        $this->pull_options();
    }

    private function push_options()
    {
        $options_updated = update_option($this->slug, $this->options);
    }

    private function pull_options()
    {
        $options = get_option($this->slug, array());
        if (empty($options)) {
            $options = $this->create_options();
        } else {
            $options = array_merge($this->default_options, $options);
        }
        $this->options = $options;
    }
}
