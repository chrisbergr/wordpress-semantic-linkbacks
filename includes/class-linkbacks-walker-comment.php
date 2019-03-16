<?php
/**
 * Comment walker subclass that skips facepile webmention comments and stores
 * emoji reactions (https://indieweb.org/reacj).
 *
 * Based on https://codex.wordpress.org/Function_Reference/Walker_Comment
 */
class Semantic_Linkbacks_Walker_Comment extends Walker_Comment {
	public static $reactions = array();

	protected static function should_facepile( $comment ) {
		$facepiles = get_option( 'semantic_linkbacks_facepiles' );
		if ( ! is_array( $facepiles ) ) {
			$facepiles = array_keys( Linkbacks_Handler::get_comment_type_strings() );
		}
		if ( self::is_reaction( $comment ) && in_array( 'reaction', $facepiles, true ) ) {
			return true;
		}

		$type = Linkbacks_Handler::get_type( $comment );

		$type = explode( ':', $type );

		if ( is_array( $type ) ) {
			$type = $type[0];
		}

		return $type && 'reply' !== $type && in_array( $type, $facepiles, true );
	}

	protected static function get_comment_author_link( $comment_id = 0 ) {
		$comment = get_comment( $comment_id );
		$url     = get_comment_author_url( $comment );
		$author  = get_comment_author( $comment );

		if ( empty( $url ) || 'http://' === $url ) {
			$return = sprintf( '<span class="p-name">%s</span>', $author );
		} else {
			$return = sprintf( '<a href="%s" rel="external" class="u-url p-name">%s</a>', $url, $author );
		}

		/**
		 * Filters the comment author's link for display.
		 *
		 * @since 1.5.0
		 * @since 4.1.0 The `$author` and `$comment_ID` parameters were added
		 * @param string $return     The HTML-formatted comment author link.
		 *                           Empty for an invalid URL.
		 * @param string $author     The comment author's username.
		 * @param int    $comment_ID The comment ID.
		 */
		return apply_filters( 'get_comment_author_link', $return, $author, $comment->comment_ID );
	}

	protected static function is_reaction( $comment ) {
		// If this library is not installed then emoji detection will not work
		if ( ! function_exists( 'mb_internal_encoding' ) ) {
			return false;
		}
		return Emoji\is_single_emoji( trim( wp_strip_all_tags( $comment->comment_content ) ) ) && empty( $comment->comment_parent );
	}

	public function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		if ( self::is_reaction( $comment ) ) {
			self::$reactions[] = $comment;
		}

		if ( ! self::should_facepile( $comment ) ) {
			return parent::start_el( $output, $comment, $depth, $args, $id );
		}
	}

	public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
		if ( ! self::should_facepile( $comment ) ) {
			return parent::end_el( $output, $comment, $depth, $args );
		}
	}

	protected function html5_comment( $comment, $depth, $args ) {
		// To use the default html5_comment set this filter to false
		if ( ! Linkbacks_Handler::render_comments() ) {
			parent::html5_comment( $comment, $depth, $args );
			return;
		}
		$tag   = ( 'div' === $args['style'] ) ? 'div' : 'li';
		$cite  = apply_filters( 'semantic_linkbacks_cite', '<small>&nbsp;@&nbsp;<cite><a href="%1s">%2s</a></cite></small>' );
		$type  = Linkbacks_Handler::get_type( $comment );
		$url   = Linkbacks_Handler::get_url( $comment );
		$coins = Linkbacks_Handler::get_prop( $comment, 'mf2_swarm-coins' );
		$host  = wp_parse_url( $url, PHP_URL_HOST );
		// strip leading www, if any
		$host = preg_replace( '/^www\./', '', $host );
		$author_link = self::get_comment_author_link( $comment );
		?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
		<?php
		set_query_var( 'args', $args );
		set_query_var( 'type', $type );
		set_query_var( 'cite', $cite );
		set_query_var( 'host', $host );
		set_query_var( 'url', $url );
		set_query_var( 'coins', $coins );
		set_query_var( 'depth', $depth );
		set_query_var( 'author_link', $author_link );
		semantic_linkbacks_load_template( 'mention', $type );
	}
}
