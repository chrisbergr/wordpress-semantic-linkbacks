<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard h-card u-author">
						<?php
						if ( 0 !== $args['avatar_size'] ) {
							echo get_avatar( $comment, $args['avatar_size'] );}
						?>
						<?php
							/* translators: %s: comment author */
							printf(
								/* translators: %s: comment author link */
								__( '%s <span class="says">says:</span>', 'semantic-linkbacks' ),
								sprintf( '<b>%s</b>', $author_link )
							);
						if ( $type && ! empty( $cite ) ) {
							printf( $cite, $url, $host );
						}

						?>
					</div><!-- .comment-author -->

					<div class="comment-metadata">
					<?php
					if ( $coins ) {
						// translators: Number of Swarm Coins
						printf( _n( '+%d coin', '+%d coins', (int) $coins, 'semantic-linkbacks' ), $coins );
						echo ' / ';
					}
					?>



						<a class="u-url" href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
							<time class="dt-published" datetime="<?php comment_time( DATE_W3C ); ?>">
								<?php
									/* translators: 1: comment date, 2: comment time */
									printf( __( '%1$s at %2$s', 'semantic-linkbacks' ), get_comment_date( '', $comment ), get_comment_time() );
								?>
							</time>
						</a>
						<?php edit_comment_link( __( 'Edit', 'semantic-linkbacks' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .comment-metadata -->

					<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your response is awaiting moderation.', 'semantic-linkbacks' ); ?></p>
					<?php endif; ?>
				</footer><!-- .comment-meta -->

				<div class="comment-content e-content p-name">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="reply">',
							'after'     => '</div>',
						)
					)
				);
				?>
			</article><!-- .comment-body -->