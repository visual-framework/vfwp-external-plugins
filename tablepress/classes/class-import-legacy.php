<?php
/**
 * TablePress Table Import Class
 *
 * @package TablePress
 * @subpackage Export/Import
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress Table Import Class
 *
 * @package TablePress
 * @subpackage Export/Import
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Import_Legacy extends TablePress_Import_Base {

	/**
	 * File/Data Formats that are available for import.
	 *
	 * The constructor also adds 'html', if the PHP prerequisites are met.
	 *
	 * @since 1.0.0
	 * @var string[]
	 */
	public array $import_formats = array( 'csv', 'json', 'xls', 'xlsx' );

	/**
	 * Whether HTML import support is available in the PHP installation on the server.
	 *
	 * @since 1.0.0
	 */
	public bool $html_import_support_available = false;

	/**
	 * Data to be imported.
	 *
	 * @since 1.0.0
	 */
	protected string $import_data;

	/**
	 * Imported table.
	 *
	 * @since 1.0.0
	 * @var array<string, mixed>|false
	 */
	protected $imported_table = false;

	/**
	 * Initialize the Import class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( class_exists( 'DOMDocument', false ) && function_exists( 'simplexml_import_dom' ) && function_exists( 'libxml_use_internal_errors' ) ) {
			$this->html_import_support_available = true;
		}

		if ( $this->html_import_support_available ) {
			$this->import_formats[] = 'html';
		}
	}

	/**
	 * Import a table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format Import format.
	 * @param string $data   Data to import.
	 * @return array<string, mixed>|false Table array on success, false on error.
	 */
	public function import_table( string $format, string $data ) /* : array|false */ {
		/**
		 * Filters the data that is to be imported.
		 *
		 * @since 1.8.1
		 *
		 * @param string $data   Data to import.
		 * @param string $format Import format.
		 */
		$this->import_data = apply_filters( 'tablepress_import_table_data', $data, $format );

		if ( ! in_array( $format, array( 'xlsx', 'xls' ), true ) ) {
			$this->fix_table_encoding();
		}

		switch ( $format ) {
			case 'csv':
				$this->import_csv();
				break;
			case 'html':
				$this->import_html();
				break;
			case 'json':
				$this->import_json();
				break;
			case 'xlsx':
				$this->import_xlsx();
				break;
			case 'xls':
				$this->import_xls();
				break;
			default:
				return false;
		}

		if ( false === $this->imported_table ) {
			return false;
		}

		// Make sure that cells are stored as strings.
		array_walk_recursive(
			$this->imported_table['data'],
			static function ( /* string|int|float|bool|null */ &$cell_content, int $col_idx ): void {
				$cell_content = (string) $cell_content;
			},
		);

		return $this->imported_table;
	}

	/**
	 * Import CSV data.
	 *
	 * @since 1.0.0
	 */
	protected function import_csv(): void {
		$csv_parser = TablePress::load_class( 'CSV_Parser', 'csv-parser.class.php', 'libraries' );
		$csv_parser->load_data( $this->import_data );
		$delimiter = $csv_parser->find_delimiter();
		$data = $csv_parser->parse( $delimiter );
		$this->pad_array_to_max_cols( $data );
		$this->normalize_line_endings( $data );
		$this->imported_table = array( 'data' => $data );
	}

	/**
	 * Import HTML data.
	 *
	 * @since 1.0.0
	 */
	protected function import_html(): void {
		if ( ! $this->html_import_support_available ) {
			return;
		}

		TablePress::load_file( 'html-parser.class.php', 'libraries' );
		$table = HTML_Parser::parse( $this->import_data );

		if ( is_wp_error( $table ) ) {
			$this->imported_table = false;
			return;
		}

		$this->pad_array_to_max_cols( $table['data'] );
		$this->normalize_line_endings( $table['data'] );
		$this->imported_table = $table;
	}

	/**
	 * Import JSON data.
	 *
	 * @since 1.0.0
	 */
	protected function import_json(): void {
		$json_table = json_decode( $this->import_data, true );

		// Check if JSON could be decoded.
		if ( is_null( $json_table ) ) {
			$json_error = json_last_error_msg();
			$output = '<strong>' . __( 'The imported file contains errors:', 'tablepress' ) . "</strong><br><br>JSON error: {$json_error}<br>";
			wp_die( $output, 'Import Error', array( 'response' => 200, 'back_link' => true ) );
		}

		// Specifically cast to an array again.
		$json_table = (array) $json_table;

		if ( isset( $json_table['data'] ) ) {
			// JSON data contained a full export.
			$table = $json_table;
		} else {
			// JSON data contained only the data of a table, but no options.
			$table = array( 'data' => array() );
			foreach ( $json_table as $row ) {
				$table['data'][] = array_values( (array) $row );
			}
		}

		$this->pad_array_to_max_cols( $table['data'] );
		$this->imported_table = $table;
	}

	/**
	 * Import Microsoft Excel 97-2003 data.
	 *
	 * @since 1.1.0
	 */
	protected function import_xls(): void {
		$excel_reader = TablePress::load_class( 'Spreadsheet_Excel_Reader', 'excel-reader.class.php', 'libraries', $this->import_data );

		// Loop through Excel file and retrieve value and colspan/rowspan properties for each cell.
		$sheet = 0; // 0 means first sheet of the Workbook
		$table = array();
		$num_rows = $excel_reader->rowcount( $sheet );
		$num_columns = $excel_reader->colcount( $sheet );
		for ( $row = 1; $row <= $num_rows; $row++ ) {
			$table_row = array();
			for ( $column = 1; $column <= $num_columns; $column++ ) {
				$cell = array();
				$cell['rowspan'] = $excel_reader->rowspan( $row, $column, $sheet );
				$cell['colspan'] = $excel_reader->colspan( $row, $column, $sheet );
				$cell['val'] = $excel_reader->val( $row, $column, $sheet );
				$table_row[] = $cell;
			}
			$table[] = $table_row;
		}

		// Transform colspan/rowspan properties to TablePress equivalent (cell content).
		foreach ( $table as $row_idx => $row ) {
			foreach ( $row as $col_idx => $cell ) {
				if ( 1 === $cell['rowspan'] && 1 === $cell['colspan'] ) {
					continue;
				}

				if ( 1 < $cell['colspan'] ) {
					for ( $i = 1; $i < $cell['colspan']; $i++ ) {
						$table[ $row_idx ][ $col_idx + $i ]['val'] = '#colspan#';
					}
				}
				if ( 1 < $cell['rowspan'] ) {
					for ( $i = 1; $i < $cell['rowspan']; $i++ ) {
						$table[ $row_idx + $i ][ $col_idx ]['val'] = '#rowspan#';
					}
				}

				if ( 1 < $cell['rowspan'] && 1 < $cell['colspan'] ) {
					for ( $i = 1; $i < $cell['rowspan']; $i++ ) {
						for ( $j = 1; $j < $cell['colspan']; $j++ ) {
							$table[ $row_idx + $i ][ $col_idx + $j ]['val'] = '#span#';
						}
					}
				}
			}
		}

		// Flatten value property to two-dimensional array.
		foreach ( $table as &$row ) {
			foreach ( $row as &$cell ) {
				$cell = $cell['val'];
			}
			unset( $cell ); // Unset use-by-reference parameter of foreach loop.
		}
		unset( $row ); // Unset use-by-reference parameter of foreach loop.

		$this->imported_table = array( 'data' => $table );
	}

	/**
	 * Import Microsoft Excel 2007-2019 data.
	 *
	 * @since 1.1.0
	 */
	protected function import_xlsx(): void {
		TablePress::load_file( 'simplexlsx.class.php', 'libraries' );
		$xlsx_file = \Shuchkin\SimpleXLSX::parse( $this->import_data, true );

		if ( ! $xlsx_file ) {
			$output = '<strong>' . __( 'The imported file contains errors:', 'tablepress' ) . '</strong><br><br>' . \Shuchkin\SimpleXLSX::parseError() . '<br>';
			wp_die( $output, 'Import Error', array( 'response' => 200, 'back_link' => true ) );
		}

		$this->imported_table = array( 'data' => $xlsx_file->rows() );
	}

	/**
	 * Fixes the encoding to UTF-8 for the entire string that is to be imported.
	 *
	 * @since 1.0.0
	 *
	 * @link http://stevephillips.me/blog/dealing-php-and-character-encoding
	 */
	protected function fix_table_encoding(): void {
		// Check and remove possible UTF-8 Byte-Order Mark (BOM).
		$bom = pack( 'CCC', 0xef, 0xbb, 0xbf );
		if ( str_starts_with( $this->import_data, $bom ) ) {
			$this->import_data = substr( $this->import_data, 3 );
			// If data has a BOM, it's UTF-8, so further checks unnecessary.
			return;
		}

		// Require the iconv() function for the following checks.
		if ( ! function_exists( 'iconv' ) ) {
			return;
		}

		// Check for possible UTF-16 BOMs ("little endian" and "big endian") and try to convert the data to UTF-8.
		if ( str_starts_with( $this->import_data, "\xFF\xFE" ) || str_starts_with( $this->import_data, "\xFE\xFF" ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$data = @iconv( 'UTF-16', 'UTF-8', $this->import_data );
			if ( false !== $data ) {
				$this->import_data = $data;
				return;
			}
		}

		// Detect the character encoding and convert to UTF-8, if it's different.
		if ( function_exists( 'mb_detect_encoding' ) ) {
			$current_encoding = mb_detect_encoding( $this->import_data, 'ASCII, UTF-8, ISO-8859-1' );
			if ( 'UTF-8' !== $current_encoding ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$data = @iconv( $current_encoding, 'UTF-8', $this->import_data ); // @phpstan-ignore argument.type
				if ( false !== $data ) {
					$this->import_data = $data;
					return;
				}
			}
		}
	}

	/**
	 * Replaces Windows line breaks \r\n with Unix line breaks \n.
	 *
	 * This function uses call by reference to save PHP memory on large arrays.
	 *
	 * @since 2.0.0
	 *
	 * @param array<int, array<int, string>> $an_array Array in which Windows line breaks should be replaced by Unix line breaks.
	 */
	protected function normalize_line_endings( array &$an_array ): void {
		array_walk_recursive(
			$an_array,
			static function ( string &$cell_content, int $col_idx ): void {
				$cell_content = str_replace( "\r\n", "\n", $cell_content );
			},
		);
	}

} // class TablePress_Import_Legacy
