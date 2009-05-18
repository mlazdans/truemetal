<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>Izveidot tabulu</title>
<meta http-equiv="Pragma" content="no-cache">
<style type="text/css"><!--
body {
	font-family: "Verdana", arial, serif;
	color: black;
	background-color: ButtonFace;
}

td {
	font-size: 10pt;
	text-align: left;
	font-family: "MS Dialog", arial, serif;
}

input, select {
	font-size: 9pt;
	background-color: ButtonFace;
}

--></style>

<script language="javascript">
<!--  
  function showColorPicker(curcolor) {
    var newcol = showModalDialog('colorpicker.php?theme=default&lang=en', curcolor, 
      'dialogHeight:250px; dialogWidth:366px; status:no');  
    try {
      table_prop.tbgcolor.value = newcol;
      //table_prop.color_sample.style.backgroundColor = table_prop.tbgcolor.value;
    }
    catch (excp) {}
  }

  function Init() {
    var tProps = window.dialogArguments;
    if (tProps)
    {
      // set attribute values
      table_prop.trows.value = '3';
      table_prop.trows.disabled = true;
      table_prop.tcols.value = '3';
      table_prop.tcols.disabled = true;

      table_prop.tborder.value = tProps.border;
      table_prop.tcpad.value = tProps.cellPadding;
      table_prop.tcspc.value = tProps.cellSpacing;
      //table_prop.tbgcolor.value = tProps.bgColor;
      //table_prop.color_sample.style.backgroundColor = table_prop.tbgcolor.value;
      if (tProps.width) {
        if (!isNaN(tProps.width) || (tProps.width.substr(tProps.width.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(tProps.width))
            table_prop.twidth.value = tProps.width;
          else
            table_prop.twidth.value = tProps.width.substr(0,tProps.width.length-2);
          table_prop.twunits.options[0].selected = false;
          table_prop.twunits.options[1].selected = true;
        }
        else
        {
          // percents
          table_prop.twidth.value = tProps.width.substr(0,tProps.width.length-1);
          table_prop.twunits.options[0].selected = true;
          table_prop.twunits.options[1].selected = false;
        }
      }
      if (tProps.width) {
        if (!isNaN(tProps.height) || (tProps.height.substr(tProps.height.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(tProps.height))
            table_prop.theight.value = tProps.height;
          else
            table_prop.theight.value = tProps.height.substr(0,tProps.height.length-2);
          table_prop.thunits.options[0].selected = false;
          table_prop.thunits.options[1].selected = true;
        }
        else
        {
          // percents
          table_prop.theight.value = tProps.height.substr(0,tProps.height.length-1);
          table_prop.thunits.options[0].selected = true;
          table_prop.thunits.options[1].selected = false;
        }
      }
    }
    else
    {
      // set default values
      table_prop.trows.value = '3';
      table_prop.tcols.value = '3';
      table_prop.tborder.value = '1';
    }
  }
  
  function validateParams()
  {
    // check whether rows and cols are integers
    if (isNaN(parseInt(table_prop.trows.value)))
    {
      alert('Error: Rows is not a number');
      table_prop.trows.focus();
      return false;
    }
    if (isNaN(parseInt(table_prop.tcols.value)))
    {
      alert('Error: Columns is not a number');
      table_prop.tcols.focus();
      return false;
    }
    // check width and height
    if (isNaN(parseInt(table_prop.twidth.value)) && table_prop.twidth.value != '')
    {
      alert('Error: Width is not a number');
      table_prop.twidth.focus();
      return false;
    }
    if (isNaN(parseInt(table_prop.theight.value)) && table_prop.theight.value != '')
    {
      alert('Error: Height is not a number');
      table_prop.theight.focus();
      return false;
    }
    // check border, padding and spacing
    if (isNaN(parseInt(table_prop.tborder.value)) && table_prop.tborder.value != '')
    {
      alert('Error: Border is not a number');
      table_prop.tborder.focus();
      return false;
    }
    if (isNaN(parseInt(table_prop.tcpad.value)) && table_prop.tcpad.value != '')
    {
      alert('Error: Cell padding is not a number');
      table_prop.tcpad.focus();
      return false;
    }
    if (isNaN(parseInt(table_prop.tcspc.value)) && table_prop.tcspc.value != '')
    {
      alert('Error: Cell spacing is not a number');
      table_prop.tcspc.focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var newtable = {};
      newtable.width = (table_prop.twidth.value)?(table_prop.twidth.value + table_prop.twunits.value):'';
      newtable.height = (table_prop.theight.value)?(table_prop.theight.value + table_prop.thunits.value):'';
      newtable.border = table_prop.tborder.value;
      newtable.cols = table_prop.tcols.value;
      newtable.rows = table_prop.trows.value
      newtable.cellPadding = table_prop.tcpad.value;
      newtable.cellSpacing = table_prop.tcspc.value;
      //newtable.bgColor = table_prop.tbgcolor.value;

      window.returnValue = newtable;
      window.close();
    }
  }

  function cancelClick() {
    window.close();
  }
  
//-->
</script>
</head>

<body onLoad="Init()" dir="ltr">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<form name="table_prop">
<tr>
  <td>Rindas:</td>
  <td><input type="text" name="trows" size="3" maxlenght="3"></td>
  <td>Kolonnas:</td>
  <td><input type="text" name="tcols" size="3" maxlenght="3"></td>
</tr>
<tr>
  <td>Platums:</td>
  <td nowrap>
    <input type="text" name="twidth" size="3" maxlenght="3">
    <select size="1" name="twunits">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
  <td>Augstums:</td>
  <td nowrap>
    <input type="text" name="theight" size="3" maxlenght="3">
    <select size="1" name="thunits">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
</tr>
<tr>
  <td>Rāmis:</td>
  <td colspan="3"><input type="text" name="tborder" size="2" maxlenght="2"> pikseļi</td>
</tr>
<tr>
  <td>Cell padding:</td>
  <td><input type="text" name="tcpad" size="3" maxlenght="3"></td>
  <td>Cell spacing:</td>
  <td><input type="text" name="tcspc" size="3" maxlenght="3"></td>
</tr>
  
<tr>
<td colspan="4" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap>
<input type="button" value="   OK   " onClick="okClick()">
<input type="button" value="Cancel" onClick="cancelClick()">
</td>
</tr>
</form>
</table>

</body>
</html>
