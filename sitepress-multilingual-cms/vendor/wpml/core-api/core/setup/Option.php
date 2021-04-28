<?php

namespace WPML\Setup;

use WPML\WP\OptionManager;

class Option {

	const OPTION_GROUP = 'setup';
	const CURRENT_STEP = 'current-step';
	const ORIGINAL_LANG = 'original-lang';
	const TRANSLATED_LANGS = 'translated-langs';
	const WHO_MODE = 'who-mode';
	const TRANSLATION_METHOD = 'translation-method';
	const TRANSLATE_EVERYTHING = 'translate-everything';
	const TRANSLATE_EVERYTHING_COMPLETED = 'translate-everything-completed';
	const TM_ALLOWED = 'is-tm-allowed';

	public static function getCurrentStep() {
		return self::get( self::CURRENT_STEP, 'languages' );
	}

	public static function saveCurrentStep( $step ) {
		self::set( self::CURRENT_STEP, $step );
	}

	public static function getOriginalLang() {
		return self::get( self::ORIGINAL_LANG );
	}

	public static function setOriginalLang( $lang ) {
		self::set( self::ORIGINAL_LANG, $lang );
	}

	public static function getTranslationLangs() {
		return self::get( self::TRANSLATED_LANGS, [] );
	}

	public static function setTranslationLangs( array $langs ) {
		self::set( self::TRANSLATED_LANGS, $langs );
	}

	public static function setOnlyMyselfAsDefault() {
		if ( self::get( self::WHO_MODE, null ) === null ) {
			self::setTranslationMode( [ 'myself' ] );
		}
	}

	public static function setTranslationMode( array $mode ) {
		self::set( self::WHO_MODE, $mode );
	}

	public static function getTranslationMode() {
		return self::get( self::WHO_MODE, [] );
	}

	public static function getTranslationMethod() {
		return self::get( self::TRANSLATION_METHOD, 'automatic' );
	}

	public static function isAutomaticTranslations() {
		return self::getTranslationMethod() === 'automatic';
	}

	/** @param string $method */
	public static function setTranslationMethod( $method ) {
		self::set( self::TRANSLATION_METHOD, $method );
	}

	public static function setTranslateEverythingAsDefault() {
		if ( self::get( self::TRANSLATE_EVERYTHING, null ) === null ) {
			self::setTranslateEverything( true );
		}
	}

	public static function shouldTranslateEverything() {
		return self::get( self::TRANSLATE_EVERYTHING, false );
	}

	/** @param bool $state */
	public static function setTranslateEverything( $state ) {
		self::set( self::TRANSLATE_EVERYTHING, $state );
	}

	public static function setTranslateEverythingCompleted( $completed ) {
		self::set( self::TRANSLATE_EVERYTHING_COMPLETED, $completed );
	}

	public static function markPostTypeAsCompleted( $postType, $languages ) {
		$completed              = self::getTranslateEverythingCompleted();
		$completed[ $postType ] = $languages;

		self::setTranslateEverythingCompleted( $completed );
	}

	public static function getTranslateEverythingCompleted() {
		return self::get( self::TRANSLATE_EVERYTHING_COMPLETED, [] );
	}

	public static function isTMAllowed() {
		return self::get( self::TM_ALLOWED );
	}

	public static function setTMAllowed( $isTMAllowed ) {
		self::set( self::TM_ALLOWED, $isTMAllowed );
	}

	private static function get( $key, $default = null ) {
		return ( new OptionManager() )->get( self::OPTION_GROUP, $key, $default );
	}

	private static function set( $key, $value ) {
		return ( new OptionManager() )->set( self::OPTION_GROUP, $key, $value );
	}


}
