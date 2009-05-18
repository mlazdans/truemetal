<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>GetModule</title>
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

.align_off {
	border : 2px solid #ffffff;
}

.align_on {
	border : 2px solid #ff0000;
}

--></style>
<script language="JavaScript" type="text/javascript" src="{http_root}/js/editor.js"></script>
<script language="javascript">
<!--  
  function showColorPicker(curcolor) {
    var newcol = color_picker(curcolor);

    try {
      td_prop.cbgcolor.value = newcol;
      td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
    }
    catch (excp) {}
  }

  function Init() {
    var cProps = window.dialogArguments;
    if (cProps)
    {
      // set attribute values
      td_prop.cbgcolor.value = cProps.bgColor;
      td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
      if (cProps.width) {
        if (!isNaN(cProps.width) || (cProps.width.substr(cProps.width.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(cProps.width))
            td_prop.cwidth.value = cProps.width;
          else
            td_prop.cwidth.value = cProps.width.substr(0,cProps.width.length-2);
          td_prop.cwunits.options[0].selected = false;
          td_prop.cwunits.options[1].selected = true;
        }
        else
        {
          // percents
          td_prop.cwidth.value = cProps.width.substr(0,cProps.width.length-1);
          td_prop.cwunits.options[0].selected = true;
          td_prop.cwunits.options[1].selected = false;
        }
      }
      if (cProps.width) {
        if (!isNaN(cProps.height) || (cProps.height.substr(cProps.height.length-2,2).toLowerCase() == "px"))
        {
          // pixels
          if (!isNaN(cProps.height))
            td_prop.cheight.value = cProps.height;
          else
            td_prop.cheight.value = cProps.height.substr(0,cProps.height.length-2);
          td_prop.chunits.options[0].selected = false;
          td_prop.chunits.options[1].selected = true;
        }
        else
        {
          // percents
          td_prop.cheight.value = cProps.height.substr(0,cProps.height.length-1);
          td_prop.chunits.options[0].selected = true;
          td_prop.chunits.options[1].selected = false;
        }
      }
      
      setHAlign(cProps.align);
      setVAlign(cProps.vAlign);
      
      if (cProps.noWrap)
        td_prop.cnowrap.checked = true;
      
    }
  }
  
  function validateParams()
  {
    // check width and height
    if (isNaN(parseInt(td_prop.cwidth.value)) && td_prop.cwidth.value != '')
    {
      alert('Kļūda: norādītais platums nav skaitlis!');
      td_prop.cwidth.focus();
      return false;
    }
    if (isNaN(parseInt(td_prop.cheight.value)) && td_prop.cheight.value != '')
    {
      alert('Kļūda: norādītais augstums nav skaitlis!');
      td_prop.cheight.focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var cprops = {};
      cprops.align = (td_prop.chalign.value)?(td_prop.chalign.value):'';
      cprops.vAlign = (td_prop.cvalign.value)?(td_prop.cvalign.value):'';
      cprops.width = (td_prop.cwidth.value)?(td_prop.cwidth.value + td_prop.cwunits.value):'';
      cprops.height = (td_prop.cheight.value)?(td_prop.cheight.value + td_prop.chunits.value):'';
      cprops.bgColor = td_prop.cbgcolor.value;
      //cprops.className = (td_prop.ccssclass.value != 'default')?td_prop.ccssclass.value:'';
      cprops.noWrap = (td_prop.cnowrap.checked)?true:false;

      window.returnValue = cprops;
      window.close();
    }
  }

  function cancelClick() {
    window.close();
  }
  
  function setSample()
  {
    try {
      td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
    }
    catch (excp) {}
  }
  
  function setHAlign(alignment)
  {
    switch (alignment) {
      case "left":
        td_prop.ha_left.className = "align_on";
        td_prop.ha_center.className = "align_off";
        td_prop.ha_right.className = "align_off";
        break;
      case "center":
        td_prop.ha_left.className = "align_off";
        td_prop.ha_center.className = "align_on";
        td_prop.ha_right.className = "align_off";
        break;
      case "right":
        td_prop.ha_left.className = "align_off";
        td_prop.ha_center.className = "align_off";
        td_prop.ha_right.className = "align_on";
        break;
    }
    td_prop.chalign.value = alignment;
  }

  function setVAlign(alignment)
  {
    switch (alignment) {
      case "middle":
        td_prop.ha_middle.className = "align_on";
        td_prop.ha_baseline.className = "align_off";
        td_prop.ha_bottom.className = "align_off";
        td_prop.ha_top.className = "align_off";
        break;
      case "baseline":
        td_prop.ha_middle.className = "align_off";
        td_prop.ha_baseline.className = "align_on";
        td_prop.ha_bottom.className = "align_off";
        td_prop.ha_top.className = "align_off";
        break;
      case "bottom":
        td_prop.ha_middle.className = "align_off";
        td_prop.ha_baseline.className = "align_off";
        td_prop.ha_bottom.className = "align_on";
        td_prop.ha_top.className = "align_off";
        break;
      case "top":
        td_prop.ha_middle.className = "align_off";
        td_prop.ha_baseline.className = "align_off";
        td_prop.ha_bottom.className = "align_off";
        td_prop.ha_top.className = "align_on";
        break;
    }
    td_prop.cvalign.value = alignment;
  }
  //-->
  </script>
</head>

<body onLoad="Init()">

<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="td_prop">
<tr>
  <td colspan="2">Horizontal align:</td>
  <td colspan="2" align="right"><input type="hidden" name="chalign">
  <img id="ha_left" src="{http_root}/editor/align_left.gif" class="align_off" onClick="setHAlign('left');" alt="Left">
  <img id="ha_center" src="{http_root}/editor/align_center.gif" class="align_off" onClick="setHAlign('center');" alt="Center">
  <img id="ha_right" src="{http_root}/editor/align_right.gif" class="align_off" onClick="setHAlign('right');" alt="Right">
  </td>
</tr>
<tr>
  <td colspan="2">Vertical align:</td>
  <td colspan="2" align="right"><input type="hidden" name="cvalign">
  <img id="ha_top" src="{http_root}/editor/valign_top.gif" class="align_off" onClick="setVAlign('top');" alt="Top">
  <img id="ha_middle" src="{http_root}/editor/valign_middle.gif" class="align_off" onClick="setVAlign('middle');" alt="Middle">
  <img id="ha_bottom" src="{http_root}/editor/valign_bottom.gif" class="align_off" onClick="setVAlign('bottom');" alt="Bottom">
  <img id="ha_baseline" src="{http_root}/editor/valign_baseline.gif" class="align_off" onClick="setVAlign('baseline');" alt="Baseline">
  </td>
</tr>
<tr>
  <td>Platums:</td>
  <td nowrap>
    <input type="text" name="cwidth" size="3" maxlenght="3" class="input_small">
    <select size="1" name="cwunits" class="input">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
  <td>Augstums:</td>
  <td nowrap>
    <input type="text" name="cheight" size="3" maxlenght="3" class="input_small">
    <select size="1" name="chunits" class="input">
      <option value="%">%</option>
      <option value="px">px</option>
    </select>
  </td>
</tr>
<tr>
  <td nowrap>Nowrap:</td>
  <td nowrap>
    <input type="checkbox" name="cnowrap">
  </td>
  <td colspan="2">&nbsp;</td>
</tr>
<tr>
  <td colspan="4">BGColor: <img src="{http_root}/img/1x1.gif" id="color_sample" border="1" width="30" height="18" align="absbottom">&nbsp;<input type="text" name="cbgcolor" size="7" maxlenght="7" class="input_color" onKeyUp="setSample()">&nbsp;
  <img src="{http_root}/editor/color_back.gif" border="0" onClick="showColorPicker(cbgcolor.value)">
  </td>
</tr>
<tr>
<td colspan="4" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap>
<input type="button" value="  OK  " onClick="okClick()" class="bt">
<input type="button" value="Atcelt" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>

</body>
</html>