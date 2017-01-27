<form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
    <input type="text" value="<?php print (empty($_REQUEST['s'])?NULL:$_REQUEST['s']) ?>" placeholder="Search GSWA" name="s" id="s"/>
    <input type="submit" id="searchsubmit" value="Search"/>
</form>
