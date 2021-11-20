<?php 
// Initialize class 
$htd = new HTML_TO_DOC();
global $wpdb,$table_prefix;
$q2 = $this->bringQuestions();
$q = [];
        $questions = $table_prefix.'learndash_pro_quiz_question';
        foreach ($q2 as $key => $value) {
            $aux = $wpdb->get_results("SELECT * FROM $questions q
                INNER JOIN {$wpdb->postmeta} m ON q.id=m.meta_value 
                WHERE meta_key='question_pro_id' AND m.post_id='{$value->ID}'");
            $q[] = $aux[0];
        } 
$totalM = 0;
foreach ($q as $key => $value) {
	$totalM += $value->points;
}
if(isset($_REQUEST['chooseAnyone']) && $_REQUEST['chooseAnyone']=='category'){		
			$categories = $table_prefix.'learndash_pro_quiz_category';
			$r = $wpdb->get_results("SELECT * FROM $categories WHERE category_id='$_REQUEST[select]'");
			$title = $r[0]->category_name;
		}else{		
			$categories = $table_prefix.'learndash_pro_quiz_master';
			$r = $wpdb->get_results("SELECT q.*,p.ID as idpost FROM $categories  q
            INNER JOIN {$wpdb->postmeta} m ON m.meta_key='quiz_pro_id' AND m.meta_value=CONVERT(q.id,char)
            INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id
            WHERE p.post_status='publish' AND p.ID='$_REQUEST[select]'");
			$title = $r[0]->name;
		}

//$htmlContent = '<table><tr><td style="background-color:rgb(50,186,255);"><h1></h1></td>
//<td style="background-color:rgb(50,186,255);"></td></tr></table><ol> ';
$htmlContent = '<table width="700" style="background-color:rgb(50,186,255); color:rgb(255,255,255);">
<tr>
<td  width="550" color="white"><h1 style="font-family:Arial;color:white;">'.$title.'</h1></td>
<td  width="150"  color="white" style="color:white;width:8cm;">'.((get_option('showMarks','')!='') ? " Total marks: ".$totalM : "").'</td>
</tr>
</table>';

if(get_option('displayName','')!='' || get_option('displayRoll','')!=''){
	$htmlContent.='<br/><br/><p align="left">';
if(get_option('displayName','')!=''){
	$htmlContent.='Name: ____________________________________________  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
}
if(get_option('displayRoll','')!=''){
	$htmlContent.='Roll:      ';
}
	$htmlContent.='</p><br/>';
}
	$htmlContent.='<hr><br/>';

$i = 0;
foreach ($q as $key => $value) {
	$htmlContent .= ' <table width="700">
<tr>
<td  width="650" color="white">'.(++$i).'. '.strip_tags($value->question).'';
	$ans = unserialize($value->answer_data);
	if(is_array($ans) && count($ans)){

		if($value->answer_type=='single'
			|| $value->answer_type=='multiple'){
			if(get_option('showChecks','')=='')
			$htmlContent .= '<ul type="a"> ';

        		$correct='';
        		foreach ($ans as $key2 => $value2) {

            if(trim(strip_tags($value2->getAnswer()))=='') continue;
 
        			if($value2->isCorrect())
		        		$correct .= strip_tags($value2->getAnswer()).','; 

			if(get_option('showChecks','')!='')
			$htmlContent.="<p> ☐ ".strip_tags($value2->getAnswer())."</p>";
		else
			$htmlContent.="<li>".strip_tags($value2->getAnswer())."</li>";
		}
		    	if(strlen($correct))
		    	  	$correct = substr($correct, 0,strlen($correct)-1);

		if(get_option('showChecks','')=='')
		$htmlContent .= '</ul> ';
		}
		elseif($value->answer_type=='assessment_answer'
			|| $value->answer_type=='cloze_answer'){ 
			$ans = unserialize($value->answer_data);
		foreach($ans as $e){
			$clean = $e->getAnswer();
$bra1 = strpos($clean, '{');
	        		$bra2 = strpos($clean, '}'); 
	        		$end = strlen($clean);
	        		$correct = substr($clean, $bra1+1,$bra2);
	        		$clean = substr($clean, 0,$bra1).'___________'.substr($clean, $bra2+1,$end);
	        		
        		 	$arr = explode(']', $correct); 
        		 	foreach ($arr as $k => $word) {
        		 		$arr[$k] = str_replace('{', '',str_replace('}', '',str_replace(']', '',str_replace('[', '', $word))));
        		 	}
        		 	$correct = implode(', ',$arr);

			    	if(strlen($correct) && $value->answer_type=='assessment_answer')
			    	  	$correct = substr($correct, 0,strlen($correct)-1); 
    				
			$htmlContent.="<p>".$clean."</p>";
		} 
    				
    				}
	}


        	if(get_option('showHints','')!='' && !empty(strip_tags($value->tip_msg))){

			$htmlContent.='<p style="color:blue;"> [Hint: '.strip_tags($value->tip_msg)."]</p>";
        	}
        	if(get_option("bestPosition",'questions')=='questions'){

			$htmlContent.='<p style="color:red;"> Answer(s): '.$correct."</p>";
        	}
        	$htmlContent.='</td>'.
(get_option('showMarks','')!=''? '<td  width="50"  color="white" style="width:8cm;">('.$value->points.')' : '').'</td></tr></table>';
	
}

    	if(get_option("bestPosition",'questions')=='end'){
$htmlContent.='<br style="page-break-before: always">'; 

$htmlContent.='<h1 style="font-family:Arial;">Correct Answers & Explanation</h1>';

$i=0;
foreach ($q as $key => $value) {
    	 	
    			$ans = unserialize($value->answer_data);
    			$htmlContent .='<p>'.(++$i).'. ';
	    	  foreach ($ans as $key2 => $value2) {

	        		$clean =  strip_tags($value2->getAnswer()); 
	        		if($value->answer_type=='single' || $value->answer_type=='multiple'){
	        		if($value2->isCorrect()){
    				 $htmlContent .= $clean.' '; 
    				}
    				}
    				else{ 
    				$bra1 = strpos($clean, '{');
	        		$bra2 = strpos($clean, '}'); 
	        		$end = strlen($clean);
	        		$correct = substr($clean, $bra1+1,$bra2);
	        		
        		 	$arr = explode(']', $correct); 
        		 	foreach ($arr as $k => $word) {
        		 		$arr[$k] = str_replace('{', '',str_replace('}', '',str_replace(']', '',str_replace('[', '', $word))));
        		 	}
        		 	$correct = implode(', ',$arr);

			    	if(strlen($correct) && $value->answer_type=='assessment_answer')
			    	  	$correct = substr($correct, 0,strlen($correct)-1);
			    	  
    				  $htmlContent .=  $correct.''; 
    				}



	        	}
	        	$htmlContent .= '</p>';
    			$htmlContent .= '<p>Answer explanation: '.strip_tags($value->incorrect_msg).'</p><br/>'; 

    	 } 



}
    $htd->createDoc($htmlContent, sanitize_title($title).".doc", 1);
		die();