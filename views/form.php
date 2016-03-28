<div class="wrap">
<h2>Keyword Counter</h2>

<form id="alt_form" name="lookup">

<table border='1' width='500' cellspacing='0' cellpadding='0' id='searchtable' align='center'>
<tr>
    <td width='300'># of Posts to check:</td><td><input type='text' name='showposts' id=showposts value="50" size='4'></td>
</tr>
<tr>
    <td width='300'>Keywords To be searched(separated by ,):</td>
    <td><input type='text' name='keywords' id='keyw' value="" size='50' autocomplete="off"><div id="tag_update"></div></td>
</tr>
<tr>
    <td valign='top'>Order By:</td><td><input type=radio name='orderby' value="date" checked> Date
<br><input type='radio' name='orderby' value="title"> Title
<br><input type='radio' name='orderby' value="category"> Category
</td>
</tr>
<tr>
    <td valign=top>Sort:</td>
    <td><input type='radio' name='sort' value="ASC" checked> Lowest to Highest/Oldest to Newest<br>
<input type='radio' name='sort' value="DESC" checked> Highest to Lowest/Newest to Oldest</td>
</tr>

</table><br>
<div style='width:100px;margin:0 auto;'>
<input type='submit' value="Check Count"></div>
<input type="hidden" id="ajaxUrl" value="<?php echo admin_url('admin-ajax.php'); ?>" />
 </form>

 <div id="result"></div>
 </div>
