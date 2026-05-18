<?php require_once("testimonials_even.php") ?>
<?php require_once("testimonials_odd.php") ?>

<div class="testimonials">
    <div class="container">
        <div class="row">

        <div class="testimonials__block col">
            <div class="row">
                <span class="col h-2 text-center">Отзывы</span>
                <br>
            </div>
            
            <div class="testimonials__list"
            data-type="slick"
            data-lazyload="ondemand"
            data-speed="900"
            data-infinite-md="true"
            data-infinite-sm="true"
            data-infinite-xs="true"
            data-stshow-md="3"
            data-stshow-sm="2"
            data-stshow-xs="1"
            data-stscroll-md="3"
            data-stscroll-sm="2"
            data-stscroll-xs="1"
            data-dots-md="false"
            data-dots-sm="false"
            data-dots-xs="false"
            data-adaptiveheight="true">

            <?php

            $comments = json_decode($testimonials_odd);

            /*
            $day = (int) date('d', time());
            if($day % 2 == 0) {
                $comments = json_decode($testimonials_even);
            } else {
                $comments = json_decode($testimonials_odd);
            }
            */

            foreach($comments as $comment) {
                $day = rand(2, 10);
                $indx = $day % 2 == 0 ? '4' : '5';
                if($day === 2) $indx = '3';
            ?>

                <div>
                    <div class="testimonials__rating testimonials__rating<?= $indx ?> col">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <p class="testimonials__comment"><?= $comment->message ?></p>
                    <div class="testimonials__cite">
                        <span class="testimonials__author"><?= $comment->name ?></span>
                        <time class="testimonials__date" datetime="<?= $comment->datetime ?>"><?= $comment->date ?></time>
                    </div>
                </div>

            <?php
            }
            ?>

            </div>

            <a class="testimonials__all" href="<?= home_url() ?>/otzivi/" title="Отзывы реальных клиентов компании Sochi Charter">Все отзывы &raquo;</a>
        </div>

        </div>
    </div>
</div>
