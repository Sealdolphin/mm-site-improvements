<?php
/**
 * Basic class for the footer object.
 *
 * @package root
 */

/**
 * Ez az osztály implementálja a testreszabható láblécet.
 * A Lábléc tartozékai:
 *  - BACKGROUND_IMAGE: háttérkép, ami megjelenik
 *  - FLAVOR_TEXT: egy szöveg, ami a kép alatt jelenik meg
 *  - MARGIN: a margó, ami a képre alkalmazandó ( és annak részeletei )
 * Ez az osztály valósítja meg a rajzolást és a beállítás funkciókat is.
 */
class Custom_Footer {

	/**
	 * A beállítás neve
	 *
	 * @var string $option_name the name of the setting.
	 */
	public static $option_name = 'mmsi_footer_options';
	/**
	 * Name of the background image setting
	 *
	 * @var string $bg_img name of the background image setting
	 */
	private static $bg_img = 'BG_IMG';
	/**
	 * Name of the flavor text setting
	 *
	 * @var string $flavor_text name of the flavor text setting
	 */
	private static $flavor_text = 'FLAVOR_TEXT';
	/**
	 * Name of the image margin setting
	 *
	 * @var string $img_margin name of the image margin setting
	 */
	private static $img_margin = 'IMG_MARGIN';
	/**
	 * Name of the text margin setting
	 *
	 * @var string $txt_margin name of the text margin setting
	 */
	private static $txt_margin = 'TXT_MARGIN';
	/**
	 * Name of the text alignment setting
	 *
	 * @var string $txt_align name of the text alignment setting
	 */
	private static $txt_align = 'TXT_ALIGN';
	/**
	 * Value of the default settings
	 *
	 * @var string $default_settings value of the default settings
	 */
	private static $default_settings = null;

	/**
	 * Basic constructor
	 */
	public static function default_settings() {
		if ( null === self::$default_settings ) {
			self::$default_settings = array(
				self::$bg_img      => plugin_dir_url( __FILE__ ) . 'assets/media/hegyek-honlap.png',
				self::$flavor_text => '',
				self::$txt_align   => 'right',
				self::$img_margin  => new Margin( -15, 0, 0, 0, Margin::PERCENTAGE ),
				self::$txt_margin  => new Margin( 0, 0, 0, 0, Margin::PIXELS ),
			);
		}
		return self::$default_settings;
	}

	/**
	 * Bizonyos beállítás lekérése ( getter )
	 *
	 * @param string $key which setting to get.
	 */
	private static function get_settings_or_default( $key ) {
		$opt_name = self::$option_name;
		$opts     = get_option( $opt_name );

		if ( empty( $opts[ $key ] ) ) {
			return self::default_settings()[ $key ];
		} else {
			return $opts[ $key ];
		}
	}
	/**
	 * Osztály konstruktora ( ide kerülnek az action-ök )
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
	}
	/**
	 * Betölti a szükséges szkripteket
	 */
	public function load_scripts() {
		// A beállításhoz szükséges CSS betöltése.
		wp_register_style( 'mm-site-margin-selector', plugin_dir_url( __FILE__ ) . 'scripts/css/margin-selector.css', array(), MM_Site_Improvements::get_version() );
		wp_enqueue_style( 'mm-site-margin-selector' );
		// A kép beállításhoz szükséges JS szkript betöltése.
		wp_enqueue_media();
		wp_enqueue_script( 'mm-site-image-uploader', plugin_dir_url( __FILE__ ) . 'scripts/js/image_upload.js', array(), MM_Site_Improvements::get_version(), true );
	}
	/**
	 * Ez a függvény rajzolja ki a láblécet a beállítások alapján
	 */
	public function apply_customisation() {
		$opts       = get_option( self::$option_name );
		$img_path   = $opts[ self::$bg_img ];
		$margin     = $opts[ self::$img_margin ];
		$txt_margin = $opts[ self::$txt_margin ];
		$align      = $opts[ self::$txt_align ];

		?>
		<div>
			<img src=<?php echo esc_html( $img_path ); ?> alt='hegyek' style='<?php echo esc_html( $margin->get_margin_css() ); ?>'/>
			<p style='text-align:<?php echo esc_html( $align ); ?>; <?php echo esc_html( $txt_margin->get_margin_css() ); ?>'><?php echo esc_html( esc_attr( $opts[ self::$flavor_text ] ) ); ?></p>
		</div>
		<?php
	}
	/**
	 * Ez regisztrálja a lábléc beállításait a WordPress admin felületen
	 */
	public function create_setting() {
		// Beállítás szekciója: lábléc!
		$section_id = MM_Site_Improvements::$prefix . 'footer_properties';
		add_settings_section(
			$section_id,
			__( 'Lábléc beállításai' ),
			function() {
				sprintf( '<p>%s</p>', __( 'Itt állíthatod be a lábléc beállításait' ) );
			},
			MM_Site_Improvements::$settings_page
		);
		// Lábléc beállítás: háttérkép.
		add_settings_field(
			self::$bg_img,
			__( 'Háttérkép' ),
			array( $this, 'upload_image' ),
			MM_Site_Improvements::$settings_page,
			$section_id
		);
		// Lábléc beállítás: kép margó.
		add_settings_field(
			self::$img_margin,
			__( 'Kép margója' ),
			array( $this, 'change_margin' ),
			MM_Site_Improvements::$settings_page,
			$section_id,
			self::$img_margin
		);
		// Lábléc beállítás: aláírás ( flavor text ).
		add_settings_field(
			self::$flavor_text,
			__( 'Lábléc Felirat' ),
			array( $this, 'change_text' ),
			MM_Site_Improvements::$settings_page,
			$section_id
		);
		// Lábléc beállítás: szöveg elrendezés.
		add_settings_field(
			self::$txt_align,
			__( 'Szöveg elrendezése' ),
			array( $this, 'text_alignment' ),
			MM_Site_Improvements::$settings_page,
			$section_id
		);
		// Lábléc beállítás: szöveg margó.
		add_settings_field(
			self::$txt_margin,
			__( 'Szöveg margója' ),
			array( $this, 'change_margin' ),
			MM_Site_Improvements::$settings_page,
			$section_id,
			self::$txt_margin
		);
	}
	/**
	 * Ez a háttérkép beállításához szükséges input mezők kirajzolása
	 */
	public function upload_image() {
		$val = self::get_settings_or_default( self::$bg_img );

		?>
		<div>
			<img id='mm-csi-image-container' src='<?php echo esc_html( esc_attr( $val ) ); ?>' alt='background' width='100%' height='200px' style='border: solid black 1px'>
			<input type='hidden' class='widefat' value='<?php echo esc_html( esc_attr( $val ) ); ?>' name='<?php echo esc_html( self::$option_name . '[' . self::$bg_img . ']' ); ?>' id=<?php echo esc_html( self::$bg_img ); ?>>
			<button id='mm-csi-btn-upload' class='button-primary'><?php echo esc_html( __( 'Kép cseréje' ) ); ?></button>
		</div>
		<?php
	}

	/**
	 * Ez az aláírás beállításához szükséges input mezők kirajzolása
	 */
	public function change_text() {
		$val = self::get_settings_or_default( self::$flavor_text );

		?>
			<div>
				<input type='text' class='widefat' value='<?php echo esc_html( esc_attr( $val ) ); ?>' name='<?php echo esc_html( self::$option_name . '[' . self::$flavor_text . ']' ); ?>'>
			</div>
		<?php
	}

	/**
	 * Ez a margó beállításához szükséges input mezők kirajzolása
	 *
	 * @param object $setting the setting to be checked.
	 */
	public function change_margin( $setting ) {
		$margin = self::get_settings_or_default( $setting );

		?>
		<div class='mm-csi-main-container'>
			<span class='mm-csi-options-container'>
				<?php
				foreach ( $margin->values as $dir => $val ) {
					$dir_id = $setting . '-' . $dir;
					?>
					<div class='mm-csi-input-group'>
						<label for=<?php echo esc_html( $dir_id ); ?>><?php echo esc_html( ucfirst( $dir ) ); ?></label>
						<input type='number' value=<?php echo esc_html( esc_attr( $val ) ); ?> name="<?php echo esc_html( self::$option_name . '[' . $dir_id . ']' ); ?>" id="<?php echo esc_html( $dir_id ); ?>">
					</div>
					<?php
				}
				?>
			</span>
			<span>
				<?php $unit_id = $setting . '-unit'; ?>
				<label for="<?php echo esc_html( $unit_id ); ?>">Units</label>
				<select name="<?php echo esc_html( self::$option_name . '[' . $unit_id . ']' ); ?>" id="<?php echo esc_html( $unit_id ); ?>">
					<?php
					foreach ( Margin::$unit_types as $i => $unit_type ) {
						?>
						<option value="<?php echo esc_html( $i ); ?>"
							<?php
							if ( $margin->unit === $unit_type ) {
								echo esc_html( 'selected' );
							}
							?>
						><?php echo esc_html( $unit_type ); ?></option>
						<?php
					}
					?>
				</select>
			</span>
		</div>
		<?php
	}

	/**
	 * Ez a szöveg elrendezéshez beállításához szükséges input mezők kirajzolása
	 */
	public function text_alignment() {
		$align      = self::get_settings_or_default( self::$txt_align );
		$alignments = array(
			'left'   => __( 'Balra' ),
			'center' => __( 'Középre' ),
			'right'  => __( 'Jobbra' ),
		);
		?>
		<div>
			<?php foreach ( $alignments as $id => $label ) { ?>
				<input 
					type='radio' 
					name="<?php echo esc_html( self::$option_name . '[' . self::$txt_align . ']' ); ?>" 
					id="<?php echo esc_html( $id ); ?>" 
					value="<?php echo esc_html( $id ); ?>"
					<?php
					if ( $id === $align ) {
						echo esc_html( 'checked' );
					}
					?>
				>
				<label for="<?php echo esc_html( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php }; ?>
		</div>
		<?php
	}

	/**
	 * A lábléchez tartozó beállítások szanitációja ( tisztítása )
	 *
	 * @param array $opts the options to be sanitized.
	 */
	public function sanitize_footer_settings( $opts ) {
		$clean_opts                  = array_map( 'sanitize_text_field', $opts );
		$clean_opts[ self::$bg_img ] = $opts[ self::$bg_img ];

		foreach ( array( self::$img_margin, self::$txt_margin ) as $i => $margin_key ) {
			$clean_opts[ $margin_key ] = new Margin(
				$opts[ $margin_key . '-top' ],
				$opts[ $margin_key . '-right' ],
				$opts[ $margin_key . '-bottom' ],
				$opts[ $margin_key . '-left' ],
				$opts[ $margin_key . '-unit' ]
			);
		}

		return $clean_opts;
	}
}

