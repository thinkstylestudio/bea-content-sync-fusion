<div class="wrap">
	<h2><?php _e( "Content Sync: Settings", 'bea-content-sync-fusion' ); ?></h2>

	<p><?php _e( 'This plugin gives you the ability to sync content published on a website network to one or more websites network of your choice.', 'bea-content-sync-fusion' ); ?></p>

	<div id="col-container">
		<table class="widefat tag fixed" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" class="manage-column "><?php _e( 'Name', 'bea-content-sync-fusion' ); ?></th>
				<th scope="col" class="manage-column "><?php _e( 'Enable ?', 'bea-content-sync-fusion' ); ?></th>
				<th scope="col"><?php _e( 'Post type', 'bea-content-sync-fusion' ); ?></th>
				<th scope="col"><?php _e( 'Taxonomies', 'bea-content-sync-fusion' ); ?></th>
				<?php if ( class_exists( 'P2P_Connection_Type_Factory' ) ) : ?>
					<th scope="col"><?php _e( 'P2P', 'bea-content-sync-fusion' ); ?></th>
				<?php endif; ?>
				<th scope="col"><?php _e( 'Mode', 'bea-content-sync-fusion' ); ?></th>
				<th scope="col"><?php _e( 'Default status', 'bea-content-sync-fusion' ); ?></th>
				<th scope="col"><?php _e( 'Emitters', 'bea-content-sync-fusion' ); ?></th>
				<th scope="col"><?php _e( 'Receivers', 'bea-content-sync-fusion' ); ?></th>
			</tr>
			</thead>
			<tbody id="the-list" class="list:clients">
			<?php
			if ( $registered_syncs == false || empty( $registered_syncs ) ) :
				echo '<tr><td colspan="9">' . sprintf( __( 'No synchronization exists. Want to <a href="%s">create one</a>?', 'bea-content-sync-fusion' ), network_admin_url( 'admin.php?page=' . 'bea-csf-add' ) ) . '</td></tr>';
			else :
				$class = 'alternate';
				$i     = 0;
				foreach ( $registered_syncs as $sync ) :
					// Skip invalid data
					$label = $sync->get_field( 'label' );
					if ( empty( $label ) ) {
						continue;
					}

					// Get post type label from cpt name
					$post_type_label  = '-';
					$post_type_object = get_post_type_object( $sync->get_field( 'post_type' ) );
					if ( $post_type_object != false ) {
						$post_type_label = $post_type_object->labels->name;
					}

					// Get taxonomies labels from taxo name
					$taxonomies_label = array();
					foreach ( $sync->get_field( 'taxonomies' ) as $taxonomy_name ) {
						$taxonomy_object = get_taxonomy( $taxonomy_name );
						if ( $taxonomy_object != false ) {
							$taxonomies_label[] = $taxonomy_object->labels->name;
						}
					}
					$taxonomies_label = implode( ', ', $taxonomies_label );

					// Get P2P labels from taxo name
					$p2p_label = implode( ', ', (array) $sync->get_field( 'p2p_connections' ) );

					$i ++;
					$class = ( $class == 'alternate' ) ? '' : 'alternate';
					?>
					<tr class="<?php echo $class; ?>">
						<td>
							<strong><?php echo esc_html( $sync->get_field( 'label' ) ); ?></strong>

							<div class="row-actions">
									<span class="edit">
										<?php if ( ! $sync->is_locked() ) : ?>
											<a href="<?php echo add_query_arg( array(
												'action'  => 'edit',
												'sync_id' => $sync->get_field( 'id' )
											), network_admin_url( 'admin.php?page=' . 'bea-csf-add' ) ); ?>"><?php _e( 'Edit', 'bea-content-sync-fusion' ); ?></a>
											|
											<a class="delete" href="<?php echo wp_nonce_url( add_query_arg( array(
												'action'  => 'delete',
												'sync_id' => $sync->get_field( 'id' )
											), network_admin_url( 'admin.php?page=' . 'bea-csf-edit' ) ), 'delete-sync' ); ?>"
											   onclick="return confirm('<?php echo esc_js( sprintf( __( "You are about to delete this sync '%s'\n  'Cancel' to stop, 'OK' to delete." ), $sync->get_field( 'label' ) ) ); ?>');"><?php _e( 'Delete', 'bea-content-sync-fusion' ); ?></a>
										<?php else : ?>
											<?php _e( 'This item is locked because registered since the developer API.', 'bea-content-sync-fusion' ); ?>
										<?php endif; ?>
									</span>
							</div>
						</td>
						<td><?php echo esc_html( $i18n_true_false[ $sync->get_field( 'active' ) ] ); ?></td>
						<td><?php echo esc_html( $post_type_label ); ?></td>
						<td><?php echo esc_html( $taxonomies_label ); ?></td>
						<?php if ( class_exists( 'P2P_Connection_Type_Factory' ) ) : ?>
							<td><?php echo esc_html( $p2p_label ); ?></td>
						<?php endif; ?>
						<td><?php echo esc_html( $sync->get_field( 'mode' ) ); ?></td>
						<td><?php echo esc_html( $sync->get_field( 'status' ) ); ?></td>
						<td><?php echo implode( '<br />', self::get_sites( $sync->get_field( 'emitters', true ), 'blogname' ) ); ?></td>
						<td><?php echo implode( '<br />', self::get_sites( $sync->get_field( 'receivers', true ), 'blogname' ) ); ?></td>
					</tr>
					<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>
	</div>
	<!-- /col-container -->


	<h3><?php _e( "Content Sync: Advanced settings", 'bea-content-sync-fusion' ); ?></h3>
	<div id="col-container">
		<form action="" method="post">
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( "Special mode", 'bea-content-sync-fusion' ); ?></th>
					<td>
						<fieldset>
							<label for="csf-unlock-mode">
								<input
									name="csf_adv_settings[unlock-mode]" <?php checked( is_array( $current_settings ) && $current_settings['unlock-mode'] == '1', true ); ?>
									type="checkbox" id="csf-unlock-mode"
									value="1"> <?php _e( "Remove all content edition restrictions", 'bea-content-sync-fusion' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				</tbody>
			</table>

			<?php wp_nonce_field( 'update-bea-csf-adv-settings' ); ?>
			<p class="submit">
				<input class="button-primary" type="submit" name="update-bea-csf-adv-settings"
				       value="<?php _e( "Save", 'bea-content-sync-fusion' ); ?>"/>
			</p>
		</form>
	</div>
</div>