<?php
add_action( 'after_setup_theme', 'blankslate_setup' );
function blankslate_setup()
{
load_theme_textdomain( 'blankslate', get_template_directory() . '/languages' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );
global $content_width;
if ( ! isset( $content_width ) ) $content_width = 640;
register_nav_menus(
array( 'main-menu' => __( 'Main Menu', 'blankslate' ) )
);
}

add_action( 'wp_enqueue_scripts', 'blankslate_load_scripts' );
function blankslate_load_scripts()
{
wp_enqueue_script( 'jquery' );
}
add_action( 'comment_form_before', 'blankslate_enqueue_comment_reply_script' );
function blankslate_enqueue_comment_reply_script()
{
if ( get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
}

add_filter( 'the_title', 'blankslate_title' );
function blankslate_title( $title ) {
if ( $title == '' ) {
return '&rarr;';
} else {
return $title;
}
}
add_filter( 'wp_title', 'blankslate_filter_wp_title' );
function blankslate_filter_wp_title( $title )
{
return $title . esc_attr( get_bloginfo( 'name' ) );
}
add_action( 'widgets_init', 'blankslate_widgets_init' );
function blankslate_widgets_init()
{
register_sidebar( array (
'name' => __( 'Sidebar Widget Area', 'blankslate' ),
'id' => 'primary-widget-area',
'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
'after_widget' => "</li>",
'before_title' => '<div class="widget-title">',
'after_title' => '</div>',
) );
}

function blankslate_custom_pings( $comment )
{
$GLOBALS['comment'] = $comment;
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo comment_author_link(); ?></li>
<?php 
}
add_filter( 'get_comments_number', 'blankslate_comments_number' );
function blankslate_comments_number( $count )
{
if ( !is_admin() ) {
global $id;
$comments_by_type = &separate_comments( get_comments( 'status=approve&post_id=' . $id ) );
return count( $comments_by_type['comment'] );
} else {
return $count;
}
}

/* MOVE COMMENT TEXTAREA BACK DOWN & Remove cookies */

function wpb_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	unset( $fields['cookies'] );
	return $fields;
}
add_filter( 'comment_form_fields', 'wpb_move_comment_field_to_bottom' );

/* Custom comments template */

function custom_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	switch( $comment->comment_type ) :
        case 'pingback' : ?>
		<?php
        case 'trackback' : ?>
            <li <?php comment_class(); ?> id="comment<?php comment_ID(); ?>">
            <div class="back-link"><?php comment_author_link(); ?></div>
        <?php break;
        default : ?>
            <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            	<div <?php comment_class(); ?>>
			
            <?php
				$status = wp_get_comment_status(get_comment_ID());
				if($status != 'approved') {
				?>
                <span class="comment-unapproved"><b><small class="half-tone">Ваш отзыв ожидает модерации.</small></b></span>
                <?php
				}
			?>
            
            <div class="comments__block">
                <div class="comments__author row">

					<div class="comments__left">
						<div class="comments__avatar"></div>

						<div class="comments__reply"><?php 
						comment_reply_link( array_merge( $args, array( 
							'reply_text' => 'Ответить',
							'after' => '', 
							'depth' => $depth,
							'max_depth' => $args['max_depth'] 
							) ) ); ?>
						</div><!-- .reply -->
					</div>

					<div class="comments__right col">
						<div class="comments_info d-flex justify-content-between">
							<span class="comments__name"><?php comment_author(); ?></span>
							<time datetime="<?php comment_time('c'); ?>" class="comments__time"><?php comment_date(); ?></time>
						</div>

						<div class="comments__text">
							<?php comment_text(); ?>
						</div>
					</div>

                </div><!-- .vcard -->
            </div><!-- comment-body -->
            </div><!-- #comment-<?php comment_ID(); ?> -->
        <?php // End the default styling of comment
        break;
    endswitch;
}
function custom_comments_rating( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	switch( $comment->comment_type ) :
        case 'pingback' : ?>
		<?php
        case 'trackback' : ?>
            <li <?php comment_class(); ?> id="comment<?php comment_ID(); ?>">
            <div class="back-link"><?php comment_author_link(); ?></div>
        <?php break;
        default : ?>
            <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            	<div <?php comment_class(); ?>>
			
            <?php
				$status = wp_get_comment_status(get_comment_ID());
				if($status != 'approved') {
				?>
                <span class="comment-unapproved"><b><small class="half-tone">Ваш отзыв ожидает модерации.</small></b></span>
                <?php
				}
			?>
            
            <div class="comments__block">
                <div class="comments__author row">

					<div class="comments__left">
						<?= get_avatar( $comment, 100, '', get_comment_author(get_comment_ID()) ); ?>

						<div class="comments__reply"><?php 
						comment_reply_link( array_merge( $args, array( 
							'reply_text' => 'Ответить',
							'after' => '', 
							'depth' => $depth,
							'max_depth' => $args['max_depth'] 
							) ) ); ?>
						</div><!-- .reply -->
					</div>

					<div class="comments__right col">
						<div class="comments_info d-flex justify-content-between">
							<div class="comments__name">
								
							   	<span><?php comment_author(); ?></span>

								<div class="comments__rating">
									<ul class="rating<?= get_comment_meta( get_comment_ID(), 'rating', true ) ?>">
										<li></li>
										<li></li>
										<li></li>
										<li></li>
										<li></li>
									</ul>
								</div>
								
						</div>

							<time datetime="<?php comment_time('c'); ?>" class="comments__time"><?php comment_date(); ?></time>
						</div>

						<div class="comments__text">
							<?php comment_text(); ?>
						</div>
					</div>

                </div><!-- .vcard -->
            </div><!-- comment-body -->
            </div><!-- #comment-<?php comment_ID(); ?> -->
        <?php // End the default styling of comment
        break;
    endswitch;
}
	
// убираем h3 в форме комментирования
function my_comment_form_before() {
    ob_start();
}
add_action( 'comment_form_before', 'my_comment_form_before' );
 
function my_comment_form_after() {
    $html = ob_get_clean();
    $html = preg_replace(
        '/<h3 id="reply-title"(.*)>(.*)<\/h3>/',
        '<div id="reply-title"\1>\2</div>',
        $html
    );
    echo $html;
}
add_action( 'comment_form_after', 'my_comment_form_after' );

//--------------------------------------------
// To save 5 stars rating in comments template
add_action( 'comment_post', 'save_extend_comment_meta_data' );
function save_extend_comment_meta_data( $comment_id ){
	if( !empty( $_POST['rating'] ) ){
		$rating = intval($_POST['rating']);
		add_comment_meta( $comment_id, 'rating', $rating );
	}
}

//======================================//
// Clean <head></head> on Front-End		//
//======================================//
if(!is_admin() && !is_user_logged_in()) {
	remove_action('wp_head', 'wp_generator');
   	remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
	remove_action('wp_head', 'wp_print_footer_scripts');
    remove_action('wp_head', 'wp_enqueue_scripts', 1);
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'noindex', 1);
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');

	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );

	wp_deregister_script('jquery');
}

//==============================
// Remove .recentcomments styles
function remove_recent_comments_style() {
    global $wp_widget_factory;
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}
add_action('widgets_init', 'remove_recent_comments_style');

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function wpdocs_custom_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

			
// add the filter 
add_filter( 'bcn_breadcrumb_title', 'filter_bcn_breadcrumb_title', 10, 3 ); 

//-----------------------
// CUSTOM POST TYPES:
// - yachts
function create_posttype() {
	// yachts
	register_post_type( 'yachts',
	  array(
		'labels' => array(
		  'name' => __( 'Яхты Сочи' ),
		  'singular_name' => __( 'Яхты Сочи' )
		),
		'public' => true,
		'publicly_queryable' => true,
		'rest_base' => '',
		'hierarchical' => true,
		'show_in_rest' => true,
		'has_archive' => false,
		'capability_type' => 'page',
		'rewrite' => array('slug' => '/catalog-yacht/yachts-sochi', 'with_front' => false),
		'menu_position' => 5,
		'menu_icon' => 'dashicons-portfolio',
		'supports'  => array( 'title', 'thumbnail', 'editor', 'comments' )
	  )
	);

	// yachts-adler
	register_post_type( 'yachts-adler',
	  array(
		'labels' => array(
		  'name' => __( 'Яхты Адлер' ),
		  'singular_name' => __( 'Яхты Адлер' )
		),
		'public' => true,
		'publicly_queryable' => true,
		'rest_base' => '',
		'hierarchical' => true,
		'show_in_rest' => true,
		'has_archive' => false,
		'capability_type' => 'page',
		'rewrite' => array('slug' => 'adler/catalog-yacht-adler/yachts-adler', 'with_front' => false),
		//'rewrite' => array('slug' => 'adler', 'with_front' => false),
		'menu_position' => 5,
		'menu_icon' => 'dashicons-portfolio',
		'supports'  => array( 'title', 'thumbnail', 'editor', 'comments' )
	  )
	);

	// yachts-lazar
	register_post_type( 'yachts-lazar',
	  array(
		'labels' => array(
		  'name' => __( 'Яхты Лазаревское' ),
		  'singular_name' => __( 'Яхты Лазаревское' )
		),
		'public' => true,
		'publicly_queryable' => true,
		'rest_base' => '',
		'hierarchical' => true,
		'show_in_rest' => true,
		'has_archive' => false,
		'capability_type' => 'page',
		'rewrite' => array('slug' => 'lazarevskoe/catalog-yacht-lazarevskoe/yachts-lazarevskoe', 'with_front' => false),
		'menu_position' => 5,
		'menu_icon' => 'dashicons-portfolio',
		'supports'  => array( 'title', 'thumbnail', 'editor', 'comments' )
	  )
	);

	// services
	register_post_type( 'uslugi',
	  array(
		'labels' => array(
		  'name' => __( 'Услуги Сочи' ),
		  'singular_name' => __( 'Услуги аренды яхт' )
		),
		'public' => true,
		'publicly_queryable' => true,
		'rest_base' => '',
		'hierarchical' => true,
		'show_in_rest' => true,
		'has_archive' => false,
		'capability_type' => 'page',
		'rewrite' => array('slug' => 'uslugi', 'with_front' => false),
		'menu_position' => 5,
		'menu_icon' => 'dashicons-portfolio',
		'supports'  => array( 'title', 'thumbnail', 'editor', 'comments', 'custom-fields' )
	  )
	);
  }
  add_action( 'init', 'create_posttype' );

// Meta Boxes


//
//	SOCHI
//

function yachts_get_meta_box( $meta_boxes ) {
	$prefix = 'yachts-';

	$meta_boxes[] = array(
		'id' => 'yachts',
		'title' => esc_html__( 'Информация о яхте', 'yachts-generator' ),
		'post_types' => array( 'yachts' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => false,
		'fields' => array(
			array(
				'id' => $prefix . 'yachts_status',
				'type' => 'select',
				'name' => esc_html__( 'Статус судна', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите подраздел: 1 - доступна, 2 - недоступна', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Статус судна', 'metabox-online-generator' ),
				'options' => array(
					1 => '1 - Доступна',
					'2 - Недоступна'
				),
				// 'std' => '2',	// Default value
			),
			array(
				'id' => $prefix . 'yachts_images',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Фотографии яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'Сюда загружаем фотографии яхт.', 'yachts-generator' ),
				'max_file_uploads' => '20',
				'force_delete' => true,
			),
			array(
				'id' => $prefix . 'yachts_videos',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Видео яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'Сюда загружаем видео яхт.', 'yachts-generator' ),
				'max_file_uploads' => '20',
				'force_delete' => true,
			),
			array(
				'id' => $prefix . 'yacht_name',
				'type' => 'text',
				'name' => esc_html__( 'Модель яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'текстовое название модели яхты (напр. "Starfisher")', 'yachts-generator' ),
				'placeholder' => esc_html__( 'Модель', 'yachts-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_nickname',
				'type' => 'text',
				'name' => esc_html__( 'Никнейм', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Никнейм яхты (напр. "Чёрная Жемчужина")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Никнейм', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_price',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\час)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за час аренды в рублях (напр. "8000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_priceday',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\день)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за сутки аренды в рублях (напр. "95000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена в сутки', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_priceweek',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\неделю)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за неделю аренды в рублях (напр. "1850000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена в неделю', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_capacity',
				'type' => 'text',
				'name' => esc_html__( 'Пассажировместимость (чел)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Максимальное кол-во людей на борту (напр. "10")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Пассажировместимость', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_class',
				'type' => 'text',
				'name' => esc_html__( 'Класс', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Класс яхты: 1 - моторная, 2 - парусная, 3 - катамаран, 4 - теплоход, 5 - катер', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Класс', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_manufacturer',
				'type' => 'text',
				'name' => esc_html__( 'Производитель', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Страна производителя', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Производитель', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_shipyard',
				'type' => 'select',
				'name' => esc_html__( 'Верфь', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите верфрь', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Верфь', 'metabox-online-generator' ),
				'options' => array(
					1 => 'Majesty Yachts (ОАЭ)',
					2 => 'Azimut Yachts (Италия)',
					3 => 'Beneteau (Франция)',
					4 => 'Bavaria (Германия)',
					5 => 'Bayliner (США)',
					6 => 'Carver (США)',
					7 => 'Chaparral (США)',
					8 => 'Concept (США)',
					9 => 'Cranchi (Италия)',
					10 => 'Dufour Yachts (Франция)',
					11 => 'Fairline (Великобритания)',
					12 => 'Ferretti (Италия)',
					13 => 'Four Winns (США)',
					14 => 'Lagoon (Франция)',
					15 => 'Fountaine Pajot (Франция)',
					16 => 'Linssen (Нидерланды)',
					17 => 'Maxum Boat (США)',
					18 => 'Meridian (США)',
					19 => 'Monterey (США)',
					20 => 'Prestige (Франция)',
					21 => 'Princess Yachts (Великобритания)',
					22 => 'Sea Ray (США)',
					23 => 'Hanse Yachts (Германия)',
					24 => 'Silverton Marine (США)',
					25 => 'Starfisher (Португалия)',
					26 => 'Sunseeker (Великобритания)',
					27 => 'Velvette Marine (Россия)',
					28 => 'Wellcraft (США)'
				),
			),
			array(
				'id' => $prefix . 'yacht_year',
				'type' => 'text',
				'name' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_motor',
				'type' => 'text',
				'name' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_length',
				'type' => 'text',
				'name' => esc_html__( 'Длина (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Длина', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Длина', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_wide',
				'type' => 'text',
				'name' => esc_html__( 'Ширина (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Ширина', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Ширина', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_draft',
				'type' => 'text',
				'name' => esc_html__( 'Осадка (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Осадка', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Осадка', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_speed',
				'type' => 'text',
				'name' => esc_html__( 'Скорость (узлов)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Скорость', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Скорость', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_cabins',
				'type' => 'text',
				'name' => esc_html__( 'Количество кают (шт)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Количество кают', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Количество кают', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_stock',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'В наличии', 'metabox-online-generator' ),
				'desc' => esc_html__( 'В наличии', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'В наличии', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_description',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'Описание', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Описание яхты', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Описание', 'metabox-online-generator' ),
			),
			array(
				'id'      => $prefix . 'yacht_similar',
				'type'    => 'checkbox_list',
				'name' => esc_html__( 'Похожие яхты', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите похожие яхты', 'metabox-online-generator' ),
				'options' => get_yachts_list('yachts'),
				'inline' => false,
				'select_all_none' => true,
			),
			
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'yachts_get_meta_box' );

//
// ADLER
//

function yachts_adler_get_meta_box( $meta_boxes ) {
	$prefix = 'yachts-adler-';

	$meta_boxes[] = array(
		'id' => 'yachts-adler',
		'title' => esc_html__( 'Информация о яхте', 'yachts-adler-generator' ),
		'post_types' => array( 'yachts-adler' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => false,
		'fields' => array(
			array(
				'id' => $prefix . 'yachts_status',
				'type' => 'select',
				'name' => esc_html__( 'Статус судна', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите подраздел: 1 - доступна, 2 - недоступна', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Статус судна', 'metabox-online-generator' ),
				'options' => array(
					1 => '1 - Доступна',
					'2 - Недоступна'
				),
				// 'std' => '2',	// Default value
			),
			array(
				'id' => $prefix . 'yachts_images',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Фотографии яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'Сюда загружаем фотографии яхт.', 'yachts-generator' ),
				'max_file_uploads' => '20',
				'force_delete' => true,
			),
			array(
				'id' => $prefix . 'yachts_videos',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Видео яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'Сюда загружаем видео яхт.', 'yachts-generator' ),
				'max_file_uploads' => '20',
				'force_delete' => true,
			),
			array(
				'id' => $prefix . 'yacht_name',
				'type' => 'text',
				'name' => esc_html__( 'Модель яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'текстовое название модели яхты (напр. "Starfisher")', 'yachts-generator' ),
				'placeholder' => esc_html__( 'Модель', 'yachts-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_nickname',
				'type' => 'text',
				'name' => esc_html__( 'Никнейм', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Никнейм яхты (напр. "Чёрная Жемчужина")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Никнейм', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_price',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\час)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за час аренды в рублях (напр. "8000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_priceday',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\день)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за сутки аренды в рублях (напр. "95000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена в сутки', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_priceweek',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\неделя)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за неделю аренды в рублях (напр. "1850000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена в неделю', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_capacity',
				'type' => 'text',
				'name' => esc_html__( 'Пассажировместимость (чел)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Максимальное кол-во людей на борту (напр. "10")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Пассажировместимость', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_class',
				'type' => 'text',
				'name' => esc_html__( 'Класс', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Класс яхты: 1 - моторная, 2 - парусная, 3 - катамаран, 4 - теплоход, 5 - катер', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Класс', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_manufacturer',
				'type' => 'text',
				'name' => esc_html__( 'Производитель', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Страна производителя', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Производитель', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_shipyard',
				'type' => 'select',
				'name' => esc_html__( 'Верфь', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите верфрь', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Верфь', 'metabox-online-generator' ),
				'options' => array(
					1 => 'Majesty Yachts (ОАЭ)',
					2 => 'Azimut Yachts (Италия)',
					3 => 'Beneteau (Франция)',
					4 => 'Bavaria (Германия)',
					5 => 'Bayliner (США)',
					6 => 'Carver (США)',
					7 => 'Chaparral (США)',
					8 => 'Concept (США)',
					9 => 'Cranchi (Италия)',
					10 => 'Dufour Yachts (Франция)',
					11 => 'Fairline (Великобритания)',
					12 => 'Ferretti (Италия)',
					13 => 'Four Winns (США)',
					14 => 'Lagoon (Франция)',
					15 => 'Fountaine Pajot (Франция)',
					16 => 'Linssen (Нидерланды)',
					17 => 'Maxum Boat (США)',
					18 => 'Meridian (США)',
					19 => 'Monterey (США)',
					20 => 'Prestige (Франция)',
					21 => 'Princess Yachts (Великобритания)',
					22 => 'Sea Ray (США)',
					23 => 'Hanse Yachts (Германия)',
					24 => 'Silverton Marine (США)',
					25 => 'Starfisher (Португалия)',
					26 => 'Sunseeker (Великобритания)',
					27 => 'Velvette Marine (Россия)',
					28 => 'Wellcraft (США)'
				),
			),
			array(
				'id' => $prefix . 'yacht_year',
				'type' => 'text',
				'name' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_motor',
				'type' => 'text',
				'name' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_length',
				'type' => 'text',
				'name' => esc_html__( 'Длина (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Длина', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Длина', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_wide',
				'type' => 'text',
				'name' => esc_html__( 'Ширина (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Ширина', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Ширина', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_draft',
				'type' => 'text',
				'name' => esc_html__( 'Осадка (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Осадка', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Осадка', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_speed',
				'type' => 'text',
				'name' => esc_html__( 'Скорость (узлов)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Скорость', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Скорость', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_cabins',
				'type' => 'text',
				'name' => esc_html__( 'Количество кают (шт)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Количество кают', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Количество кают', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_stock',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'В наличии', 'metabox-online-generator' ),
				'desc' => esc_html__( 'В наличии', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'В наличии', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_description',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'Описание', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Описание яхты', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Описание', 'metabox-online-generator' ),
			),
			array(
				'id'      => $prefix . 'yacht_similar',
				'type'    => 'checkbox_list',
				'name' => esc_html__( 'Похожие яхты', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите похожие яхты', 'metabox-online-generator' ),
				'options' => get_yachts_list('yachts-adler'),
				'inline' => false,
				'select_all_none' => true,
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'yachts_adler_get_meta_box' );


// -----------
// Lazarevskoe
// -----------

function yachts_lazar_get_meta_box( $meta_boxes ) {
	$prefix = 'yachts-lazar-';

	$meta_boxes[] = array(
		'id' => 'yachts-lazar',
		'title' => esc_html__( 'Информация о яхте', 'yachts-lazar-generator' ),
		'post_types' => array( 'yachts-lazar' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => false,
		'fields' => array(
			array(
				'id' => $prefix . 'yachts_status',
				'type' => 'select',
				'name' => esc_html__( 'Статус судна', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите подраздел: 1 - доступна, 2 - недоступна', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Статус судна', 'metabox-online-generator' ),
				'options' => array(
					1 => '1 - Доступна',
					'2 - Недоступна'
				),
				// 'std' => '2',	// Default value
			),
			array(
				'id' => $prefix . 'yachts_images',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Фотографии яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'Сюда загружаем фотографии яхт.', 'yachts-generator' ),
				'max_file_uploads' => '20',
				'force_delete' => true,
			),
			array(
				'id' => $prefix . 'yachts_videos',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Видео яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'Сюда загружаем видео яхт.', 'yachts-generator' ),
				'max_file_uploads' => '20',
				'force_delete' => true,
			),
			array(
				'id' => $prefix . 'yacht_name',
				'type' => 'text',
				'name' => esc_html__( 'Модель яхты', 'yachts-generator' ),
				'desc' => esc_html__( 'текстовое название модели яхты (напр. "Starfisher")', 'yachts-generator' ),
				'placeholder' => esc_html__( 'Модель', 'yachts-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_nickname',
				'type' => 'text',
				'name' => esc_html__( 'Никнейм', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Никнейм яхты (напр. "Чёрная Жемчужина")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Никнейм', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_price',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\час)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за час аренды в рублях (напр. "8000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_priceday',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\день)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за сутки аренды в рублях (напр. "95000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена в сутки', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_priceweek',
				'type' => 'text',
				'name' => esc_html__( 'Цена (руб\неделю)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Цена за неделю аренды в рублях (напр. "1850000")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Цена в неделю', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_capacity',
				'type' => 'text',
				'name' => esc_html__( 'Пассажировместимость (чел)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Максимальное кол-во людей на борту (напр. "10")', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Пассажировместимость', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_class',
				'type' => 'text',
				'name' => esc_html__( 'Класс', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Класс яхты: 1 - моторная, 2 - парусная, 3 - катамаран, 4 - теплоход, 5 - катер', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Класс', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_manufacturer',
				'type' => 'text',
				'name' => esc_html__( 'Производитель', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Страна производителя', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Производитель', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_shipyard',
				'type' => 'select',
				'name' => esc_html__( 'Верфь', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите верфрь', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Верфь', 'metabox-online-generator' ),
				'options' => array(
					1 => 'Majesty Yachts (ОАЭ)',
					2 => 'Azimut Yachts (Италия)',
					3 => 'Beneteau (Франция)',
					4 => 'Bavaria (Германия)',
					5 => 'Bayliner (США)',
					6 => 'Carver (США)',
					7 => 'Chaparral (США)',
					8 => 'Concept (США)',
					9 => 'Cranchi (Италия)',
					10 => 'Dufour Yachts (Франция)',
					11 => 'Fairline (Великобритания)',
					12 => 'Ferretti (Италия)',
					13 => 'Four Winns (США)',
					14 => 'Lagoon (Франция)',
					15 => 'Fountaine Pajot (Франция)',
					16 => 'Linssen (Нидерланды)',
					17 => 'Maxum Boat (США)',
					18 => 'Meridian (США)',
					19 => 'Monterey (США)',
					20 => 'Prestige (Франция)',
					21 => 'Princess Yachts (Великобритания)',
					22 => 'Sea Ray (США)',
					23 => 'Hanse Yachts (Германия)',
					24 => 'Silverton Marine (США)',
					25 => 'Starfisher (Португалия)',
					26 => 'Sunseeker (Великобритания)',
					27 => 'Velvette Marine (Россия)',
					28 => 'Wellcraft (США)'
				),
			),
			array(
				'id' => $prefix . 'yacht_year',
				'type' => 'text',
				'name' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Год постройки', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_motor',
				'type' => 'text',
				'name' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Двигатель', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_length',
				'type' => 'text',
				'name' => esc_html__( 'Длина (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Длина', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Длина', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_wide',
				'type' => 'text',
				'name' => esc_html__( 'Ширина (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Ширина', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Ширина', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_draft',
				'type' => 'text',
				'name' => esc_html__( 'Осадка (М)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Осадка', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Осадка', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_speed',
				'type' => 'text',
				'name' => esc_html__( 'Скорость (узлов)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Скорость', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Скорость', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_cabins',
				'type' => 'text',
				'name' => esc_html__( 'Количество кают (шт)', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Количество кают', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Количество кают', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_stock',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'В наличии', 'metabox-online-generator' ),
				'desc' => esc_html__( 'В наличии', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'В наличии', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'yacht_description',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'Описание', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Описание яхты', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Описание', 'metabox-online-generator' ),
			),
			array(
				'id'      => $prefix . 'yacht_similar',
				'type'    => 'checkbox_list',
				'name' => esc_html__( 'Похожие яхты', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите похожие яхты', 'metabox-online-generator' ),
				'options' => get_yachts_list('yachts-lazar'),
				'inline' => false,
				'select_all_none' => true,
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'yachts_lazar_get_meta_box' );

//	------------------------------------------------------
//	Services custom fields for custom post type 'uslugi'

function services_get_meta_box( $meta_boxes ) {
	$prefix = 'uslugi-';

	$meta_boxes[] = array(
		'id' => 'services',
		'title' => esc_html__( 'Информация об услуге', 'services-generator' ),
		'post_types' => array( 'uslugi' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => false,
		'fields' => array(
			
			array(
				'id' => $prefix . 'uslugi_type',
				'type' => 'select',
				'name' => esc_html__( 'Подраздел', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Выберите подраздел: 1 - Свадебные услуги, 2 - Организационные услуги, 3 - Услуги для мероприятий, 4 - Водные развлечения', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Подраздел услуги', 'metabox-online-generator' ),
				'options' => array(
					1 => '1 - Свадебные услуги',
					'2 - Организационные услуги',
					'3 - Услуги для мероприятий',
					'4 - Водные развлечения'
				),
				// 'std' => '2',	// Default value
			),
			
			array(
				'id' => $prefix . 'uslugi_shortdesc',
				'type' => 'wysiwyg',
				'name' => esc_html__( 'Краткое описание', 'metabox-online-generator' ),
				'desc' => esc_html__( 'Краткое описание', 'metabox-online-generator' ),
				'placeholder' => esc_html__( 'Краткое описание', 'metabox-online-generator' ),
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'services_get_meta_box' );

/* -------------------------------------------- */
/* Get custom posts for loops in checkbox lists */

function get_yachts_list($post_type) {

	$args = array(
		'post_type' => $post_type,
		'numberposts' => 200
	);
	$list = get_posts($args);

	$arr = array();
	for($x = 0; $x < count($list); $x++) {
		$key = $list[$x] -> ID;
		$value = $list[$x] -> post_name;
		$arr[$key] = $value;
	}

	return $arr;

}

  /*-------------*/
  /* BREADCRUMBS */

  function filter_bcn_breadcrumb_title( $title, $this_type, $this_id ) { 
	//$bc_name = get_post_custom_values('bc_name', $this_id)[0];
	$result = $title;
	
	//if(count($bc_name) > 0 && $this_type[0] == 'post' || $this_type[1] == 'post') {
	//	$result = $bc_name;
	//}

	return $result;
}; 
			
// add the filter 
add_filter( 'bcn_breadcrumb_title', 'filter_bcn_breadcrumb_title', 10, 3 ); 

// remove for w3c
add_filter('script_loader_tag', 'clean_script_tag');
    function clean_script_tag($src) {
    return str_replace(" type='text/javascript'", '', $src);
}

add_filter( 'rest_allow_anonymous_comments', function ( $allow_anonymous, $request ) {
    // ... custom logic here ...
    return true; // or false to prohibit anonymous comments via post
}, 10, 2 ); 


// turbo pages
require('parts/functions/yandex-turbo.php');

// --------------------------------------
// Run `npm run build` at sochicharter.ru 
/*
add_action('save_post', function ($post_id, $post, $update) {

    // Avoid autosaves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }

    $url = 'https://sochicharter.ru/php/build.php';
    $data = [
        'password' => 'scsecret',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);
}, 10, 3);
*/

// ----
// CORS
// ----

add_action('init', function () {
    register_post_meta('post', 'count', [
        'type'         => 'integer',
        'single'       => true,
        'show_in_rest' => true,
        'default'      => 0,
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
});
