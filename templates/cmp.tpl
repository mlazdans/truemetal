<!DOCTYPE html>
<html lang="lv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>[ TRUEMETAL {title} ]</title>
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=">
<meta name="description" content="{meta_descr}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<link href="/css/diff-table.css" rel="stylesheet" type="text/css">
<style>
.merged, .ignored {
	background-color: aliceblue;
}
</style>
</head>
<body>

<!-- BEGIN cmp -->
<table class="{table_class}">
<tr>
	<td>res_id:</td>
	<td>{merge_status}{res_id}, <a href="https://truemetal.lv/resroute/{res_id}/">tm.lv</a>, <a href="http://truemetal-remote/resroute/{res_id}/">local</a></td>
</tr>
<tr>
	<td>forum_id:</td>
	<td>{forum_id}</td>
</tr>
<tr>
	<td>res_visible:</td>
	<td>{res_visible}</td>
</tr>
<tr>
	<td>res_data:</td>
	<td>{res_data_diff}</td>
</tr>
<tr>
	<td>res_data_compiled:</td>
	<td>{res_data_compiled_diff}</td>
</tr>
<tr>
	<td>Actions:</td>
	<td>
		<a href="#" onclick="merge({forum_res_id}, {comment_res_id}); return false;">merge</a> |
		<a href="#" onclick="ignore({forum_res_id}, {comment_res_id}); return false;">ignore</a>
	</td>
</tr>
<tr>
	<td colspan="2" style="border-bottom: 1px solid black;"></td>
</tr>
</table>
<!-- END cmp -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/jsload/?v={script_version}"></script>
<script>

function _register(forum_res_id, comment_res_id, ignored){
	$.ajax({
		url: "/comment_check/?action=merge&forum_res_id=" + forum_res_id + "&comment_res_id=" + comment_res_id + "&ignored=" + ignored,
		dataType: 'json',
		complete: function(req, status){
			console.log(req, status);
		}
	});
}

function merge(forum_res_id, comment_res_id){
	_register(forum_res_id, comment_res_id, 0);
}

function ignore(forum_res_id, comment_res_id){
	_register(forum_res_id, comment_res_id, 1);
}
</script>
{tmpl_finished}
</body>
</html>
