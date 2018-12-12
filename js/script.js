/**
 * custom script
 */
var editorHTML = CodeMirror.fromTextArea(document.getElementById('contentblockHTML'), {
  parserfile: 'parsexml.js',
  lineNumbers: true,
  matchBrackets: true,
});
var editorCSS = CodeMirror.fromTextArea(document.getElementById('contentblockCSS'), {
  lineNumbers: true,
  extraKeys: { 'Ctrl-Space': 'autocomplete' },
  matchBrackets: true,
});
var editorCSSRes = CodeMirror.fromTextArea(document.getElementById('contentblockCSSResponsive'), {
  lineNumbers: true,
  extraKeys: { 'Ctrl-Space': 'autocomplete' },
  matchBrackets: true,
});

function refreshPreview() {
  var HTMLDATA = editorHTML.getValue();
  var CSSDATA = editorCSS.getValue();
  var CSSResDATA = editorCSSRes.getValue();
  jQuery('#css-part').empty().append(CSSDATA).append(CSSResDATA);
  jQuery('#preview-html-css').empty().append(HTMLDATA);
}

refreshPreview();
jQuery(document).on('click', '.bluebutton', function () {
  refreshPreview();
});
