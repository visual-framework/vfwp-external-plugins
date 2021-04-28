<?php

namespace WPML\LIB\WP;

use WPML\Collect\Support\Traits\Macroable;
use WPML\FP\Either;
use WPML\FP\Fns;
use WPML\FP\Logic;
use function WPML\FP\curryN;
use function WPML\FP\partialRight;
use function WPML\FP\pipe;

/**
 * Class Post
 * @package WPML\LIB\WP
 * @method static callable|Either getTerms( ...$postId, ...$taxonomy )  - Curried:: int → string → Either false|WP_Error [WP_Term]
 * @method static callable|mixed getMetaSingle( ...$postId, ...$key ) - Curried :: int → string → mixed
 * @method static callable|int|bool updateMeta( ...$postId, ...$key, ...$value ) - Curried :: int → string → mixed → int|bool
 * @method static callable|string|false getType( ...$postId ) - Curried :: int → string|bool
 * @method static callable|\WP_Post|null get( ...$postId ) - Curried :: int → \WP_Post|null
 */
class Post {

	use Macroable;

	/**
	 * @return void
	 */
	public static function init() {

		self::macro( 'getTerms', curryN( 2, pipe(
			'get_the_terms',
			Logic::ifElse( Logic::isArray(), [ Either::class, 'right' ], [ Either::class, 'left' ] )
		) ) );

		self::macro( 'getMetaSingle', curryN( 2, partialRight( 'get_post_meta', true ) ) );

		self::macro( 'updateMeta', curryN( 3, 'update_post_meta' ) );

		self::macro( 'deleteMeta', curryN( 2, 'delete_post_meta' ) );

		self::macro( 'getType', curryN( 1, 'get_post_type' ) );

		self::macro( 'get', curryN( 1, Fns::unary( 'get_post' ) ) );

	}
}

Post::init();
