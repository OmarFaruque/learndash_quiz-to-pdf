<?php

/**
 * Plugin Name: Learndash Quiz to PDF
 * Version: 1.0.0
 * Description: This plugin allows you to generate a PDF/DOCX file for your creating Quiz Test Paper based on multiple criteria.
 * Author: pluginswizard
 * Text Domain: quiztodocxandpdf 
 */


class QuizToDocxAndPdf{

	function settings(){
		$exito = 0;
		if(isset($_POST['change']))
		{   
			
			if(!isset($_POST['footerCredits']))
				$_POST['footerCredits'] =''; 

			if($_POST['footerCredits']=='on')
			{
				update_option("footerCredits",$_POST['footerCredits']);
				update_option("footerText",$_POST['footerText']);
			}
			else{
				update_option("footerCredits",'');
				update_option("footerText",''); 
			}

			if(!isset($_POST['displayName']))
				$_POST['displayName'] =''; 

			if($_POST['displayName']=='on')
				update_option("displayName",$_POST['displayName']);
			else
				update_option("displayName",'');

			if(!isset($_POST['displayRoll']))
				$_POST['displayRoll'] =''; 

			if($_POST['displayRoll']=='on')
				update_option("displayRoll",$_POST['displayRoll']);
			else
				update_option("displayRoll",'');

			if(!isset($_POST['showHints']))
				$_POST['showHints'] =''; 

			if($_POST['showHints']=='on')
				update_option("showHints",$_POST['showHints']);
			else
				update_option("showHints",'');

			if(!isset($_POST['showMarks']))
				$_POST['showMarks'] =''; 

			if($_POST['showMarks']=='on')
				update_option("showMarks",$_POST['showMarks']);
			else
				update_option("showMarks",'');

			if(!isset($_POST['showChecks']))
				$_POST['showChecks'] =''; 

			if($_POST['showChecks']=='on')
				update_option("showChecks",$_POST['showChecks']);
			else
				update_option("showChecks",'');

			update_option("bestPosition",$_POST['bestPosition']); 
			$exito = 1;
		}
		$displayName = get_option("displayName","");
		$displayRoll = get_option("displayRoll","");
		$footerCredits = get_option("footerCredits","");
		$footerText = get_option("footerText","");
		$showHints = get_option("showHints","");
		$showMarks = get_option("showMarks","");
		$showChecks = get_option("showChecks","");
		$bestPosition = get_option("bestPosition","questions");
		include("templates/settings-page.php");
	}

	function questionSetter(){

		include("templates/questions-setter.php");
	}

	function enqueue(){ 
		wp_enqueue_style('lqecss',
						plugin_dir_url(__FILE__).'/css/learndashquizexport.css',
						[],
						'0.4'); 
	}

	function bringQuestions(){
		global $wpdb, $table_prefix;
		$arr = [];
		$questions = $table_prefix.'learndash_pro_quiz_question';

		if(isset($_REQUEST['chooseAnyone']) && $_REQUEST['chooseAnyone']=='category'){	 
			$sql = "SELECT * FROM $questions WHERE category_id=".$_REQUEST['select']."" ;
		}else{		
			$sql =  "SELECT  * FROM {$wpdb->postmeta} m 
			INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id 
			WHERE  m.meta_key='quiz_id' AND m.meta_value=".$_REQUEST['select']." AND p.post_status='publish'";
		} 
		if(isset($_REQUEST['noOfQuestions']) && is_numeric($_REQUEST['noOfQuestions']))
			$sql .= " LIMIT $_REQUEST[noOfQuestions]";

		$questions = $wpdb->get_results($sql);
		if(isset($_REQUEST['randomize']) && $_REQUEST['randomize']=='on')
			shuffle($questions);
		return $questions;
	}
 

	function addPages(){ 

        add_menu_page("Learndash Question Setter","Learndash Question Setter",'manage_options','question-setter', [$this,'questionSetter'],'dashicons-welcome-learn-more',2);
        
        add_submenu_page('question-setter','Display Options','Display Options','manage_options','learndashquizexport',[$this,'settings'] );
	 
	}

	public function bringCategories(){
		global $wpdb, $table_prefix;
		$arr = [];
		$categories = $table_prefix.'learndash_pro_quiz_category';
		$r = $wpdb->get_results("SELECT * FROM $categories");
		foreach ($r as $key => $value) {
			$arr[$value->category_id] = $value->category_name;
		}
		die(json_encode($arr));
	}

	public function bringQuizzes(){
		global $wpdb, $table_prefix;
		$arr = [];
		$categories = $table_prefix.'learndash_pro_quiz_master';
		$r = $wpdb->get_results("SELECT q.*,p.ID as idpost FROM $categories  q
			INNER JOIN {$wpdb->postmeta} m ON m.meta_key='quiz_pro_id' AND m.meta_value=q.id 
			INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id
			WHERE p.post_status='publish'");
		foreach ($r as $key => $value) {
			$arr[$value->idpost] = $value->name;
		}
		die(json_encode($arr));
	}

	public function doc(){
		if(isset($_REQUEST['exportIn']) && $_REQUEST['exportIn']=='docx'){
			$this->docx();
		}
		if(isset($_REQUEST['exportIn']) && $_REQUEST['exportIn']=='pdf'){
			$this->pdf();
		}
	}

	function docx(){  
		require_once 'htmltodoc.php';
		require_once 'renderdocx.php';
	}

	function pdf(){
		global $wpdb, $table_prefix;
		require_once 'renderpdf.php';
	}

	public function __construct(){ 
		add_action('init' , [ $this , 'doc' ]);
		add_action( 'admin_menu' , [ $this , 'addPages' ] ); 
		add_action( 'admin_enqueue_scripts' , [ $this , 'enqueue' ] ); 
		add_action( 'wp_ajax_bring_category' , [ $this , 'bringCategories' ] ); 
		add_action( 'wp_ajax_bring_quizwise' , [ $this , 'bringQuizzes' ] ); 

	}
}

new QuizToDocxAndPdf();
