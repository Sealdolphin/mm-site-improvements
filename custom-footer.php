<?php
/**
 * Ez az osztály implementálja a testreszabható láblécet.
 * A Lábléc tartozékai:
 *  - BACKGROUND_IMAGE: háttérkép, ami megjelenik
 *  - FLAVOR_TEXT: egy szöveg, ami a kép alatt jelenik meg
 *  - MARGIN: a margó, ami a képre alkalmazandó (és annak részeletei)
 * Ez az osztály valósítja meg a rajzolást és a beállítás funkciókat is.
 */
class Custom_Footer {

    /**
     * A beállítás neve
     */
    public static $option_name = "mmsi_footer_options";
    /**
     * Egyes beállítások
     */
    private static $bg_img = "BG_IMG";
    private static $flavor_text = "FLAVOR_TEXT";
    private static $img_margin = "IMG_MARGIN";
    /**
     * Az alapbeállítás értéke
     */
    private static $default_settings = null;
    /**
     * Alapbeállítás konstruktor
     */
    public static function default_settings() {
        if(self::$default_settings == null) {
            self::$default_settings = array(
                self::$bg_img => plugin_dir_url(__FILE__) . "assets/media/hegyek-honlap.png",
                self::$flavor_text => "",
                self::$img_margin => new Margin(-15, 0, 0, 0, Margin::PERCENTAGE)
            );
        }
        return self::$default_settings;
    }
    /**
     * Bizonyos beállítás lekérése (getter)
     */
    private static function getSettingOrDefault($key) {
        $opt_name = self::$option_name;
        $opts = get_option($opt_name);

        if (empty($opts[$key])) {
            return self::default_settings()[$key];
        } else {
            return $opts[$key];
        }
    }
    /**
     * Osztály konstruktora (ide kerülnek az action-ök)
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts' , array( $this, 'load_scripts'));
    }
    /**
     * Betölti a szükséges szkripteket
     */
    public function load_scripts() {
        //A beállításhoz szükséges CSS betöltése
        wp_register_style('mm-site-margin-selector', plugin_dir_url(__FILE__) . "scripts/css/margin-selector.css");
        wp_enqueue_style('mm-site-margin-selector');
        //A kép beállításhoz szükséges JS szkript betöltése
        wp_enqueue_media();
        wp_enqueue_script('mm-site-image-uploader',plugin_dir_url(__FILE__) . "scripts/js/image_upload.js", array(), Custom_Site_Improvements_Plugin::get_version());
    }
    /**
     * Ez a függvény rajzolja ki a láblécet a beállítások alapján
     */
    function apply_customisation() {
        $opts = get_option( self::$option_name );
        $img_path = $opts[self::$bg_img];
        $margin = $opts[self::$img_margin];

        ?>
        <div>
            <img src=<?php _e($img_path) ?> alt="hegyek" style="<?php _e($margin->get_margin_css()) ?>"/>
            <p style="text-align:right"><?php _e(esc_attr($opts[self::$flavor_text])) ?></p>
        </div>
        <?php
    }
    /**
     * Ez regisztrálja a lábléc beállításait a Wordpress admin felületen
     */
    public function create_setting() {
        //Beállítás szekciója: lábléc!
        $section_id = Custom_Site_Improvements_Plugin::$prefix . "footer_properties";
        add_settings_section(
            $section_id,
            "Footer Properties",
            function() {
                printf("<p>%s</p>", __("Set the footer properties"));
            },
            Custom_Site_Improvements_Plugin::$settings_page
        );
        //Lábléc beállítás: háttérkép
        add_settings_field(
            self::$bg_img,
            "Set Background image",
            array($this, "upload_image"),
            Custom_Site_Improvements_Plugin::$settings_page,
            $section_id
        );
        //Lábléc beállítás: aláírás (flavor text)
        add_settings_field(
            self::$flavor_text,
            "Set Flavor Text",
            array($this, "change_text"),
            Custom_Site_Improvements_Plugin::$settings_page,
            $section_id
        );
        //Lábléc beállítás: margó
        add_settings_field(
            "footer_margin",
            "Set Margin",
            array($this, "change_margin"),
            Custom_Site_Improvements_Plugin::$settings_page,
            $section_id
        );
    }
    /**
     * Ez a háttérkép beállításához szükséges input mezők kirajzolása
     */
    public function upload_image() {
        $val = self::getSettingOrDefault(self::$bg_img);
        
        ?>
        <div>
            <img id="mm-csi-image-container" src="<?php _e(esc_attr($val)) ?>" alt="background" width="100%" height="200px" style="border: solid black 1px">
            <input type="text" class="widefat" value="<?php _e(esc_attr($val)) ?>" name="<?php _e(self::$option_name . "[" . self::$bg_img . "]") ?>" id=<?php _e(self::$bg_img) ?>>
            <button id="mm-csi-btn-upload" class="button-primary"><?php _e(__("Kép cseréje")) ?></button>
        </div>
        <?php
    }
    /**
     * Ez az aláírás beállításához szükséges input mezők kirajzolása
     */
    public function change_text() {
        $val = self::getSettingOrDefault(self::$flavor_text);

        ?>
        <div>
        <input type="text" class="widefat" value="<?php _e(esc_attr($val)) ?>" name="<?php _e(self::$option_name . "[" . self::$flavor_text . "]") ?>">
        </div>
        <?php
    }
    /**
     * Ez a margó beállításához szükséges input mezők kirajzolása
     */
    public function change_margin() {
        $margin =  self::getSettingOrDefault(self::$img_margin);

        ?>
        <div class="mm-csi-main-container">
            <span class="mm-csi-options-container">
                <?php foreach ($margin->values as $dir => $val) {
                    ?>
                    <div class="mm-csi-input-group">
                        <label for=<?php _e("margin-" . $dir) ?>><?php _e(ucfirst($dir)) ?></label>
                        <input type="number" value=<?php _e(esc_attr($val)) ?> name=<?php _e(self::$option_name . "[margin-" . $dir . "]") ?> id=<?php _e("margin-" . $dir) ?>>
                    </div>
                    <?php
                }
                ?>
            </span>
            <span>
                <label for="margin-unit">Units</label>
                <select name="<?php _e(self::$option_name) ?>[margin-unit]" id="margin-unit">
                    <?php foreach (Margin::$unit_types as $i => $type) {
                        ?>
                        <option value="<?php _e($i) ?>" <?php if($margin->unit == $type) {_e("selected"); } ?>><?php _e($type) ?></option>
                        <?php
                    }?>
                </select>
            </span>
        </div>
        <?php
    }
    /**
     * A lábléchez tartozó beállítások szanitációja (tisztítása)
     */
    public function sanitize_footer_settings($opts) {
        $clean_opts = array_map("sanitize_text_field", $opts);
        $clean_opts[self::$bg_img] = $opts[self::$bg_img];
            // self::$bg_img => $opts[self::$bg_img],
            // self::$flavor_text => sanitize_text_field($opts[self::$flavor_text]),
            // self::$img_margin => Margin::sanitize(
            //     new Margin(
            //         intval($opts["margin-top"]),
            //         intval($opts["margin-right"]),
            //         intval($opts["margin-bottom"]),
            //         intval($opts["margin-left"]),
            //         intval($opts["margin-unit"])
            //     )
            // )
        

        return $clean_opts;
    }
}

/**
 * A lábléchez tartozó Margó osztály
 * Részei:
 *  - ALSÓ, FELSŐ, JOBB és BAL margók méretei (VALUES <array>)
 *  - A margó mértékegysége (UNIT)
 */
class Margin {

    /**
     * Elfogadható mértékegységei a margónak (vö. HTML)
     */
    public static $unit_types = array("px", "em", "%", "vw", "vh");
    public const PIXELS = 0;
    public const FONT_SIZE = 1;
    public const PERCENTAGE = 2;
    public const VIEWPORT_WIDTH = 3;
    public const VIEWPORT_HEIGHT = 4;

    /**
     * Osztály konstruktora
     */
    public function __construct(int $top = 0, int $right = 0, int $bottom = 0, int $left = 0, int $unit = self::PIXELS) {
        $this->unit = self::$unit_types[$unit];
        $this->values = array(
            "top" => $top,
            "right" => $right,
            "bottom" => $bottom,
            "left" => $left
        );
    }

    /**
     * A kirajzoláshoz szükséges CSS-t adja vissza a beállított értékek alapján
     */
    public function get_margin_css() {
        $css = "";
        foreach ($this->values as $dir => $val) {
            $css = $css . ' margin-' . $dir . ': ' . $val . $this->unit . ';';
        }
        return $css;
    }

    /**
     * A margó értékeit tisztítja (DEPRECATED)
     */
    public static function sanitize(Margin $margin) {
        array_map(function($val) {
            $val = intval($val);
        }, $margin->values);

        return $margin;
    }

}

