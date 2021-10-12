<?php
/*
Plugin Name: Video Feed Box
*/
?>
<?php
    global $SMTheme;
    
    $video_defaults = array(
		'width' =>'272',
        'title' => 'Video',
        'videos' => array(
			array(
				'title' => 'The Mountain', 
				'url' => 'http://vimeo.com/22439234/', 
				'type' => 'vimeo', 
				'videoid' => '22439234'
			),
			array(
				'title' => 'Amazing nature scenery', 
				'url' => 'http://www.youtube.com/watch?v=6v2L2UGZJAM', 
				'type' => 'youtube', 
				'videoid' => '6v2L2UGZJAM'
			)
		)
    );



    

class VideoFeed extends WP_Widget 
{
    function __construct(){
        
        $widget_options = array('description' => 'Video Feed Box widget.' );
        $control_options = array();
        parent::__construct('VideoFeed', '&raquo; Video Feed', $widget_options, $control_options);
    }

    function widget($args, $instance){
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$width = intval($instance['width']);
        $videos = $instance['videos'];
   
        if(is_array($videos)) {
            ?>
			<?php echo $args['before_widget']?>
			<?php  if ( $title ) {  ?><?php echo $args['before_title']?><?php echo $title?><?php echo $args['after_title']?><ul><?php } 
				foreach ($videos as $video) {?><li>
				<a href='<?php echo $video['url']; ?>' rel='nofollow' target='_blank'><?php echo $video['title']; ?></a>
			<?php
				switch( $video['type'] ) {
					case 'vimeo':
					$videoinf = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$video['videoid'].".php"));
					echo '<p style="text-align:center;display:block;overflow:hidden;"><a href="http://vimeo.com/'.$video['videoid'].'" target="_blank" alt="'.$video['videoid'].'" class="vimeo"><img alt="" src="'.$videoinf[0]['thumbnail_large'].'" width="'.$width.'"></a></p><script>loadVimeo();</script>';
					
					break;
					case 'youtube':
					echo '<p style="text-align:center;display:block;overflow:hidden;clear:left"><a href="http://www.youtube.com/watch?v='.$video['videoid'].'" target="_blank" alt="'.$video['videoid'].'" class="youtube"><img src="http://img.youtube.com/vi/'.$video['videoid'].'/0.jpg" width="'.$width.'" /></a></p><script>loadYouTube();</script>';
				}?>
				</li>
				<?php }
            ?>
			</ul>
           <?php echo $args['after_widget']?>
            <?php
        }
    }

    function update($new_instance, $old_instance) 
    {				
    	$instance = $old_instance;
    	$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = intval($new_instance['width']);
        $instance['videos'] = $new_instance['videos'];
		unset($instance['videos']['the__id__']);
        return $instance;
    }
    
     function form($instance){
		global $video_defaults;
		$instance = wp_parse_args( (array) $instance, $video_defaults );
        $get_videos = $instance['videos'];
		$get_this_id = preg_replace("/[^0-9]/", '', $this->get_field_id('this_id_videos'));
        $get_this_id = !$get_this_id ? 'this_id_videos___i__' : 'this_id_videos_' . $get_this_id;
        ?>

        <div style="margin-bottom: 20px;">
			<p><label for="<?php echo $this->get_field_id('title')?>">Title:</label><input class="widefat" id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title')?>" type="text" value="<?php echo esc_attr($instance['title'])?>" /></p>
			<p><label for="<?php echo $this->get_field_id('width')?>">Width(px):</label><input class="widefat" id="<?php echo $this->get_field_id('width')?>" name="<?php echo $this->get_field_name('width')?>" type="text" value="<?php echo esc_attr($instance['width'])?>" /></p>
			<a class="button new_video" alt='<?php echo $get_this_id?>'>Add New Video</a>
        </div>
		<div class="videos_<?php echo $get_this_id?>">
        <?php
            if(is_array($get_videos)) {
                foreach($get_videos as $video_id=>$video_source) {
                    ?>
                    <div class="tt-clearfix " style="padding: 0 0 20px 0; border-bottom: 1px solid #ddd; margin-bottom: 20px;">
						<div>
							<p><label>Title:</label><input alt='title' class="widefat" name="<?php echo $this->get_field_name('videos')?>[<?php echo $video_id ?>][title]" type="text" value="<?php echo $video_source['title']?>" /></p>
							<p><label>URL:</label><input alt='url' class="widefat" name="<?php echo $this->get_field_name('videos')?>[<?php echo $video_id ?>][url]" type="text" value="<?php echo $video_source['url']?>" /></p>
							<p><label>Type:</label>
							<select alt='type' class="widefat" name="<?php echo $this->get_field_name('videos')?>[<?php echo $video_id ?>][type]">
								<?php if ($video_source['type']=='vimeo') { ?>
								<option value='vimeo' selected='selected'>Vimeo</option>
								<option value='youtube'>YouTube</option>
								<?php } else { ?>
								<option value='vimeo'>Vimeo</option>
								<option value='youtube' selected='selected'>YouTube</option>
								<?php } ?>
								
							</select>
							</p>
							<p><label>Video ID:</label><input alt='videoid' class="widefat" name="<?php echo $this->get_field_name('videos')?>[<?php echo $video_id ?>][videoid]" type="text" value="<?php echo $video_source['videoid']?>" /></p>
						</div>
                        <div style='margin-top:10px;'>
                            <div><a class="button preview_video" alt="<?php echo $this->get_field_id($video_id)?>">Preview</a> <a class="button tt-button-red delete_video" alt="<?php echo $this->get_field_id($video_id)?>">Delete</a></div>
                        </div>
                    </div>
                    <?php
                }
            }
        
        ?>
		</div>
            <div class="videos_new_<?php echo $get_this_id?>">
                <div class="new_video_container" style="display: none;">
                    <div class="tt-clearfix" style="padding: 0 0 20px 0; border-bottom: 1px solid #ddd; margin-bottom: 20px;">
						<div>
							<p><label>Title:</label><input alt='title' class="widefat" name="<?php echo $this->get_field_name('videos')?>[the__id__][title]" type="text" value="" /></p>
							<p><label>URL:</label><input alt='url' class="widefat" name="<?php echo $this->get_field_name('videos')?>[the__id__][url]" type="text" value="" /></p>
							<p><label>Type:</label><select alt='type' class="widefat" name="<?php echo $this->get_field_name('videos')?>[the__id__][type]">
								<option value='vimeo'>Vimeo</option>
								<option value='youtube'>YouTube</option>
							</select></p>
							<p><label>Video ID:</label><input alt='videoid' class="widefat" name="<?php echo $this->get_field_name('videos')?>[the__id__][videoid]" type="text" value="" /></p>
						</div>
                        <div style='margin-top:10px;'>
                            <div><a class="button preview_banner">Preview</a> <a class="button tt-button-red delete_banner">Delete</a></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
} 
add_action('widgets_init', create_function('', 'return register_widget("VideoFeed");'));
?>