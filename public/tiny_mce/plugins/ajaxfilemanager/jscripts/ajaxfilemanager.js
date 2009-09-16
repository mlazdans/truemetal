/*
	* author: Logan Cai
	* Email: cailongqun [at] yahoo [dot] com [dot] cn
	* Website: www.phpletter.com
	* Created At: 21/April/2007
	* Modified At: 1/June/2007
*/
// Returns true if the passed value is found in the
// array. Returns false if it is not.
Array.prototype.inArray = function (value,caseSensitive){
	var i;
	for (i=0; i < this.length; i++)
	{
		// use === to check for Matches. ie., identical (===),
		if(caseSensitive)
		{
			//performs match even the string is case sensitive
			if (this[i].toLowerCase() == value.toLowerCase())
			{
				return true;
			}
		} else {
			if (this[i] == value)
			{
				return true;
			}
		}
	}
	return false;
};

// Init
/*
var urls = urls || {};
var files = files || {};
var thickbox = thickbox || {};
var currentFolder = currentFolder || {};
var permits = permits || {};
var window = window || {};
var document = document || {};
var queryString = queryString || '';
var supporedPreviewExts = supporedPreviewExts || '';
var warningCloseWindow = warningCloseWindow || '';
var numFiles = numFiles || 0;
*/

var dcTime = 250;             // doubleclick time
var dcDelay = 100;            // no clicks after doubleclick
var dcAt = 0;                 // time of doubleclick
var savEvent = null;          // save Event for handling doClick().
var savEvtTime = 0;           // save time of click event.
var savTO = null;             // handle of click setTimeOut
var linkElem = null;
var fileUploadElemIds = [];   //keep track of the file element ids

function getFileExtension(filename)
{
	if( filename.length === 0 ) {
		return "";
	}

	var dot = filename.lastIndexOf(".");
	if( dot == -1 ) {
		return "";
	}

	var extension = filename.substr(dot + 1, filename.length);

	return extension;
} // getFileExtension

/**
*	append Query string to the base url
* @param string baseUrl the base url
* @param string the query string
* @param array remove thost url variable from base url if any matches
*/
function appendQueryString(baseUrl, queryStr, excludedQueryStr)
{
	if(typeof(excludedQueryStr) == 'object' && excludedQueryStr.length)
	{
		var isMatched = false;
		var urlParts = baseUrl.split("?");
		baseUrl = urlParts[0];
		var count = 1;
		if((typeof(urlParts[1]) !== "undefined") && (urlParts[1] !== ''))
		{
			//this is the query string parts
			var queryStrParts = urlParts[1].split("&");
			for(var i=0; i < queryStrParts.length; i++)
			{
				//split into query string variable name & value
				var queryStrVariables = queryStrParts[i].split('=');
				for(var j=0; j < excludedQueryStr.length; j++)
				{
					if(queryStrVariables[0] == excludedQueryStr[j])
					{
						isMatched = true;
					}
				}
				if(!isMatched)
				{
					baseUrl += ((count==1?'?':'&') + queryStrVariables[0] + '=' + queryStrVariables[1]);
					count++;
				}
			}
		}

	}

	if(queryStr !== '')
	{
		return (baseUrl.indexOf('?') > -1 ? baseUrl + '&' + queryStr : baseUrl + '?' + queryStr);
	} else {
		return baseUrl;
	}
} // appendQueryString


/**
*	return the url with query string
*/
function getUrl(index, limitNeeded, viewNeeded, searchNeeded)
{

	var queryStr = '';
	var excluded = [];

	if(typeof(limitNeeded) == 'boolean' && limitNeeded)
	{
		var limit = document.getElementById('limit');
		var typeLimit = typeof(limit);

		if(typeLimit !== undefined && limit )
		{
			excluded[excluded.length] = 'limit';
			queryStr += (queryStr === '' ? '' : '&') + 'limit=' + limit.options[limit.selectedIndex].value;
		}

	}

	if(typeof(viewNeeded) == 'boolean' && viewNeeded)
	{
		queryStr += (queryStr === '' ? '' : '&') + 'view=' +  getView();
		excluded[excluded.length] = 'view';

	}

	if(
		(typeof(searchNeeded) == 'boolean') &&
		searchNeeded &&
		searchRequired
		)
	{
		var search_recursively = 0;
		$('input[name=search_recursively]:checked').each(function(){
				search_recursively = this.value;
		});

		var searchFolder = document.getElementById('search_folder');
		queryStr += (queryStr === '' ? '' : '&') + 'search=1&search_name=' + $('#search_name').val() + '&search_recursively=' + search_recursively + '&search_mtime_from=' + $('#search_mtime_from').val() + '&search_mtime_to=' + $('#search_mtime_to').val() + '&search_folder=' +  searchFolder.options[searchFolder.selectedIndex].value;
		excluded[excluded.length] = 'search';
		excluded[excluded.length] = 'search_recursively';
		excluded[excluded.length] = 'search_mtime_from';
		excluded[excluded.length] = 'search_mtime_to';
		excluded[excluded.length] = 'search_folder';
		excluded[excluded.length] = 'search_name';
		excluded[excluded.length] = 'search';
	}

	return appendQueryString(appendQueryString(urls[index], queryString), queryStr, excluded);
} // getUrl


function hadDoubleClick()
{
	var d = new Date();
	var now = d.getTime();
	if ((now - dcAt) < dcDelay)
	{
		return true;
	}
	return false;
} // hadDoubleClick


/**
*	enable left click to preview certain files
*/
var Preview = {
	ondblclick: function(){
		var num = getNum(this.id);
		var d = new Date();
		dcAt = d.getTime();
		if(savTO !== null)
		{
			// Clear pending Click
			clearTimeout(savTO);
			savTO = null;
		}

		if(typeof(selectFile) === "function")
		{
			selectFile(files[num].url);
		} else {
			generateDownloadIframe(appendQueryString(getUrl('download'), 'path=' + files[num].path, ['path']));
		}

	},
	onclick: function(){
		var i;
		var num = getNum(this.id);
		var path = files[num].path;
		if (hadDoubleClick())
		{
			return false;
		} else {
			linkElem = $('#a' + num).get(0);
		}

		var d = new Date();
		savEvtTime = d.getTime();
		savTO = setTimeout(function(){
				if(savEvtTime - dcAt > 0)
				{
					//check if this file is previewable
					var ext = getFileExtension(path);
					var supportedExts = supporedPreviewExts.split(",");
					var isSupportedExt = false;
					//for(i in supportedExts)
					for(i = 0; i < supportedExts.length; i++)
					{
						var typeOf = typeof(supportedExts[i]);
						if(
							(typeOf.toLowerCase() == 'string') &&
							(supportedExts[i].toLowerCase() == ext.toLowerCase())
							)
						{
							isSupportedExt = true;
							break;
						}
					}

					if(isSupportedExt)
					{
						switch(files[num].cssClass)
						{
							case 'fileVideo':
							case 'fileMusic':
							case 'fileFlash':
								$('#playGround').html('<a id="playGround' + num + '" href="' + files[num].path + '"><div id="player">&nbsp;this is mine</div></a> ');
								$('#playGround' + num).html('');
								$('#playGround' + num).media({
										width: 255,
										height: 210,
										autoplay: true
								});
								showThickBox($('#a' + num).get(0), appendQueryString('#TB_inline', 'height=250'  + '&width=256' + '&inlineId=winPlay&modal=true'));
								break;
							default:
								showThickBox(linkElem, appendQueryString(path, 'KeepThis=true&TB_iframe=true&height=' + thickbox.height + '&width=' + thickbox.width));
						}
					}
				}
				return false;
		}, dcTime);
		return false;
	}
};

function enablePreview(elem, num)
{
	$(elem).each(function(){
			$(this).click(Preview.onclick);
			$(this).dblclick(Preview.ondblclick);

	});
} // enablePreview

/**
*	initiate when the listing page is loaded
* add main features according to the view
*/
function initAfterListingLoaded()
{
	var i;
	parsePagination();
	parseCurrentFolder();
	var view = getView();
	setDocInfo('root');

	if(view == 'thumbnail')
	{
		//enableContextMenu('dl.thumbnailListing, dl.thumbnailListing dt, dl.thumbnailListing dd, dl.thumbnailListing a');
		enableContextMenu('dl.thumbnailListing');
		//for(i in files)
		for(i = 0; i < files.length; i++)
		{
			//this is foder item
			if(files[i].type== 'folder')
			{
				enableFolderBrowsable(i);
			} else {
				//this is file item
				switch(files[i].cssClass)
				{
					case 'filePicture':
						//$('#a' + i).attr('rel', 'ajaxphotos');
						//retrieveThumbnail(i);
						break;
					case 'fileFlash':
						break;
					case 'fileVideo':
						break;
					case 'fileMusic':
						break;
					default:
				}
				enablePreview('#dt' + i, i);
				enablePreview('#thumbUrl' + i, i);
				enablePreview('#a' + i, i);

			}
			enableShowDocInfo( i);
		}
	} else {
		enableContextMenu('#fileList tr');
		//for(i in files)
		for(i = 0; i < files.length; i++)
		{
			if(files[i].type== 'folder')
			{
				//this is foder item
				enableFolderBrowsable(i);
			} else {
				//this is file item
				switch(files[i].cssClass)
				{
					case 'filePicture':
						$('#row' + i + ' td a').attr('rel', 'ajaxphotos');
						break;
					case 'fileFlash':
						break;
					case 'fileVideo':
						break;
					case 'fileMusic':
						break;
					default:

				}
				enablePreview('#row' + i + ' td a', i);
			}
			enableShowDocInfo(i);
		}
	}
} // initAfterListingLoaded

function doEnableFolderBrowsable(elem, num)
{
	$(elem).click(function(){
			var fpath;
			var typeNum = typeof(num);
			searchRequired = false;

			if(typeNum.toUpperCase() == 'STRING')
			{
				fpath = (num.indexOf(urls.view) >=0 ? num: files[num].path);
			} else {
				fpath = files[num].path;
			}

			var url = appendQueryString(getUrl('view', true, true), 'path=' + fpath, ['path']);

			$('#rightCol').empty();
			ajaxStart('#rightCol');
			$('#rightCol').load(url, {}, function(){
					urls.present = appendQueryString(getUrl('home', true, true), 'path=' + fpath, ['path']);
					ajaxStop('#rightCol img.ajaxLoadingImg');
					initAfterListingLoaded();
			});

			return false;

	});
} // doEnableFolderBrowsable

function enableFolderBrowsable(num, debug)
{
	var view = getView();
	if(view == 'thumbnail')
	{
		$('#dt'+ num + ' , #dd' + num + ' a').each(function(){
				doEnableFolderBrowsable(this, num);
		});
	} else {
		$('#row' + num + ' td[a]').each(function(){
				doEnableFolderBrowsable(this, num );
		});
	}
} // enableFolderBrowsable

/**
* add over class to the specific table
*/
function tableRuler(element)
{

	var rows = $(element);

	$(rows).each(function(){
		$(this).mouseover(function(){
			$(this).addClass('over');
		});
		$(this).mouseout(function(){
			$(this).removeClass('over');
		});
	});
}


function previewMedia(rowNum)
{
	$('#preview' +rowNum).html('');
	$('#preview' +rowNum).media({ width: 255, height: 210,  autoplay: true  });
	return false;
} // previewMedia

function closeWindow()
{
	if(window.confirm(warningCloseWindow))
	{
		window.close();
	}
	return false;
}

/**
*	change view
*/
function changeView()
{
	var url = getUrl('view', true, true);
	$('#rightCol').empty();
	ajaxStart('#rightCol');

	$('#rightCol').load(url, {}, function(){
			ajaxStop('#rightCol img.ajaxLoadingImg');
			urls.present = getUrl('home', true, true);
			initAfterListingLoaded();
	});
} // changeView

function goParentFolder()
{
	searchRequired = false;
	var url = appendQueryString(getUrl('view', true, true), 'path=' + parentFolder.path , ['path']);
	$('#rightCol').empty();
	ajaxStart('#rightCol');

	$('#rightCol').load(url, {}, function(){
			urls.present = appendQueryString(getUrl('home', true, true), 'path=' + parentFolder.path , ['path']);
			ajaxStop('#rightCol img.ajaxLoadingImg');
			initAfterListingLoaded();
	});
} // goParentFolder


/**
* @param mixed destinationSelector where the animation image will be append to
*	@param mixed selectorOfAnimation the jquery selector of the animation
*/
function ajaxStart(destinationSelector, id, selectorOfAnimation)
{
	//set defaullt animation
	if(typeof(selectorOfAnimation) === "undefined")
	{
		selectorOfAnimation = '#ajaxLoading img';
	}

	if(typeof(id) !== "undefined")
	{
		$(selectorOfAnimation).clone().attr('id', id).appendTo(destinationSelector);
	} else {
		$(selectorOfAnimation).clone(true).appendTo(destinationSelector);
	}
} // ajaxStart


/**
* remove the ajax animation
*	@param mixed selectorOfAnimation the jquery selector of the animation
*/
function ajaxStop(selectorOfAnimation)
{
	$(selectorOfAnimation).remove();
} // ajaxStop


/**
*	change pagination limit
*/
function changePaginationLimit(elem)
{
	var url = getUrl('view', true, true, true);
	$('#rightCol').empty();
	ajaxStart('#rightCol');
	$('#rightCol').load(url, {}, function(){
			urls.present = appendQueryString(getUrl('home', true, true), 'path=' + parentFolder.path , ['path']);
			ajaxStop('#rightCol img.ajaxLoadingImg');
			initAfterListingLoaded();
	});
} // changePaginationLimit


/**
*	get a query string variable value from an url
* @param string index
* @param string url
*/
function getUrlVarValue(url, index)
{
	if(
		(url !== '') &&
		(index !== '')
		)
	{
		var urlParts = url.split("?");
		baseUrl = urlParts[0];
		if(
			(typeof(urlParts[1]) !== "undefined") &&
			(urlParts[1] !== '')
			)
		{
			//this is the query string parts
			var queryStrParts = urlParts[1].split("&");
			for(var i=0; i < queryStrParts.length; i++)
			{
				//split into query string variable name & value
				var queryStrVariables = queryStrParts[i].split('=');
				if(queryStrVariables[0] == index)
				{
					return queryStrVariables[1];
				}
			}
		}
	}

	return '';
} // getUrlVarValue


/**
*	parse current folder
*/
function parseCurrentFolder()
{
	var folders = currentFolder.friendly_path.split('/');
	var str = '';
	var url = getUrl('view', true, true);
	var parentPath = '';

	for(var i = 0; i < folders.length; i++)
	{
		if(i === 0)
		{
			parentPath += paths.root;
			str += '/<a href="' + appendQueryString(url, 'path='+ parentPath, ['path']) + '"><span class="folderRoot">' + paths.root_title + '</span></a>';
		} else {
			if(folders[i] !== '')
			{
				parentPath += folders[i] + '/';
				str += '/<a href="' + appendQueryString(url, 'path='+ parentPath , ['path']) + '"><span class="folderSub">' + folders[i] + '</span></a>';
			}
		}
	}
	$('#currentFolderPath').empty().append(str);
	$('#currentFolderPath a').each(function(){
			doEnableFolderBrowsable(this, $(this).attr('href'));
	});
} // parseCurrentFolder


/**
*	enable pagination as ajax function call
*/
function parsePagination()
{
	$('p.pagination a[id!=pagination_parent_link]').each(function(){
			$(this).click(function(){
					var page = getUrlVarValue($(this).attr('href'), 'page');
					var url = appendQueryString(getUrl('view', true, true, searchRequired),'page=' + page, ['page']);
					$('#rightCol').empty();
					ajaxStart('#rightCol');
					$('#rightCol').load(url, {}, function(){
							urls.present = appendQueryString(getUrl('home', true, true, searchRequired),'page=' + page, ['page']);
							ajaxStop('#rightCol img.ajaxLoadingImg');
							initAfterListingLoaded();
					});
					return false;
			});
	});
} // parsePagination


/**
*	get current view
*/
function getView()
{
	var view = $('input[name=view]:checked').get(0);
	if(typeof(view) !== "undefined")
	{
		return view.value;
	} else {
		return '';
	}
} // getView


function getNum(elemId)
{
	if(
		(typeof(elemId) !== "undefined") &&
		(elemId !== '')
		)
	{
		var r = elemId.match(/[\d\.]+/g);
		if(
			(typeof(r) !== "undefined") &&
			r &&
			(typeof(r[0]) !== "undefined")
			)
		{
			return r[0];
		}
	}

	return 0;
}

function enableContextMenu(jquerySelectors)
{
	var contextMenu = {
		bindings: {
			menuSelect: function(t){
				var num = getNum($(t).attr('id'));
				selectFile(files[num].url);
			},
			menuPlay: function(t){
				var num = getNum($(t).attr('id'));
				$('#playGround').html('<a id="playGround' + num + '" href="' + files[num].path + '"><div id="player">&nbsp;this is mine</div></a> ');
				$('#playGround' + num).html('');
				$('#playGround' + num).media({
						width: 255,
						height: 210,
						autoplay: true
				});
				showThickBox($('#a' + num).get(0), appendQueryString('#TB_inline', 'height=250'  + '&width=258' + '&inlineId=winPlay&modal=true'));
			},
			menuPreview: function(t){
				var num = getNum($(t).attr('id'));
				$('#a' + num).click();
			},
			menuDownload: function(t){
				var num = getNum($(t).attr('id'));
				generateDownloadIframe(appendQueryString(getUrl('download', false, false), 'path=' + files[num].path, ['path']));
			},
			menuRename: function(t){
				var num = getNum($(t).attr('id'));
				showThickBox($('#a' + num).get(0), appendQueryString('#TB_inline', 'height=100' + '&width=350' + '&inlineId=winRename&modal=true'));
				$('div#TB_window #renameName').val(files[num].name);
				$('div#TB_window #original_path').val(files[num].path);
				$('div#TB_window #renameNum').val(num);
			},
			menuEdit: function(t){
				var num = (getNum($(t).attr('id')));
				var url = '';

				if(files[num].cssClass == 'filePicture')
				{
					url = getUrl('image_editor');
				} else {
					url = getUrl('text_editor');
				}

				var param = "status=yes,menubar=no,resizable=yes,scrollbars=yes,location=no,toolbar=no";
				param += ",height=" + screen.height + ",width=" + screen.width;
				if(typeof(window.screenX) !== "undefined")
				{
					param += ",screenX = 0,screenY=0";
				} else if(typeof(window.screenTop) !== "undefined" ) {
					param += ",left = 0,top=0" ;
				}

				var newWindow = window.open(url + ((url.lastIndexOf("?") > - 1) ? "&" : "?") + "path="  + files[num].path,'', param);
				newWindow.focus( );
			},
			menuCut: function(t){
			},
			menuCopy: function(t){
			},
			menuPaste: function(t){
			},
			menuDelete: function(t){
				var num = getNum($(t).attr('id'));
				if(!window.confirm(warningDelete))
				{
					return;
				}
				$.getJSON(appendQueryString(getUrl('delete', false,false), 'delete=' + files[num].path, ['delete']), function(data){
						if(typeof(data.error) === "undefined")
						{
							alert('Unexpected Error.');
						} else if(data.error !== '') {
							alert(data.error);
						} else {
							var view = getView();
							//remove deleted files
							if(view == 'thumbnail')
							{
								$('#dl' + num ).remove();
							} else {
								$('#row' + num).remove();
							}
							files[num] = null;
						}
				});
			}
		},
		onContextMenu:function(events){
			return true;
		},
		onShowMenu: function(events, menu){
			var num;
			var view = getView();
			if(view == 'thumbnail')
			{
				num = getNum(events.target.id);
			} else {
				switch(events.target.tagName.toLowerCase())
				{
					case 'span':
						if($(events.target).parent().get(0).tagName.toLowerCase()  == 'a')
						{
							num = getNum($(events.target).parent().parent().parent().attr('id'));
						} else {
							num = getNum($(events.target).parent().parent().parent().parent().attr('id'));
						}
						break;
					case 'td':
						num = getNum($(events.target).parent().attr('id'));
						break;
					case 'a':
					case 'input':
						num = getNum($(events.target).parent().parent().attr('id'));
						break;
				}
			}

			var menusToRemove = [];
			if(typeof(selectFile) === "undefined")
			{
				menusToRemove[menusToRemove.length] = '#menuSelect';
			}
			menusToRemove[menusToRemove.length] = '#menuCut';
			menusToRemove[menusToRemove.length] = '#menuCopy';
			menusToRemove[menusToRemove.length] = '#menuPaste';

			if(files[num] && files[num].type == 'folder')
			{
				if(numFiles < 1)
				{
					menusToRemove[menusToRemove.length] = '#menuPaste';
				}
				menusToRemove[menusToRemove.length] = '#menuPreview';
				menusToRemove[menusToRemove.length] = '#menuDownload';
				menusToRemove[menusToRemove.length] = '#menuEdit';
				menusToRemove[menusToRemove.length] = '#menuPlay';
				menusToRemove[menusToRemove.length] = '#menuDownload';
			} else {
				var isSupportedExt = false;
				if(permits.edit)
				{
					var ext = getFileExtension(files[num].path);
					var supportedExts = supporedPreviewExts.split(",");

					for(var i = 0; i < supportedExts.length; i++)
					{
						if(typeof(supportedExts[i]) !== "undefined" && typeof(supportedExts[i]).toLowerCase() == 'string' && supportedExts[i].toLowerCase() == ext.toLowerCase())
						{
							isSupportedExt = true;
							break;
						}
					}
				}

				if(!isSupportedExt || permits.view_only)
				{
					menusToRemove[menusToRemove.length] = '#menuEdit';
				}

				switch(files[num].cssClass)
				{
					case 'filePicture':
						menusToRemove[menusToRemove.length] = '#menuPlay';
						break;
					case 'fileCode':
						menusToRemove[menusToRemove.length] = '#menuPlay';
						break;
					case 'fileVideo':
					case 'fileFlash':
					case 'fileMusic':

						menusToRemove[menusToRemove.length] = '#menuPreview';																					menusToRemove[menusToRemove.length] = '#menuEdit';
						break;
					default:
						menusToRemove[menusToRemove.length] = '#menuPreview';
						menusToRemove[menusToRemove.length] = '#menuPlay';
				}
				menusToRemove[menusToRemove.length] = '#menuPaste';
			}

			if(!permits.edit|| permits.view_only)
			{
				menusToRemove[menusToRemove.length] = '#menuEdit';
			}

			if(!permits.del || permits.view_only)
			{
				menusToRemove[menusToRemove.length] = '#menuDelete';
			}

			if(!permits.cut || permits.view_only)
			{
				menusToRemove[menusToRemove.length] = '#menuCut';
			}

			if(!permits.copy || permits.view_only)
			{
				menusToRemove[menusToRemove.length] = '#menuCopy';
			}

			if((!permits.cut  && !permits.copy) || permits.view_only)
			{
				menusToRemove[menusToRemove.length] = '#menuPaste';
			}

			if(!permits.rename || permits.view_only)
			{
				menusToRemove[menusToRemove.length] = '#menuRename';
			}

			$(menu).children().children().children().each(function(){
					if(menusToRemove.inArray('#' + this.id))
					{
						$(this).parent().remove();
					}
			});
			return menu;
		}
	};
	$(jquerySelectors).contextMenu('contextMenu', contextMenu);
} // enableContextMenu


/**
*	add more file type of input file for multiple uploads
*/
function addMoreFile()
{
	var elementId;
	var newFileUpload = $($('div#TB_window #fileUploadBody  tr').get(0)).clone();

	do {
		elementId = 'upload' + generateUniqueId(10);
	} while(fileUploadElemIds.inArray(elementId));

	fileUploadElemIds[fileUploadElemIds.length] = elementId;

	$(newFileUpload).appendTo('div#TB_window #fileUploadBody');
	$('input[type=file]', newFileUpload).attr('id', elementId);
	$('span.uploadProcessing', newFileUpload).attr('id', 'ajax' + elementId);
	$('input[type=button]', newFileUpload).click(function(){
			uploadFile(elementId);
	});
	$('a', newFileUpload).show().click(function(){
			cancelFileUpload(elementId);
	});

	$(newFileUpload).show();

	return false;
} // addMoreFile


/**
*	cancel uploading file
*   remove hidden upload frame
*   remove hidden upload form
*/
function cancelFileUpload(elementId)
{
	$('div#TB_window #' + elementId).parent().parent().remove();

	//ensure there is at least one visible upload element
	while($('div#TB_window #fileUploadBody tr').length < 2)
	{
		addMoreFile();
	}

	return false;
} // cancelFileUpload


/**
*	upload file
*/
function uploadFile(elementId)
{
	var i;
	var ext = getFileExtension($('#' + elementId).val());
	if(ext === '')
	{
		alert(noFileSelected );
		return false;
	}
	var supportedExts = supportedUploadExts.split(",");
	var isSupportedExt = false;

	//for (i in supportedExts)
	for(i = 0; i < supportedExts.length; i++)
	{
		if(typeof(supportedExts[i]) == 'string')
		{
			isSupportedExt = true;
			break;
		}
	}

	if(!isSupportedExt)
	{
		alert(msgInvalidExt);
		return false;
	}

	$('#ajax' + elementId).hide();
	$('#ajax' + elementId).show();
	$.ajaxFileUpload({
			url: appendQueryString(getUrl('upload', false, false), 'folder=' + currentFolder.path, ['folder']),
			secureuri: false,
			fileElementId: elementId,
			dataType: 'json',
			success: function (data, status){
				if(typeof(data.error) !== "undefined")
				{
					if(data.error !== '')
					{
						alert(data.error);
						$('#ajax' + elementId).hide();
					} else {
						//remove the file type of input
						cancelFileUpload(elementId);
						numRows++;
						files[numRows] = {};

						for(var i in data)
						{
							if(i !== 'error')
							{
								files[numRows][i] =  data[i];
							}
						}
						addDocumentHtml(numRows);
					}
				}
			},
			error: function (data, status, e){
				$('#ajax' + elementId).hide();
				alert(e);
			}
	});

	return false;
} // uploadFile


/**
*	 generate unique id
*/
function generateUniqueId(leng)
{
	var idLength = leng || 32;
	var chars = "0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";
	var id = '';
	for(var i = 0; i <= idLength; i++)
	{
		id += chars.substr( Math.floor(Math.random() * 62), 1 );
	}

	return (id );
} // generateUniqueId


/**
*	generate a hidden iframe and force to download the specified file
*/
function generateDownloadIframe(url)
{
	var io;
	var frameId = 'ajaxDownloadIframe';

	$('#' + frameId).remove();

	if(window.ActiveXObject) {
		io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
	} else {
		io = document.createElement('iframe');
		io.id = frameId;
		io.name = frameId;
	}

	io.style.position = 'absolute';
	io.style.top = '-1000px';
	io.style.left = '-1000px';
	io.src = url;
	document.body.appendChild(io);
} // generateDownloadIframe


/**
*	show the url content in thickbox
*/
function showThickBox(linkElem, url)
{
	$(linkElem).attr('href', url);
	var t = linkElem.title || linkElem.name || null;
	var a = linkElem.href || linkElem.alt;
	var g = linkElem.rel || false;
	tb_show(t,a,g);
	linkElem.blur();
	return false;
} // showThickBox


/**
*	bring up a file upload window
*/
function uploadFileWin(linkElem)
{
	showThickBox(linkElem, appendQueryString('#TB_inline', 'height=200' + '&width=500' + '&inlineId=winUpload&modal=true'));
	while($('div#TB_window #fileUploadBody tr').length < 2)
	{
		addMoreFile();
	}
} // uploadFileWin


/**
*	bring up a new folder window
*/
function newFolderWin(linkElem)
{
	showThickBox(linkElem, appendQueryString('#TB_inline', 'height=100'  + '&width=250' + '&inlineId=winNewFolder&modal=true'));
	return false;
} // newFolderWin


/**
*	ajax call to create a folder
*/
function doCreateFolder()
{
	$('div#TB_window  #currentNewfolderPath').val(currentFolder.path);
	var pattern=/^[A-Za-z0-9_ \-]+$/i;

	var folder = $('div#TB_window #new_folder');
	if(!pattern.test($(folder).val()))
	{
		alert(msgInvalidFolderName);
		return false;
	}

	var options = {
		dataType: 'json',
		url: getUrl('create_folder'),
		error: function (data, status, e){
			alert(e);
		},
		success: function(data) {
			//remove those selected items
			if(data.error !== '')
			{
				alert(data.error);
			} else {
				numRows++;
				files[numRows] = {};
				for(var i in data)
				{
					if(i != 'error')
					{
						files[numRows][i] =  data[i];
					}
				}
				addDocumentHtml(numRows);
				tb_remove();
			}
		}
	};

	$('div#TB_window  #formNewFolder').ajaxSubmit(options);

	return false;
} // doCreateFolder


/**
* selecte documents and fire an ajax call to delete them
*/
function deleteDocuments(msgNoDocSelected, msgUnableToDelete, msgWarning, elements)
{
	if(!window.confirm(warningDel))
	{
		return false;
	}

	var checkedDocs;
	var view = getView();

	if(view == 'thumbnail')
	{
		checkedDocs = $('#rightCol dl.thumbnailListing input[type=checkbox]:checked');
	} else {
		checkedDocs = $('#fileList input[type=checkbox]:checked');
	}

	//var selectedDoc = document.getElementById('selectedDoc');
	var isSelected = false;

	//remove all options
	$('#selectedDoc').removeOption(/./);
	$(checkedDocs).each(function(i){
			$('#selectedDoc').addOption($(this).val(), getNum($(this).attr('id')), true);
			isSelected = true;
	});

	if(!isSelected)
	{
		alert(msgNoDocSelected);
	} else {
		//remove them via ajax call
		var options = {
			dataType: 'json',
			url: getUrl('delete'),
			error: function(data, status, e){
				alert(e);
			},
			success: function(data) {
				if(typeof(data.error) === "undefined")
				{
					alert('Unexpected error.');
				} else if(data.error !== '') {
					alert(data.error);
				} else {
					var view = getView();
					//remove all files
					/*
					TODO: fix
					for(var i = 0; i < hiddenSelectedDoc.options.length; i++)
					{
						if(view == 'thumbnail')
						{
							$('#dl' + hiddenSelectedDoc.options[i].text).remove();
						} else {
							$('#row' + hiddenSelectedDoc.options[i].text).remove();
						}
					}
					*/
				}
			}
		};
		$('#formAction').ajaxSubmit(options);
	}

	return false;
} // deleteDocuments


/**
*	renmae the specific file/folder
*/
function doRename()
{

	var pattern;
	var num = $('div#TB_window #renameNum').val();

	if(files[num].fileType == 'folder')
	{
		pattern=/^[A-Za-z0-9_ \-]+$/i;
	} else {
		pattern=/^[A-Za-z0-9_ \-\.]+$/i;
	}

	if(!pattern.test($('div#TB_window  #renameName').val()))
	{
		if(files[num].fileType == 'folder')
		{
			alert(msgInvalidFolderName);
		} else {
			alert(msgInvalidFileName);
		}
	} else {
		var options = {
			dataType: 'json',
			url: getUrl('rename'),
			error: function (data, status, e){
				alert(e);
			},
			success: function(data){
				//remove those selected items
				if(data.error !== '')
				{
					alert(data.error);
				} else {
					for(var i in data)
					{
						if(i != 'error')
						{
							files[num][i] = data[i];
						}

					}

					var view = getView();
					if(view == 'thumbnail')
					{
						$('#thumbUrl' + num).attr('href', files[num].path);
						$('#thumbImg' + num).attr('src', appendQueryString(getUrl('thumbnail'), 'path=' + files[num].path, ['path']));
						$('#cb' + num).val(files[num].path);
						$('#a' + num).attr('href', files[num].path).text(files[num].name);
					} else {
						$('#check' + num).val(files[num].path);
						$('#a' + num).attr('href', files[num].path);
						$('#tdnd' + num).text(files[num].name);
						$('#tdth' + num).text(files[num].name);
					}
					tb_remove();
				}
			}
		};
		$('div#TB_window #formRename').ajaxSubmit(options);
	}
} // doRename


/**
* reload the whole window
*/
function windowRefresh()
{
	document.location.href = urls.present;
	//document.location.reload();
}


/**
*	show the system information
*/
function infoWin(linkElem)
{
	showThickBox(linkElem, appendQueryString('#TB_inline', 'height=180' + '&width=500'+ '&inlineId=winInfo&modal=true'));
} // infoWin


/**
*check all checkboxs and uncheck all checkbox
*/
function checkAll(checkbox)
{
	var view = getView();
	if($(checkbox).attr('class') == "check_all")
	{
		$('#tickAll, #actionSelectAll').attr('class', 'uncheck_all');
		$('#tickAll, #actionSelectAll').attr('title', unselectAllText);
		$('#actionSelectAll span').html(unselectAllText);
		if(view == 'thumbnail')
		{
			$('#rightCol dl.thumbnailListing input[type=checkbox]').each(function(i){
					$(this).attr("checked", 'checked');
			});
		} else {
			$("#fileList tr[id^=row] input[type=checkbox]").each(function(i){
					$(this).attr("checked", 'checked');
			});
		}
	} else {
		$('#tickAll, #actionSelectAll').attr('class', 'check_all');
		$('#tickAll, #actionSelectAll').attr('title', selectAllText);
		$('#actionSelectAll span').html( selectAllText);
		if(view == 'thumbnail')
		{
			$('#rightCol dl.thumbnailListing input[type=checkbox]').each(function(i){
					$(this).removeAttr("checked");
			});
		} else {
			$("#fileList tr[id^=row] input[type=checkbox]").each(function(i){
					$(this).removeAttr("checked");
			});
		}
	}

	return false;
} // checkAll


function cutDocuments(msgNoDocSelected)
{
	repositionDocuments(msgNoDocSelected, getUrl('cut'), 'cut');
	return false;
} // cutDocuments

function copyDocuments(msgNoDocSelected)
{
	repositionDocuments(msgNoDocSelected, getUrl('copy'), 'copy');
	return false;
} // copyDocuments


/**
* selecte documents and fire an ajax call to delete them
*/
function repositionDocuments(msgNoDocSelected, formActionUrl, actionVal)
{
	var selectedDoc;
	var view = getView();
	if(view == 'thumbnail')
	{
		selectedDoc = $('#rightCol dl.thumbnailListing input[type=checkbox]:checked');
	} else {
		selectedDoc = $('#fileList input[type=checkbox]:checked');
	}
	//var hiddenSelectedDoc = document.getElementById('selectedDoc');
	var isSelected = false;

	//remove all options
	$('#selectedDoc').removeOption(/./);
	$(selectedDoc).each(function(i){
			$('#selectedDoc').addOption($(this).val(), getNum($(this).attr('id')), true);
			isSelected = true;
	});

	if(!isSelected)
	{
		alert(msgNoDocSelected);
	} else {
		//var formAction =  document.formAction;
		$('#formAction').attr('action', formActionUrl);
		$('#currentFolderPathVal').val(currentFolder.path);
		$('#action_value').val(actionVal);

		var options = {
			dataType: 'json',
			error: function (data, status, e){
				alert(e);
			},
			success: function(data){
				if(typeof(data.error) === "undefined")
				{
					alert('Unexpected Error');
				} else if(data.error !== '') {
					alert(data.error);
				} else {
					//set change flags
					numFiles = parseInt(data.num ,10);
					var i;
					var flag = (actionVal == 'copy' ? 'copyFlag' : 'cutFlag');
					action = actionVal;

					//clear all flag
					for(i = 1; i < numRows; i++)
					{
						$('#flag' + i).attr('class', 'noFlag');
					}
					/*
					TODO: fix
					for(i =0; i < hiddenSelectedDoc.options.length; i++)
					{
						$('#flag' + hiddenSelectedDoc.options[i].text).attr('class', flag);
					}
					*/
				}
			}
		};
		$('#formAction').ajaxSubmit(options);
	}

	return false;
} // repositionDocuments


function pasteDocuments(msgNoDocSelected)
{
	if(!numFiles)
	{
		alert(msgNoDocSelected);
		return false;
	}

	var warningMsg = (action == 'copy'?warningCopyPaste:warningCutPaste);
	if(!window.confirm(warningMsg))
	{
		return false;
	}

	$.getJSON(appendQueryString(getUrl('paste'), 'current_folder_path='+ currentFolder.path, ['current_folder_path']), function(json){
			if(typeof(json.error) === "undefined")
			{
				alert('Unexpected Error.');
				return false;
			}

			if(json.error !== '')
			{
				alert(json.error);
				return false;
			}

			var j, i;
			//for(var j in json.files)
			for(j = 0; j < json.files.length; j++)
			{
				numRows++;
				files[numRows] = {};
				//for(var i in json.files[j])
				for(i = 0; i < json.files[j].length; i++)
				{
					files[numRows][i] = json.files[j][i];
				}
				addDocumentHtml(numRows);
			}
			numFiles = parseInt(json.unmoved_files, 10);
	});

	return false;
} // pasteDocuments


/**
*	add document item html to the file listing body
*/
function addDocumentHtml(num)
{
	var view = getView();
	var strDisabled = "";
	if(!files[num].is_writable)
	{
		strDisabled = "disabled";
	}

	if(view == 'thumbnail')
	{
		var html = '';
		html += '<dl class="thumbnailListing" id="dl' + num + '">';
		html += ' <dt id="dt' + num + '" class="' + files[num].cssClass + '"></dt>';
		html += ' <dd id="dd' + num + '" class="thumbnailListing_info">';
		html += '  <span id="flag' + num + '" class="' + files[num].flag + '">&nbsp;</span>';
		html += '   <input id="cb' + num + '" type="checkbox"  class="radio" ' + strDisabled +' name="check[]" class="input" value="' + files[num].path + '" />';
		html += '   <a href="' + files[num].path + '" title="' + files[num].name + '" id="a' + num + '">' + (typeof(files[num].short_name) !== "undefined" ? files[num].short_name : files[num].name) + '</a>';
		html += ' </dd>';
		html += '</dl>';
		$(html).appendTo('#content');

		if(files[num].type == 'folder')
		{
			//this is foder item
			enableFolderBrowsable(num);
		} else {
			//this is file item
			switch(files[num].cssClass)
			{
				case 'filePicture':
					$('<a id="thumbUrl' + num + '" rel="thumbPhotos" href="' + files[num].path + '"><img src="' + appendQueryString(getUrl('thumbnail', false, false), 'path=' + files[num].path, ['path']) + '" id="thumbImg' +  num + '"></a>').appendTo('#dt' + num);
					break;
				case 'fileFlash':
					break;
				case 'fileVideo':
					break;
				case 'fileMusic':
					break;
			}
			enablePreview('#dl' + num + ' a', [num]);

		}
		enableContextMenu('#dl' + num);
		enableShowDocInfo( num);
	} else {
		var cssRow = (num % 2 ? "even" : "odd");
		$('<tr class="' + cssRow + '" id="row' + num + '"><td id="tdz' + num +'" align="center"><span id="flag' + num +'" class="' + files[num].flag +'">&nbsp;</span><input type="checkbox" class="radio" name="check[]" id="cb' + num +'" value="' + files[num].path +'" ' + strDisabled + ' /></td><td align="center" class="fileColumns"   id="tdst1">&nbsp;<a id="a' + num +'" href="' + files[num].path +'"><span class="' + files[num].cssClass + '">&nbsp;</span></a></td><td class="left docName" id="tdnd' + num +'">'  + (typeof(files[num].short_name) !== "undefined" ? files[num].short_name : files[num].name) + '</td><td class="docInfo" id="tdrd' + num +'">' + files[num].size +'</td><td class="docInfo" id="tdth' + num +'">' + files[num].mtime +'</td></tr>').appendTo('#fileList');

		if(files[num].type== 'folder')
		{
			//this is foder item
			enableFolderBrowsable(num);
		} else {
			//this is file item
			switch(files[num].cssClass)
			{
				case 'filePicture':
					break;
				case 'fileFlash':
					break;
				case 'fileVideo':
					break;
				case 'fileMusic':
					break;
			}
			enablePreview('#row' + num + ' td a', num);
		}
		enableContextMenu('#row' + num);
		enableShowDocInfo(num);
	}
} // addDocumentHtml


function enableShowDocInfo(num)
{
	$('#cb' + num).click(function(){
			setDocInfo('doc', num);
	});
} // enableShowDocInfo


/**
*	show up the selected document information
* @param   type root or doc
*/
function setDocInfo(type, num)
{
	var info = {};
	if(type == 'root')
	{
		info = currentFolder;
	} else {
		info = files[num];
	}

	if(info.type=="folder")
	{
		$('#folderPath').text(info.name);
		$('#folderFile').text(info.file);
		$('#folderSubdir').text(info.subdir);
		$('#folderCtime').text(info.ctime);
		$('#folderMtime').text(info.mtime);
		if(info.is_readable == '1')
		{
			$('#folderReadable').html("<span class=\"flagYes\">&nbsp;</span>");
		} else {
			$('#folderReadable').html("<span class=\"flagNo\">&nbsp;</span>");
		}

		if(info.is_writable == '1')
		{
			$('#folderWritable').html("<span class=\"flagYes\">&nbsp;</span>");
		} else {
			$('#folderWritable').html("<span class=\"flagNo\">&nbsp;</span>");
		}

		$('#folderFieldSet').css('display', '');
		$('#fileFieldSet').css('display', 'none');
	} else {
		$('#fileName').text(info.name);
		$('#fileSize').text(info.size);
		$('#fileType').text(info.fileType);
		$('#fileCtime').text(info.ctime);
		$('#fileMtime').text(info.mtime);

		if(info.is_readable == '1')
		{
			$('#fileReadable').html("<span class=\"flagYes\">&nbsp;</span>");
		} else {
			$('#fileReadable').html("<span class=\"flagNo\">&nbsp;</span>");
		}

		if(info.is_writable == '1')
		{
			$('#fileWritable').html("<span class=\"flagYes\">&nbsp;</span>");
		} else {
			$('#fileWritable').html("<span class=\"flagNo\">&nbsp;</span>");
		}

		$('#folderFieldSet').css('display', 'none');
		$('#fileFieldSet').css('display', '');
		if(typeof(selectFile) !== "undefined")
		{
			$('#selectCurrentUrl').unbind('click').click(function(){
					selectFile(info.url);
			});
			$('#returnCurrentUrl').show();
		} else {
			$('#returnCurrentUrl').hide();
		}
	}
} // setDocInfo

function search()
{
	searchRequired = true;
	var url = getUrl('view', true, true, true);

	$('#rightCol').empty();
	ajaxStart('#rightCol');

	$('#rightCol').load(url, {}, function(){
			ajaxStop('#rightCol img.ajaxLoadingImg');
			initAfterListingLoaded();
	});

	return false;
} // search

function closeWinPlay()
{
	tb_remove();
	$('#playGround').empty();
} // closeWinPlay

function closeWindow(msg)
{
	if(window.confirm(msg))
	{
		window.close();
	} else {
		return false;
	}
} // closeWindow

