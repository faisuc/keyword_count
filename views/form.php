<div class="tablenav top">
    <form id="alt_form" name="lookup">
        <div class="alignleft actions">
			<label for="showposts"># of Posts to check:</label>
            <input type='text' name='showposts' id=showposts value="50" size='4' >
            <label for="keyw">Keywords To be searched(separated by ,):</label>
        </div>

        <div class="alignleft actions">
            <input type='text' name='keywords' id='keyw' value="" size='50' autocomplete="off"><div id="tag_update"></div>
        </div>

        <div class="alignleft actions">
            <select name="orderby" id="orderby">
                <option value="">-- ORDER BY --</option>
            	<option value="date">Date</option>
            	<option value="title">Title</option>
                <option value="category">Category</option>
            </select>
        </div>

        <div class="alignleft actions">
            <select name="sort" id="sort">
                <option value="">-- SORT --</option>
            	<option value="ASC">ASC</option>
            	<option value="DESC">DESC</option>
            </select>
        </div>

        <div class="alignleft actions">
            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Check Count">
            <input type="hidden" id="ajaxUrl" value="<?php echo admin_url('admin-ajax.php'); ?>" />
        </div>

		<br class="clear">
    </form>
</div>

 <div id="result"></div>
 </div>
