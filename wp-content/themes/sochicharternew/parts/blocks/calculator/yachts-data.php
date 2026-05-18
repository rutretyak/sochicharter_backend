<?php
// -------------
// Start to loop
$yachts = [];        // Stores all yachts in sochi
$yachts_adler = [];  // Stores all yachts in adler
$yachts_lazar = [];  // Stores all yachts in lazarevskoe



function fillData($prefix) {
$arr = [];
$args = array( 'post_type' => $prefix, 'posts_per_page' => 1024 );
$loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        
        array_push($arr, [
            'id'        => get_the_ID(),
            'name'      => get_the_title(),
            'price'     => rwmb_meta($prefix . '-yacht_price'),
            'type'      => rwmb_meta($prefix . '-yacht_class'),
            'capacity'  => rwmb_meta($prefix . '-yacht_capacity'),
            'cabins'    => rwmb_meta($prefix . '-yacht_cabins'),
            'status'    => rwmb_meta($prefix . '-yachts_status'),
            'permalink' => get_the_permalink(),
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'medium')
        ]);

    endwhile;

return $arr;
}
$yachts = fillData('yachts');
$yachts_adler = fillData('yachts-adler');
$yachts_lazar = fillData('yachts-lazar');
?>
<script>
const YACHTS = <?= json_encode($yachts) ?>,
YACHTS_ADLER = <?= json_encode($yachts_adler) ?>,
YACHTS_LAZAR = <?= json_encode($yachts_lazar) ?>;
</script>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php endwhile; endif; ?>

