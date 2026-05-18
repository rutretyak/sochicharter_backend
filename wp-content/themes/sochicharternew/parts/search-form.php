<form class="search__form" role="search" action="<?= home_url( '/' ) ?>" method="get">
    <input class="input input_search col" placeholder="Найти" type="text" id="s" name="s" value="<?php the_search_query() ?>" >
    <input type="hidden" name="post_type[]" value="post" />
    <input type="hidden" name="post_type[]" value="page" />
    <input type="hidden" name="post_type[]" value="uslugi" />
    <input type="hidden" name="post_type[]" value="yachts" /> 
    <input type="hidden" name="post_type[]" value="yachts-adler" /> 
    <input type="hidden" name="post_type[]" value="yachts-lazar" />
    <button class="button button_close" type="button" title="Закрыть поиск" data-type="close-search">
        <?php get_template_part('svg/close') ?>
    </button> 
    <button class="button button_search" type="submit" title="Поиск по сайту">
        <?php get_template_part('svg/search') ?>
    </button>
</form>