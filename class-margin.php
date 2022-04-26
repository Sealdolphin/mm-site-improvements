<?php
/**
 * Image margin
 *
 * @package root
 */

/**
 * A lábléchez tartozó Margó osztály
 * Részei:
 *  - ALSÓ, FELSŐ, JOBB és BAL margók méretei ( VALUES <array> )
 *  - A margó mértékegysége ( UNIT )
 */
class Margin {

	/**
	 * Elfogadható mértékegységei a margónak ( vö. HTML )
	 *
	 * @var array $unit_types the allowed CSS unit types.
	 */
	public static $unit_types    = array( 'px', 'em', '%', 'vw', 'vh' );
	public const PIXELS          = 0;
	public const FONT_SIZE       = 1;
	public const PERCENTAGE      = 2;
	public const VIEWPORT_WIDTH  = 3;
	public const VIEWPORT_HEIGHT = 4;

	/**
	 * Osztály konstruktora
	 *
	 * @param int $top the value of the top margin.
	 * @param int $right the value of the right margin.
	 * @param int $bottom the value of the bottom margin.
	 * @param int $left the value of the left margin.
	 * @param int $unit the value of the margin unit (px by default).
	 */
	public function __construct( int $top = 0, int $right = 0, int $bottom = 0, int $left = 0, int $unit = self::PIXELS ) {
		$this->unit   = self::$unit_types[ $unit ];
		$this->values = array(
			'top'    => $top,
			'right'  => $right,
			'bottom' => $bottom,
			'left'   => $left,
		);
	}

	/**
	 * A kirajzoláshoz szükséges CSS-t adja vissza a beállított értékek alapján
	 */
	public function get_margin_css() {
		$css = 'width:100%;';
		foreach ( $this->values as $dir => $val ) {
			$css = $css . ' margin-' . $dir . ': ' . $val . $this->unit . ';';
		}
		return $css;
	}

}

