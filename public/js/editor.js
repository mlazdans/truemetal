// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

function doFormat(editor, command) {

	window.frames[editor].focus();

	if(arguments[2] == null) {
		this[editor].document.execCommand(command, false, null);
	} else {
		//alert(command + ', ' + arguments[2] + ', null');
		this[editor].document.execCommand(command, arguments[2], null);
	}
}

function swapModes(editor) { 
	if(editor.html_mode) { 
		editor.document.body.innerText = editor.document.body.innerHTML;
		editor.document.body.style.fontFamily = "Fixedsys,monospace";
		editor.document.body.style.fontSize = "10pt";
	} else {
		editor.document.body.innerHTML = editor.document.body.innerText;
		editor.document.body.style.fontFamily = "";
		editor.document.body.style.fontSize = "";
	}

	editor.focus();
	var s = editor.document.body.createTextRange();
	s.collapse(false);
	s.select();
	editor.html_mode = 1 - editor.html_mode;
} 

function init(editor) {
	editor.html_mode = 1;
	editor.document.designMode = "On";
	editor.focus();
	editor.loaded = true;
}

function saveData(form, editor) {
	form.elements["data[editor_data]"].value = (editor.html_mode ? 
	editor.document.body.innerHTML :
	editor.document.body.innerText);

	if(editor.onSubmitHandler)
		editor.onSubmitHandler();
	else
		form.submit();

	return true;
}

function makeRaised(el) {
	with (el.style) {
		borderLeft = "1px solid ButtonHighlight";
		borderRight = "1px solid ButtonShadow";
		borderTop = "1px solid ButtonHighlight";
		borderBottom = "1px solid ButtonShadow";
		paddingleft = "1px";
	}
}

function makeNormal(el) {
	el.style.border = "1px solid ButtonFace";
	el.style.paddingleft = "1px";
}

function blank_editor(editor) {
	window.frames[editor].focus();
	this[editor].document.body.innerHTML = '';
}

function get_table(editor)
{
	if (window.frames[editor].document.selection.type == "Control")
	{ 
	  var tControl = window.frames[editor].document.selection.createRange();
	  if (tControl(0).tagName == 'TABLE')
		return(tControl(0));
	  else
		return(null);
	}
	else
	{
	  var tControl = window.frames[editor].document.selection.createRange();
	  tControl = tControl.parentElement();
	  while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
	  {
		tControl = tControl.parentElement;
	  }
	  if (tControl.tagName == 'TABLE')
		return(tControl);
	  else
		return(null);
	}
}

function get_td(editor)
{
	if (window.frames[editor].document.selection.type != "Control")
	{
	  var tControl = window.frames[editor].document.selection.createRange();
	  tControl = tControl.parentElement();
	  while ((tControl.tagName != 'TD') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
	  {
		tControl = tControl.parentElement;
	  }
	  if (tControl.tagName == 'TD')
		return(tControl);
	  else
		return(null);
	}
	else
	{
	  return(null);
	}
}

function get_tr(editor)
{
	if (window.frames[editor].document.selection.type != "Control")
	{
	  var tControl = window.frames[editor].document.selection.createRange();
	  tControl = tControl.parentElement();
	  while ((tControl.tagName != 'TR') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
	  {
		tControl = tControl.parentElement;
	  }
	  if (tControl.tagName == 'TR')
		return(tControl);
	  else
		return(null);
	}
	else
	{
	  return(null);
	}
}

function SPAW_formCellMatrix(ct)
{
	var tm = new Array();
	for (i=0; i<ct.rows.length; i++)
	  tm[i]=new Array();

	for (i=0; i<ct.rows.length; i++)
	{
	  jr=0;
	  for (j=0; j<ct.rows(i).cells.length;j++)
	  {
		while (tm[i][jr] != undefined) 
		  jr++;

		for (jh=jr; jh<jr+(ct.rows(i).cells(j).colSpan?ct.rows(i).cells(j).colSpan:1);jh++)
		{
		  for (jv=i; jv<i+(ct.rows(i).cells(j).rowSpan?ct.rows(i).cells(j).rowSpan:1);jv++)
		  {
			if (jv==i)
			{
			  tm[jv][jh]=ct.rows(i).cells(j).cellIndex;
			}
			else
			{
			  tm[jv][jh]=-1;
			}
		  }
		}
	  }
	}
	return(tm);
}
/* ------------------------------------------------------------------------- */
function _image_insert(editor, imgSrc, doc) {
	if(doc != null)
		ed = doc[editor]
	else
		ed = this;

	if(imgSrc != null) {
		ed.document.execCommand('insertimage', false, imgSrc);
		var im = get_image(editor, doc);
		im.border = 0;
		//im.bordercolor = 'white';
		//im.style = 'border-color: white';
	}
}

function image_insert(editor)
{
	var w = 525;
	var h = 600;
	
  var l = (screen.width - w) / 2;
  var t = (screen.height - h) / 2;

	window.frames[editor].focus();

	window.open(editor_root + 'getfile/' + editor + '/image/', 'insert_image', 'fullscreen=0,toolbar=0,status=0,scrollbars=1,menubar=0,location=0,resizable=0,channelmode=0,directories=0,width=' + w + ',height=' + h + ',top=' + t +',left=' + l);
}

function get_image(editor, win)
{
	if(!win)
		win = window;
	if (win.frames[editor].document.selection.type == "Control")
	{ 
		var tControl = win.frames[editor].document.selection.createRange();
		if (tControl(0).tagName == 'IMG')
			return(tControl(0));
		else
			return(null);
	}
	else
	{
		return(null);
	}
}

function image_properties(editor)
{
	var im = get_image(editor); // current image
    
	if (im)
	{
		var iProps = {};
		iProps.src = im.src;
		iProps.alt = im.alt;
		iProps.width = (im.style.width)?im.style.width:im.width;
		iProps.height = (im.style.height)?im.style.height:im.height;
		iProps.border = im.border;
		iProps.align = im.align;
		iProps.hspace = im.hspace;
		iProps.vspace = im.vspace;

		var niProps = showModalDialog(editor_root + 'image_properties/', iProps, 
			'dialogHeight:230px; dialogWidth:400px; status:no');
      
		if (niProps)
		{
			im.src = (niProps.src)?niProps.src:'';
			if (niProps.alt) {
				im.alt = niProps.alt;
			}
			else
			{
				im.alt = '';
				//im.removeAttribute("alt");
			}
			im.align = (niProps.align)?niProps.align:'';
			im.width = (niProps.width)?niProps.width:'';
			//im.style.width = (niProps.width)?niProps.width:'';
			im.height = (niProps.height)?niProps.height:'';
			//im.style.height = (niProps.height)?niProps.height:'';
			if (niProps.border) {
				im.border = niProps.border;
			}
			else
			{
				im.removeAttribute("border");
			}
			if (niProps.hspace) {
				im.hspace = niProps.hspace;
			}
			else
			{
				im.removeAttribute("hspace");
			}
			if (niProps.vspace) {
				im.vspace = niProps.vspace;
			}
			else
			{
				im.removeAttribute("vspace");
			}
		}      
		//SPAW_updateField(editor,"");
	} // if im
}

function _file_insert(editor, fileSrc, doc) {
	if(doc != null)
		ed = doc[editor]
	else
		ed = this;

	if(fileSrc != null)
		ed.document.execCommand('createLink', false, fileSrc);
}

function file_insert(editor)
{
	var w = 350;
	var h = 400;
	
  var l = (screen.width - w) / 2;
  var t = (screen.height - h) / 2;

	window.frames[editor].focus();

	window.open(editor_root + 'getfile/' + editor, 'insert_file', 'fullscreen=0,toolbar=0,status=0,scrollbars=1,menubar=0,location=0,resizable=0,channelmode=0,directories=0,width=' + w + ',height=' + h + ',top=' + t +',left=' + l);
}

function table_create(editor)
{
	if (window.frames[editor].document.selection.type != "Control")
	{
	  // selection is not a control => insert table 
		var nt = showModalDialog(editor_root + 'table_create/', null, 
			'dialogHeight:180px; dialogWidth:420px; status:no');
	   
	  if (nt)
	  {
			window.frames[editor].focus();	 
	
		var newtable = document.createElement('TABLE');
		try {
		  newtable.width = (nt.width)?nt.width:'';
		  newtable.height = (nt.height)?nt.height:'';
		  newtable.border = (nt.border)?nt.border:'';
		  if (nt.cellPadding) newtable.cellPadding = nt.cellPadding;
		  if (nt.cellSpacing) newtable.cellSpacing = nt.cellSpacing;
		  //newtable.bgColor = (nt.bgColor)?nt.bgColor:'';
		  
		  // create rows
		  for (i=0;i<parseInt(nt.rows);i++)
		  {
				var newrow = document.createElement('TR');
				for (j=0; j<parseInt(nt.cols); j++)
				{
				  var newcell = document.createElement('TD');
				  newrow.appendChild(newcell);
				}
				newtable.appendChild(newrow);
			  }
			  var selection = window.frames[editor].document.selection.createRange();
				selection.pasteHTML(newtable.outerHTML);	  
			}
		catch (excp)
		{
		  alert('error');
		}
	  }
	}
}

function table_properties(editor)
{
	window.frames[editor].focus();	 

	var tTable
	// check if table selected
	if (window.frames[editor].document.selection.type == "Control")
	{ 
		var tControl = window.frames[editor].document.selection.createRange();
		if (tControl(0).tagName == 'TABLE')
		{
		tTable = tControl(0);
		}
	}
	else
	{
		var tControl = window.frames[editor].document.selection.createRange();
		tControl = tControl.parentElement();
		while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
		{
		tControl = tControl.parentElement;
		}
		if (tControl.tagName == 'TABLE')
		tTable = tControl;
		else
		return false;
	}

	var tProps = {};
	tProps.width = (tTable.style.width)?tTable.style.width:tTable.width;
	tProps.height = (tTable.style.height)?tTable.style.height:tTable.height;
	tProps.border = tTable.border;
	tProps.cellPadding = tTable.cellPadding;
	tProps.cellSpacing = tTable.cellSpacing;
	tProps.bgColor = tTable.bgColor;

	var ntProps = showModalDialog(editor_root + 'table_create/', tProps, 
		'dialogHeight:180px; dialogWidth:420px; status:no');
	
	if (ntProps)
	{
		// set new settings
		if(ntProps.width && ntProps.width != '0px') {
			tTable.width = ntProps.width;
			tTable.style.width = ntProps.width;
		} else {
			tTable.width = '';
			tTable.style.width = '';
		}
		//tTable.width = (ntProps.width)?ntProps.width:0;
		//tTable.style.width = (ntProps.width)?ntProps.width:'';
		tTable.height = (ntProps.height)?ntProps.height:'';
		tTable.style.height = (ntProps.height)?ntProps.height:'';
		tTable.border = (ntProps.border)?ntProps.border:'';
		if (ntProps.cellPadding) tTable.cellPadding = ntProps.cellPadding;
		if (ntProps.cellSpacing) tTable.cellSpacing = ntProps.cellSpacing;
		tTable.bgColor = (ntProps.bgColor)?ntProps.bgColor:'';
	}

	//SPAW_updateField(editor,"");
}

function row_insert(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row

	if (ct && cr)
	{
	  var newr = ct.insertRow(cr.rowIndex+1);
	  for (i=0; i<cr.cells.length; i++)
	  {
		if (cr.cells(i).rowSpan > 1)
		{
		  // increase rowspan
		  cr.cells(i).rowSpan++;
		}
		else
		{
		  var newc = cr.cells(i).cloneNode();
		  newr.appendChild(newc);
		}
	  }
	  // increase rowspan for cells that were spanning through current row
	  for (i=0; i<cr.rowIndex; i++)
	  {
		var tempr = ct.rows(i);
		for (j=0; j<tempr.cells.length; j++)
		{
		  if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
			tempr.cells(j).rowSpan++;
		}
	  }
	}
} // insertRow

function column_insert(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current row

	if (cd && cr && ct)
	{
	  // get "real" cell position and form cell matrix
	  var tm = SPAW_formCellMatrix(ct);
	  
	  for (j=0; j<tm[cr.rowIndex].length; j++)
	  {
		if (tm[cr.rowIndex][j] == cd.cellIndex)
		{
		  realIndex=j;
		  break;
		}
	  }
	  
	  // insert column based on real cell matrix
	  for (i=0; i<ct.rows.length; i++)
	  {
		if (tm[i][realIndex] != -1)
		{
		  if (ct.rows(i).cells(tm[i][realIndex]).colSpan > 1)
		  {
			ct.rows(i).cells(tm[i][realIndex]).colSpan++;
		  }
		  else
		  {
			var newc = ct.rows(i).insertCell(tm[i][realIndex]+1)
			var nc = ct.rows(i).cells(tm[i][realIndex]).cloneNode();
			newc.replaceNode(nc);
		  }
		}
	  }
	}
} // insertColumn

function row_delete(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current cell

	if (cd && cr && ct)
	{
	  // if there's only one row just remove the table
	  if (ct.rows.length<=1)
	  {
		ct.removeNode(true);
	  }
	  else
	  {
		// get "real" cell position and form cell matrix
		var tm = SPAW_formCellMatrix(ct);
		
		
		// decrease rowspan for cells that were spanning through current row
		for (i=0; i<cr.rowIndex; i++)
		{
		  var tempr = ct.rows(i);
		  for (j=0; j<tempr.cells.length; j++)
		  {
			if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
			  tempr.cells(j).rowSpan--;
		  }
		}
	
		
		curCI = -1;
		// check for current row cells spanning more than 1 row
		for (i=0; i<tm[cr.rowIndex].length; i++)
		{
		  prevCI = curCI;
		  curCI = tm[cr.rowIndex][i];
		  if (curCI != -1 && curCI != prevCI && cr.cells(curCI).rowSpan>1 && (cr.rowIndex+1)<ct.rows.length)
		  {
			ni = i;
			nrCI = tm[cr.rowIndex+1][ni];
			while (nrCI == -1) 
			{
			  ni++;
			  if (ni<ct.rows(cr.rowIndex+1).cells.length)
				nrCI = tm[cr.rowIndex+1][ni];
			  else
				nrCI = ct.rows(cr.rowIndex+1).cells.length;
			}
			
			var newc = ct.rows(cr.rowIndex+1).insertCell(nrCI);
			ct.rows(cr.rowIndex).cells(curCI).rowSpan--;
			var nc = ct.rows(cr.rowIndex).cells(curCI).cloneNode();
			newc.replaceNode(nc);
			// fix the matrix
			cs = (cr.cells(curCI).colSpan>1)?cr.cells(curCI).colSpan:1;
			for (j=i; j<(i+cs);j++)
			{
			  tm[cr.rowIndex+1][j] = nrCI;
			  nj = j;
			}
			for (j=nj; j<tm[cr.rowIndex+1].length; j++)
			{
			  if (tm[cr.rowIndex+1][j] != -1)
				tm[cr.rowIndex+1][j]++;
			}
		  }
		}
		// delete row
		ct.deleteRow(cr.rowIndex);
	  }
	}
}
  
function column_delete(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current cell

	if (cd && cr && ct)
	{
	  // get "real" cell position and form cell matrix
	  var tm = SPAW_formCellMatrix(ct);

	  // if there's only one column delete the table
	  if (tm[0].length<=1)  
	  {
		ct.removeNode(true);
	  }
	  else
	  {
		for (j=0; j<tm[cr.rowIndex].length; j++)
		{
		  if (tm[cr.rowIndex][j] == cd.cellIndex)
		  {
			realIndex=j;
			break;
		  }
		}
		
		for (i=0; i<ct.rows.length; i++)
		{
		  if (tm[i][realIndex] != -1)
		  {
			if (ct.rows(i).cells(tm[i][realIndex]).colSpan>1)
			  ct.rows(i).cells(tm[i][realIndex]).colSpan--;
			else
			  ct.rows(i).deleteCell(tm[i][realIndex]);
		  }
		}
	  }
	}
}

function merge_right(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current row

	if (cd && cr && ct)
	{
	  // get "real" cell position and form cell matrix
	  var tm = SPAW_formCellMatrix(ct);
	  
	  for (j=0; j<tm[cr.rowIndex].length; j++)
	  {
		if (tm[cr.rowIndex][j] == cd.cellIndex)
		{
		  realIndex=j;
		  break;
		}
	  }
	  
	  if (cd.cellIndex+1<cr.cells.length)
	  {
		ccrs = cd.rowSpan?cd.rowSpan:1;
		cccs = cd.colSpan?cd.colSpan:1;
		ncrs = cr.cells(cd.cellIndex+1).rowSpan?cr.cells(cd.cellIndex+1).rowSpan:1;
		nccs = cr.cells(cd.cellIndex+1).colSpan?cr.cells(cd.cellIndex+1).colSpan:1;
		// check if theres nothing between these 2 cells
		j=realIndex;
		while(tm[cr.rowIndex][j] == cd.cellIndex) j++;
		if (tm[cr.rowIndex][j] == cd.cellIndex+1)
		{
		  // proceed only if current and next cell rowspans are equal
		  if (ccrs == ncrs)
		  {
			// increase colspan of current cell and append content of the next cell to current
			cd.colSpan = cccs+nccs;
			cd.innerHTML += cr.cells(cd.cellIndex+1).innerHTML;
			cr.deleteCell(cd.cellIndex+1);
		  }
		}
	  }
	}
} // mergeRight

function merge_down(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current row

	if (cd && cr && ct)
	{
	  // get "real" cell position and form cell matrix
	  var tm = SPAW_formCellMatrix(ct);
	  
	  for (j=0; j<tm[cr.rowIndex].length; j++)
	  {
		if (tm[cr.rowIndex][j] == cd.cellIndex)
		{
		  crealIndex=j;
		  break;
		}
	  }
	  ccrs = cd.rowSpan?cd.rowSpan:1;
	  cccs = cd.colSpan?cd.colSpan:1;
	  
	  if (cr.rowIndex+ccrs<ct.rows.length)
	  {
		ncellIndex = tm[cr.rowIndex+ccrs][crealIndex];
		if (ncellIndex != -1 && (crealIndex==0 || (crealIndex>0 && (tm[cr.rowIndex+ccrs][crealIndex-1]!=tm[cr.rowIndex+ccrs][crealIndex]))))
		{
	
		  ncrs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan:1;
		  nccs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan:1;
		  // proceed only if current and next cell colspans are equal
		  if (cccs == nccs)
		  {
			// increase rowspan of current cell and append content of the next cell to current
			cd.innerHTML += ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).innerHTML;
			ct.rows(cr.rowIndex+ccrs).deleteCell(ncellIndex);
			cd.rowSpan = ccrs+ncrs;
		  }
		}
	  }
	}
} // mergeDown

// split cell horizontally
function split_horizontal(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current cell

	if (cd && cr && ct)
	{
	  // get "real" cell position and form cell matrix
	  var tm = SPAW_formCellMatrix(ct);
  
	  for (j=0; j<tm[cr.rowIndex].length; j++)
	  {
		if (tm[cr.rowIndex][j] == cd.cellIndex)
		{
		  realIndex=j;
		  break;
		}
	  }
	  
	  if (cd.rowSpan>1) 
	  {
		// split only current cell
		// find where to insert a cell in the next row
		i = realIndex;
		while (tm[cr.rowIndex+1][i] == -1) i++;
		if (i == tm[cr.rowIndex+1].length) 
		  ni = ct.rows(cr.rowIndex+1).cells.length;
		else
		  ni = tm[cr.rowIndex+1][i];
		  
		var newc = ct.rows(cr.rowIndex+1).insertCell(ni);
		cd.rowSpan--;
		var nc = cd.cloneNode();
		newc.replaceNode(nc);
  
		cd.rowSpan = 1;
	  }
	  else
	  {
		// add new row and make all other cells to span one row more
		ct.insertRow(cr.rowIndex+1);
		for (i=0; i<cr.cells.length; i++)
		{
		  if (i != cd.cellIndex)
		  {
			rs = cr.cells(i).rowSpan>1?cr.cells(i).rowSpan:1;
			cr.cells(i).rowSpan = rs+1;
		  }
		}
  
		for (i=0; i<cr.rowIndex; i++)
		{
		  var tempr = ct.rows(i);
		  for (j=0; j<tempr.cells.length; j++)
		  {
			if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
			  tempr.cells(j).rowSpan++;
		  }
		}
		
		// clone current cell to new row
		var newc = ct.rows(cr.rowIndex+1).insertCell(0);
		var nc = cd.cloneNode();
		newc.replaceNode(nc);
	  }
	}
} // splitH
  
function split_vertical(editor)
{
	var ct = get_table(editor); // current table
	var cr = get_tr(editor); // current row
	var cd = get_td(editor); // current cell

	if (cd && cr && ct)
	{
	  // get "real" cell position and form cell matrix
	  var tm = SPAW_formCellMatrix(ct);
  
	  for (j=0; j<tm[cr.rowIndex].length; j++)
	  {
		if (tm[cr.rowIndex][j] == cd.cellIndex)
		{
		  realIndex=j;
		  break;
		}
	  }
	  
	  if (cd.colSpan>1)	
	  {
		// split only current cell
		var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
		cd.colSpan--;
		var nc = cd.cloneNode();
		newc.replaceNode(nc);
		cd.colSpan = 1;
	  }
	  else
	  {
		// clone current cell
		var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
		var nc = cd.cloneNode();
		newc.replaceNode(nc);
		
		for (i=0; i<tm.length; i++)
		{
		  if (i!=cr.rowIndex && tm[i][realIndex] != -1)
		  {
			cs = ct.rows(i).cells(tm[i][realIndex]).colSpan>1?ct.rows(i).cells(tm[i][realIndex]).colSpan:1;
			ct.rows(i).cells(tm[i][realIndex]).colSpan = cs+1;
		  }
		}
	  }
	}
} // splitV

/*
function _hyperlink_insert(editor, hyperlink, target, doc)
{
	if(doc != null)
		ed = doc[editor]
	else
		ed = this;

	tControl = win.frames[editor].document.selection.createRange();
	if(hyperlink != null && tControl != null) {
		alert(tControl.TagName);
		//editor_insertHTML(objname, "<A href='link.htm' target='_blank'>"); 
		//ed.document.execCommand('insertimage', false, imgSrc);
		//var im = get_image(editor, doc);
		//im.border = 0;
	}
}
	//tControl = win.frames[editor].document.selection.createRange();
}
*/

function hyperlink_insert(editor)
{
	var w = 550;
	var h = 150;
	
  var l = (screen.width - w) / 2;
  var t = (screen.height - h) / 2;

	window.frames[editor].focus();
	//window.open(editor_root + 'link_properties/' + editor, 'link_properties', 'fullscreen=0,toolbar=0,status=1,scrollbars=1,menubar=0,location=0,resizable=0,channelmode=0,directories=0,width=' + w + ',height=' + h + ',top=' + t +',left=' + l);
	var l = this[editor].document.execCommand('createlink');
}


function item_add(editor)
{
	window.frames[editor].focus();

  var modSrc = showModalDialog(editor_root + 'getmodule/', '', 
      'dialogHeight:500px; dialogWidth:350px; status:no');
	if(modSrc != null)
		this[editor].document.execCommand('createLink', false, modSrc);
}

function format_font(editor)
{
	window.frames[editor].focus();
	this[editor].document.execCommand('FontName', false, 'Times New Roman');
}

function color_picker(curcolor)
{
	return showModalDialog(editor_root + 'getcolor/', curcolor,
		'dialogHeight:250px; dialogWidth:366px; status:no');
}

function color_fore(editor,curcolor)
{
	window.frames[editor].focus();     

	var fCol = color_picker(null);

	if(fCol != null)
		this[editor].document.execCommand('ForeColor', false, fCol);
}

function color_back(editor,curcolor)
{
	window.frames[editor].focus();     

	var fCol = color_picker(null);

	if(fCol != null)
		this[editor].document.execCommand('BackColor', false, fCol);
}


function cell_properties(editor, sender)
{
	var cd = get_td(editor); // current cell
	
	if (cd)
	{
	  var cProps = {};
	  cProps.width = (cd.style.width)?cd.style.width:cd.width;
	  cProps.height = (cd.style.height)?cd.style.height:cd.height;
	  cProps.bgColor = cd.bgColor;
	  cProps.align = cd.align;
	  cProps.vAlign = cd.vAlign;
	  cProps.className = cd.className;
	  cProps.noWrap = cd.noWrap;
	  cProps.styleOptions = new Array();
		/*
	  if (document.all['SPAW_'+editor+'_tb_style'] != null)
	  {
		cProps.styleOptions = document.all['SPAW_'+editor+'_tb_style'].options;
	  }
  */
	  var ncProps = showModalDialog(editor_root + 'cell_properties/', cProps, 
		'dialogHeight:220px; dialogWidth:366px; status:no');
	  
	  if (ncProps)  
	  {
		cd.align = (ncProps.align)?ncProps.align:'';
		cd.vAlign = (ncProps.vAlign)?ncProps.vAlign:'';
		cd.width = (ncProps.width)?ncProps.width:'';
		cd.style.width = (ncProps.width)?ncProps.width:'';
		cd.height = (ncProps.height)?ncProps.height:'';
		cd.style.height = (ncProps.height)?ncProps.height:'';
		cd.bgColor = (ncProps.bgColor)?ncProps.bgColor:'';
		cd.className = (ncProps.className)?ncProps.className:'';
		cd.noWrap = ncProps.noWrap;
	  }	  
	}
	//SPAW_updateField(editor,"");
}

function font_face(editor, sender)
{
	fontname = sender.options[sender.selectedIndex].value;
	window.frames[editor].focus();	 
	this[editor].document.execCommand('fontname', false, fontname);
	sender.selectedIndex = 0;
}

function font_size(editor, sender)
{
	fontsize = sender.options[sender.selectedIndex].value;
	window.frames[editor].focus();	 
	this[editor].document.execCommand('fontsize', false, fontsize);
	sender.selectedIndex = 0;
}

function data_filter(editor, form) {
	var filter = showModalDialog(editor_root + 'datafilter/', false, 
		'dialogHeight:150px; dialogWidth:150px; status:no');

	if(filter) {
		form.elements['action'].value='filter';
		form.elements['data[filter_data]'].value=filter;
		saveData(form, window.frames[editor]);
	} 
}

function split(editor, sender) {
	this[editor].document.execCommand('InsertHorizontalRule', false, 'editor_splitter');
}

/*
function selection_multiple(editor) {
	//alert(editor.document.execCommand('MultipleSelection', false, true));
	window.frames[editor].focus();
	this[editor].document.execCommand('MultipleSelection', false, true);
}

*/