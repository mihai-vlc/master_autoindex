<div class='content'>
{$message}
{$upload_files}
<form action='?' method='post'
enctype='multipart/form-data'>
<select name='path'>
<option value=''>./</option>
{$path_opt}
</select><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<input type='file' name='f[]'><br/>
<br/><input type='submit' value='{$upload_files}'>
</form>
{$max_file_size}
</div>