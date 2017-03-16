<?php
    $options = $this->options->get_options();
    $option_group = $this->options->option_group;
    $option_slug = $this->options->slug;
?>
<div class="wrap">
    <h1><?php _e('Softbladefor Post Scrapper Settings', 'sbf_post_scrapper'); ?></h1>
    <form method="post" action="<?php echo admin_url('options.php'); ?>">
        <?php settings_fields( $option_group ); ?>
        <?php do_settings_sections( $option_slug ); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="sbf_post_scrapper_url"><?php _e('Scrapping URL', 'sbf_post_scrapper'); ?></label></th>
                    <td>
                    	<input type="text" name="<?php echo "{$option_slug}[scrapping_url]";?>" id="sbf_post_scrapper_url" class="regular-text ltr" value="<?php echo isset($options['scrapping_url']) ? $options['scrapping_url'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="sbf_post_scrapper_posts_num"><?php _e('Scrapping Posts Count', 'sbf_post_scrapper'); ?></label></th>
                    <td>
                    	<input type="text" name="<?php echo "{$option_slug}[posts_num]";?>" id="sbf_post_scrapper_posts_num" class="regular-text ltr" value="<?php echo isset($options['posts_num']) ? $options['posts_num'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="sbf_post_scrapper_schedule"><?php _e('Scrapping Schedule', 'sbf_post_scrapper'); ?></label></th>
                    <td>
                    	<input type="text" name="<?php echo "{$option_slug}[schedule]";?>" id="sbf_post_scrapper_posts_num" class="regular-text ltr" value="<?php echo isset($options['schedule']) ? $options['schedule'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <div class="button-container">
                            <span class="spinner"></span>
                            <input id="sbf_post_scrapper_do_scrapping" type="button" class="button button-primary" value="<?php _e('Scrape'); ?>">
                        </div>
                    </th>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
