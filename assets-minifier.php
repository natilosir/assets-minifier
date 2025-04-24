<?php
/*
Plugin Name: CSS & JS Collector and Minifier
Description: Collects and minifies all CSS and JS files in theme directory
Version: 1.2
Author: Natilos.ir
*/

class CSS_JS_Minifier {
    private $theme_path;
    private $output_path;

    public function __construct() {
        $this->theme_path  = get_template_directory();
        $this->output_path = $this->theme_path . '/assets/';

        // Create output directory if it doesn't exist
        if ( !file_exists($this->output_path) ) {
            wp_mkdir_p($this->output_path);
        }

        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        add_submenu_page('tools.php', 'CSS/JS Minifier', 'CSS/JS Minifier', 'manage_options', 'css-js-minifier', [$this,
                                                                                                                  'admin_page']);
    }

    public function admin_page() {
        if ( isset($_POST['minify_files']) && check_admin_referer('minify_action') ) {
            $result = $this->process_files();

            if ( $result['css_count'] > 0 || $result['js_count'] > 0 ) {
                echo '<div class="notice notice-success"><p>Files processed successfully! Found ' . $result['css_count'] . ' CSS files and ' . $result['js_count'] . ' JS files.</p></div>';
            } else {
                echo '<div class="notice notice-warning"><p>No CSS or JS files found in theme directory!</p></div>';
            }
        }

        echo '<div class="wrap">';
        echo '<h1>CSS & JS Minifier</h1>';
        echo '<form method="post">';
        wp_nonce_field('minify_action');
        echo '<p>This tool will collect and minify all CSS and JS files in your theme directory.</p>';
        echo '<p><strong>Output files will be saved to:</strong> ' . $this->output_path . '</p>';
        echo '<p><input type="submit" name="minify_files" class="button button-primary" value="Process Files"></p>';
        echo '</form>';
        echo '</div>';
    }

    private function process_files() {
        $css_files = $this->find_files('css');
        $js_files  = $this->find_files('js');

        $this->minify_css($css_files);
        $this->minify_js($js_files);

        return ['css_count' => count($css_files),
                'js_count'  => count($js_files)];
    }

    private function find_files( $extension ) {
        $directory = new RecursiveDirectoryIterator($this->theme_path);
        $iterator  = new RecursiveIteratorIterator($directory);
        $regex     = new RegexIterator($iterator, '/^.+\.' . $extension . '$/i', RecursiveRegexIterator::GET_MATCH);

        $found_files = [];

        foreach ( $regex as $file ) {
            $file_path = $file[0];

            // Skip minified files and our output files
            if ( strpos($file_path, '.min.') !== false )
                continue;
            if ( basename($file_path) === 'all.css' || basename($file_path) === 'all.js' )
                continue;

            $found_files[] = $file_path;
        }

        return $found_files;
    }

    private function minify_css( $files ) {
        $output = '';

        foreach ( $files as $file ) {
            if ( !file_exists($file) )
                continue;

            $content = file_get_contents($file);

            // Remove all comments (including /* FILE: ... */)
            $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

            // Remove spaces, newlines, tabs
            $content = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $content);

            // Remove unnecessary spaces
            $content = preg_replace('/\s+/', ' ', $content);
            $content = preg_replace('/\s?([{}|:;,])\s?/', '$1', $content);

            $output .= $content;
        }

        if ( !empty($output) ) {
            file_put_contents($this->output_path . 'all.css', $output);
        }
    }

    private function minify_js( $files ) {
        $output = '';

        foreach ( $files as $file ) {
            if ( !file_exists($file) )
                continue;

            $content = file_get_contents($file);

            // Remove all comments (including /* FILE: ... */)
            $content = preg_replace('/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/m', '$1', $content);

            // Remove extra spaces
            $content = preg_replace('/\s+/', ' ', $content);
            $content = preg_replace('/\s?([{}|:;,\[\]\(\)])\s?/', '$1', $content);

            // Add semicolon at end if missing
            $content = trim($content);
            if ( substr($content, - 1) !== ';' && !empty($content) ) {
                $content .= ';';
            }

            $output .= $content;
        }

        if ( !empty($output) ) {
            file_put_contents($this->output_path . 'all.js', $output);
        }
    }
}

new CSS_JS_Minifier();