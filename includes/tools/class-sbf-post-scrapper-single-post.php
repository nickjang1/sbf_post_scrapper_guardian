<?php

require_once SBF_POST_SCRAPPER_DIR.'includes/vendors/simple_html_dom/simple_html_dom.php';
require_once SBF_POST_SCRAPPER_DIR.'includes/tools/class-sbf-post-scrapper-attachment.php';

class SBF_PostScrapper_SinglePost
{
    private $content;
    private $data;
    private $featured_attachment;
    private $attachments;
    private $options;
    private $duplicate;

    public function __construct($options, $content)
    {
        $this->options = $options;
        $this->content = $content;
        $this->duplicate = false;
    }

    public function set_content($content)
    {
        $this->content = $content;
    }

    public function configure()
    {
        $data = array();

        $content = $this->content;
        preg_match("/<body[^>]*>(.*?)<\/body>/is", $content, $matches);
        if (count($matches) < 2) {
            return;
        }
        $content = $matches[1];

        $html = str_get_html($content);
        $html_container = $html->find('#article', 0);

        $post_title = '';
        $post_content = '';
        $post_date = new DateTime();

        // get post title
        $html_title = $html_container->find('header h1', 0);
        if ($html_title !== null) {
            $post_title = $html_title->innertext;
            $data['post_title'] = $post_title;
        }

        // get publish date
        $html_post_date = $html_container->find('.js-content-meta time[itemprop="datePublished"]', 0);
        if ($html_post_date !== null) {
            $post_date_timestamp = (int) (((int) $html_post_date->getAttribute('data-timestamp')) / 1000);
            $post_date->setTimestamp($post_date_timestamp);
        }

        $post_date = $post_date->format('Y-m-d H:i:s');
        $data['post_date'] = $post_date;

        // check if post is duplicated
        global $wpdb;
        $query = "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND publish_date=%s";
        $post_id = $wpdb->get_var($wpdb->prepare($query, $post_title, $post_date));
        if ($post_id !== null) {
            $this->duplicate = true;

            return false;
        }

        // get post content
        $html_content = $html_container->find('.content__main-column--article .content__article-body', 0);

        // aside html remove
        $html_asides = $html_content->find('aside');
        foreach ($html_asides as $html_aside) {
            $html_aside->outertext = '';
        }

        // get media
        $html_attachments = $html_content->find('figure');
        foreach ($html_attachments as $html_attachment) {
            $attachment = new SBF_PostScrapper_Attachment($this->options, $html_attachment);
            if ($attachment->configure()) {
                $attachment_data = $attachment->get_data();
                $attachment_id = $attachment_data['attachment_id'];
                $attachment_html = sprintf('<figure><picture>%s</picture>', wp_get_attachment_image($attachment_id));
                if (isset($attachment_data['caption'])) {
                    $attachment_html = sprintf('%s<caption>%s</caption>', $attachment_data['caption']);
                }
                $attachment_html = sprintf('%s</figure>');
                $html_attachment->outertext = $attachment_html;
            }
        }

        if ($html_content !== null) {
            $post_content = $html_content->innertext;
        }
        $data['post_content'] = $post_content;

        echo json_encode(array(
            'post_date' => $post_date,
            'post_title' => $post_title,
            'post_content' => $post_content,
            // 'post_status' => 'publish',
        ));

        // insert post
        $post_id = wp_insert_post(
            array(
                'post_date' => $post_date,
                'post_title' => $post_title,
                'post_content' => $post_content,
                // 'post_status' => 'publish',
            )
        );

        if ($post_id) {

            // get featured media
            $html_featured_attachment = $html_container->find('.content__main-column--article>figure', 0);
            $featured_attachment = new SBF_PostScrapper_Attachment($this->options, $html_featured_attachment);

            if ($featured_attachment->configure()) {
                $featured_attachment_data = $featured_attachment->get_data();
                $featured_attachment_id = $featured_attachment_data['attachment_id'];
                $featured_attachment_type = $featured_attachment_data['attachment_type'];

                if ($featured_attachment_type == 'image') {
                    set_post_thumbnail($postID, $attachment_id);
                } elseif ($featured_attachment_type == 'video') {
                }
            }
        }
    }

    public function get_data()
    {
        return $this->data;
    }

    public function is_duplicate()
    {
        return $this->duplicate;
    }
}
