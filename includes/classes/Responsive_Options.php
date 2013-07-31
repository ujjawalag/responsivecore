<?php
/**
 * Theme Options Class
 *
 * Creates the options from supplied arrays
 *
 * Requires a sections array and an options array
 *
 *
 * @file           Responsive_Options.php
 * @package        Responsive
 * @author         CyberChimps
 * @copyright      CyberChimps
 * @license        license.txt
 * @version        Release: 1.0
 * @since          available since Release 1.9.5
 */

Class Responsive_Options {

    public $sections;

    public $options;

    public $responsive_options;

    /**
     * Pulls in the arrays for the options and sets up the responsive options
     *
     * @param $sections array
     * @param $options array
     */
    public function __construct( $sections, $options ) {
        $this->sections           = $sections;
        $this->options            = $options;
        $this->responsive_options = get_option( 'responsive_theme_options' );
        // Set confirmaton text for restore default option as attributes of submit_button().
        $this->attributes['onclick'] = 'return confirm("' . __( 'Do you want to restore? \nAll theme settings will be lost! \nClick OK to Restore.', 'responsive' ) . '")';
    }

    /**
     * Displays the options, called from class instance
     *
     * Loops through sections array
     *
     * @return string
     */
    public function render_display() {
        $html = '';
        foreach( $this->sections as $section ) {
            $sub = $this->options[$section['id']];
            $html .= $this->container( $section['title'], $sub );
        }

        return $html;
    }

    /**
     * Creates main sections title and container
     *
     * Loops through the options array
     *
     * @param $title string
     * @param $sub array
     *
     * @return string
     */
    protected function container( $title, $sub ) {

        $html = '<h3 class="rwd-toggle"><a href="#">' . esc_html( $title ) . '</a></h3>
                <div class="rwd-container">
                <div class="rwd-block">';
        foreach( $sub as $opt ) {
            $html .= $this->sub_heading( $opt['title'], $opt['subtitle'] );
            $html .= $this->section( $opt );
        }
        $html .= $this->save();
        $html .= '</div><!-- rwd-block --></div><!-- rwd-container -->';

        return $html;

    }

    /**
     * Creates the title section for each option input
     *
     * @param $title string
     * @param $subtitle string
     *
     * @return string
     */
    protected function sub_heading( $title, $subtitle ) {

        $html = '<div class="grid col-300">';

        $html .= $title;

        $html .= $subtitle;

        $html .= '</div><!-- end of .grid col-300 -->';

        return $html;
    }

    /**
     * Creates option section with inputs
     *
     * Calls option type
     *
     * @param $options array
     *
     * @return string
     */
    protected function section( $options ) {

        $html = $options['heading'];

        $html .= '<div class="grid col-620 fit">';

        $html .= self::$options['type']( $options );

        $html .= '</div>';

        return $html;
    }

    /**
     * Creates text input
     *
     * @param $args array
     *
     * @return string
     */
    protected function text( $args ) {

        extract( $args );

        $value = ( !empty( $this->responsive_options[$id] ) ) ? ( $this->responsive_options[$id] ) : '';

        $html = '<input id="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" class="regular-text" type="text" name="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" value="'
            . esc_html( $value ) . '"
        placeholder="' .
            esc_attr( $placeholder ) . '" />
                 <label class="description" for="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '">' . esc_html( $description ) . '</label>';

        return $html;
    }

    /**
     * Creates textarea input
     *
     * @param $args array
     *
     * @return string
     */
    protected function textarea( $args ) {

        extract( $args );

        $value = ( !empty( $this->responsive_options['responsive_inline_js_head'] ) ) ? $this->responsive_options['responsive_inline_js_head'] : '';

        $html = '<p>' . esc_html( $heading ) . '</p>
                <textarea id="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" class="large-text" cols="50" rows="30" name="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" placeholder="' . $placeholder . '">' .
            esc_html( $value ) .
            '</textarea>
            <label class="description" for="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '">' . esc_html( $description ) . '</label>';

        return $html;
    }

    /**
     * Creates select dropdown input
     *
     * Loops through options
     *
     * @param $args array
     *
     * @return string
     */
    protected function select( $args ) {

        extract( $args );

        $html = '<select id="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" name="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '">';
        foreach( $options as $key => $value ) {
            $html .= '<option' . selected( $this->responsive_options[$id], $key, false ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
        }
        $html .= '</select>';

        return $html;

    }

    /**
     * Creates checkbox input
     *
     * @param $args array
     *
     * @return string
     */
    protected function checkbox( $args ) {

        extract( $args );

        $html = '<input id="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" name="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '" type="checkbox" value="1" ' . checked(
                '1', esc_attr( $this->responsive_options[$id] ), false ) . ' />
                <label class="description" for="' . esc_attr( 'responsive_theme_options[' . $id . ']' ) . '">' . wp_kses_post( $description ) . '</label>';

        return $html;
    }

    /**
     * Creates description only. No input
     */
    protected function description( $args ) {

        extract( $args );

        $html = '<p>' . wp_kses_post( $description ) . '</p>';

        return $html;
    }

    /**
     * Creates save, reset and upgrade buttons
     *
     * @return string
     */
    protected function save() {
        $html = '<div class="grid col-940">
                <p class="submit">
				' . get_submit_button( __( 'Save Options', 'responsive' ), 'primary', 'responsive_theme_options[submit]', false ) .
            get_submit_button( __( 'Restore Defaults', 'responsive' ), 'secondary', 'responsive_theme_options[reset]', false, $this->attributes ) . '
                <a href="http://cyberchimps.com/store/responsivepro/" class="button">' . __( 'Upgrade', 'responsive' ) . '</a>
                </p>
                </div>';

        return $html;
    }

    /**
     * Default layouts static function
     *
     * @return array
     */
    public static function valid_layouts() {
        $layouts = array(
            'content-sidebar-page'      => __( 'Content/Sidebar', 'responsive' ),
            'sidebar-content-page'      => __( 'Sidebar/Content', 'responsive' ),
            'content-sidebar-half-page' => __( 'Content/Sidebar Half Page', 'responsive' ),
            'sidebar-content-half-page' => __( 'Sidebar/Content Half Page', 'responsive' ),
            'full-width-page'           => __( 'Full Width Page (no sidebar)', 'responsive' )
        );

        return apply_filters( 'responsive_valid_layouts', $layouts );
    }
}