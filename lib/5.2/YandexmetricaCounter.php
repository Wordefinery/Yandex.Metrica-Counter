<?php

final class Wordefinery_YandexmetricaCounter {

    const VERSION = '0.6.8.1';
    const DB = false;
    private $path = '';
    private $_is_counter = 0;

    private $size_idx  = array(3=>'88x31', 2=>'80x31', 1=>'80x15');

    function __construct($path) {
        $this->path = $path;

        $this->plugin_title = wr___('Wordefinery Yandex.Metrica Counter');
        $this->plugin_slug = 'wordefinery-yandexmetricacounter';

        $this->store = Wordefinery_Settings::bind(array('wordefinery', $this->plugin_slug));

        $this->store->defvalue(array(
            'site_id'          => '',
            'webvisor'         => 0,
            'clickmap'         => 0,
            'extended'         => 0,
            'bounce'           => 0,
            'hashtrack'        => 0,
            'goal_shortcode'   => 0,
            'test_shortcode'   => 0,
            'informer'         => array(
                'show'		       => 0,
                'mode'             => 'widget',
                'size'             => key($this->size_idx),
                'style'            => 0,
                'color_top'        => 'FFFFFF',
                'alpha_top'        => 'FF',
                'gradient'         => '0',
                'color_bottom'     => 'FFFFFF',
                'alpha_bottom'     => 'FF',
                'align'            => 'center',
                'text'             => '0',
                'arrow'            => '0',
                'info'             => 'pageviews',
                'type'             => '0',
            ),
            'user_params'       => 0,
            'post_params'       => 1,
            'taxonomy_params'   => 1,
            'custom_params'     => 1,
        ));

        $this->informer = $this->store->informer();

        list($this->informer->width, $this->informer->height) = explode('x', $this->size_idx[$this->informer->size]);
        if ($this->informer->gradient != 0) {
            $r = hexdec(substr($this->informer->color_top,0,2)) + $this->informer->gradient;
            $g = hexdec(substr($this->informer->color_top,2,2)) + $this->informer->gradient;
            $b = hexdec(substr($this->informer->color_top,4,2)) + $this->informer->gradient;
            $this->informer->color_bottom = sprintf('%02x%02x%02x',
                $r>255?255:($r<0?0:$r),
                $g>255?255:($g<0?0:$g),
                $b>255?255:($b<0?0:$b)
            );
        } else {
        $this->informer->color_bottom = $this->informer->color_top;
        }
        $this->informer->alpha_bottom = $this->informer->alpha_top;

    	// wp_version < 2.8+ compat
        if (version_compare($GLOBALS['wp_version'], '2.8') < 0 && $this->informer->mode == 'widget') $this->informer->mode = 'footer';

        if (!$this->store->site_id) {
            Wordefinery::Notice($this->plugin_title, sprintf(wr___('set site identifier on <a href="%1$s">plugin settings page</a>.'), 'options-general.php?page='.$this->plugin_slug.'-settings'));
        }

        add_action('admin_menu', array(&$this, 'AdminMenu'));
        add_action('admin_init', array(&$this, 'AdminInit'));

        if ($this->store->site_id) {
            if ($this->informer->show) {
                switch ($this->informer->mode) {
                    case 'widget':
                        add_action('widgets_init', create_function('', "register_widget('Wordefinery_YandexmetricaCounterWidget');"));
                        break;
                    case 'footer':
                        break;
                    case 'shortcode':
                        add_shortcode( 'metricacounter', array(&$this, 'Shortcode'));
                        break;
                }
            }
            add_action('wp_footer', array(&$this, 'Footer'));
            add_filter('wp_nav_menu', array(&$this, 'Counter'));
            add_filter('wp_page_menu', array(&$this, 'Counter'));
        }
    }

    function AdminInit() {
        $size_idx = $this->size_idx;
//        $this->informer->size()->validator(function ($data) use ($size_idx) { if (!isset($size_idx[$data])) return key($size_id); } );
        $this->informer->align()->validator(create_function ('$data', "if (!in_array(\$data, array('center', 'left', 'right'))) return 'center'; ") );
        $this->informer->mode()->validator(create_function ('$data', "if (!in_array(\$data, array('widget', 'shortcode', 'footer'))) return 'widget'; ") );
        $this->store->site_id()->validator(create_function ('$data', "if (preg_match('|[^\\d]|', \$data)) throw new SettingsValidateException('error', sprintf(wr___('Invalid Site Identifier <code>%1\$s</code>'), \$data)); ") );
        $this->informer->color_top()->validator(create_function ('$data', "\$data = preg_replace('|[^0-9a-fA-F]+|', '', \$data); if (strlen(\$data)==3) \$data = \$data{0}.\$data{0}.\$data{1}.\$data{1}.\$data{2}.\$data{2}; if (strlen(\$data)!=6) return 'FFFFFF'; return \$data;") );
        $this->informer->alpha_top()->validator(create_function ('$data', "\$data = preg_replace('|[^0-9a-fA-F]+|', '', \$data); if (strlen(\$data)==1) \$data = '0'.\$data; if (strlen(\$data)!=2) return 'FF'; return $data; ") );

        register_setting( $this->plugin_slug, 'wordefinery' );
//        add_action('wp_ajax_get_site_id', array(&$this, 'SettingsGetSiteId'));
//        add_action('wp_ajax_check_site_id', array(&$this, 'SettingsCheckSiteId'));
        wp_register_style($this->plugin_slug.'-settings', WP_PLUGIN_URL . '/' . $this->path . '/(css)/yandexmetricacounter-settings-page.css', array(), self::VERSION );
        wp_register_script($this->plugin_slug.'-settings', WP_PLUGIN_URL . '/' . $this->path . '/(js)/yandexmetricacounter-settings-page.js', array('jquery-ui-slider',  'jquery', 'farbtastic'), self::VERSION );
        wp_register_style('wordefinery-tabs', WP_PLUGIN_URL . '/' . $this->path . '/(css)/tabs.css', array());
        wp_register_style($this->plugin_slug.'-ui-slider', WP_PLUGIN_URL . '/' . $this->path . '/(css)/jquery-ui-1.8.17.custom.css', array());
    }

    function AdminMenu() {
        $page = add_options_page(
            wr___('Settings') . ' &mdash; ' . $this->plugin_title,
            wr___('Yandex.Metrica Counter'),
            'manage_options',
            $this->plugin_slug . '-settings',
            array(&$this, 'SettingsPage')
        );

        $slug = $this->plugin_slug;
    	// wp_version < 3.3.x compat
		// todo: do it in wp way
        if (version_compare($GLOBALS['wp_scripts']->registered['jquery']->ver, '1.7.1') < 0) {
            add_action( 'admin_print_styles-' . $page, create_function ('', "
                global \$concatenate_scripts;
                \$concatenate_scripts = false;
                wp_deregister_script( 'jquery' );
                wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
                wp_enqueue_script( 'jquery' );
                wp_deregister_script( 'jquery-ui-core' );
                wp_register_script( 'jquery-ui-core', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
                wp_enqueue_script( 'jquery-ui-core' );
                wp_deregister_script( 'jquery-ui-widget' );
                wp_register_script( 'jquery-ui-widget', '');
                wp_enqueue_script( 'jquery-ui-widget' );
                wp_deregister_script( 'jquery-ui-mouse' );
                wp_register_script( 'jquery-ui-mouse', '');
                wp_enqueue_script( 'jquery-ui-mouse' );
                wp_deregister_script( 'jquery-ui-slider' );
                wp_register_script( 'jquery-ui-slider', '');
                wp_enqueue_script( 'jquery-ui-slider' );
            ") );
        }
        add_action( 'admin_print_styles-' . $page, create_function('', "wp_enqueue_style('wordefinery-tabs'); wp_enqueue_style( 'farbtastic' ); wp_enqueue_style('{$slug}-ui-slider'); wp_enqueue_style('{$slug}-settings');") );
        add_action( 'admin_print_scripts-' . $page, create_function('', "wp_enqueue_script('{$slug}-settings');") );
        // add_action("load-$page", array( &$this, 'help_tabs'));
    }

    function SettingsPage() {
        global $pagenow, $plugin_page;
        $tab = $_GET['tab'];

        $tabs = array(''=>wr___('Counter'), 'informer'=>wr___('Informer'), /* 'shortcodes'=>wr___('Shortcodes') */ );
        include(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->path . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . '5.2' . DIRECTORY_SEPARATOR . 'settings-page-tabs.php');
        switch ($tab) {
            case 'shortcodes':
                include(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->path . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . '5.2' . DIRECTORY_SEPARATOR . 'yandexmetricacounter-settings-page-shortcodes-tab.php');
                break;
            case 'informer':
                include(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->path . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . '5.2' . DIRECTORY_SEPARATOR . 'yandexmetricacounter-settings-page-informer-tab.php');
                break;
            default:
                include(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->path . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . '5.2' . DIRECTORY_SEPARATOR . 'yandexmetricacounter-settings-page.php');
                break;
        }
    }

    function SettingsGetSiteId() {
    }

    function SettingsCheckSiteId() {
    }

    function CheckSiteId($id = null) {
    }

    function GetSiteId($host = null) {
    }


    function Shortcode($args) {
        static $x = 0;
        if ($x) return;
        $x = 1;
        $ret = $this->Counter();
        if ($this->informer->show && $this->informer->mode == 'shortcode') $ret .= $this->Informer(0);
        return $ret;
    }

    function Footer() {
        echo $this->Counter();
        if ($this->informer->show && $this->informer->mode == 'footer') echo $this->Informer(1);
    }

    function Informer($is_align = 0) {
        $ret = '';
        if ($is_align) $ret .= "<div style='text-align:{$this->informer->align}'>";
        $ret .= "<!-- Yandex.Metrika informer -->\n";
        $ret .= "<a href=\"" . wr___('http://metrica.yandex.com/stat/') . "?id={$this->store->site_id}&amp;from=informer\"\n";
        $ret .= "target=\"_blank\" rel=\"nofollow\"><img src=\"//bs.yandex.ru/informer/{$this->store->site_id}/{$this->informer->size}_{$this->informer->arrow}_{$this->informer->color_top}{$this->informer->alpha_top}_{$this->informer->color_bottom}{$this->informer->alpha_bottom}_{$this->informer->text}_{$this->informer->info}\"\n";
        $ret .= "style=\"width:{$this->informer->width}px; height:{$this->informer->height}px; border:0;\" alt=\"" . wr___('Yandex.Metrica') . "\" title=\"" . wr___('Yandex.Metrica: data for today (page views)') . "\" ";
        if ($this->informer->type) $ret .= "onclick=\"try{Ya.Metrika.informer({i:this,id:{$this->store->site_id},type:0,lang:'" . wr__x('en', 'metrica.lang') . "'});return false}catch(e){}\" ";
        $ret .= "/></a>\n";
        $ret .= "<!-- /Yandex.Metrika informer -->\n";
        if ($is_align) $ret .= "</div>";
        return $ret;

        // wp_version < 2.8 compat
        __('en|metrica.lang');
    }

    function Counter($nav_menu = '') {
        global $wp_the_query;
        if ($this->_is_counter) return $nav_menu;
        $this->_is_counter = 1;
        $init = '{';
        $init .= "id:{$this->store->site_id}";
        $init .= $this->store->webvisor?', webvisor:true':'';
        $init .= $this->store->clickmap?', clickmap:true':'';
        $init .= $this->store->extended?', trackLinks:true':'';
        $init .= $this->store->bounce?', accurateTrackBounce:true':'';
        $init .= $this->store->hashtrack?', trackHash:true':'';
        $init .= '}';
        $params = array();

        if ($this->store->user_params) {
            $current_user = wp_get_current_user();
            if ($current_user->ID != 0) {
                $params['user']['name'][$current_user->display_name] = true;
                $params['user']['id'][$current_user->ID] = $current_user->display_name;
                foreach ($current_user->roles as $r) {
                    $params['user']['role'][$r] = $current_user->display_name;
                }
            }
        }

        if ($this->store->post_params || $this->store->custom_params || $this->store->taxonomy_params) {
            if ($wp_the_query->is_singular && in_array($wp_the_query->post->post_status, array('publish', 'private'))) {
                $id = $wp_the_query->post->ID;

                if ($this->store->post_params) {
                    $params['id'][$wp_the_query->post->ID] = true;
                    $params['type'][$wp_the_query->post->post_type] = $wp_the_query->post->ID;
                    $params['status'][$wp_the_query->post->post_status] = $wp_the_query->post->ID;
                }

                if ($this->store->post_params) {
                    $customs = get_post_custom($id);
                    $custom = '';
                    if (isset($customs['metrika'])) $custom .= $customs['metrika'][0];
                    elseif (isset($customs['metrica'])) $custom .= ',' . $customs['metrica'][0];
                    $custom = explode(',', $custom);
                    foreach ($custom as $k => $v) { if (!trim($v)) unset($custom[$k]); else $custom[$k] = trim($v); }
                    if (isset($custom) && count($custom)) $params['custom'] = array_fill_keys(array_values($custom), true);
                }

                if ($this->store->taxonomy_params) {
                    $taxonomies = get_object_taxonomies($wp_the_query->post, 'names');
                    $tax = wp_get_object_terms($id, $taxonomies);
                    $taxonomy = array();
                    foreach ($tax as $t) {
                        if (!isset($taxonomy[$t->taxonomy]) || !is_array($taxonomy[$t->taxonomy])) $taxonomy[$t->taxonomy] = array('name'=>array(), 'id'=>array(), 'slug'=>array());
                        if (!isset($taxonomy[$t->taxonomy]['name'][$t->name])) $taxonomy[$t->taxonomy]['name'][$t->name] = true;
                        if (!isset($taxonomy[$t->taxonomy]['id'][$t->term_id])) $taxonomy[$t->taxonomy]['id'][$t->term_id] = $t->name;
                        if (!isset($taxonomy[$t->taxonomy]['slug'][$t->slug])) $taxonomy[$t->taxonomy]['slug'][$t->slug] = $t->name;
                    }
                    if (isset($taxonomy['category'])) $params['category'] = $taxonomy['category'];
                    if (isset($taxonomy['post_tag'])) $params['tag'] = $taxonomy['post_tag'];
                    unset($taxonomy['category']);
                    unset($taxonomy['post_tag']);
                    if (count($taxonomy)) $params['taxonomy'] = $taxonomy;
                }
            }
        }

        $p = json_encode($params);

        return $nav_menu.
<<<END
<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
(w[c] = w[c] || []).push(function() {
try {
w.yaCounter{$this->store->site_id} = new Ya.Metrika({$init});
var p = {$p};
w.yaCounter{$this->store->site_id}.params(p);
}
catch(e) { }
});
})(window, "yandex_metrika_callbacks");
</script></div>
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/{$this->store->site_id}" style="border:0; height:1px; width:1px; position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
END;
    }
}
