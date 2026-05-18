<?php /*    
<iframe src="https://www.youtube.com/embed/0F-xsTjuqzU" width="560" height="315" allowfullscreen="allowfullscreen"></iframe>
*/ ?>

<?php
    $video_file_url = get_home_url('/') . '/wp-content/themes/sochicharternew/docs/promo/sochicharter';
?>
<figure id="videoContainer">
    <video id="video" controls preload="metadata">
        <source src="<?= $video_file_url ?>.mp4" type="video/mp4">
        <source src="<?= $video_file_url ?>.webm" type="video/webm">
        <source src="<?= $video_file_url ?>.ogg" type="video/ogg">
        <!-- Flash fallback -->
        <object type="application/x-shockwave-flash" data="flash-player.swf?videoUrl=<?= $video_file_url ?>.mp4" width="1024" height="576">
            <param name="movie" value="flash-player.swf?videoUrl=<?= $video_file_url ?>.mp4" />
            <param name="allowfullscreen" value="true" />
            <param name="wmode" value="transparent" />
            <param name="flashvars" value="controlbar=over&amp;image=img/poster.jpg&amp;file=flash-player.swf?videoUrl=<?= $video_file_url ?>.mp4" />
            <?php /*<img alt="Tears of Steel poster image" src="img/poster.jpg" width="1024" height="428" title="No video playback possible, please download the video from the link below" /> */?>
        </object>
        <!-- Offer download -->
        <a href="<?= $video_file_url ?>.mp4">Скачать</a>
    </video>
    <figcaption>&copy; Sochi Charter — Морские приключения на яхтах</figcaption>
</figure>