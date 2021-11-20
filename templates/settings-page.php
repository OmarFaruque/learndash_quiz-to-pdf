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
	color:#666;
}
.dashicons{
	font-size: 2.6em;
}

hr{
	border: none;
	border-bottom:  1px solid #ccc;
}

.lined.after{
	position: relative;
}

.lined.after:after {
    position: absolute;
    top: 51%;
    overflow: hidden;
    width: 50%;
    height: 1px;
    content: '\a0';
    background-color: #bbb;
}
</style>
<form method="post">
	<input type="hidden" name="change" value="1">
<table>
		<tr>
			<td style="width:32.5%;    vertical-align: top;   padding-top: 1.5em;"><span class="dashicons dashicons-admin-generic"></span></td>
			<td><h2> Settings</h2></td>
		</tr>
	</table> 
	
	<?php if(isset($exito) && $exito==1): ?>
				<div style="padding: .5em 1em; color: white;background-color: limegreen; margin:1em;" >
					<p>Settings saved successfully.</p>
				</div>
				<?php endif; ?>
	<h3 class="lined after">
		Display Options
	</h3>
	<table>
 
		<tr  >
			<td><label>1. Display name and roll in the document?</label></td>
			<td><span>Name</span> <input type="checkbox" id="displayName" class="switch-input" name="displayName"  <?php if($displayName=='on') echo 'checked'; ?>><label for="displayName" class="switch-label"> </label>
				<span>Roll</span> <input type="checkbox" id="displayRoll" class="switch-input" name="displayRoll"  <?php if($displayRoll=='on') echo 'checked'; ?>><label for="displayRoll" class="switch-label"> </label>
			</td>
		</tr>

		<tr  >
			<td><label>2. Do you want to print footer credits?</label></td>
			<td><input type="checkbox" id="footerCredits" onchange="this.checked ? jQuery('.rc').removeAttr('disabled') : jQuery('.rc').attr('disabled','disabled')" class="switch-input" name="footerCredits"  <?php if($footerCredits=='on') echo 'checked'; ?>><label for="footerCredits" class="switch-label">Switch</label>
				<input type="text" class="rc" name="footerText" <?php if($footerCredits!='on') echo 'disabled'; ?> value="<?php echo $footerText; ?>"></td>
		</tr>
		 
		<tr >
			<td><label>3. Do you want to show hint?</label></td>
			<td><input type="checkbox" id="showHints" class="switch-input" name="showHints"  <?php if($showHints=='on') echo 'checked'; ?>><label for="showHints" class="switch-label">Switch</label></td>
		</tr>
		<tr >
			<td><label>4. Do you want to show marks?</label></td>
			<td><input type="checkbox" id="showMarks" class="switch-input" name="showMarks"  <?php if($showMarks=='on') echo 'checked'; ?>><label for="showMarks" class="switch-label">Switch</label></td>
		</tr>
		<tr >
			<td><label>5. Do you want to show checkboxes in single/multiple options questions?</label></td>
			<td><input type="checkbox" id="showChecks" class="switch-input" name="showChecks"  <?php if($showChecks=='on') echo 'checked'; ?>><label for="showChecks" class="switch-label">Switch</label></td>
		</tr>
	</table>
	 
	<h3 class="lined after">
		Answer Settings
	</h3>
	 
	
	<table>
 
		<tr  >
			<td><label>1.  Which is your best position to show answer ?</label></td>
			<td> <input type="radio" name="bestPosition" <?php if($bestPosition=='questions') echo 'checked'; ?> id="questions" value="questions"><label for="questions"  >Below the Questions</label>
				<input type="radio" name="bestPosition" <?php if($bestPosition=='end') echo 'checked'; ?> id="end" value="end"><label for="end"  >End of the document</label>
			</td>
		</tr>
 
	</table>
	
	<div><input type="submit" class="button button-primary" value="Save Fields"></div>

</form>