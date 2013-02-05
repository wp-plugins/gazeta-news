<?php
/*
Plugin Name: Plugin Gazeta News - Gazeta da Ilha
Plugin URI: http://www.gazetadailha.com.br/
Description: Este widget lhe permite manter seu site sempre atualizado com as principais noticias do Portal Gazeta da Ilha
Author: Erick Pessoa
Version: 0.2
Author URI: http://www.websisti.com.br/
*/

/**
 * Multiplicador Gazeta da Ilha Plugin
 * 
 * @author Erick Pessoa <erickpessoa@websisti.com.br>
 * @package WPG
 *
 */
class websisti_GINewsWidget extends WP_Widget {

//	private static $wpdb;
//	private static $info;

	public function __construct() {
		
		$widget_ops = array(
			'classname' => "websisti_GINews",
			'description' => 'Use este widget para exibir as &uacute;ltimas not&iacute;cias do Portal Gazeta da Ilha em seu site.'
		);

		parent::__construct("websisti_GINewsWidget-widgets",__('Gazeta da Ilha News') , $widget_ops );
		
	}
	
	public function form ($instance) {

		echo "
		<p>
			<label for=\"".$this->get_field_id('numberposts')."\">N&uacute;mero de not&iacute;cias:</label>
            <select id=\"".$this->get_field_id('numberposts')."\" name=\"".$this->get_field_name('numberposts')."\">";
		for ($i=1;$i<=5;$i++) {
			echo '<option value="'.$i.'"';
			if ($i==$instance['numberposts']) echo ' selected="selected"';
			echo '>'.$i.'</option>';
		}
	    echo "
			</select>
        </p>
		<p>
        	<label for=\"".$this->get_field_id('show_on')."\">Exibir em:</label></td>
			<select id=\"".$this->get_field_id('show_on')."\" name=\"".$this->get_field_name('show_on')."\">
				<option>All Pages</option>";
				
				echo "<option value=\"front_only\" ";
				echo ($instance['show_on'] == 'front_only') ? ' SELECTED ' : '';
				echo " >Front Page Only</option>";
				
				echo "<option value=\"pages_post\" ";
				echo ($instance['show_on']=='pages_post') ? ' SELECTED ' : '';
				echo " >Pages and Posts Only</option>";
				
				echo "<option value=\"pages_only\" ";
				echo ($instance['show_on']=='pages_only') ? ' SELECTED ' : '';
				echo " >Pages Only</option>";
				
				echo "<option value=\"post_only\" ";
				echo ($instance['show_on']=='post_only') ? ' SELECTED ' : '';
				echo " >Posts Only</option>";
		
		echo "                
			</select>
        </p>";
	}

	public function update ($new_instance, $old_instance) {
		// used when the user saves their widget 
		$instance = ($old_instance != NULL) ? $old_instance : array();
		$instance['numberposts'] = $new_instance['numberposts'];
		$instance['show_on'] = $new_instance['show_on'];

		return $instance;
	}
	
	private function registraStyle () {

		$plugin_directory = plugin_dir_url(__FILE__);
		wp_register_style( 'W_GINews-style', 
			$plugin_directory . 'GINews.css', 
			array(), 
			'20120208', 
			'all' );
		
		// enqueing:
		wp_enqueue_style( 'W_GINews-style' );

	}

	public function widget ($args,$instance) {
		// used when the sidebar calls in the widget
		extract($args);

		echo "
		<style type=\"text/css\">
			.ginp_noticias {
				border:1px ridge #B0B0B0;
				overflow: hidden;
			}			
			.ginp_noticias .noticia {
				font-family:Verdana, Geneva, sans-serif;
				font-size:8pt;
			/*	background-color:#F0F0F0;
				border: 1px ridge #CECECE;
				width:200px; 
				padding:5px;*/
				width:90%;
				margin:10px auto;
			}
			.ginp_noticias .noticia img {
				float:left;
				margin-right:5px;
				margin-bottom:5px;
				border:1px ridge #D4D4D4;
			}
			.ginp_noticias .noticia .titulo {
				margin-bottom:10px;
			}
			.ginp_noticias .noticia .categoria {
				color:#959595;
				text-align:right;
				margin-top:10px;
			}
			.ginp_noticias .noticia .data {
				color:#959595;
				text-align:right;
			}
			.ginp_noticias .copyright {
				font-size: 8pt !important;
				padding: 5px;
				background: #EBEBEB;
			}
			.hgazetaTopo {
				background-image:url(".plugin_dir_url(__FILE__)."giportal.jpg);
				background-position:left;
				background-color:#E0E0DE;
				min-width:200px;
				height:40px;
			}
		</style>
		";
		
		$numberposts = $instance['numberposts'];
		$showon = $instance['show_on'];

		$canalRSS_G = new SimpleXMLElement("http://www.gazetadailha.com.br/category/noticias/geral/feed/", NULL, TRUE);
		$canalRSS_M = new SimpleXMLElement("http://www.gazetadailha.com.br/category/noticias/maranhao/feed/", NULL, TRUE);
		$canalRSS_P = new SimpleXMLElement("http://www.gazetadailha.com.br/category/noticias/politica/feed/", NULL, TRUE);
		$canalRSS_E = new SimpleXMLElement("http://www.gazetadailha.com.br/category/noticias/esportes/feed/", NULL, TRUE);
		
		// Vendo atributos
		$canal = $canalRSS_M->channel[0];
		$quantidadeItens = $numberposts;

		$x = 0;
		$out = "<div class=\"ginp_noticias\">";
		$out .= "<a href=\"http://www.gazetadailha.com.br/\" target=\"_blank\"><div class=\"hgazetaTopo\"></div></a>";
		$out .= "<div style=\"background-color:#EB3C00\"><img src=\"".plugin_dir_url(__FILE__)."ultimos_maranhao.png\" /></div>";
		while ($x < $quantidadeItens) {
			$item = $canal->item[$x];
			//$data = explode(" ",$item->pubDate);
			//$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div><div>$item->description</div><div class=\"categoria\">";
			$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div></div>";
			$x++;
		}

		$x = 0;
		$canal = $canalRSS_G->channel[0];
		$out .= "<div style=\"background-color:#E1830F\"><img src=\"".plugin_dir_url(__FILE__)."ultimos_geral.png\" /></div>";
		while ($x < $quantidadeItens) {
			$item = $canal->item[$x];
			//$data = explode(" ",$item->pubDate);
			//$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div><div>$item->description</div><div class=\"categoria\">";
			$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div></div>";
			$x++;
		}

		$x = 0;
		$canal = $canalRSS_E->channel[0];
		$out .= "<div style=\"background-color:#68C332\"><img src=\"".plugin_dir_url(__FILE__)."ultimos_esportes.png\" /></div>";
		while ($x < $quantidadeItens) {
			$item = $canal->item[$x];
			//$data = explode(" ",$item->pubDate);
			//$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div><div>$item->description</div><div class=\"categoria\">";
			$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div></div>";
			$x++;
		}

		$x = 0;
		$canal = $canalRSS_P->channel[0];
		$out .= "<div style=\"background-color:#2D036B\"><img src=\"".plugin_dir_url(__FILE__)."ultimos_politica.png\" /></div>";
		while ($x < $quantidadeItens) {
			$item = $canal->item[$x];
			//$data = explode(" ",$item->pubDate);
			//$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div><div>$item->description</div><div class=\"categoria\">";
			$out .= "<div class=\"noticia\"><div class=\"titulo\"><a href=\"$item->link\" target=\"_blank\">$item->title</a></div></div>";
			$x++;
		}

		$before_widget = '';
		$after_widget = '<div class="copyright">&copy; 2012 - Gazeta da Ilha / GI Portal</div></div>';
		
		//print the widget for the sidebar
		if(@$showon=='front_only'){
			if(function_exists('is_front_page')){
				if(!is_front_page()) return;
			}
		}
		if(@$showon=='pages_post'){
			if ( ((get_post_type(get_the_ID()) == "page") || (get_post_type(get_the_ID()) == "post")) && !is_front_page() ) {
				echo $before_widget;
				//echo $before_title.$title.$after_title;
				echo $out;
				echo $after_widget;
			}
		}
		if(@$showon=='pages_only'){
			if (get_post_type(get_the_ID()) == "page") {
				echo $before_widget;
				//echo $before_title.$title.$after_title;
				echo $out;
				echo $after_widget;
			}
		}
		if(@$showon=='posts_only'){
			if (get_post_type(get_the_ID()) == "post") {
				echo $before_widget;
				//echo $before_title.$title.$after_title;
				echo $out;
				echo $after_widget;
			}
		}
		if(@$showon=='All Pages'){
			echo $before_widget;
			//echo $before_title.$title.$after_title;
			echo $out;
			echo $after_widget;
		}
		
	}

}

function GINews () { return register_widget('websisti_GINewsWidget'); }

add_action( 'widgets_init', 'GINews' );

?>
