<?php

		if(isset($_REQUEST['chooseAnyone']) && $_REQUEST['chooseAnyone']=='category'){		
			$categories = $table_prefix.'learndash_pro_quiz_category';
			$r = $wpdb->get_results("SELECT * FROM $categories WHERE category_id=".$_REQUEST['select']."");
			$title = $r[0]->category_name;
		}else{		
			$categories = $table_prefix.'learndash_pro_quiz_master';
			$r = $wpdb->get_results("SELECT q.*,p.ID as idpost FROM $categories  q
            INNER JOIN {$wpdb->postmeta} m ON m.meta_key='quiz_pro_id' AND m.meta_value=q.id 
            INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id
            WHERE p.post_status='publish' AND p.ID=".$_REQUEST['select']."");
			$title = $r[0]->name;
		}
		require('fpdf17/fpdf.php');
		require('pdf.php');


		$filename = str_replace(' ', '_', $title);
		$filename = strtolower($filename);
		
        $questions = $table_prefix.'learndash_pro_quiz_question';
        $q2 = $this->bringQuestions();
        $q = [];



		if(isset($_REQUEST['chooseAnyone']) && $_REQUEST['chooseAnyone']=='category'){	
			$q = $q2;
		}else{
			foreach ($q2 as $key => $value) {
				$aux = $wpdb->get_results("SELECT * FROM $questions q
					INNER JOIN {$wpdb->postmeta} m ON q.id=m.meta_value 
					WHERE meta_key='question_pro_id' AND m.post_id='{$value->ID}'");
				$q[] = $aux[0];
			} 
		}
 
	$totalM = 0;
	foreach ($q as $key => $value) {
		$totalM += $value->points;
	}

  		
        $pdf = new PDF('P');
        $pdf->AddFont('robotol','','Roboto-Light.php');
        $pdf->AddPage();
        $pdf->SetFont('robotol','',50);
        //32baff
        $pdf->setTextColor(255,255,255);
        $pdf->setFillColor(50,186,255);
        $pdf->setFontSize(35);
        $pdf->Cell(150, 30, " ".$title, 0, 0, 'L',1); 
        $pdf->setFontSize(12);
        $pdf->Cell(40, 30, (get_option('showMarks','')!='') ? " Total marks: ".$totalM : "", 0, 1, 'L',1);  
        $pdf->setTextColor(0,0,0);
        $pdf->setFillColor(255,255,255);
        $pdf->setTextColor(110,110,110);
        $pdf->setFontSize(15);
        $pdf->Ln(10);
        if(get_option('displayName','')!='' || get_option('displayRoll','')!=''){
        if(get_option('displayName','')!=''){
        	$pdf->Cell(45, 10, "   Student Name         ", 0, 0, 'L',0);  
        	$pdf->Cell(50, 10, "", 'B', 0, 'L',0);
        }  
        if(get_option('displayRoll','')!=''){
        	$pdf->Cell(40, 10, " Roll      ", 0, 0, 'R',0);  
        	$pdf->Cell(50, 10, "", 'B', 1, 'L',0); 
        } 
        $pdf->Ln(10);
    	}


    	foreach ($q as $key => $value) {
    		$key=$key+1;
        	$pdf->setFontSize(15);
    		$pdf->multiCell(175,10,"$key. ".iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value->question))),0,1,'L',0);

            $pdf->Cell(0,10, (get_option('showMarks','')!='') ? "(".$value->points.")" : "",0,1,'R',0);

    		switch ($value->answer_type) {
    			case 'single':
    			case 'multiple':

    		$ans = unserialize($value->answer_data);
    	  
    	  foreach ($ans as $key2 => $value2) {
            if(trim(strip_tags($value2->getAnswer()))=='') continue;
        		$pdf->setFontSize(12);  

            if(get_option('showChecks','')!=''){
                $pdf->Cell(5, 5, ' ', 0, 0);
                $pdf->Cell(5, 5, ' ', 1, 0);
    			$pdf->Cell(160,5,  ' '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value2->getAnswer()))),0,1);
            }
            else{
                $pdf->Cell(180,5,  ' [    ]   '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value2->getAnswer()))),0,1);
            }
                $pdf->Ln();
    	  }
        	if(get_option('showHints','')!='' && !empty(strip_tags(html_entity_decode($value->tip_msg)))){
        		$pdf->setFontSize(12); 
    			$pdf->multiCell(0,10, '[Hint: '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value->tip_msg)))."]");
        	}
        	if(get_option("bestPosition",'questions')=='questions'){
        		$pdf->setFontSize(12); 
        		$pdf->setTextColor(255,0,0);
        		$stringC='';
        		foreach ($ans as $key2 => $value2) {
        			if($value2->isCorrect())
		        		$stringC .= iconv('UTF-8', 'windows-1252',strip_tags($value2->getAnswer())).',';
		    	  }

		    	if(strlen($stringC))
		    	  	$stringC = substr($stringC, 0,strlen($stringC)-1);

    			$pdf->multiCell(0,10, 'Answer(s): '.iconv('UTF-8', 'windows-1252',$stringC));
    			if(strip_tags($value->incorrect_msg)!='')
    				$pdf->multiCell(0,10, 'Answer(s) details: '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value->incorrect_msg))));
        		$pdf->setTextColor(110,110,110);
        	}

    				# code...
    				break;
    			
    			case 'essay':
    			$pdf->multiCell(0,10, '','B');
        	if(get_option('showHints','')!='' && !empty(html_entity_decode(strip_tags($value->tip_msg)))) {
        		$pdf->setFontSize(12); 
    			$pdf->multiCell(0,10, '[Hint: '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value->tip_msg)))."]");
        	}
    				break;
    			case 'cloze_answer': 
    			case 'assessment_answer':
    			$correct = '';
    			$ans = unserialize($value->answer_data);
    	  
	    	  foreach ($ans as $key2 => $value2) {
	        		$pdf->setFontSize(12); 
	        		$clean =  strip_tags($value2->getAnswer()); 
	        		$bra1 = strpos($clean, '{');
	        		$bra2 = strpos($clean, '}'); 
	        		$end = strlen($clean);
	        		$correct = substr($clean, $bra1+1,$bra2);
	        		$clean = iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags(substr($clean, 0,$bra1).'___________'.substr($clean, $bra2+1,$end))));
	    			$pdf->multiCell(0,10, $clean);
	    	  }

        	if(get_option('showHints','')!='' && !empty(strip_tags($value->tip_msg))){
        		$pdf->setFontSize(12); 
    			$pdf->multiCell(0,10, '[Hint: '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value->tip_msg)))."]");
        	}
        	if(get_option("bestPosition",'questions')=='questions'){
        		$pdf->setFontSize(12); 
        		$pdf->setTextColor(255,0,0); 
        		 	$arr = explode(']', $correct); 
        		 	foreach ($arr as $k => $word) {
        		 		$arr[$k] = str_replace('{', '',str_replace('}', '',str_replace(']', '',str_replace('[', '', $word))));
        		 	}
        		 	$correct = implode(', ',$arr);
        		 
		    	if(strlen($correct) && $value->answer_type=='assessment_answer')
		    	  	$correct = substr($correct, 0,strlen($correct)-1);

    			$pdf->multiCell(0,10, 'Answer(s): '.iconv('UTF-8', 'windows-1252',$correct));
    			if(strip_tags($value->incorrect_msg)!='')
    				$pdf->multiCell(0,10, 'Answer(s) details: '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($value->incorrect_msg))));
        		$pdf->setTextColor(110,110,110);
        	}
    				break;
    			default:
    				# code...
    				break;
    		}
    	}
    	if(get_option("bestPosition",'questions')=='end'){
    		$pdf->AddPage();
    		$pdf->setFontSize(20);
    	$pdf->Cell(0,20,'Correct Answer & Explanation',0,1);
    		$pdf->setFontSize(12);
    	foreach ($q as $key => $value) {
    	 	
    			$ans = unserialize($value->answer_data);
	    	  foreach ($ans as $key2 => $value2) {

	        		$clean =  strip_tags($value2->getAnswer()); 
	        		if($value->answer_type=='single' || $value->answer_type=='multiple'){
	        		if($value2->isCorrect()){
    				$pdf->multiCell(0,10, html_entity_decode(strip_tags(($key+1).'. '.$clean)));
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
			    	  
    				$pdf->multiCell(0,10, ($key+1).'. '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($correct)))); 
    				}



	        	}
    				$pdf->multiCell(0,10,  'Answer explanation: '.iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags(  $value->incorrect_msg))) );

    	 } 

    	}
         
        $pdf->Output($filename, 'D');
        die();