<?php
/*
Plugin Name: Recent Posts Category Plugin
Description: Creates a menu of recent posts nested by post category. Options include how many posts appear under each category as well as which categories appear.
Plugin URI: http://zacherybosek.com/
Version: 1.0
Author: Zachery Bosek
Author URI: http://zacherybosek.com/
License: GPLv2 or later
*/

defined('ABSPATH') or die("No script kiddies please!");

// Creating the widget 

class recent_posts_by_category extends WP_Widget {

function __construct() {

	$blogdetails = get_bloginfo('name');

	parent::__construct(

	// Base ID of the widget
	'recent_posts_by_category', 

	// Widget name will appear in UI
	__('Recent Posts By Category', 'recent_posts_by_category'), 

	// Widget description

	array( 'description' => __( 'Creates a menu of recent posts nested by post category. Options include how many posts appear under each category as well as which categories appear.', $blog_details ), ) 
	);
}

// Creating widget front-end

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		$number =  $instance['number'];

		echo $args['before_widget'];

		if ( ! empty( $title ) )

		echo $args['before_title'] . $title . $args['after_title'];

		$catIDs = array();


		if ( $instance['nullcats'] ){

			foreach ($instance['nullcats'] as $cat) {

				$cat_id = get_cat_ID($cat);		

				if( $cat != "" ){

				array_push($catIDs, $cat_id);

				}

			}

		sort($catIDs);

		$catString = implode(",", $catIDs);

			$category_args = array(

				'exclude'                  => $catString

				);
		}

		else{

			$category_args = array(

				'exclude'                  => ''

			); 
		
		}

		$categories = get_categories($category_args);

		echo __('<ul class="recent_posts_by_category_menu_inner">', $blog_details);

		foreach ($categories as $category) {

			$post_args = array(
				'numberposts' => $number,
				'category' => $category->cat_ID,
				'orderby' => 'post_date'
			);

			$recentPosts = wp_get_recent_posts($post_args);

			echo __('<li>', $blog_details);

			echo __( (string)$category->name, 'zach-portfolio' );

			echo __('</li>', 'zach-portfolio');

			echo __('<ul class="recent_posts_by_category_submenu">', $blog_details);

			foreach ($recentPosts as $post) {
				
				echo __('<li><a href="'.get_permalink($post[ID]).'">', $blog_details);
				
				echo __($post[post_title], $blog_details);
				
				echo __('</a></li>', $blog_details);

			}

			echo __('</ul>', $blog_details);
			
		}
		echo __('</ul>', $blog_details);
		
		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {

	if ( isset( $instance[ 'title' ] ) ) {

		$title = $instance[ 'title' ];
		
		}

		else {

		$title = __( 'New title', $blog_details );

	}

	// Widget admin form
	?>
	<p>
	
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	
	</p>

	<?php 
	if ( isset( $instance["number"] ) ){

			$number = $instance["number"];
		
		}else{

			$number = 5;
		}

		?>

	<p>

		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of recent posts per category:' ); ?></label> 

		<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
	
	</p>
<?php
	if ( isset( $instance["nullcats"] ) ){

			$nullcats = $instance["nullcats"];
		
		}else{

			$nullcats = array();
		}
		$cats = get_categories();

		$count =0;

		?>

	<p>
		<label for="<?php echo $this->get_field_id( 'nullcats' ); ?>"><?php _e( 'Which categories if any should be excluded?' ); ?></label> 

		<?php foreach($cats as $cat):

        $Allnullcats = $instance["nullcats"];

        ?>

        <input class="widefat" id="<?php echo $this->get_field_id( 'nullcats' ).'[]'; ?>" name="<?php echo $this->get_field_name( 'nullcats').'[]'; ?>" type="checkbox" <?php if ( $Allnullcats ) : if( in_array($cat->name, $Allnullcats) ) : ?> checked="checked"  <?php endif; endif;?> value="<?php echo esc_attr( $cat->name ); ?>" /><?php _e( $cat->name ); ?>
       
        <?php $count = $count +1;
        ?>
        <?php endforeach; ?>
	</p>

	<?php 

	}

		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance["number"] = ( ! empty( $new_instance["number"] ) ) ? strip_tags( $new_instance["number"] ) : "";

		$cats = get_categories();

		$count =0;

		foreach ($cats as $cat) {
			
			$instance["nullcats"][$count] = ( ! empty( $new_instance["nullcats"][$count]  ) ) ? strip_tags( $new_instance["nullcats"][$count] ) : "";

			$count = $count +1;
		}
		

		return $instance;

	}
} // Class wpb_widget ends here

// Register and load the widget
function register_widget_recent_posts_by_category() {

	register_widget( 'recent_posts_by_category' );

}

add_action( 'widgets_init', 'register_widget_recent_posts_by_category' );

?>
