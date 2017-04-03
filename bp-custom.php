<?php
// hacks and mods will go here

//Formaly declare the members types
function using_mt_register_member_types() {
	bp_register_member_type( 'student', array(
		'labels' => array(
			'name'          => __( 'Students', 'using-mt' ),
			'singular_name' => __( 'Student Athlete', 'using-mt' ),
		),
	) );

	bp_register_member_type( 'expert', array(
		'labels' => array(
			'name'          => __( 'Experts', 'using-mt' ),
			'singular_name' => __( 'Expert', 'using-mt' ),
		),
	) );

//	bp_register_member_type( 'coach', array(
//		'labels' => array(
//			'name'          => __( 'Coaches', 'using-mt' ),
//			'singular_name' => __( 'Coach', 'using-mt' ),
//		),
//	) );
}
add_action( 'bp_register_member_types', 'using_mt_register_member_types' );

function using_mt_count_member_types( $member_type = '', $taxonomy = 'bp_member_type' ) {
	global $wpdb;
	$member_types = bp_get_member_types();

	if ( empty( $member_type ) || empty( $member_types[ $member_type ] ) ) {
		return false;
	}

	$count_types = wp_cache_get( 'using_mt_count_member_types', 'using_mt_bp_member_type' );

	if ( ! $count_types ) {
		if ( ! bp_is_root_blog() ) {
			switch_to_blog( bp_get_root_blog_id() );
		}

		$sql = array(
			'select' => "SELECT t.slug, tt.count FROM {$wpdb->term_taxonomy} tt LEFT JOIN {$wpdb->terms} t",
			'on'     => 'ON tt.term_id = t.term_id',
			'where'  => $wpdb->prepare( 'WHERE tt.taxonomy = %s', $taxonomy ),
		);

		$count_types = $wpdb->get_results( join( ' ', $sql ) );
		wp_cache_set( 'using_mt_count_member_types', $count_types, 'using_mt_bp_member_type' );

		restore_current_blog();
	}

	$type_count = wp_filter_object_list( $count_types, array( 'slug' => $member_type ), 'and', 'count' );
	$type_count = array_values( $type_count );

	if ( empty( $type_count ) ) {
		return 0;
	}

	return (int) $type_count[0];
}

function using_mt_display_directory_tabs() {
	$member_types = bp_get_member_types( array(), 'objects' );

	// Loop in member types to build the tabs
	foreach ( $member_types as $member_type ) : ?>

	<li id="members-<?php echo esc_attr( $member_type->name ) ;?>">
		<a href="<?php bp_members_directory_permalink(); ?>"><?php printf( '%s <span>%d</span>', $member_type->labels['name'], using_mt_count_member_types( $member_type->name ) ); ?></a>
	</li>

	<?php endforeach;
}
add_action( 'bp_members_directory_member_types', 'using_mt_display_directory_tabs' );
//End declaration for member types

//Sorting members list
function using_mt_set_has_members_type_arg( $args = array() ) {
	// Get member types to check scope
	$member_types = bp_get_member_types();

	// Set the member type arg if scope match one of the registered member type
	if ( ! empty( $args['scope'] ) && ! empty( $member_types[ $args['scope'] ] ) ) {
		$args['member_type'] = $args['scope'];
	}

	return $args;
}
add_filter( 'bp_before_has_members_parse_args', 'using_mt_set_has_members_type_arg', 10, 1 );
//End sort member lists

//Clean the cache to stay up to date with the output
function using_mt_clean_count_cache( $term = 0, $taxonomy = null ) {
	if ( empty( $term ) || empty( $taxonomy->name ) || 'bp_member_type' != $taxonomy->name )  {
		return;
	}

	wp_cache_delete( 'using_mt_count_member_types', 'using_mt_bp_member_type' );
}
add_action( 'edited_term_taxonomy', 'using_mt_clean_count_cache', 10, 2 );
//End clean Cache

//showing the type of a member on his profile header
function using_mt_member_header_display() {
	$member_type = bp_get_member_type( bp_displayed_user_id() );

	if ( empty( $member_type ) ) {
		return;
	}

	$member_type_object = bp_get_member_type_object( $member_type );
	?>
	<p class="member_type"><?php echo esc_html( $member_type_object->labels['singular_name'] ); ?></p>

	<?php
}
add_action( 'bp_before_member_header_meta', 'using_mt_member_header_display' );

//display the type under each user avatar
function who_are_you_directory() { // by member_type name + geoloc (wpgeo me)
$user = bp_get_member_user_id();
$terms = bp_get_object_terms( $user,  'bp_member_type' );

	if ( ! empty( $terms ) ) {
		if ( ! is_wp_error( $terms ) ) {
				foreach( $terms as $term ) {
					echo '<p>' . $term->name . '</p>';
					// echo do_shortcode('[gmw_member_info]');
				}
		}
	}

}
add_filter ( 'bp_directory_members_item', 'who_are_you_directory' );


?>
