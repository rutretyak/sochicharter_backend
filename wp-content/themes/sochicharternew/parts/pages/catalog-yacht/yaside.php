<?php 
    $prefix = get_query_var('yaside_posttype');
?>

<?php
    if($prefix == 'yachts') {
?>
    <?php get_template_part('parts/pages/catalog-yacht/ylinks') ?>
<?php
    } 
?>

<?php
    if($prefix == 'yachts-adler') {
?>
    <?php get_template_part('parts/pages/catalog-yacht/ylinks') ?>
<?php
    } 
?>

<?php
    if($prefix == 'yachts-lazar') {
?>
    <?php get_template_part('parts/pages/catalog-yacht/ylinks') ?>
<?php
    } 
?>