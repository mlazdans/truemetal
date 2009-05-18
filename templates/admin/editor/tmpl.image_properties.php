<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>Attēla parametri</title>
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
  function Init() {
    var iProps = window.dialogArguments;
    if (iProps)
    {
      // set attribute values
      if (iProps.width) {
        img_prop.cwidth.value = iProps.width;
      }
      if (iProps.height) {
        img_prop.cheight.value = iProps.height;
      }
      
      setAlign(iProps.align);
      
      if (iProps.src) {
        img_prop.csrc.value = iProps.src;
      }
      if (iProps.alt) {
        img_prop.calt.value = iProps.alt;
      }
      if (iProps.border) {
        img_prop.cborder.value = iProps.border;
      }
      if (iProps.hspace) {
        img_prop.chspace.value = iProps.hspace;
      }
      if (iProps.vspace) {
        img_prop.cvspace.value = iProps.vspace;
      }
    }
  }
  
  function validateParams()
  {
    // check width and height
    if (isNaN(parseInt(img_prop.cwidth.value)) && img_prop.cwidth.value != '')
    {
      alert('Error: Width is not a number');
      img_prop.cwidth.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.cheight.value)) && img_prop.cheight.value != '')
    {
      alert('Error: Height is not a number');
      img_prop.cheight.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.cborder.value)) && img_prop.cborder.value != '')
    {
      alert('Error: Border is not a number');
      img_prop.cborder.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.chspace.value)) && img_prop.chspace.value != '')
    {
      alert('Error: Horizontal space is not a number');
      img_prop.chspace.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.cvspace.value)) && img_prop.cvspace.value != '')
    {
      alert('Error: Vertical space is not a number');
      img_prop.cvspace.focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var iProps = {};
      iProps.align = (img_prop.calign.value)?(img_prop.calign.value):'';
      iProps.width = (img_prop.cwidth.value)?(img_prop.cwidth.value):'';
      iProps.height = (img_prop.cheight.value)?(img_prop.cheight.value):'';
      iProps.border = (img_prop.cborder.value)?(img_prop.cborder.value):'';
      iProps.src = (img_prop.csrc.value)?(img_prop.csrc.value):'';
      iProps.alt = (img_prop.calt.value)?(img_prop.calt.value):'';
      iProps.hspace = (img_prop.chspace.value)?(img_prop.chspace.value):'';
      iProps.vspace = (img_prop.cvspace.value)?(img_prop.cvspace.value):'';

      window.returnValue = iProps;
      window.close();
    }
  }

  function cancelClick() {
    window.close();
  }
  
  
  function setAlign(alignment)
  {
    for (i=0; i<img_prop.calign.options.length; i++)  
    {
      al = img_prop.calign.options.item(i);
      if (al.value == alignment.toLowerCase()) {
        img_prop.calign.selectedIndex = al.index;
      }
    }
  }

  //-->
</script>
</head>

<body onLoad="Init()" dir="ltr">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<form name="img_prop">
<tr>
  <td>Ceļš:</td>
  <td colspan="3"><input type="text" name="csrc" class="input" size="48"></td>
</tr>
<tr>
  <td>Alternatīvais teksts:</td>
  <td colspan="3"><input type="text" name="calt" class="input" size="48"></td>
</tr>
<tr>
  <td>Iecentrēt:</td>
  <td align="left">
  <select name="calign" size="1" class="input">
    <option value=""></option>
    <option value="left">left</option>
    <option value="right">right</option>
    <option value="top">top</option>
    <option value="middle">middle</option>
    <option value="bottom">bottom</option>
    <option value="absmiddle">absmiddle</option>
    <option value="texttop">texttop</option>
    <option value="baseline">baseline</option>
  </select>
  </td>
  <td>Rāmis:</td>
  <td align="left"><input type="text" name="cborder" size="3" maxlenght="3" class="input_small"></td>
</tr>
<tr>
  <td>Platums:</td>
  <td nowrap>
    <input type="text" name="cwidth" size="3" maxlenght="3" class="input_small">
  </td>
  <td>Augstums:</td>
  <td nowrap>
    <input type="text" name="cheight" size="3" maxlenght="3" class="input_small">
  </td>
</tr>
<tr>
  <td>Horizontālā atstarpe:</td>
  <td nowrap>
    <input type="text" name="chspace" size="3" maxlenght="3" class="input_small">
  </td>
  <td>Vertikālā atstarpe:</td>
  <td nowrap>
    <input type="text" name="cvspace" size="3" maxlenght="3" class="input_small">
  </td>
</tr>
<tr>
<td colspan="4" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap>
<input type="button" value="   OK   " onClick="okClick()" class="bt">
<input type="button" value="Atcelt" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>

</body>
</html>
