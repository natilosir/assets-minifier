<?php
/*
Plugin Name: Minify Assets
Description: جمع‌آوری و فشرده‌سازی فایل‌های JS و CSS
Version: 1.0
Author: شما
*/

defined ('ABSPATH') or die('دسترسی ممنوع!');

class WP_Assets_Minifier {
    private $minified_dir = 'minified-assets';

    public function __construct () {
        add_action ('wp_enqueue_scripts', [$this, 'enqueue_minified_assets'], 9999);
        add_action ('admin_menu', [$this, 'add_admin_page']);
        add_action ('admin_post_generate_minified', [$this, 'generate_minified_files']);
    }

    public function add_admin_page () {
        add_menu_page ('Minify Assets', 'Minify Assets', 'manage_options', 'wp-assets-minifier',
                       [$this, 'render_admin_page'], 'dashicons-performance', 80);
    }

    public function render_admin_page () {
        ?>
        <div class="wrap">
            <h1>Minify Assets</h1>

            <div class="card">
                <h2 class="title">فایل‌های CSS</h2>
                <p>تمام فایل‌های CSS را به یک فایل فشرده تبدیل می‌کند</p>
                <form action="<?php echo admin_url ('admin-post.php'); ?>" method="post">
                    <input type="hidden" name="action" value="generate_minified">
                    <input type="hidden" name="file_type" value="css">
                    <?php wp_nonce_field ('generate_minified_action', 'minify_nonce'); ?>
                    <button type="submit" class="button button-primary">تولید فایل CSS</button>
                </form>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2 class="title">فایل‌های JS</h2>
                <p>تمام فایل‌های JavaScript را به یک فایل فشرده تبدیل می‌کند</p>
                <form action="<?php echo admin_url ('admin-post.php'); ?>" method="post">
                    <input type="hidden" name="action" value="generate_minified">
                    <input type="hidden" name="file_type" value="js">
                    <?php wp_nonce_field ('generate_minified_action', 'minify_nonce'); ?>
                    <button type="submit" class="button button-primary">تولید فایل JS</button>
                </form>
            </div>

            <?php
            if ( isset($_GET['minify_status']) ) {
                $status = sanitize_text_field ($_GET['minify_status']);
                $class  = ( $status === 'success' ) ? 'notice-success' : 'notice-error';
                echo '<div class="notice ' . $class . ' is-dismissible"><p>' . esc_html ($_GET['message']) . '</p></div>';
            }
            ?>
        </div>
        <?php
    }

    public function generate_minified_files () {
        // بررسی nonce و دسترسی
        if ( !isset($_POST['minify_nonce'])
             || !wp_verify_nonce ($_POST['minify_nonce'], 'generate_minified_action')
             || !current_user_can ('manage_options') ) {
            wp_die ('دسترسی غیرمجاز!');
        }

        $file_type = sanitize_text_field ($_POST['file_type']);
        $result    = false;

        if ( $file_type === 'css' ) {
            $result = $this->minify_css_files ();
        } elseif ( $file_type === 'js' ) {
            $result = $this->minify_js_files ();
        }

        // ریدایرکت با پیام مناسب
        $status  = $result ? 'success' : 'error';
        $message = $result ? 'فایل‌های ' . strtoupper ($file_type) . ' با موفقیت فشرده شدند.' : 'خطا در فشرده‌سازی فایل‌ها!';

        wp_redirect (add_query_arg (['minify_status' => $status, 'message' => urlencode ($message),],
                                    admin_url ('admin.php?page=wp-assets-minifier')));
        exit;
    }

    private function minify_css_files () {
        global $wp_styles;

        // ایجاد پوشه minified اگر وجود ندارد
        $upload_dir    = wp_upload_dir ();
        $minified_path = trailingslashit ($upload_dir['basedir']) . $this->minified_dir;

        if ( !file_exists ($minified_path) ) {
            wp_mkdir_p ($minified_path);
        }

        // جمع‌آوری تمام فایل‌های CSS
        $css_content = '';
        foreach ( $wp_styles->queue as $handle ) {
            $src = $wp_styles->registered[$handle]->src;

            // اگر فایل داخلی است
            if ( strpos ($src, site_url ()) !== false ) {
                $file_path = str_replace (site_url (), ABSPATH, $src);
                $file_path = preg_replace ('/\?.*/', '', $file_path);

                if ( file_exists ($file_path) ) {
                    $css_content .= file_get_contents ($file_path);
                }
            }
        }

        // فشرده‌سازی CSS
        $css_content = preg_replace ('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_content);
        $css_content = str_replace (["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css_content);
        $css_content = str_replace ([' { ', ' } ', '; '], ['{', '}', ';'], $css_content);

        $minified_file = $minified_path . '/all.min.css';
        $bytes_written = file_put_contents ($minified_file, $css_content);

        return $bytes_written !== false;
    }

    private function minify_js_files () {
        global $wp_scripts;

        // ایجاد پوشه minified اگر وجود ندارد
        $upload_dir    = wp_upload_dir ();
        $minified_path = trailingslashit ($upload_dir['basedir']) . $this->minified_dir;

        if ( !file_exists ($minified_path) ) {
            wp_mkdir_p ($minified_path);
        }

        // جمع‌آوری تمام فایل‌های JS
        $js_content = '';
        foreach ( $wp_scripts->queue as $handle ) {
            $src = $wp_scripts->registered[$handle]->src;

            // اگر فایل داخلی است
            if ( strpos ($src, site_url ()) !== false ) {
                $file_path = str_replace (site_url (), ABSPATH, $src);
                $file_path = preg_replace ('/\?.*/', '', $file_path);

                if ( file_exists ($file_path) ) {
                    $js_content .= file_get_contents ($file_path) . ";\n";
                }
            }
        }

        // فشرده‌سازی JS (ساده)
        $js_content = preg_replace ("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", '', $js_content);
        $js_content = preg_replace ('/\s+/', ' ', $js_content);
        $js_content = preg_replace ('/\s*(?:(?=[=\-\+\|%&\*\)\{\}\[\];:\,\.\<\>\!\@\#\^`~]))/', '', $js_content);
        $js_content = preg_replace ('/(?:(?<=[=\-\+\|%&\*\)\{\}\[\];:\,\.\<\>\!\@\#\^`~]))\s*/', '', $js_content);

        // ذخیره فایل نهایی
        $minified_file = $minified_path . '/all.min.js';
        $bytes_written = file_put_contents ($minified_file, $js_content);

        return $bytes_written !== false;
    }

    public function enqueue_minified_assets () {
        $upload_dir   = wp_upload_dir ();
        $minified_url = trailingslashit ($upload_dir['baseurl']) . $this->minified_dir;

        // غیرفعال کردن تمام CSS
        global $wp_styles;
        foreach ( $wp_styles->queue as $handle ) {
            wp_dequeue_style ($handle);
        }

        // اضافه کردن فایل minified CSS
        if ( file_exists (trailingslashit ($upload_dir['basedir']) . $this->minified_dir . '/all.min.css') ) {
            wp_enqueue_style ('minified-css', $minified_url . '/all.min.css', [],
                              filemtime (trailingslashit ($upload_dir['basedir']) . $this->minified_dir . '/all.min.css'));
        }

        // غیرفعال کردن تمام JS
        global $wp_scripts;
        foreach ( $wp_scripts->queue as $handle ) {
            wp_dequeue_script ($handle);
        }

        // اضافه کردن فایل minified JS
        if ( file_exists (trailingslashit ($upload_dir['basedir']) . $this->minified_dir . '/all.min.js') ) {
            wp_enqueue_script ('minified-js', $minified_url . '/all.min.js', [],
                               filemtime (trailingslashit ($upload_dir['basedir']) . $this->minified_dir . '/all.min.js'),
                               true);
        }
    }

}

new WP_Assets_Minifier();
