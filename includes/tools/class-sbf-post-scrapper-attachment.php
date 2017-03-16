<?php

require_once SBF_POST_SCRAPPER_DIR.'includes/vendors/simple_html_dom/simple_html_dom.php';

class SBF_PostScrapper_Attachment
{
    private $options;
    private $html_attachment;
    private $data;

    public function __construct($options, $html_attachment)
    {
        $this->options = $options;
        $this->html_attachment = $html_attachment;
    }

    public function configure()
    {
        $html_attachment = $this->html_attachment;
        $data = array();
        $class = $html_attachment->getAttribute('class');
        $pos = strpos($class, 'image');
        if (strpos($class, 'image')) {
            $html_attachment_url = $html_attachment->find('picture source', 0);
            $attachment_url = $html_attachment_url->getAttribute('srcset');
            $data['attachment_type'] = 'video';
        } elseif (strpos($class, 'element-video')) {
            $html_attachment_url = $html_attachment->find('picture source', 0);
            $attachment_url = $html_attachment_url->getAttribute('srcset');
            $data['attachment_type'] = 'image';
        } else {
            return false;
        }

        $data['attachment_url'] = $attachment_url;

        $html_caption = $html_attachment->find('figcaption');
        if ($html_caption !== null) {
            $data['caption'] = $html_caption->innertext;
        }

        if (!$this->download()) {
            return false;
        }

        return $this->add();
    }

    private function download()
    {
        $url = $data['attachment_url'];
        $path = parse_url($url, PHP_URL_PATH);
        $filename = basename($path);
        $download_path = SBF_POST_SCRAPPER_UPLOAD_TEMP_DIR.$filename;
        $curl = new Curl();
        if (!$curl->download($url, $download_path)) {
            return false;
        }
        $data['attachment_downloaded'] = $download_path;

        return true;
    }

    private function add()
    {
        $filename = basename($data['attachment_downloaded']);
        $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
        $parent_post_id = 0;
        if (!$upload_file['error']) {
            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_parent' => $parent_post_id,
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                'post_content' => '',
                'post_status' => 'inherit',
            );
            $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $parent_post_id);
            if (!is_wp_error($attachment_id)) {
                require_once ABSPATH.'wp-admin'.'/includes/image.php';
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id,  $attachment_data);
                $data['attachment_id'] = $attachment_id;
                $this->data = $data;

                return true;
            }
        }

        return false;
    }

    public function get_data()
    {
        return $this->data;
    }
}
