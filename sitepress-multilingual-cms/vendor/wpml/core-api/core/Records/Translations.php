<?php

namespace WPML\Records;

use WPML\Collect\Support\Collection;
use WPML\FP\Logic;
use WPML\FP\Obj;
use WPML\FP\Relation;
use function WPML\FP\curryN;

class Translations {

	const OLDEST_FIRST = 'ASC';
	const NEWEST_FIRST = 'DESC';

	/**
	 * @param \wpdb|null  $wpdb
	 * @param array|null  $order
	 * @param string|null $postType
	 *
	 * @return callable|Collection
	 */
	public static function getForPostType( \wpdb $wpdb = null, array $order = null, $postType = null ) {
		$get = function ( \wpdb $wpdb, array $order, string $postType ) {
			$orderBy = Obj::propOr( self::NEWEST_FIRST, $postType, $order );
			$sql = "SELECT element_id, language_code, source_language_code, trid 
					FROM {$wpdb->prefix}icl_translations
					WHERE element_type = %s
					ORDER BY element_id $orderBy
					";

			return wpml_collect( $wpdb->get_results( $wpdb->prepare( $sql, 'post_' . $postType ) ) );
		};

		return call_user_func_array( curryN( 3, $get ), func_get_args() );
	}

	/**
	 * @param string|null     $defaultLang
	 * @param Collection|null $translations
	 *
	 * @return callable|Collection
	 */
	public static function getSource( $defaultLang = null, Collection $translations = null ) {
		$getSource = function ( $defaultLang, Collection $translations ) {
			$findSource = Logic::allPass( [
				Relation::propEq( 'source_language_code', null ),
				Relation::propEq( 'language_code', $defaultLang )
			] );

			return $translations->filter( $findSource )->values();
		};

		return call_user_func_array( curryN( 2, $getSource ), func_get_args() );
	}

}
