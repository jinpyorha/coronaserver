function coronaHeader(lang,page) {
  var headerStr = '';
  //###############헤더 시작
  // headerStr += '<link rel="icon" href="img/locosmall.png">'
  headerStr += '<nav class="navbar navbar-expand-lg navbar-light">';
  // headerStr += '<a class="navbar-brand" href="#">CoronaNotBeer</a>';
  headerStr += '<a class="navbar-brand" href="https://coronanotbeer.com"><img src="/coronaserver/img/logobig.png" alt="big logo"></a>';
  headerStr += '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
  headerStr += '<span class="navbar-toggler-icon"></span>';
  headerStr += '</button>';
  headerStr += '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
  headerStr += '<ul class="navbar-nav mr-auto">';
  if (country == 'global'&&page=='report') {
    headerStr += '<li class="nav-item active">';
  } else{
    headerStr += '<li class="nav-item">';
  }
  headerStr += '<a class="nav-link" href="https://coronanotbeer.com?lang='+lang+'&country=global">';
  headerStr += lang=='en'?'World':'세계현황';
  headerStr+='<span class="sr-only">(current)</span></a>';
  headerStr += '</li>';
  if (country == 'USA'&&page=='report') {
    headerStr += '<li class="nav-item active">';
  } else {
    headerStr += '<li class="nav-item">';
  }
  headerStr += '<a class="nav-link" href="https://coronanotbeer.com?lang='+lang+'&country=US">';
  headerStr += lang=='en'?'USA':'미국현황';
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
  if(page=='info'){
    headerStr += '<li class="nav-item active">';
  }else{
    headerStr += '<li class="nav-item">';
  }
  headerStr += '<a class="nav-link" href="https://coronanotbeer.com/coronainfo?lang='+lang+'&country='+country+'">';
  headerStr += lang=='en'?'COVID-19':'COVID-19 이란?';
  headerStr+='<span class="sr-only">(current)</span></a>';
  headerStr += '</li>';
  if(page=='about'){
    headerStr += '<li class="nav-item active">';
  }else{
    headerStr += '<li class="nav-item">';
  }
  headerStr += '<a class="nav-link" href="https://coronanotbeer.com/aboutus?lang='+lang+'&country='+country+'">';
  headerStr += lang=='en'?'About us':'About us';
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
      headerStr += '" href="https://coronanotbeer.com?lang=en">English</a>';
      headerStr += '<a class="dropdown-item';
      headerStr += lang=='en'?'':' active';
      headerStr +='" href="https://coronanotbeer.com?lang=ko">한국어</a>';
    headerStr += '</div>';
  headerStr += '</div>';
  //
  headerStr += '</div>';
  headerStr += '</nav>';
  //###############헤더 끝

  //#########tab
  var tabStr='';
  tabStr+='<ul class="nav nav-tabs">';
    tabStr+='<li class="nav-item">';
      if(country=='USA'){
        tabStr+='<a class="nav-link active" href="https://coronanotbeer.com?country=US">US</a>';
      }else{
        tabStr+='<a class="nav-link" href="https://coronanotbeer.com?country=US">US</a>';
      }
    tabStr+='</li>';
  tabStr+='<li class="nav-item">';
      if(country!='USA'){
        tabStr+='<a class="nav-link active" href="https://coronanotbeer.com?country=global">Global</a>';
      }else{
        tabStr+='<a class="nav-link" href="https://coronanotbeer.com?country=global">Global</a>';
      }
    tabStr+='</li>';
  tabStr+='</ul>';
  $('#tab').html(tabStr);
  //#########tab

  $('#header').html(headerStr);
}
