<?php
/**
 * Plugin Name: Epik Keyword Counter
 * Plugin URI: http://www.epikmedia.com
 * Description: Search your wordpress posts for the keywords and get the count of the words
 * Version: 2.0
 * Author: Epik Media
 * Author URI: http://www.epikmedia.com
 * Text Domain: epik-keyword-counter
*/


class Epik_Keyword_Counter
{

    private static $instance = null;

    const CPT = 'epik-keyword-counter';

    const SLUG = 'epik-keyword-counter';

    const PLUGIN_NAME = 'Epik Keyword Counter';

    const TABLE_NAME = 'wp_epik_keyword_counter';

    public function __construct()
    {

        global $wpdb;

        if ( isset( self::$instance ) )
        {
            wp_die( esc_html( 'The Epik Keyword Counter class has already been loaded' , 'epik-keyword-counter' ) );
        }

        self::$instance = $this;

        add_action( 'init' ,                        array( $this , 'init' ) , 1 );
        add_action( 'admin_menu' ,                  array( $this , 'add_submenu_page' ) );
        add_action( 'admin_enqueue_scripts' ,       array( $this , 'admin_enqueue_scripts' ) );
        add_action( 'wp_ajax_form_submit' ,         array( $this , 'ajax_form_submit' ) );
        add_action( 'wp_ajax_keywords_search' ,     array( $this , 'ajax_keywords_search' ) );
        add_filter( 'generate_table' ,              array( $this , 'generate_table') , 10 , 3 );
        add_filter( 'trim_explode' ,                array( $this , 'trim_explode') , 10 , 2 );

    }

    public function init()
    {

    }

    public function add_submenu_page()
    {
        add_submenu_page(
            'tools.php' ,
            esc_html__( self::PLUGIN_NAME , self::SLUG ) ,
            esc_html__( self::PLUGIN_NAME , self::SLUG ) ,
            'manage_options' ,
            self::SLUG ,
            array( $this , 'generate_submenu_page' )
        );
    }

    public function generate_submenu_page()
    {
        require_once( trailingslashit( dirname( __FILE__ ) ) . 'views/form.php' );
    }

    public function admin_enqueue_scripts( $hook )
    {
        if ( 'tools_page_epik-keyword-counter' == $hook )
        {
            wp_enqueue_script( self::SLUG , plugins_url( 'js/script.js' , __FILE__ ) , array( 'jquery' ) );
            wp_enqueue_style( self::SLUG , plugins_url( 'css/style.css' , __FILE__ ) );
            wp_enqueue_script('oxfam_js_cookie', 'http://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js', array( 'jquery' ), '1.10.11', true);
        }
    }

    public function ajax_form_submit()
    {
        if ( isset( $_POST[ 'form_submit' ]))
        {
            $showposts  = trim( $_POST['showposts'] );
            $keywords   = trim( $_POST['keywords'] );
            $orderby    = $_POST['orderby'];
            $sort       = $_POST['sort'];

            if ( $orderby == 'category' )
            {
                $orderby = 'parent';
            }

            $posts = $this->get_posts( $showposts , $orderby , $sort );

            if ( count( $posts ) > 0 && $keywords != "" )
            {
                $table = apply_filters( 'generate_table' , $posts , explode( "," , $keywords ) );

                $this->insert_keywords( $keywords );

                $response = array( 'status' => true , 'results' => $posts , 'result' => $table );

            }
            else
            {
                $response = array( 'status' => false );
            }

            wp_send_json( $response );

        }
    }

    public function ajax_keywords_search()
    {
        if ( isset( $_POST['keywords']) )
        {
            global $wpdb;

            $keywords = esc_sql( trim( $_POST['keywords'] ) );

            $results = $wpdb->get_results( "SELECT DISTINCT(`keywords`) FROM wp_epik_keyword_counter WHERE `keywords` LIKE '%" . $keywords . "%'" );

            $response = array( 'status' => true , 'results' => $results );
            wp_send_json( $response );
        }
    }

    private function get_posts( $showposts = 50 , $orderby = 'date' , $sort = 'ASC' )
    {
        $args = array(
        	'posts_per_page'       => $showposts ,
        	'offset'               => 0 ,
        	'category'             => '' ,
        	'category_name'        => '' ,
        	'orderby'              => $orderby ,
        	'order'                => $sort ,
        	'include'              => '' ,
        	'exclude'              => '' ,
        	'meta_key'             => '' ,
        	'meta_value'           => '' ,
        	'post_type'            => 'post' ,
        	'post_mime_type'       => '' ,
        	'post_parent'          => '' ,
        	'author'               => '' ,
        	'post_status'          => 'publish' ,
        	'suppress_filters'     => true
        );

        $query = new WP_Query($args);

        return $query->posts;
    }

    public function generate_table( $posts , $keywords )
    {
        $html = "<table border=1>";
        $html .= "<thead><tr>";
        $html .= "<th>Post Title</th>";
        foreach ( $keywords as $word )
        {
            $html .= "<th>Count for '" . $word . "'</th>";
        }
        $html .= "<thead></tr><tbody>";
        foreach ( $posts as $key => $post )
        {
            $html .= "<tr>";
            $html .= "<td>" . $post->post_title . "</td>";

            foreach ( $keywords as $keyword )
            {
                $html .= "<td>" . $this->count_keywords( $post->post_content , $keyword ) . "</td>";
            }

            $html .= "</tr>";
        }
        $html .= "</tbody></table>";

        return $html;
    }

    private function count_keywords( $content , $keyword )
    {
        $counter = 0;

        $pattern = '/' . strtoupper( $keyword ) . '/i';
        preg_match_all($pattern, strtoupper( $content ) , $matches);

        return count( $matches[0] );
    }

    public function trim_explode( $string , $delimeter )
    {
        return array_map( 'trim' , explode( $delimeter , $string ) );
    }

    public static function install()
    {
        global $wpdb;
        $table_name = self::TABLE_NAME;

    	if($wpdb->get_var("show tables like '$table_name'") != $table_name)
    	{
    		$sql = "CREATE TABLE " . $table_name . " (
    		      `ID` bigint(20) unsigned NOT NULL auto_increment ,
    		      `keywords` text NOT NULL ,
    		      UNIQUE KEY id (id)
    		);";

    		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    		dbDelta($sql);
    	}
    }

    private function insert_keywords( $keywords )
    {
        global $wpdb;

        $query = $wpdb->insert(
        	self::TABLE_NAME ,
        	array(
        		'keywords' => $keywords
        	),
        	array(
        		'%s'
        	)
        );

        return $query ? true : false;
    }

}

$epik_keyword_counter = new Epik_Keyword_Counter();

register_activation_hook( __FILE__ , array( 'Epik_Keyword_Counter', 'install' ) );
