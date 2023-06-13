<!-- BEGIN BLOCK_comment_edit_form disabled -->
<form action="#comment_form" method="post" id="comment_form">
<input type="hidden" name="action" value="update_comment">
<table width="100%" cellpadding="2" cellspacing="0">
<!-- BEGIN BLOCK_comment_error disabled -->
<tr>
	<td colspan="2" class="error">{error_msg}</td>
</tr>
<!-- END BLOCK_comment_error -->
<tr>
	<td align="right">Vārds:</td>
	<td style="width: 100%">{USER_l_nick}</td>
</tr>
<tr>
	<td colspan="2" valign="top">Ziņa:</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<textarea name="res_data" cols="50" rows="15" style="width: 100%;">{res_data}</textarea>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<input type="submit" value=" Saglabāt ">
		<a href="{res_route}" class="button"> Atcelt </a>
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_comment_edit_form -->
