<?php

namespace wordefinery;

?>

<form method="post" action="options.php">
            <?php settings_fields($this->plugin_slug); ?>
            <input type="hidden" name="wordefinery[__section__]" value="<?php echo $this->plugin_slug; ?>" />
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Site Identifier') ?><br/>
                    <?php if (!$this->store->site_id) : ?>
                        &mdash; <?php _e('<a href="http://metrica.yandex.com/add/" target="_blank">Add counter</a>'); ?>
                    <?php else : ?>
                        &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/dashboard/?counter_id=%s" target="_blank">Dashboard</a>'), $this->store->site_id); ?><br/>
                    <?php endif; ?>
                    </th>
                    <td>
                    <input type="text" size="40" name="wordefinery[site_id]" id="<?php echo $this->plugin_slug; ?>-site_id" value="<?php echo $this->store->site_id; ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('WebVisor') ?><br/>
                    <?php if ($this->store->site_id) : ?>
                        &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/visor/?counter_id=%s" target="_blank">WebVisor report</a>'), $this->store->site_id); ?><br/>
                    <?php endif; ?>
                    </th>
                    <td>
                    <input type="hidden" name="wordefinery[webvisor]" value="0" />
                    <label><input type="checkbox" name="wordefinery[webvisor]" value="1" <?php \checked('1', $this->store->webvisor); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Visitor behavior recording and analysis'); ?>
                    [<?php _e('<a href="http://help.yandex.com/metrika/?id=1121994" target="_blank" title="What is WebVisor">help</a>'); ?>]
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Click map') ?><br/>
                    <?php if ($this->store->site_id) : ?>
                        &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/clickmap/?counter_id=%s" target="_blank">Click map report</a>'), $this->store->site_id); ?><br/>
                    <?php endif; ?>
                    </th>
                    <td>
                    <input type="hidden" name="wordefinery[clickmap]" value="0" />
                    <label><input type="checkbox" name="wordefinery[clickmap]" value="1" <?php \checked('1', $this->store->clickmap); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Statistics used to create the "Click map" report'); ?>
                    [<?php _e('<a href="http://help.yandex.com/metrika/?id=1122003" target="_blank" title="Click map report">help</a>'); ?>]
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Extended track') ?><br/>
                    <?php if ($this->store->site_id) : ?>
                        &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/content/links/?counter_id=%s" target="_blank">External links report</a>'), $this->store->site_id); ?><br/>
                        &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/content/downloads/?counter_id=%s" target="_blank">File downloads report</a>'), $this->store->site_id); ?><br/>
                        &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/content/shares/?counter_id=%s" target="_blank">"Share" button report</a>'), $this->store->site_id); ?><br/>
                    <?php endif; ?>
                    </th>
                    <td>
                    <input type="hidden" name="wordefinery[extended]" value="0" />
                    <label><input type="checkbox" name="wordefinery[extended]" value="1" <?php \checked('1', $this->store->extended); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php printf(
                        __('External links [%1$s], file downloads [%2$s] and "Share" button report [%3$s]'),
                        __('<a href="http://help.yandex.com/metrika/?id=1121988" target="_blank" title="External links report">help</a>'),
                        __('<a href="http://help.yandex.com/metrika/?id=1121989" target="_blank" title="File downloads report">help</a>'),
                        __('<a href="http://help.yandex.com/metrika/?id=1121987" target="_blank" title="Share button report">help</a>')
                    ); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Accurate bounce rate') ?></th>
                    <td>
                    <input type="hidden" name="wordefinery[bounce]" value="0" />
                    <label><input type="checkbox" name="wordefinery[bounce]" value="1" <?php \checked('1', $this->store->bounce); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Any visit where a user only views one page and spends less than 15 seconds on it will be counted as a bounce'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Hash tracking') ?></th>
                    <td>
                    <input type="hidden" name="wordefinery[hashtrack]" value="0" />
                    <label><input type="checkbox" name="wordefinery[hashtrack]" value="1" <?php \checked('1', $this->store->hashtrack); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Hash tracking in the browser address window; applied to AJAXed sites'); ?>
                    </td>
                </tr>
            </table>
        <h3><?php _e('Visit parameters') ?></h3>
        <?php if ($this->store->site_id) : ?>
        <p>
            &mdash; <?php printf(__('<a href="http://metrica.yandex.com/stat/content/user_vars/?counter_id=%s" target="_blank">Visit parameters report</a>'), $this->store->site_id); ?><br/>
        </p>
        <?php endif; ?>
        <p>
        <?php _e('Send statistics data to create Visit parameters reports'); ?>
        [<?php _e('<a href="http://help.yandex.com/metrika/?id=1121990" target="_blank" title="Visit parameters report">help</a>'); ?>]
        </p>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Taxonomy') ?></th>
                    <td>
                    <input type="hidden" name="wordefinery[taxonomy_params]" value="0" />
                    <label><input type="checkbox" name="wordefinery[taxonomy_params]" value="1" <?php \checked('1', $this->store->taxonomy_params); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Send full taxonomy (tags, categories, and custom taxonomies) of single post or page'); ?><br/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Custom field') ?></th>
                    <td>
                    <input type="hidden" name="wordefinery[custom_params]" value="0" />
                    <label><input type="checkbox" name="wordefinery[custom_params]" value="1" <?php \checked('1', $this->store->custom_params); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Send value of <code>metrica</code> custom field'); ?><br/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Post data') ?></th>
                    <td>
                    <input type="hidden" name="wordefinery[post_params]" value="0" />
                    <label><input type="checkbox" name="wordefinery[post_params]" value="1" <?php \checked('1', $this->store->post_params); ?> />
                    <?php _e('Enable'); ?></label><br/>
                    <?php _e('Send ID, type and status of single post or page'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('User data') ?></th>
                    <td>
                    <input type="hidden" name="wordefinery[user_params]" value="0" />
                    <label><input type="checkbox" name="wordefinery[user_params]" value="1" <?php \checked('1', $this->store->user_params); ?> />
                    <?php _e('Enable'); ?></label> <i>(<?php _e('disabled by default'); ?>)</i><br/>
                    <?php _e('Send user name, ID and role'); ?>
                    </td>
                </tr>
            </table>

<p class="submit">
    <input type="submit" name="Submit" class="button-primary" value="<?php \esc_attr_e('Save Changes') ?>" />
</p>
</form>

<img src="http://wordefinery.com/i/yandexmetrica-counter.gif?wp=<?php echo $GLOBALS['wp_version']; ?>&v=<?php echo self::VERSION; ?>" width="1" height="1" border="0" alt="" />
</div>
