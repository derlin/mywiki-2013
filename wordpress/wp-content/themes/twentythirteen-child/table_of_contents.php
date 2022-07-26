<?php
// working on it: I tried to add it to the header.php file, at the end
global $query_string;
query_posts( $query_string . '&posts_per_page=-1&orderby=title&order=ASC' );
while ( have_posts() ): the_post();
?>
    <div>
        <a href="#post-<?php echo the_id() ?>"> <?php echo the_title() ?></a>
    </div>

<?php
endwhile;
?>
