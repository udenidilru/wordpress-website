<?php 
//__Using bootstrap and do shortcode__//
$itemIncrement = 1;
$wrapInc = 1;
function tabs_shortcode( $atts, $content= null ){
  global $wrapInc;
  $GLOBALS['tab_count'] = 0;
  $default = array(
      'style' => 'xa-default'
       );
  $data = shortcode_atts($default,$atts);
  do_shortcode( $content );
  
  if( is_array( $GLOBALS['tabs'] ) ){
    global $itemIncrement;
    $flag = 1;

    $conFlag = 'active';
    foreach( $GLOBALS['tabs'] as $tab ){
      $tabs[] = '<li role="presentation" class="'.$conFlag.'"><a href="#xt-tab-'.$itemIncrement.'" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true"><i class="fa fa-'.$tab['icon'].'"></i>'.$tab['title'].'</a></li>';
      $tabcontent[] = '<div role="tabpanel" class="tab-pane fade in '.$conFlag.'" id="xt-tab-'.$itemIncrement.'" aria-labelledby="home-tab">
        <p>'.$tab['content'].'</p></div>'."\n";
      $itemIncrement++;
      if($flag != $itemIncrement){
        $conFlag = ' ';
      }
    }

    $return  = '<div class="'.$data['style'].'" role="tabpanel" data-example-id="togglable-tabs">';
    $return .= '<ul id="myTab-'.$wrapInc.'" class="nav nav-tabs" role="tablist">'.implode( "\n", $tabs ).'</ul>';
    
    $return .= '<div id="myTabContent-'.$wrapInc.'" class="tab-content">'.implode( "\n", $tabcontent ).'</div>';
    $return .= '</div>';
    $wrapInc++;
  }
  return $return;
}
add_shortcode('xt_tab','tabs_shortcode');

function tabs_shortcode_nested( $atts, $content= null ){
  extract(shortcode_atts(array(
      'title' => '', 
      'icon' => ''
      ), $atts) );
  
  $x = $GLOBALS['tab_count'];
  $content = do_shortcode($content);
  //__User input bind in supper globar array__//
  $GLOBALS['tabs'][$x] = array( 'title' => $title, 'content' => $content, 'icon' => $icon );
  $GLOBALS['tab_count']++;
}
add_shortcode('xt_item','tabs_shortcode_nested');

?>