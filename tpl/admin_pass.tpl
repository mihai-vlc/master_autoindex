<div class='content'>
{$password} <br/>
<form action='?' method='post'>
<input type='password' name='pass'> <input type='submit' value='{$login}'><br/>
<input type='checkbox' name='r' value='1' id='r'> <label for='r'>{$remember}</label>
<input type='hidden' name='token' value='{$token}'>
</form>
</div>