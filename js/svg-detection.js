function setCSS() {
  var docBody = document.getElementsByTagName('body');
  docBody[0].className = 'svg';
}
function SVGDetect() {
  var testImg = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNzUiIGhlaWdodD0iMjc1Ij48L3N2Zz4%3D';
  var img = document.createElement('img');
  img.setAttribute('src', testImg);
  img.onload = setCSS;
}
window.onload = SVGDetect;