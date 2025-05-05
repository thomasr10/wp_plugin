<?php

/*
Plugin Name: Témoignages Clients
Description: Un plugin pour gérer et afficher des témoignages clients
Version: 1.0
Author: Thomas
*/

function create_post_type() {
    register_post_type('temoignage', [
        'label' => 'Témoignage',
        'public' => true,
        'show_in_menu' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'temoignage'],
        'taxonomies' => ['post_tag']
    ]);
}

add_action('init', 'create_post_type');



function get_temoignage($param) {

    $param = shortcode_atts([
        'n' => 3,
        'tag' => ''
    ], $param);

    $n = intval($param['n']);
    $tag = sanitize_text_field($param['tag']);
    
    $args = [
        'post_type' => 'temoignage',
        'posts_per_page' => $n,
    ];

    if (!empty($tag)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'post_tag',
                'field'    => 'slug',
                'terms'    => $tag,
                'operator' => 'IN',
            ],
        ];
    }

    $the_query = new WP_Query($args);

    $color = isset($_POST['color']) ? esc_attr($_POST['color']) : 'white';
    $selected_num = isset($_POST['num-post']) ? esc_attr($_POST['num-post']) : '';
    $selected_tag = isset($_POST['tags']) ? esc_attr($_POST['tags']) : '';

    $output = '<div class="container">';
    $output .= '<div class="options">
                    <form method="POST">' .
                        '<label for="num-post">Nombre de témoignages</label>' .
                        '<select name="num-post" id="num-post">' .
                            '<option value=""> Tous les témoignages </option>' .
                            '<option value="1"' . selected($_POST['num-post'], '1', false) . '> 1 </option>' .
                            '<option value="2"' . selected($_POST['num-post'], '2', false) . '> 2 </option>' .
                            '<option value="3"' . selected($_POST['num-post'], '3', false) . '> 3 </option>' .
                            '<option value="4"' . selected($_POST['num-post'], '4', false) . '> 4 </option>' .
                            '<option value="5"' . selected($_POST['num-post'], '5', false) . '> 5 </option>' .
                            '<option value="6"' . selected($_POST['num-post'], '6', false) . '> 6 </option>' .
                            '<option value="7"' . selected($_POST['num-post'], '7', false) . '> 7 </option>' .
                            '<option value="8"' . selected($_POST['num-post'], '8', false) . '> 8 </option>' .
                            '<option value="9"' . selected($_POST['num-post'], '9', false) . '> 9 </option>' .
                            '<option value="10"' . selected($_POST['num-post'], '10', false) . '> 10 </option>' .
                        '</select>' .
                    
                        '<label for="color">Couleur</label>' .
                        '<select name="color" id="color">' .
                            '<option value="white"' . selected($color, 'white', false) . '>White</option>' .
                            '<option value="black"' . selected($color, 'black', false) . '>Black</option>' .
                            '<option value="purple"' . selected($color, 'purple', false) . '>Purple</option>' .
                        '</select>' .

                        '<label for="tags">Filtrer par tag</label>' .
                        '<select name="tags" id="tags">' .
                            '<option value=""> Filtrer par tag </option>' .
                            '<option value="rapidite"' . selected($selected_tag, 'rapidite', false) . '>Rapidité</option>' .
                            '<option value="qualite_service"' . selected($selected_tag, 'qualite_service', false) . '>Qualité du service</option>' .
                            '<option value="support_client"' . selected($selected_tag, 'support_client', false) . '>Support client</option>' .
                        '</select>' .
                        '<input type="submit">' .
                    '</form>' .
                '</div>';
    
    if ( $the_query->have_posts() ) {
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $output .= '<div class="post ' . $color . '"' . '><div class="title">' . '<h3>' . esc_html( get_the_title() ) . '</h3>' . ' - ' . '<p>' . esc_html( get_field('poste') ) . '</p>' . '</div>';
            $output .= '<p>' . esc_html( get_the_content() ) . '</p>';
            $tags = get_the_tags();

            if ($tags) {
                $output .= '<div class="tags">';
                foreach($tags as $tag) {
                    $output .= '<span class="tag">#' . esc_html($tag->name) . '</span> ';
                }
                $output .= '</div>';
            }
            $output .= '<p class="note">'. 'Note : ';
            $note = esc_html(get_field('note'));

            if(ctype_digit($note)){
                for($i = 0; $i < $note; $i++){
                    $output .= '<i class="fa-solid fa-star"></i>';
                }

                $diff = 5 - $note;

                for($i = 0; $i < $diff; $i++){
                    $output .= '<i class="fa-regular fa-star"></i>';
                }

            } else {
                for($i = 0; $i < $note - 0.5; $i++){
                    $output .= '<i class="fa-solid fa-star"></i>';
                }

                $output .= '<i class="fa-solid fa-star-half-stroke"></i>';

                $diff = 4.5 - $note;

                for($i = 0; $i < $diff; $i++){
                    $output .= '<i class="fa-regular fa-star"></i>';
                }
            }

            $output .= '</p>'.'</div>';
        }
    } else {
        $output .= '<li>Aucun témoignage trouvé.</li>';
    }
    $output .= '</div>';

    wp_reset_postdata();
    return $output;
}
add_shortcode( 'temoignage', 'get_temoignage' );


// link css

function style_temoignage() {
    wp_enqueue_style(
        'temoignage-style',
        plugin_dir_url(__FILE__) . 'asset/style.css',
    );
}

add_action('wp_enqueue_scripts', 'style_temoignage');

function link_fontawesome() {
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css'
    );
}

add_action('wp_enqueue_scripts', 'link_fontawesome');