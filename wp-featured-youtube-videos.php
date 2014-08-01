<?php
/**
 * Plugin Name: Featured YouTube Playlist
 * Description: Creates a display of embedded YouTube videos from the playlist given.
 * Version: 1.1
 * Author: Michael Codner
 * Author URI: http://www.surgestuff.com
 */
?>
<?php

add_shortcode( 'featuredVideos', 'yfp_GetHtml' );
  
function getYouTubeIdFromURL($url)
{
  $url_string = parse_url($url, PHP_URL_QUERY);
  parse_str($url_string, $args);
  return isset($args['v']) ? $args['v'] : false;
}

function getYouTubePlayListIdFromURL($url)
{
  $url_string = parse_url($url, PHP_URL_QUERY);
  parse_str($url_string, $args);
  return isset($args['list']) ? $args['list'] : false;
}

function yfp_admin(){
  include('yfp_import_admin.php');
}

function yfp_admin_actions(){
  add_options_page('Featured YouTube Playlist', 'Featured YouTube Playlist', 'manage_options', 'featured-youtube-playlist', 'yfp_admin');
}

add_action('admin_menu', 'yfp_admin_actions');

// Grab JSON and format it into PHP arrays from YouTube.
function get_youtube_info ( $vid, $info ) {
    $youtube = "http://gdata.youtube.com/feeds/api/videos/$vid?v=2&alt=json";
    $ch = curl_init($youtube);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    //If $assoc = true doesn't work, try:
    //$output = json_decode($output, true);
    $output = json_decode($output, $assoc = true);

    //Add the ['feed'] in if it exists.
    if ($output['feed']) {
        $path = &$output['feed']['entry'];
    } else {
        $path = &$output['entry'];
    }

    //set up a switch to return various data bits to return.
    switch($info) {
        case 'title':
            $output = $path['title']['$t'];
            break;
        case 'description':
            $output = $path['media$group']['media$description']['$t'];
            break;
        case 'author':
            $output = $path['author'][0]['name'];
            break;
        case 'author_uri':
            $output = $path['author'][0]['uri'];
            break;
        case 'thumbnail_small':
            $output = $path['media$group']['media$thumbnail'][0]['url'];
            break;
        case 'thumbnail':
            $output = $path['media$group']['media$thumbnail'][1]['url'];
            break;
        case 'thumbnail_medium':
            $output = $path['media$group']['media$thumbnail'][2]['url'];
            break;
        case 'thumbnail_large':
            $output = $path['media$group']['media$thumbnail'][3]['url'];
            break;
        default:
            return $output;
            break;
    }
    return $output;
}


function yfp_GetHtml(){
  $fplaylist = getYouTubePlayListIdFromURL(get_option('yfp_URL'));
  $fplaylist1 = getYouTubePlayListIdFromURL(get_option('yfp_URL_1'));
  $fplaylist2 = getYouTubePlayListIdFromURL(get_option('yfp_URL_2'));
  $EmbedHtml = '';

  $fpcont = json_decode(wp_remote_get('http://gdata.youtube.com/feeds/api/playlists/' . $fplaylist . '/?v=2&alt=json&feature=plcp')['body']);
  $fpcont1 = json_decode(wp_remote_get('http://gdata.youtube.com/feeds/api/playlists/' . $fplaylist1 . '/?v=2&alt=json&feature=plcp')['body']);
  $fpcont2 = json_decode(wp_remote_get('http://gdata.youtube.com/feeds/api/playlists/' . $fplaylist2 . '/?v=2&alt=json&feature=plcp')['body']);
  
  $fpfeed = $fpcont->feed->entry;
  $fpfeed1 = $fpcont1->feed->entry;
  $fpfeed2 = $fpcont2->feed->entry;

  $videoID_array = array();
  $videoID_array1 = array();
  $videoID_array2 = array();
  
  $videoTitle_array = array();
  $videoTitle_array1 = array();
  $videoTitle_array2 = array();

  if(count($fpfeed))
  {
    $i = 0;
    foreach($fpfeed as $item){
	  if ($i < 5){
      array_push($videoID_array, $item->{'media$group'}->{'yt$videoid'}->{'$t'});
      array_push($videoTitle_array, get_youtube_info($item->{'media$group'}->{'yt$videoid'}->{'$t'}, 'title'));
	  }
	  $i++;
    }
  }
  
  if(count($fpfeed1))
  {
    $i = 0;
    foreach($fpfeed1 as $item){
	  if ($i < 5){
      array_push($videoID_array1, $item->{'media$group'}->{'yt$videoid'}->{'$t'});
      array_push($videoTitle_array1, get_youtube_info($item->{'media$group'}->{'yt$videoid'}->{'$t'}, 'title'));
	  }
	  $i++;
    }
  }
  
  if(count($fpfeed2))
  {
    $i =0;
    foreach($fpfeed2 as $item){
	  if ($i < 5){
      array_push($videoID_array2, $item->{'media$group'}->{'yt$videoid'}->{'$t'});
      array_push($videoTitle_array2, get_youtube_info($item->{'media$group'}->{'yt$videoid'}->{'$t'}, 'title'));
	  }
	  $i++;
    }
  }  
  
  $EmbedHtml .= '
  <!-- Nav tabs -->
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#featured" data-toggle="tab">Featured Videos</a>
    </li>
    <li>
      <a href="#1" data-toggle="tab">'. get_option('yfp_1_name') .'</a>
    </li>
    <li>
	  <a href="#2" data-toggle="tab">'. get_option('yfp_2_name') .'</a>
	</li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <div class="tab-pane fade in active" id="featured">
      <div class="col-md-6">
        <div class="embed-container">
          <div class="youtube" id="' . $videoID_array[0] .'"></div>
        </div>
        <div class="caption"><p>' . $videoTitle_array[0] . '</p></div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array[1] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array[1] . '</p></div>
          </div>
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array[2] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array[2] . '</p></div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array[3] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array[3] . '</p></div>
          </div>
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array[4] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array[4] . '</p></div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="1">
      <div class="col-md-6">
        <div class="embed-container">
          <div class="youtube" id="' . $videoID_array1[0] .'"></div>
        </div>
        <div class="caption"><p>' . $videoTitle_array1[0] . '</p></div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array1[1] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array1[1] . '</p></div>
          </div>
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array1[2] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array1[2] . '</p></div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array1[3] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array1[3] . '</p></div>
          </div>
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array1[4] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array1[4] . '</p></div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="2">
      <div class="col-md-6">
        <div class="embed-container">
          <div class="youtube" id="' . $videoID_array2[0] .'"></div>
        </div>
        <div class="caption"><p>' . $videoTitle_array2[0] . '</p></div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array2[1] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array2[1] . '</p></div>
          </div>
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array2[2] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array2[2] . '</p></div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array2[3] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array2[3] . '</p></div>
          </div>
          <div class="col-md-6">
            <div class="embed-container">
              <div class="youtube" id="' . $videoID_array2[4] .'"></div>
            </div>
            <div class="caption"><p>' . $videoTitle_array2[4] . '</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>';

  return $EmbedHtml;
}

?>