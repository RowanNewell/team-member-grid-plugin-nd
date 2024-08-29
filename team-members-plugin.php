<?php
/*
Plugin Name: Team Members Grid Plugin
Description: A plugin to display team members with customizable fields and styling options.
Version: 0.29
Author: NUEL Digital
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Enqueue styles and scripts
function team_members_nd_enqueue_scripts() {
    wp_enqueue_style( 'team-members-nd-style', plugins_url( 'assets/css/team-members.css', __FILE__ ) );
    wp_enqueue_script( 'jquery' ); // Ensure jQuery is enqueued
    wp_enqueue_script( 'team-members-nd-script', plugins_url( 'assets/js/team-members.js', __FILE__ ), array('jquery'), null, true );
    wp_enqueue_style( 'wp-color-picker' ); // Enqueue WordPress color picker
    wp_enqueue_script( 'team-members-nd-script', plugins_url( 'assets/js/team-members.js', __FILE__ ), array('jquery', 'wp-color-picker'), null, true );

    // Pass the close icon URL to the JavaScript file
    $icon_data = array(
        'closeIcon' => plugins_url( 'assets/icons/close-icon.svg', __FILE__ ),
    );
    wp_localize_script( 'team-members-nd-script', 'teamMembersData', $icon_data );
    
    // Inline styles for custom settings
    $custom_css = "
    .team-member-nd {
        color: " . esc_attr( get_option('text_color_nd', '#000000') ) . ";
        background-color: " . esc_attr( get_option('background_color_nd', '#ffffff') ) . ";
        border-color: " . esc_attr( get_option('border_color_nd', '#cccccc') ) . ";
        border-radius: " . esc_attr( get_option('border_radius_nd', '5px') ) . ";
        padding: " . esc_attr( get_option('padding_nd', '10px') ) . ";
        margin: " . esc_attr( get_option('margin_nd', '10px') ) . ";
    }
    .team-member-nd h3 {
        color: " . esc_attr( get_option('title_color_nd', '#000000') ) . ";
        font-size: " . esc_attr( get_option('title_font_size_nd', '24px') ) . ";
    }
    .team-member-nd h4 {
        color: " . esc_attr( get_option('position_color_nd', '#333333') ) . ";
        font-size: " . esc_attr( get_option('position_font_size_nd', '18px') ) . ";
    }
    .team-member-content-nd p {
        color: " . esc_attr( get_option('description_color_nd', '#555555') ) . ";
        font-size: " . esc_attr( get_option('description_font_size_nd', '16px') ) . ";
    }
    .team-member-detail-nd {
        padding: " . esc_attr( get_option('detail_padding_nd', '20px') ) . ";
        margin: " . esc_attr( get_option('detail_margin_nd', '20px') ) . ";
    }
    .team-member-nd img {
        border-radius: " . esc_attr( get_option('image_border_radius_nd', '5px') ) . ";
    }
    .team-member-nd .chevron-nd, .team-member-nd .close-btn-nd {
        color: " . esc_attr( get_option('icon_color_nd', '#000000') ) . ";
        background-color: " . esc_attr( get_option('icon_background_color_nd', '#ffffff') ) . ";
        border-color: " . esc_attr( get_option('icon_border_color_nd', '#cccccc') ) . ";
        border-radius: " . esc_attr( get_option('icon_border_radius_nd', '50%') ) . ";
        font-size: " . esc_attr( get_option('icon_size_nd', '24px') ) . ";
        padding: " . esc_attr( get_option('icon_padding_nd', '5px') ) . ";
    }
    @media (max-width: " . esc_attr( get_option('tablet_breakpoint_nd', '1024') ) . "px) {
        .team-grid-nd {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: " . esc_attr( get_option('mobile_breakpoint_nd', '767') ) . "px) {
        .team-grid-nd {
            grid-template-columns: 1fr;
        }
    }
    ";
    wp_add_inline_style( 'team-members-nd-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'team_members_nd_enqueue_scripts' );

// Register custom post type with custom fields
function create_team_member_cpt_nd() {
    $labels = array(
        'name' => __( 'Team Members', 'textdomain' ),
        'singular_name' => __( 'Team Member', 'textdomain' ),
        'menu_name' => __( 'Team Members', 'textdomain' ),
        'name_admin_bar' => __( 'Team Member', 'textdomain' ),
        'add_new' => __( 'Add New', 'textdomain' ),
        'add_new_item' => __( 'Add New Team Member', 'textdomain' ),
        'new_item' => __( 'New Team Member', 'textdomain' ),
        'edit_item' => __( 'Edit Team Member', 'textdomain' ),
        'view_item' => __( 'View Team Member', 'textdomain' ),
        'all_items' => __( 'All Team Members', 'textdomain' ),
        'search_items' => __( 'Search Team Members', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Team Members:', 'textdomain' ),
        'not_found' => __( 'No team members found.', 'textdomain' ),
        'not_found_in_trash' => __( 'No team members found in Trash.', 'textdomain' ),
        'archives' => __( 'Team Member Archives', 'textdomain' ),
        'insert_into_item' => __( 'Insert into team member', 'textdomain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this team member', 'textdomain' ),
        'filter_items_list' => __( 'Filter team members list', 'textdomain' ),
        'items_list_navigation' => __( 'Team members list navigation', 'textdomain' ),
        'items_list' => __( 'Team members list', 'textdomain' ),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'team-member' ),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'thumbnail' ),
        'menu_icon' => 'dashicons-groups',
    );

    register_post_type( 'team_member_nd', $args );

    // Add meta boxes for additional fields
    add_action('add_meta_boxes', 'team_member_nd_add_meta_boxes');
    add_action('save_post', 'team_member_nd_save_meta_boxes');
}
add_action( 'init', 'create_team_member_cpt_nd' );

// Add Meta Boxes for Custom Fields
function team_member_nd_add_meta_boxes() {
    add_meta_box(
        'team_member_nd_meta_box',
        'Team Member Details',
        'team_member_nd_meta_box_callback',
        'team_member_nd',
        'normal',
        'high'
    );
}

// Meta Box Callback Function
function team_member_nd_meta_box_callback($post) {
    wp_nonce_field('team_member_nd_save_meta_boxes', 'team_member_nd_nonce');

    $position_title = get_post_meta($post->ID, 'position_title', true);
    $description = get_post_meta($post->ID, 'description', true);

    echo '<p><label for="position_title">Position Title:</label>';
    echo '<input type="text" id="position_title" name="position_title" value="' . esc_attr($position_title) . '" class="widefat" /></p>';

    echo '<p><label for="description">Description:</label>';
    echo '<textarea id="description" name="description" class="widefat" rows="4">' . esc_textarea($description) . '</textarea></p>';
}

// Save Meta Boxes Data
function team_member_nd_save_meta_boxes($post_id) {
    if (!isset($_POST['team_member_nd_nonce']) || !wp_verify_nonce($_POST['team_member_nd_nonce'], 'team_member_nd_save_meta_boxes')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['position_title'])) {
        update_post_meta($post_id, 'position_title', sanitize_text_field($_POST['position_title']));
    }

    if (isset($_POST['description'])) {
        update_post_meta($post_id, 'description', sanitize_textarea_field($_POST['description']));
    }
}

// Create Admin Menu for ND Team Members Settings
function team_members_nd_create_menu() {
    add_menu_page(
        'ND Team Members Settings',
        'ND Team Members Settings',
        'manage_options',
        'team-members-settings-nd',
        'team_members_nd_settings_page',
        'dashicons-groups',
        80
    );
}
add_action( 'admin_menu', 'team_members_nd_create_menu' );

// Register and Define Settings for CSS Customization
function team_members_nd_register_settings() {
    register_setting( 'team-members-settings-group-nd', 'text_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'background_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'background_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'border_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'border_radius_nd' );
    register_setting( 'team-members-settings-group-nd', 'padding_nd' );
    register_setting( 'team-members-settings-group-nd', 'margin_nd' );
    
    // Image border radius setting
    register_setting( 'team-members-settings-group-nd', 'image_border_radius_nd' );

    // Typography settings
    register_setting( 'team-members-settings-group-nd', 'title_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'title_font_size_nd' );
    register_setting( 'team-members-settings-group-nd', 'position_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'position_font_size_nd' );
    register_setting( 'team-members-settings-group-nd', 'description_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'description_font_size_nd' );

    // Detail Pane settings
    register_setting( 'team-members-settings-group-nd', 'detail_padding_nd' );
    register_setting( 'team-members-settings-group-nd', 'detail_margin_nd' );

    // Icon settings
    register_setting( 'team-members-settings-group-nd', 'icon_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'icon_background_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'icon_border_color_nd' );
    register_setting( 'team-members-settings-group-nd', 'icon_border_radius_nd' );
    register_setting( 'team-members-settings-group-nd', 'icon_size_nd' );
    register_setting( 'team-members-settings-group-nd', 'icon_padding_nd' );

    // Responsive settings
    register_setting( 'team-members-settings-group-nd', 'tablet_breakpoint_nd' );
    register_setting( 'team-members-settings-group-nd', 'mobile_breakpoint_nd' );
}
add_action( 'admin_init', 'team_members_nd_register_settings' );

// ND Team Members Settings Page Layout with Instructions and Color Picker
function team_members_nd_settings_page() {
?>
    <div class="wrap">
        <h1>ND Team Members Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'team-members-settings-group-nd' ); ?>
            <?php do_settings_sections( 'team-members-settings-group-nd' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" colspan="2"><strong>Instructions:</strong></th>
                </tr>
                <tr valign="top">
                    <td colspan="2">
                        <p>Use the <strong>[team_members_nd]</strong> shortcode to display the team members grid on any page or post.</p>
                        <p>The following settings allow you to customize the appearance of the team members grid:</p>
                    </td>
                </tr>

                <!-- General Settings -->
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td><input type="text" name="text_color_nd" value="<?php echo esc_attr( get_option('text_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td><input type="text" name="background_color_nd" value="<?php echo esc_attr( get_option('background_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Border Color</th>
                    <td><input type="text" name="border_color_nd" value="<?php echo esc_attr( get_option('border_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Border Radius</th>
                    <td><input type="text" name="border_radius_nd" value="<?php echo esc_attr( get_option('border_radius_nd') ); ?>" placeholder="5px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Padding</th>
                    <td><input type="text" name="padding_nd" value="<?php echo esc_attr( get_option('padding_nd') ); ?>" placeholder="10px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Margin</th>
                    <td><input type="text" name="margin_nd" value="<?php echo esc_attr( get_option('margin_nd') ); ?>" placeholder="10px" /></td>
                </tr>

                <!-- Image Border Radius Setting -->
                <tr valign="top">
                    <th scope="row">Image Border Radius</th>
                    <td><input type="text" name="image_border_radius_nd" value="<?php echo esc_attr( get_option('image_border_radius_nd', '5px') ); ?>" placeholder="5px" /></td>
                </tr>

                <!-- Typography Settings -->
                <tr valign="top">
                    <th scope="row">Title Color</th>
                    <td><input type="text" name="title_color_nd" value="<?php echo esc_attr( get_option('title_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Title Font Size</th>
                    <td><input type="text" name="title_font_size_nd" value="<?php echo esc_attr( get_option('title_font_size_nd') ); ?>" placeholder="24px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Position Color</th>
                    <td><input type="text" name="position_color_nd" value="<?php echo esc_attr( get_option('position_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Position Font Size</th>
                    <td><input type="text" name="position_font_size_nd" value="<?php echo esc_attr( get_option('position_font_size_nd') ); ?>" placeholder="18px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Description Color</th>
                    <td><input type="text" name="description_color_nd" value="<?php echo esc_attr( get_option('description_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Description Font Size</th>
                    <td><input type="text" name="description_font_size_nd" value="<?php echo esc_attr( get_option('description_font_size_nd') ); ?>" placeholder="16px" /></td>
                </tr>

                <!-- Detail Pane Settings -->
                <tr valign="top">
                    <th scope="row">Detail Pane Padding</th>
                    <td><input type="text" name="detail_padding_nd" value="<?php echo esc_attr( get_option('detail_padding_nd') ); ?>" placeholder="20px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Detail Pane Margin</th>
                    <td><input type="text" name="detail_margin_nd" value="<?php echo esc_attr( get_option('detail_margin_nd') ); ?>" placeholder="20px" /></td>
                </tr>

                <!-- Icon Settings -->
                <tr valign="top">
                    <th scope="row">Icon Color</th>
                    <td><input type="text" name="icon_color_nd" value="<?php echo esc_attr( get_option('icon_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Icon Background Color</th>
                    <td><input type="text" name="icon_background_color_nd" value="<?php echo esc_attr( get_option('icon_background_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Icon Border Color</th>
                    <td><input type="text" name="icon_border_color_nd" value="<?php echo esc_attr( get_option('icon_border_color_nd') ); ?>" class="team-members-color-picker" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Icon Border Radius</th>
                    <td><input type="text" name="icon_border_radius_nd" value="<?php echo esc_attr( get_option('icon_border_radius_nd') ); ?>" placeholder="50%" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Icon Size</th>
                    <td><input type="text" name="icon_size_nd" value="<?php echo esc_attr( get_option('icon_size_nd') ); ?>" placeholder="24px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Icon Padding</th>
                    <td><input type="text" name="icon_padding_nd" value="<?php echo esc_attr( get_option('icon_padding_nd') ); ?>" placeholder="5px" /></td>
                </tr>

                                <!-- Responsive Breakpoints -->
                                <tr valign="top">
                    <th scope="row">Tablet Breakpoint (px)</th>
                    <td><input type="number" name="tablet_breakpoint_nd" value="<?php echo esc_attr( get_option('tablet_breakpoint_nd', 1024) ); ?>" placeholder="1024" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Mobile Breakpoint (px)</th>
                    <td><input type="number" name="mobile_breakpoint_nd" value="<?php echo esc_attr( get_option('mobile_breakpoint_nd', 767) ); ?>" placeholder="767" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Shortcode to display team members
function team_members_nd_shortcode() {
    ob_start();

    $args = array( 'post_type' => 'team_member_nd', 'posts_per_page' => -1 );
    $team_query = new WP_Query( $args );

    if ( $team_query->have_posts() ) :
        echo '<div class="team-container-nd">';
        echo '<div class="team-grid-nd">';

        // Fetch the icon URLs
        $chevron_icon_url = plugins_url( 'assets/icons/chevron-icon.svg', __FILE__ );
        $close_icon_url = plugins_url( 'assets/icons/close-icon.svg', __FILE__ );

        while ( $team_query->have_posts() ) : $team_query->the_post();
            $picture = get_the_post_thumbnail_url();
            $name = get_the_title();
            $position_title = get_post_meta(get_the_ID(), 'position_title', true);
            $description = get_post_meta(get_the_ID(), 'description', true);

            echo '<div class="team-member-nd" data-description="'. esc_attr($description) .'">';
            echo '<img src="'. esc_url($picture) .'" alt="'. esc_attr($name) .'" style="border-radius: ' . esc_attr(get_option('image_border_radius_nd', '5px')) . ';">';
            echo '<div class="team-member-titles-nd"><h3>'. esc_html($name) .'</h3>';
            echo '<h4>'. esc_html($position_title) .'</h4>';
            echo '<img src="' . esc_url($chevron_icon_url) . '" class="chevron-nd" alt="Chevron Icon">';
            echo '</div></div>';
        endwhile;

        echo '</div>';
        echo '</div>';

    endif;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('team_members_nd', 'team_members_nd_shortcode');

// Enqueue the color picker script for the settings page
function team_members_nd_admin_enqueue_scripts($hook_suffix) {
    if ($hook_suffix == 'toplevel_page_team-members-settings-nd') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('team-members-nd-admin-script', plugins_url('assets/js/team-members-admin.js', __FILE__), array('wp-color-picker'), false, true);
    }
}
add_action('admin_enqueue_scripts', 'team_members_nd_admin_enqueue_scripts');