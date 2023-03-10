<!-- *************************************************************************************** -->
<!-- PHP FILE - HTML STRUCTURE -->
<!-- *************************************************************************************** -->

<!-- section start - added search bar for category dropdown, search box, tag listing -->
<section class="md-fd-banner" style="margin:100px;">
    <div class="md-search-bar">
    
        <!-- category dopdown / searching -->
        <div class="md-film-cat-search">
            <?php
                $args = array( 'taxonomy' => 'film_category', 'hide_empty' => false );
                $cats = get_terms($args);            
                echo '<select class="md-film-cat" id="md_film_catID" >';
                foreach($cats as $cat) { ?>
                    <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                <?php }
                echo '</select>'; ?> 
        </div>
        
        <!-- post title / searching -->
        <div class="md-film-text-search">
            <input type="text" name="md_search_txtbox" id="md-search-txtbox" />
        </div>

        <!-- listing tag / searching -->
        <div class="md-tag-search">
            <?php   $args = array(
                            'taxonomy' => 'film_tag',
                            'hide_empty' => false
                        );
                $tags = get_terms($args);
                        
                foreach($tags as $tag) { ?>
                    <div class="md-film-tag" >
                        <span id="md_film_tagID" datatagid="<?php echo $tag->term_id; ?>"><?php echo $tag->name; ?></span>
                    </div>
            <?php } ?> 
            <div class="md-film-tag">
                <a href="#"><span id="md_twitter">Talk to us on Twitter</span></a>
            </div>
        </div>

    </div>
</section>

<section class="search-result-sec">
    <!-- ajax div response wrapper -->
    <div class="md-response-wrap"></div>
</section>



<!-- *************************************************************************************** -->
<!-- JS FILE -->
<!-- *************************************************************************************** -->
<script>
    // page on load ajax call
    jQuery(window).on('load', function () {    
        if (jQuery("body").hasClass("page-id-3144")){
            var catID = null; var TagID = null; var searchText = ''; var page = 1;
            var catID = jQuery('#md_film_catID :selected').val();
    
            md_film_ajax_callback(catID, TagID, searchText, page);   

            setTimeout(function () {
                jQuery('.md-film-links').eq(1).addClass('activeLink');            
            }, 3000);
        }
    });

    // jQuery ready function call for below conditions
        // - choosencategory from dropdown, - search post title on textbox, - select listing tags
    jQuery(document).ready(function() {

    //  Category wise film searching
    jQuery('.md-film-cat').change(function() {    
       var catID = null; var TagID = null; var searchText = ''; var page = 1;

        var catID = this.value;
        var TagID = jQuery('.activeTag').attr('datatagid');
        var searchText = jQuery('#md-search-txtbox').val();
       
        md_film_ajax_callback(catID, TagID, searchText, page);

        setTimeout(function () {
            if(page == 1){
                jQuery('.md-prev').addClass('linkDeactive');       
            }
        }, 3500);
            
    });
    
    //  Tag wise film searching
    jQuery(document).on("click","#md_film_tagID",function() {
        var catID = null; var TagID = null; var searchText = ''; var page = 1;

        var TagID =  jQuery(this).attr('datatagid');
        var catID = jQuery('#md_film_catID :selected').val();
        var searchText = jQuery('#md-search-txtbox').val();
        
        jQuery('.md-film-tag #md_film_tagID').removeClass('activeTag');
        jQuery(this).addClass('activeTag');
        
        md_film_ajax_callback(catID, TagID, searchText, page);        
    });
    
    //  Input wise film searching
    jQuery('#md-search-txtbox').on('input',function(e){
        var catID = null; var TagID = null; var searchText = ''; var page = 1;     

        var searchText = jQuery(this).val();
        var TagID = jQuery('.activeTag').attr('datatagid');
        var catID = jQuery('#md_film_catID :selected').val();
        
        md_film_ajax_callback(catID, TagID, searchText, page);        
    });
    
    // Pagination
    jQuery(document).on("click",".md-page.md-film-links",function() {
        var catID = null; var TagID = null; var searchText = ''; var page = 1;

        page =  parseInt(jQuery(this).attr('data-link'));        
        var catID = jQuery('#md_film_catID :selected').val();
        var TagID = jQuery('.activeTag').attr('datatagid');
        var searchText = jQuery('#md-search-txtbox').val();
        
        md_film_ajax_callback(catID, TagID, searchText, page);

        setTimeout(function () {
            if(page == 1){
                jQuery('.md-prev').addClass('linkDeactive');  
                jQuery('.md-prev').attr('data-prev', 0);  
            }
        }, 3500);
    });

    // Pagination prev button
    jQuery(document).on("click",".md-film-links.md-prev",function() {
        var catID = null; var TagID = null; var searchText = ''; var page = 1;
        page =  parseInt(jQuery('.activeLink').attr('data-link')) - 1;
        
        if(page >= 1){  
            var catID = jQuery('#md_film_catID :selected').val();
            var TagID = jQuery('.activeTag').attr('datatagid');
            var searchText = jQuery('#md-search-txtbox').val();
            
            md_film_ajax_callback(catID, TagID, searchText, page);
            
            setTimeout(function () {
                if(page == 1){
                    jQuery('.md-prev').addClass('linkDeactive');    
                }
            }, 3500);
        }       
    });
  
    // Pagination next button
    jQuery(document).on("click",".md-film-links.md-next",function() {
        var catID = null; var TagID = null; var searchText = ''; var page = 1;
        page =  parseInt(jQuery('.activeLink').attr('data-link')) + 1;
        lastpage =  parseInt(jQuery('.md-next').attr('data-next-def'));
        
        if(page >= 1 && page <= lastpage){  
            var catID = jQuery('#md_film_catID :selected').val();
            var TagID = jQuery('.activeTag').attr('datatagid');
            var searchText = jQuery('#md-search-txtbox').val();
            
            md_film_ajax_callback(catID, TagID, searchText, page);
            
            setTimeout(function () {
                if(page == 1){
                    jQuery('.md-prev').addClass('linkDeactive');    
                }
            }, 3500);
        }
    });
   
});

// ajax call function
function md_film_ajax_callback(catID, TagID, searchText, page){
    jQuery.ajax({
        url: admin_ajax.ajax_url,
        type: 'POST',
        data: {
                'cat_id': catID,
                'tag_id': TagID,
                'searchText': searchText,
                'page': page,
                'action': 'md_fdatabase_filter'
            },
            success: function(response) {
                jQuery('.md-response-wrap').empty();
                jQuery('.md-response-wrap').append(response);
                
                jQuery('.md-film-links').removeClass('activeLink');
                jQuery('.md-page-'+page).addClass('activeLink');            

                var next_page =  parseInt(jQuery('.md-next').attr('data-next-def'));
                page = parseInt(page);
                
                if(next_page > 1 && page > 1){
                    jQuery('.md-film-links.md-prev').removeClass('linkDeactive');
                    jQuery('.md-film-links.md-prev').attr('data-prev', page - 1);
                }
                if(page == 1){
                    jQuery('.md-prev').addClass('linkDeactive');
                    jQuery('.md-film-links.md-prev').attr('data-prev', 0);  
                }

                if(next_page > 1 && page < next_page ){
                    jQuery('.md-film-links.md-prev').removeClass('linkDeactive');
                    jQuery('.md-film-links.md-next').attr('data-next', page + 1);
                }
                
                if(next_page == page){
                    jQuery('.md-film-links.md-next').addClass('linkDeactive');
                    jQuery('.md-film-links.md-next').attr('data-next', 0);
                }
            }
    });
}

</script>


<!-- *************************************************************************************** -->
<!-- FUNCTION FILE/ all logic -->
<!-- *************************************************************************************** -->
<?php
/**
 * film filter - START
 *  */ 
function md_fdatabase_filter(){
	$cat_ID = (int)$_POST['cat_id'];
	$tag_ID = (int)$_POST['tag_id'];
	$searchText = $_POST['searchText'];
	$page = (int)$_POST['page'];

	$cat_array = array( array( 'taxonomy' => 'film_category', 'field' => 'term_id', 'terms' => $cat_ID, ) );
	$tag_array = array( 'taxonomy' => 'film_tag', 'field' => 'term_id', 'terms' => $tag_ID );

	$per_page_value = -1;
	if($cat_ID != 0 && $tag_ID == 0 && $searchText == ''){

		$args = array(
			'post_type' => 'our_films',
			'posts_per_page' => $per_page_value,
			'tax_query' => array($cat_array)
		);
	}else if($cat_ID != 0 && $tag_ID != 0 && $searchText == ''){
		$args = array(
			'post_type' => 'our_films',
			'posts_per_page' => $per_page_value,
			'tax_query' => array(
				'relation' => 'AND',
				$cat_array,
				$tag_array
			  )
		);
	}else if($cat_ID != 0 && $tag_ID == 0 && $searchText != ''){
		$args = array(
			'post_type' => 'our_films',
			'search_prod_title' => $searchText,
			'posts_per_page' => $per_page_value,
			'tax_query' => array(
				'relation' => 'AND',
				$cat_array
			  )
		);
	}else if($cat_ID != 0 && $tag_ID != 0 && $searchText != ''){
		$args = array(
			'post_type' => 'our_films',
			'search_prod_title' => $searchText,
			'posts_per_page' => $per_page_value,
			'tax_query' => array(
				'relation' => 'AND',
				$cat_array,
				$tag_array
			  )
		);
	}
    
	add_filter( 'posts_where', 'title_filter', 10, 2 );
	$films_posts = new WP_Query( $args ); 
	// remove_filter( 'posts_where', 'title_filter', 10, 2 );

	$post_ids = wp_list_pluck( $films_posts->posts, 'ID' );	
	$total = $films_posts->found_posts; 
	$per_page = 2;
	$links = ceil($total / $per_page);
	$array_start = ($page - 1) * $per_page;
	$film_items = array_slice($post_ids, $array_start, $per_page); 	
	
	if (count($film_items) != 0) {

		$args = array(
			'post_type' => 'our_films',
			'posts_per_page' => -1,
			'post__in' => $film_items
		);
		$films_postss = new WP_Query( $args ); 	?>

			<div class = "ed-fdabases-wrapper">		
			
				<?php if ( $films_postss->have_posts() ) : ?>
				<div class = "es-fdabase-posts row">
					<?php while ( $films_postss->have_posts() ) : $films_postss->the_post(); ?>
					<div class="col-lg-4 col-md-6 col-12">
						<article id = "es-post-<?php the_ID(); ?>" class="fdabase-tiles">
								<h2 class = "es-post-title"><?php the_title(); ?></h2>
								<?php echo get_the_post_thumbnail( $post_id, 'medium', array( 'class' => 'alignleft' ) ); ?>
						</article>
					</div>
					<?php endwhile; ?>
				</div>				
				<?php endif; 
					wp_reset_postdata(); ?>
			</div>
		<?php }else{ ?>
			<p class = "es-no-fdabase-posts">
						<?php esc_html_e('Sorry, no posts matched your criteria.', 'theme-domain'); ?> 
					</p>
		<?php } ?>
		
        <!-- pagination with prev, next arrow -->
		<nav class="md-film-pagi">
			<ul class="md-pagi">
				<li class="md-film-links md-prev" data-prev="" >
				<svg class="prev-arrow" width="13" height="24" viewBox="0 0 13 24" fill="none" xmlns="http://www.w3.org/2000/svg" style=""><path d="M12.6211 23.6208L1.00011 11.9999L12.6211 0.378879" stroke="white"></path></svg>
				</li>
				<?php
				for ($i=1; $i <= $links ; $i++) { 
					echo '<li class="md-page md-film-links md-page-'.$i.'" data-link='.$i.' style="font-size: 18px;">'.$i.'</li>';
				}
				?>
				<li class="md-film-links md-next" data-next-def="<?php echo $links; ?>" data-next="" >
				<svg class="next-arrow" width="14" height="24" viewBox="0 0 14 24" fill="none" xmlns="http://www.w3.org/2000/svg" style=""><path d="M1 0.378906L12.621 11.9999L1 23.6209" stroke="white"></path></svg></li>
			</ul>
		</nav>

	<?php
	die();
}
add_action( 'wp_ajax_md_fdatabase_filter', 'md_fdatabase_filter' );
add_action( 'wp_ajax_nopriv_md_fdatabase_filter', 'md_fdatabase_filter' );
/**
 * film filter - END
 *  */ 

// searching by custom post film title filter
function title_filter( $where, &$wp_query ){
    global $wpdb;
    if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
    }
    return $where;
}
