function coronaAnalysis(){
  var urlApi = '/coronaserver/coronaDataAnalysis.php';
  console.log('coronaAnalysis = '+ urlApi);
  $.ajax({
    type: 'GET',
    url: urlApi,
    contentType: false,
    data: false,
    processData: false,
    success: function(data) {
      isUploading = false;
      var obj = JSON.parse(data);
      //console.log(data);

      if (obj.result == "success") {

        var ConfirmedAsc = obj.data.ConfirmedAsc;
        console.log(ConfirmedAsc[0]['CountryRegion']);
        var ConfirmedDesc = obj.data.ConfirmedDesc;
        var DeathsAsc = obj.data.DeathsAsc;
        var DeathsDesc = obj.data.DeathsDesc;

        //출력 str 시작
        var sliderStr='';
        sliderStr+='<div class="slide-cont">';

          sliderStr+='<div class="swiper-container">';
            sliderStr+='<div class="swiper-wrapper">';
              sliderStr+='<div class="swiper-slide">';
                sliderStr+='<h3>Stepest increase<span class="up">▲</span> in <b>confirmed</b> case (top5)</h3>';
                sliderStr+='<div class="ol-box">';
                  sliderStr+='<ol>';
                  for(var i=0;i<ConfirmedDesc.length;i++){
                    sliderStr+='<li onclick="coronaReport(\'\',\''+ConfirmedDesc[i]['CountryRegion']+'\',\'global\',\'\')">'+ConfirmedDesc[i]['CountryRegion']+'<b>'+ConfirmedDesc[i]['value']+'%</b></li>';
                  }
                  sliderStr+='</ol>';
                sliderStr+='</div>';
              sliderStr+='</div>';
              sliderStr+='<div class="swiper-slide">';
                sliderStr+='<h3>Stepest decrease<span class="dwn">▼</span> in <b>confirmed</b> case (top5)</h3>';
                sliderStr+='<div class="ol-box">';
                  sliderStr+='<ol>';
                  for(var i=0;i<ConfirmedAsc.length;i++){
                    //sliderStr+=('<li>'+ConfirmedAsc[i]['CountryRegion']+'<b>'+ConfirmedAsc[i]['value']+'<span class="'+ConfirmedAsc[i]['value']>0?'up':'dwn'+'">'+ConfirmedAsc[i]['value']>0?'▲':'▼'+'</span></b></li>');
                    sliderStr+='<li onclick="coronaReport(\'\',\''+ConfirmedAsc[i]['CountryRegion']+'\',\'global\',\'\')">'+ConfirmedAsc[i]['CountryRegion']+'<b>'+ConfirmedAsc[i]['value']+'%</b></li>';
                  }
                  sliderStr+='</ol>';
                sliderStr+='</div>';
              sliderStr+='</div>';
              sliderStr+='<div class="swiper-slide">';
                sliderStr+='<h3>Highest<span class="up">▲</span> case <b>fatality</b> rates (top5):confirmed>1000</h3>';
                sliderStr+='<div class="ol-box">';
                  sliderStr+='<ol>';
                  for(var i=0;i<DeathsDesc.length;i++){
                    sliderStr+='<li onclick="coronaReport(\'\',\''+DeathsDesc[i]['CountryRegion']+'\',\'global\',\'\')">'+DeathsDesc[i]['CountryRegion']+'<b>'+DeathsDesc[i]['value']+'%</b></li>';
                  }
                  sliderStr+='</ol>';
                sliderStr+='</div>';
              sliderStr+='</div>';
              sliderStr+='<div class="swiper-slide">';
                sliderStr+='<h3>Lowest<span class="dwn">▼</span> case <b>fatality</b> rates (top5):confirmed>1000</h3>';
                sliderStr+='<div class="ol-box">';
                  sliderStr+='<ol>';
                  for(var i=0;i<DeathsAsc.length;i++){
                    sliderStr+='<li onclick="coronaReport(\'\',\''+DeathsAsc[i]['CountryRegion']+'\',\'global\',\'\')">'+DeathsAsc[i]['CountryRegion']+'<b>'+DeathsAsc[i]['value']+'%</b></li>';
                  }
                  sliderStr+='</ol>';
                sliderStr+='</div>';
              sliderStr+='</div>';
            sliderStr+='</div>';
            sliderStr+='<div class="swiper-pagination"></div>';
          sliderStr+='</div>';
        sliderStr+='</div>';
        $('#recommend-slide').html(sliderStr);
        var swiper = new Swiper('.swiper-container', {
          slidesPerView: 'auto',
          spaceBetween: 30,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
        });
      }
    }
  });
}
