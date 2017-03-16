<?php

require_once SBF_POST_SCRAPPER_DIR.'/includes/class-sbf-post-scrapper-options.php';
require_once SBF_POST_SCRAPPER_DIR.'/includes/vendors/php-curl-class/Curl.php';
require_once SBF_POST_SCRAPPER_DIR.'/includes/tools/class-sbf-post-scrapper-post-list.php';
require_once SBF_POST_SCRAPPER_DIR.'/includes/tools/class-sbf-post-scrapper-single-post.php';

use \Curl\Curl;

class SBF_PostScrapper_Manager
{
    private $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function start()
    {
        $next_url = $this->options->get_option('scrapping_url', false);
        $scrapper_url = $this->options->get_option('scrapping_url', SBF_POST_SCRAPPER_OPTIONS_DEFAULT_SCRAPPING_URL);
        $posts_num = $this->options->get_option('scrapping_url', SBF_POST_SCRAPPER_OPTIONS_DEFAULT_POSTS_NUM);
        $scrapped_num = 0;
        $duplicated = false;

        while ($next_url !== false && !$duplicated && $scrapped_count <= $posts_num) {
            $curl = new Curl();
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
            $curl->setOpt(CURLOPT_HEADER, false);
            $curl->setOpt(CURLOPT_USERAGENT, SBF_POST_SCRAPPER_USER_AGENT);
            $curl->setOpt(CURLOPT_TIMEOUT, 60);
            $curl->get($next_url);

            if ($curl->error) {
                return;
            }
            $content = $curl->response;
            $curl->close();
            $post_list = new SBF_PostScrapper_PostList(null, $content);
            $post_list->configure();

            $post_urls = $post_list->get_post_urls();
            foreach ($post_urls as $post_url) {
                $curl = new Curl();
                $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
                $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
                $curl->setOpt(CURLOPT_HEADER, false);
                $curl->setOpt(CURLOPT_USERAGENT, SBF_POST_SCRAPPER_USER_AGENT);
                $curl->setOpt(CURLOPT_TIMEOUT, 60);
                $curl->get($post_url);
                if ($curl->error) {
                    continue;
                }
                $content = $curl->response;

                $single_post = new SBF_PostScrapper_SinglePost(null, $content);
                $single_post->configure();

                if ($single_post->is_duplicate()) {
                    $duplicated = true;
                }
                ++$scrapped_count;
                if ($scrapped_count >= $post_num) {
                    break;
                }
            }
            $next_url = $post_list->get_next_url();
        }
    }
}
