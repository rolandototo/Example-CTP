<?php
/*
Plugin Name: Example CPT Plugin
Description: Custom plugin to create an Example CPT with custom meta field.
Version: 1.0
Author: RolandoEscobar
*/

// Register Custom Post Type

/**
 * Registers the Example CPT.
 */
function register_example_cpt()
{
    $args = array(

        'labels' => array(
            'name' => 'Example CPT',
            'singular_name' => 'Example CPT',
            'menu_name' => 'Example CPT',
        ),
        'label' => 'Example CPT',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'example-cpt'),
    );
    register_post_type('example-cpt', $args);
}

add_action('init', 'register_example_cpt');

// Register the custom meta field

/**
 * Registers the custom meta field.
 */
function register_example_meta()
{
    add_meta_box('example_meta', 'Example Meta', 'example_meta_callback', 'example-cpt', 'normal', 'high');
}

/**
 * The callback function for the custom meta box.
 *
 * @param $post The post object.
 */
function example_meta_callback($post)
{
    $meta = get_post_meta($post->ID, 'example_meta', true);
?>
    <label for="example_meta">Example Meta:</label>
    <input type="text" name="example_meta" id="example_meta" value="<?php echo esc_html($meta); ?>" />
<?php
}

add_action('add_meta_boxes', 'register_example_meta');

// Save the meta field

/**
 * Saves the custom meta field.
 *
 * @param $post_id The post ID.
 */
function save_example_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['example_meta'])) {
        update_post_meta($post_id, '_example_meta', sanitize_text_field($_POST['example_meta']));
    }
}
add_action('save_post', 'save_example_meta');

// Display the meta field and its values in the wp-json/wp/v2 rest api response for the CPT

/**
 * Registers the custom meta field in the REST API.
 */
function add_example_meta_to_rest_api()
{
    register_rest_field('example-cpt', 'example_meta', array(
        'get_callback' => 'get_example_meta',
        'update_callback' => null,
        'schema' => 'WP_REST_POST_SCHEMA_FULL',
    ));
}
add_action('rest_api_init', 'add_example_meta_to_rest_api');

/**
 * Gets the value of the custom meta field.
 *
 * @param $object The REST API object.
 * @param $field_name The name of the field.
 * @param $request The REST API request.
 *
 * @return The value of the custom meta field.
 */
function get_example_meta($object, $field_name, $request)
{
    return get_post_meta($object['id'], 'example_meta', true);
}
