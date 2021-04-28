<?php

namespace WPML\API;

use WPML\FP\Lst;
use WPML\FP\Obj;

class PostTypes {

	/**
	 * @return array  eg. [ 'page', 'post' ]
	 */
	public static function getTranslatable() {
		global $sitepress;

		return Obj::keys( $sitepress->get_translatable_documents() );
	}

	/**
	 * @return array  eg. [ 'page', 'post' ]
	 */
	public static function getDisplayAsTranslated() {
		global $sitepress;

		return Obj::keys( $sitepress->get_display_as_translated_documents() );
	}

	/**
	 * Gets post types that are translatable and excludes ones that are display as translated.
	 *
	 * @return array  eg. [ 'page', 'post' ]
	 */
	public static function getOnlyTranslatable() {
		return Obj::values( Lst::diff( self::getTranslatable(), self::getDisplayAsTranslated() ) );
	}
}
