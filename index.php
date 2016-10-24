<?php
/**
* Plugin Name: Gallery Of Galleries
* Description: Get Atachment files on posts like a Gallery.
* Version: 1.0
* Author: Jose Toro
* License: GPL12
*/

/*

function get_galleries() {
    $query = new WP_Query(Array(
      'post_type' => "gallery",
      'has_password' => False,
      'orderby' => 'date',
      'order' => 'DESC'
    ));
	$galleries = array();
	foreach ($query->posts as $post)
    {
		$attachments = get_attached_media('image', $post->ID );

        if (count($attachments) > 0) {

            $gallery = new stdClass();
            $gallery->idNota = $post->ID;
            $gallery->title = $post->post_title;
            $gallery->media = array();
            foreach ($attachments as $attachment)
            {
                $image = new stdClass();
                $image->idAdjunto = $attachment->ID;
                $image->type = "Imagen";
                $image->altTitle = $attachment->post_title;
                $image->caption = $attachment->post_title;
                $image->url = wp_get_attachment_image_src($attachment->ID, 'full')[0];
                $image->thumbnail = wp_get_attachment_image_src($attachment->ID, 'thumbnail')[0];

                $image->link_name = "#";
                $image->linkTarget = "#";

                $gallery->media[] = $image;
            }

            $galleries[] = $gallery;
        }
    }
    return $galleries;
}

*/

function get_galleries() {

    global $wpdb;

    $sql = "SELECT gallery.id AS idNota,
		    gallery.name as title,
            image.id AS idAdjunto,
            image.name AS altTitle,
            TRIM( TRAILING ';' from image.image_url) AS url
            FROM " . $wpdb->prefix . "huge_itportfolio_portfolios AS gallery
            INNER JOIN " . $wpdb->prefix . "huge_itportfolio_images AS image ON image.portfolio_id = gallery.id
            ORDER BY gallery.id DESC, image.ordering";


    $query = $wpdb->prepare($sql, "");
    $result = $wpdb->get_results($query, OBJECT);

    $galleries = array();

    foreach ($result as $result) {

        if (! $galleries[$result->idNota]) {

            $galleries[$result->idNota] = (object)array(
                "idNota" => $result->idNota,
                "title" => $result->title,
                "media" => array()
            );

        }

        $image = new stdClass();
        $image->idAdjunto = $result->idAdjunto;
        $image->type = "Imagen";
        $image->altTitle = $result->altTitle;
        $image->caption = $result->altTitle;
        $image->url =  $result->url;
        $image->thumbnail = $result->url;

        $image->link_name = "#";
        $image->linkTarget = "#";

        $galleries[$result->idNota]->media[] = $image;
    }

    $return = array();

    foreach ($galleries as $gallery) {

        $return[] = $gallery;

    };

    return $return;
}

function print_posts() {
	wp_enqueue_style( 'photo-gallery', plugins_url( '/css/photo-gallery.css', __FILE__ ), false, '1.0', 'all' );
    wp_enqueue_style( 'gallery-of-galleries', plugins_url( '/css/gallery-of-galleries.css', __FILE__ ), false, '1.0', 'all' );
    wp_enqueue_script( 'jquery', plugins_url( '/js/jquery-1.10.1.min.js', __FILE__ ), false, '1.0', 'all' );
    wp_enqueue_script( 'videos-gallery', plugins_url( '/js/videos-gallery.min.js', __FILE__ ), false, '1.0', 'all' );
	wp_enqueue_script( 'photo-gallery', plugins_url( '/js/photo-gallery.min.js', __FILE__ ), false, '1.0', 'all' );

    echo "<script> var g3cObject_1988_1034Content = " . json_encode(get_galleries()) . "</script>";

?>

    <div class="photo-gallery-container air gallery-three-columns ">
        <div id="g3cObject_1988_1034" class="photo-gallery" data-gallery data-gallery3cobject_1988_1034 data-gallery-position="vertical">
            <div class="gallery-area">
                <h2 class="photo-gallery-title clearfix">
                    <a href="#">
                        <span class="content"></span>
                        <span class="photo-gallery-counter"></span>
                    </a>
                </h2>
                <div class="photo-gallery-active media slide-container">
                    <a href="" data-controller="prev" class="controller prev clear-me">
                        <span class="icon-container">
                            <span class="icon icon-arrow-left"></span>
                        </span>
                    </a>
                    <a href="" data-controller="next" class="controller next clear-me">
                        <span class="icon-container">
                            <span class="icon icon-arrow-right"></span>
                        </span>
                    </a>
                    <div class="slide" data-template data-type="Imagen" data-slide>
                        <img x-src="{[ url ]}" alt="{[ altTitle ]}" title="{[ altTitle ]}" width="891" height="592" class="full" />
                        <a href="{[ link ]}" target="{[ linkTarget ]}" class="photo-gallery-caption positioned bottom-left clear-me">{[ caption ]}</a>
                    </div>
                    <div class="slide" data-template data-type="Script" data-slide>
                        <div class="media adjust video">
                            {[ content ]}
                        </div>
                    </div>
                </div>
                <div class="photo-gallery-wrapper thumbnails-wrapper clearfix">
                    <div class="photo-gallery-slider thumbnails">
                        <div class="photo-gallery-item item" data-template>
                            <a href="">
                                <img class="photo-gallery-thumbnail" x-src="{[ thumbnail ]}" alt="{[ altTitle ]}" title="{[ altTitle ]}" width="122" height="68" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gallery-selector">
                <a href="" data-controller="prev-gallery" class="controller prev small full keep"></a>
                <a href="" data-controller="next-gallery" class="controller next small full keep"></a>
                <div class="gallery-selector-slider">
                    <div class="gallery-selector-wrapper keep">
                        <div class="gallery-selector-item" data-template>
                            <div class="gallery-selector-media">
                                <img x-src="{[ thumbnail]}" alt="{[ altTitle ]}" title="{[ altTitle ]}" width="204" height="115" class="thumbnail" />
                            </div>
                            <h2 class="gallery-title">{[ title ]}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin foto galeria activa -->
    </div>

    <?php

}
add_shortcode('galleries', 'print_posts');
