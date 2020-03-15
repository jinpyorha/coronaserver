function coronaHeader(lang) {
  var headerStr = '';
  //###############헤더 시작
  headerStr += '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
  headerStr += '<a class="navbar-brand" href="#">CoronaNotBeer</a>';
  headerStr += '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
  headerStr += '<span class="navbar-toggler-icon"></span>';
  headerStr += '</button>';

  headerStr += '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
  headerStr += '<ul class="navbar-nav mr-auto">';
  if (country == 'US') {
    headerStr += '<li class="nav-item">';
  } else if (country == 'global') {
    headerStr += '<li class="nav-item active">';
  }
  headerStr += '<a class="nav-link" href="report?lang='+lang+'&country=global">';
  headerStr += lang=='en'?'World':'세계현황';
  headerStr+='<span class="sr-only">(current)</span></a>';
  headerStr += '</li>';
  if (country == 'US') {
    headerStr += '<li class="nav-item active">';
  } else if (country == 'global') {
    headerStr += '<li class="nav-item">';
  }
  headerStr += '<a class="nav-link" href="report?lang='+lang+'&country=US">';
  headerStr += lang=='en'?'US':'미국현황';
  headerStr += '</a>';
  headerStr += '</li>';
  /*headerStr += '<li class="nav-item dropdown">';
  headerStr += '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
  headerStr += lang=='en'?'MORE':'더보기';
  headerStr += '</a>';
  headerStr += '<div class="dropdown-menu" aria-labelledby="navbarDropdown">';
  headerStr += '<a class="dropdown-item" href="Components/coronainfo.html">';
  headerStr += lang=='en'?'What is corona':'코로나란?';
  headerStr += '</a>';
  headerStr += '<a class="dropdown-item" href="#">';
  headerStr += lang=='en'?'How to prevent':'예방수칙';
  headerStr += '</a>';
  headerStr += '<a class="dropdown-item" href="#">';
  headerStr += lang=='en'?'Travel restrictoin':'여행 제한구역';
  headerStr += '</a>';
  headerStr += '<div class="dropdown-divider"></div>';
  headerStr += '<a class="dropdown-item" href="#">';
  headerStr += lang=='en'?'Srouces':'소스';
  headerStr += '</a>';
  headerStr += '<a class="dropdown-item" href="#">';
  headerStr += lang=='en'?'Summary':'요약';
  headerStr += '</a>';
  headerStr += '<a class="dropdown-item" href="#">';
  headerStr += lang=='en'?'FAQ':'문의';
  headerStr += '</a>';
  headerStr += '</div>';
  headerStr += '</li>';*/
  headerStr += '<li class="nav-item">';
  headerStr += '<a class="nav-link" href="coronainfo.html?lang='+lang+'&country='+country+'">';
  headerStr += lang=='en'?'information':'대상별 맞춤 정보';
  headerStr+='<span class="sr-only">(current)</span></a>';
  headerStr += '</li>';
  headerStr += '</ul>';
  //
  headerStr += '<div class="form-inline my-2 my-lg-0">';
    headerStr += '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">';
    headerStr += lang=='en'?'Language':'언어';
    headerStr +='</a>';
    headerStr += '<div class="dropdown-menu">';
      headerStr += '<a class="dropdown-item';
      headerStr += lang=='en'?' active':'';
      headerStr += '" href="report?lang=en">English</a>';
      headerStr += '<a class="dropdown-item';
      headerStr += lang=='en'?'':' active';
      headerStr +='" href="report?lang=ko">한국어</a>';
    headerStr += '</div>';
  headerStr += '</div>';
  //
  headerStr += '</div>';
  headerStr += '</nav>';
  //###############헤더 끝
  $('#header').html(headerStr);
}
