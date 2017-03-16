<?php

require_once SBF_POST_SCRAPPER_DIR.'/includes/vendors/simple_html_dom/simple_html_dom.php';

class SBF_PostScrapper_PostList
{
    private $post_urls;
    private $next_url;
    private $content;

    public function __construct($options, $content)
    {
        $this->options = $options;
        $this->content = $content;
    }

    public function set_content($content)
    {
        $this->content = $content;
    }

    public function configure()
    {
        $this->post_urls = array();
        $content = $this->content;
        preg_match("/<body[^>]*>(.*?)<\/body>/is", $content, $matches);
        if (count($matches) < 2) {
            return;
        }
        $content = $matches[1];
        $html = str_get_html($content);

        if ($html == null) {
            return;
        }

        $html_container = $html->find('div.index-page', 0);
        if ($html_container === null) {
            return;
        }

        $html_post_links = $html_container->find('section .fc-item__container .fc-item__content a');
        foreach ($html_post_links as $html_post_link) {
            $this->post_urls[] = $html_post_link->getAttribute('href');
        }

        $this->next_url = false;
        $html_next_url = $html_container->find('.fc-container__pagination .pagination__list [rel=next]', 0);

        if ($html_next_url !== null) {
            $this->next_url = $html_next_url->getAttribute('href');
        }
    }

    public function get_post_urls()
    {
        return $this->post_urls;
    }

    public function get_next_url()
    {
        return $this->next_url;
    }
}
