<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
<style>
	form *{
		font-family: Roboto, sans-serif;
		font-weight: 400;
	}
	h2{
		font-size: 2em;
		font-weight: 300;
	}
	h3{
		font-size: 1.5em;
		font-weight: 300;
	}

	[type="radio"]:checked,
[type="radio"]:not(:checked) {
    position: absolute;
    left: -9999px;
}
[type="radio"]:checked + label,
[type="radio"]:not(:checked) + label
{
    position: relative;
    padding-left: 28px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: #666;
}
[type="radio"]:checked + label:before,
[type="radio"]:not(:checked) + label:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 18px;
    height: 18px;
    border: 1px solid #ddd;
    border-radius: 100%;
    background: #fff;
}
[type="radio"]:checked + label:after,
[type="radio"]:not(:checked) + label:after {
    content: '';
    width: 12px;
    height: 12px;
    background: #b20000;
    position: absolute;
    top: 4px;
    left: 4px;
    border-radius: 100%;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
}
[type="radio"]:not(:checked) + label:after {
    opacity: 0;
    -webkit-transform: scale(0);
    transform: scale(0);
}
[type="radio"]:checked + label:after {
    opacity: 1;
    -webkit-transform: scale(1);
    transform: scale(1);
}

td:nth-child(1){
	width: 300px;
}

input[type=number],input[type=text],select{ 
	border-radius: 3px;
	min-width: 200px;
}

.sub, .dashicons{
	color:#888;
}

.dashicons{
	font-size: 2.6em;
}

hr{
	border: none;
	border-bottom:  1px solid #ccc;
}

.error{
	color:red;
	display: none;
}
</style>
<form id="setter" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="change" value="1">
	<table>
		<tr>
			<td style="width:15%;    vertical-align: top;    padding-top: 1.5em;"><span class="dashicons dashicons-admin-generic"></span></td>
			<td><h2>Learndash Question Setter</h2>
				
 <h3 class="sub">Export Questions for Exam</h3></td>
		</tr>
	</table>

 <hr> 
	
	<table>
 
		<tr  >
			<td><label>1.  Choose anyone</label></td>
			<td> <input type="radio" name="chooseAnyone"   id="category" value="category"><label for="category"  >Category</label>
				<input type="radio" name="chooseAnyone"   id="quizwise" value="quizwise"><label for="quizwise"  >Quizwise</label>
				<span class="error errorChooseAnyone">You must select one option.</span>
			</td>
		</tr>

		<tr  >
			<td><label>2. Select</label></td>
			<td><select required name="select" id="select2">
				<option value="">Choose Above</option>
			</select><span class="error errorSelect">You must select one option.</span></td>
		</tr>
		<tr  >
			<td><label>3. No. of Questions</label></td>
			<td> <input type="number"   name="noOfQuestions"   placeholder="10"><span class="error errornoOfQuestions">You must input some number.</span></td>
		</tr>
		<tr >
			<td><label>4. Do you want to randomize questions order?</label></td>
			<td><input type="checkbox" id="randomize" class="switch-input" name="randomize" ><label for="randomize" class="switch-label">Switch</label></td>
		</tr>
 
		<tr  >
			<td><label>5.  Export in</label></td>
			<td> <input type="radio" name="exportIn"   id="pdf" value="pdf"><label for="pdf"  >PDF</label>
				<input type="radio" name="exportIn"   id="docx" value="docx"><label for="docx"  >Docx</label>
				<span class="error errorExportIn">You must select one option.</span>
			</td>
		</tr>
	</table>
	
	<div><input type="submit" class="button button-primary" value="Download"></div>

</form>
<script>
 

	(function($){
		$(function(){
			$('#setter').on('submit',function(e){ 
			var err = false;
				$('.error').css('display','none');
				 if($('[name=chooseAnyone]:checked').val()==undefined){
				 	$('.errorChooseAnyone').css('display','inline-block');
				 	err=true;
				 }
				 if($('[name=exportIn]:checked').val()==undefined){
				 	$('.errorExportIn').css('display','inline-block');
				 	err=true;
				 }
				 if(err)
					return false;  
			});
			$('[name=chooseAnyone]').on('change',function(){
				$.ajax({url: '<?php echo admin_url('admin-ajax.php'); ?>',
						method:'POST',
						data: {action:'bring_'+$(this).val()} ,
						success: function(data){
							var options = JSON.parse(data);

							

							console.log('options: ', options);
							$('[name=select]').html('');
							for ( key in options) {
								$('[name=select]').append('<option value="'+key+'">'+options[key]+'</option>');
							}
						}
						});
			});
		});
	})(jQuery);
</script>