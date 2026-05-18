<?php if ( 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) ) return; ?>

<?php
ob_start();
if(comments_open()) comment_form(array(
	
	'title_reply' => '',
	'class_form' => 'comments__form',
	'class_submit' => 'col-xl-5 button button_submit transitioned',
	'label_submit' => 'Оставить отзыв',
		
	'fields' => apply_filters('comment_form_default_fields', array(
		
		'author' => '<div class="col-xl-7 col-lg-8 col-md-8 col-sm-8 col-12 form-auth__group form-auth__group_name" data-type="form-group">'. 
                    '<span class="form-auth__errtext" data-type="form-errtext"></span>' .
                    '<div class="form-auth__valid" data-type="form-valid"></div>' .
                    '<div class="form-auth__error" data-type="form-error">' . '				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						width="10px" height="10px" viewBox="0 0 10 10" enable-background="new 0 0 10 10" xml:space="preserve">
				   <path fill="#E80000" d="M8.882,0.009L5,3.891L1.118,0.009L0.009,1.118L3.891,5L0.009,8.882l1.109,1.109L5,6.109l3.882,3.882
					   l1.109-1.109L6.109,5l3.882-3.882L8.882,0.009z"/>
				   </svg>' . '</div>' .
					'<input data-validation="name" data-required="true" data-type="ui-input" placeholder="Ваше имя" data-placeholder="Ваше имя" class="col input input_field input_m input-name" id="author" name="author" type="text" maxlength="68" value="' . /*esc_attr($commenter['comment_author'])*/'" size="30">'.
					'</div>',   

        'email'  => '<div class="col-xl-7 col-lg-8 col-md-8 col-sm-8 col-12 form-auth__group form-auth__group_name" data-type="form-group">' .
                    '<span class="form-auth__errtext" data-type="form-errtext"></span>' .
                    '<div class="form-auth__valid" data-type="form-valid"></div>' .
                    '<div class="form-auth__error" data-type="form-error">' . '				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						width="10px" height="10px" viewBox="0 0 10 10" enable-background="new 0 0 10 10" xml:space="preserve">
				   <path fill="#E80000" d="M8.882,0.009L5,3.891L1.118,0.009L0.009,1.118L3.891,5L0.009,8.882l1.109,1.109L5,6.109l3.882,3.882
					   l1.109-1.109L6.109,5l3.882-3.882L8.882,0.009z"/>
				   </svg>' . '</div>' .
                	'<input data-validation="email" data-required="true" data-type="ui-input" placeholder="Ваш email" data-placeholder="Ваш email" class="col input input_field input_m input-email" id="email" name="email" type="email" value="' . /*esc_attr(  $commenter['comment_author_email'] )*/'"' . $aria_req . ' />'.
					'</div>',

    	'url'    => '' ) ),

    'comment_field' =>  '<div class="col form-auth__group form-auth__group_name" data-type="form-group">' .
                        '<span class="form-auth__errtext" data-type="form-errtext"></span>' .
                        '<div class="form-auth__valid" data-type="form-valid"></div>' .
                        '<div class="form-auth__error" data-type="form-error">' . '				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							width="10px" height="10px" viewBox="0 0 10 10" enable-background="new 0 0 10 10" xml:space="preserve">
					   <path fill="#E80000" d="M8.882,0.009L5,3.891L1.118,0.009L0.009,1.118L3.891,5L0.009,8.882l1.109,1.109L5,6.109l3.882,3.882
						   l1.109-1.109L6.109,5l3.882-3.882L8.882,0.009z"/>
					   </svg>' . '</div>' .
                		'<textarea data-validation="textarea" data-required="true" data-type="ui-input" placeholder="Комментарий" data-placeholder="Комментарий" class="col input input_field input_m input-comment" id="comment" name="comment" aria-required="true"></textarea>' .
						'</div>',
    
	'comment_notes_before' => '',
	
	'comment_notes_after' => '',
	
));
echo str_replace('id="commentform"','id="commentform" novalidate',ob_get_clean());
?>

<?php 
if ( have_comments() ) : 
	global $comments_by_type;
	$comments_by_type = &separate_comments( $comments );
	if (!empty($comments_by_type['comment'])) : 
?>

<div id="comments-list" class="comments col-lg-12 col-md-12 col-sm-12 col-12 no-paddings">
<?php /*<div class="comments-title"><?php comments_number(); ?></div> */ ?>
<?php if ( get_comment_pages_count() > 1 ) : ?>
<nav id="comments-nav-above" class="comments-navigation" role="navigation">
<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
</nav>
<?php endif; ?>
<ul>
<?php wp_list_comments('type=comment&callback=custom_comments&reverse_top_level=true'); ?>

</ul>
<?php if ( get_comment_pages_count() > 1 ) : ?>
<nav id="comments-nav-below" class="comments-navigation" role="navigation">
<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
</nav>
<?php endif; ?>
</div>

<?php 
endif; 
if ( ! empty( $comments_by_type['pings'] ) ) : 
$ping_count = count( $comments_by_type['pings'] ); 
?>
	<div id="trackbacks-list" class="comments">
		<div class="comments-title">
			<?php echo '<span class="ping-count">' . $ping_count . '</span> ' . ( $ping_count > 1 ? __( 'Trackbacks', 'blankslate' ) : __( 'Trackback', 'blankslate' ) ); ?>
		</div>
		<ul>
			<?php wp_list_comments( 'type=pings&callback=blankslate_custom_pings' ); ?>
		</ul>
	</div>
<?php 
endif; 
endif;
?>