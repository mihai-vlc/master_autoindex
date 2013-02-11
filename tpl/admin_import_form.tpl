<div class='content'>
{$message}
{$import_files}
<form action='?' method='post'>
<select name='path'>
<option value=''>./</option>
{$path_opt}
</select><br/>
{$url} | {$name}<br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<input type='text' name='f[]' value='http://'>|<input type='text' name='n[]'><br/>
<br/><input type='submit' value='{$import_files}'>
</form>
</div>