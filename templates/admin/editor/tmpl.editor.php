<input type="hidden" name="data[filter_data]" value="0">
<input type="hidden" name="data[editor_data]" value="">
<table width="580" cellpadding="0" cellspacing="0" bgcolor="ButtonFace" style="border: 1px solid black">
	<tr>
		<td width="100%">
			<table width="100%" cellpadding="0" style="border-top: 1px solid ButtonHighlight; border-bottom: 1px solid ButtonShadow;">
				<tr>
					<td><img class="menu-button" src="{http_root}/editor/new.gif" alt="Jauns" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)" onClick="blank_editor('textEdit{editor_id}');"></td>
					<td><img class="menu-button" src="{http_root}/editor/save.gif" alt="Saglabāt" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)" onClick="saveData({editor_id}, textEdit{editor_id})"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/cut.gif" onClick="doFormat('textEdit{editor_id}', 'Cut')" alt="Izgriezt" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/copy.gif" onClick="doFormat('textEdit{editor_id}', 'Copy')" alt="Kopēt" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/paste.gif" onClick="doFormat('textEdit{editor_id}', 'Paste')" alt="Ievietot" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/image_insert.gif" onClick="image_insert('textEdit{editor_id}')" alt="Ievietot attēlu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/image_properties.gif" onClick="image_properties('textEdit{editor_id}')" alt="Attēla parametri" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/undo.gif" onClick="doFormat('textEdit{editor_id}', 'undo', '')" alt="Atcelt" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/redo.gif" onClick="doFormat('textEdit{editor_id}', 'redo')" alt="" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/link_create.gif" onClick="hyperlink_insert('textEdit{editor_id}')" alt="Ielikt saiti" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/item_add.gif" onClick="item_add('textEdit{editor_id}')" alt="Ielikt saiti uz moduli" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/file_insert.gif" onClick="file_insert('textEdit{editor_id}')" alt="Ielikt saiti uz failu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" width="1" height="24" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/convert.gif" alt="Pārslēgt režīmus (HTML/Design)" onClick="swapModes(textEdit{editor_id});" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td width="100%"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%" style="border-top: 1px solid ButtonHighlight; border-bottom: 1px solid ButtonShadow;">
			<table width="100%" cellpadding="0">
				<tr>
					<td><select style="font-size=10px" name="selectfontface" onChange="font_face('textEdit{editor_id}', this)">
						<option value="">-Fonts-</option>
						<option value="Arial,Helvetica,Verdana, Sans Serif">Arial</option>
						<option value="Courier, Courier New">Courier</option>
						<option value="Tahoma, Verdana, Arial, Helvetica, Sans Serif">Tahoma</option>
						<option value="Times New Roman, Times, Serif">Times</option>
						<option value="Verdana, Tahoma, Arial, Helvetica, Sans Serif">Verdana</option>
					</select></td>
					<td><select style="font-size=10px" name="selectfontsize" onChange="font_size('textEdit{editor_id}', this)">
						<option value="">-Izmērs-</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select></td>
					<td><img class="menu-button" src="{http_root}/editor/text_bold.gif" onClick="doFormat('textEdit{editor_id}', 'Bold')" alt="Bold" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/text_italic.gif" onClick="doFormat('textEdit{editor_id}', 'Italic')" alt="Italic" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/text_underline.gif" onClick="doFormat('textEdit{editor_id}', 'Underline')" alt="Underline" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/align_left.gif" onClick="doFormat('textEdit{editor_id}', 'JustifyLeft')" alt="Align left" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/align_center.gif" onClick="doFormat('textEdit{editor_id}', 'JustifyCenter')" alt="Align jystify" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/align_right.gif" onClick="doFormat('textEdit{editor_id}', 'JustifyRight')" alt="Align right" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/list_bullet.gif" onClick="doFormat('textEdit{editor_id}', 'InsertUnorderedList')" alt="Saraksts" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/list_ordered.gif" onClick="doFormat('textEdit{editor_id}', 'InsertOrderedList')" alt="Sakārtots saraksts" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/indent.gif" onClick="doFormat('textEdit{editor_id}', 'Indent')" alt="Atkāpe" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/outdent.gif" onClick="doFormat('textEdit{editor_id}', 'Outdent')" alt="Atcelt atkāpi" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/color_fore.gif" onClick="color_fore('textEdit{editor_id}')" alt="Krāsa" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/color_back.gif" onClick="color_back('textEdit{editor_id}')" alt="Fona Krāsa" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td class="raised"><img src="{http_root}/img/1x1.gif" alt=""></td>
					<td><img class="menu-button" src="{http_root}/editor/hr.gif" onClick="split('textEdit{editor_id}')" alt="Atdalīt ievadu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td width="100%"></td>
					<!-- BEGIN BLOCK_html_clear disabled -->
					<td><img class="menu-button" src="{http_root}/editor/html_clear.gif" onClick="data_filter('textEdit{editor_id}', {editor_id})" alt="Attīrīt datus" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<!-- END BLOCK_html_clear -->
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%" style="border-top: 1px solid ButtonHighlight; border-bottom: 1px solid ButtonShadow;">
			<table width="100%" cellpadding="0">
				<tr>
					<td><img class="menu-button" src="{http_root}/editor/table_insert.gif" onClick="table_create('textEdit{editor_id}')" alt="Ielikt tabulu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/table_properties.gif" onClick="table_properties('textEdit{editor_id}')" alt="Tabulas uzstādījumi" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/cell_properties.gif" onClick="cell_properties('textEdit{editor_id}')" alt="Šūnas uzstādījumi" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/row_insert.gif" onClick="row_insert('textEdit{editor_id}')" alt="Ielikt rindu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/column_insert.gif" onClick="column_insert('textEdit{editor_id}')" alt="Ielikt kolonnu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/column_delete.gif" onClick="column_delete('textEdit{editor_id}')" alt="Dzēst kolonnu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/row_delete.gif" onClick="row_delete('textEdit{editor_id}')" alt="Dzēst rindu" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/merge_right.gif" onClick="merge_right('textEdit{editor_id}')" alt="Apvienot pa labi" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/merge_down.gif" onClick="merge_down('textEdit{editor_id}')" alt="Apvienot uz leju" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/split_horizontal.gif" onClick="split_horizontal('textEdit{editor_id}')" alt="Atdalīt pa horizontāli" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td><img class="menu-button" src="{http_root}/editor/split_vertical.gif" onClick="split_vertical('textEdit{editor_id}')" alt="Atdalīt pa vertikāli" onMouseOver="makeRaised(this)" onMouseOut="makeNormal(this)"></td>
					<td width="100%"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><iframe name="textEdit{editor_id}" style="width:100%; height:400px; direction:ltr;" onLoad="init(textEdit{editor_id});" src="{admin_root}/editor/getdata/{editor_data_module}/{editor_data_id}/{editor_filter}/{editor_filter_level}/"></iframe></td>
	</tr>
</table>
