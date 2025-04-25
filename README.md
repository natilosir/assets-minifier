<div class="_4f9bf79 d7dc56a8 _43c05b5">
    <div class="ds-markdown ds-markdown--block" style="--ds-md-zoom: 1.143;">
        <h1>CSS &amp; JS Collector and Minifier - Installation and Usage Guide</h1>
        <h2>Description</h2>
        <p class="ds-markdown-paragraph">This WordPress plugin collects and minifies all CSS and JavaScript files in your theme directory, combining them into single
            <code>all.css</code> and
            <code>all.js</code> files in your theme's assets folder. This helps improve website performance by reducing HTTP requests and file sizes.
        </p>
        <h2>Installation</h2>
        <h3>Method 1: Composer Installation (Recommended)</h3>
        <ol start="1">
            <li><p class="ds-markdown-paragraph">Run the following command in your WordPress root directory:</p>
                <div class="md-code-block md-code-block-dark">
                    <div class="md-code-block-banner-wrap">
                        <div class="md-code-block-banner md-code-block-banner-lite">
                            <div class="_121d384">
                            </div>
                        </div>
                    </div>
                    <pre><span class="token function">composer</span> require natilosir/assets-minifier</pre>
                </div>
            </li>
            <li><p class="ds-markdown-paragraph">After installation, execute the move script:</p>
                <div class="md-code-block md-code-block-dark">
                    <div class="md-code-block-banner-wrap">
                    </div>
                    <pre>php vendor/natilosir/assets-minifier/move-to-plugins.php</pre>
                </div>
            </li>
        </ol>
        <h3>Method 2: Manual Installation</h3>
        <ol start="1">
            <li><p class="ds-markdown-paragraph">Download the plugin ZIP file</p></li>
            <li>
                <p class="ds-markdown-paragraph">Upload it to your WordPress plugins directory (<code>wp-content/plugins/</code>)
                </p></li>
            <li>
                <p class="ds-markdown-paragraph">Activate the plugin through the WordPress admin panel under "Plugins"</p>
            </li>
        </ol>
        <h2>Usage</h2>
        <ol start="1">
            <li><p class="ds-markdown-paragraph">After installation, navigate to
                <strong>Tools &gt; CSS/JS Minifier</strong> in your WordPress admin panel.</p></li>
            <li><p class="ds-markdown-paragraph">You'll see an overview page showing:</p>
                <ul>
                    <li><p class="ds-markdown-paragraph">The purpose of the tool</p></li>
                    <li><p class="ds-markdown-paragraph">The output path where minified files will be saved (typically
                        <code>/wp-content/themes/your-theme/assets/</code>)</p></li>
                </ul>
            </li>
            <li><p class="ds-markdown-paragraph">Click the <strong>"Process Files"</strong> button to:</p>
                <ul>
                    <li><p class="ds-markdown-paragraph">Scan your theme directory for all CSS and JS files</p></li>
                    <li><p class="ds-markdown-paragraph">Minify and combine them into single files</p></li>
                    <li><p class="ds-markdown-paragraph">Save them as <code>all.css</code> and
                        <code>all.js</code> in your assets folder</p></li>
                </ul>
            </li>
            <li>
                <p class="ds-markdown-paragraph">After processing, you'll see a success message showing how many files were processed.</p>
            </li>
        </ol>
        <h2>Important Notes</h2>
        <ol start="1">
            <li><p class="ds-markdown-paragraph">The plugin automatically skips:</p>
                <ul>
                    <li><p class="ds-markdown-paragraph">Files with
                        <code>.min.</code> in their name (already minified files)</p></li>
                    <li><p class="ds-markdown-paragraph">Files named <code>all.css</code> or
                        <code>all.js</code> (previous output files)</p></li>
                </ul>
            </li>
            <li><p class="ds-markdown-paragraph">To use the minified files in your theme:</p>
                <ul>
                    <li><p class="ds-markdown-paragraph">Include them in your theme's
                        <code>functions.php</code> or template files:</p>
                        <div class="md-code-block md-code-block-dark">
                            <pre><span class="token function">wp_enqueue_style</span><span
                                    class="token punctuation">(</span><span
                                    class="token string single-quoted-string">'minified-css'</span><span
                                    class="token punctuation">,</span> <span
                                    class="token function">get_template_directory_uri</span><span
                                    class="token punctuation">(</span><span class="token punctuation">)</span> <span
                                    class="token operator">.</span> <span
                                    class="token string single-quoted-string">'/assets/all.css'</span><span
                                    class="token punctuation">)</span><span class="token punctuation">;</span>
<span class="token function">wp_enqueue_script</span><span class="token punctuation">(</span><span
                                        class="token string single-quoted-string">'minified-js'</span><span
                                        class="token punctuation">,</span> <span
                                        class="token function">get_template_directory_uri</span><span
                                        class="token punctuation">(</span><span class="token punctuation">)</span> <span
                                        class="token operator">.</span> <span
                                        class="token string single-quoted-string">'/assets/all.js'</span><span
                                        class="token punctuation">)</span><span class="token punctuation">;</span></pre>
                        </div>
                    </li>
                </ul>
            </li>
            <li><p class="ds-markdown-paragraph">The minification process:</p>
                <ul>
                    <li><p class="ds-markdown-paragraph">Removes all comments</p></li>
                    <li><p class="ds-markdown-paragraph">Eliminates unnecessary whitespace</p></li>
                    <li><p class="ds-markdown-paragraph">Optimizes syntax where possible</p></li>
                    <li><p class="ds-markdown-paragraph">Ensures proper semicolon placement in JS</p></li>
                </ul>
            </li>
            <li><p class="ds-markdown-paragraph">For best results:</p>
                <ul>
                    <li>
                        <p class="ds-markdown-paragraph">Run the minifier whenever you make changes to your CSS/JS files</p>
                    </li>
                    <li><p class="ds-markdown-paragraph">Clear any caching plugins after minifying files</p></li>
                </ul>
            </li>
        </ol>
        <h2>Requirements</h2>
        <ul>
            <li><p class="ds-markdown-paragraph">WordPress 5.0 or higher</p></li>
            <li><p class="ds-markdown-paragraph">PHP 7.0 or higher</p></li>
            <li><p class="ds-markdown-paragraph">Write permissions in your theme directory</p></li>
        </ul>
    </div>
</div>
