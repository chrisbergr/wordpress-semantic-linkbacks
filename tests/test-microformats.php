<?php
class MicroformatsTest extends WP_UnitTestCase {

	public static function assert_array_subset( array $expectation, array $reality, $strict = false, $message = '' ) {
		foreach ( $expectation as $key => $value ) {
			self::assertArrayHasKey( $key, $reality, $message );
			if ( is_array( $value ) ) {
				self::assert_array_subset( $value, $reality[ $key ], $strict, $message . '[' . $key . ']' );
			} else {
				self::assertEquals( $value, $reality[ $key ], $message . '[' . $key . ']' );
			}
		}
	}

	/**
	 * @dataProvider templateProvider
	 */
	public function test_interpreter( $path ) {
		$comment = Linkbacks_MF2_Handler::generate_commentdata(
			array(
				'remote_source_original' => file_get_contents( $path ),
				'comment_author_url'     => 'http://example.com/webmention/target/placeholder',
				'target'                 => 'http://example.com/webmention/target/placeholder',
				'comment_type'           => 'webmention',
				'comment_author'         => basename( $path, '.html' ),
			)
		);

		$subset = json_decode( file_get_contents( substr( $path, 0, -4 ) . 'json' ), true );
		$this->assert_array_subset( $subset, $comment );
	}

	public function templateProvider() {
		return array_map(
			function( $path ) {
					return array( $path ); },
			glob( dirname( __FILE__ ) . '/templates/*.html' )
		);
	}
}
