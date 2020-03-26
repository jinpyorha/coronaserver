function coronaTotalReport(type,country){
  var country= country!=null&&country!=''?country:'global';
  if(type==''){
    $('#coronaUsMap').html('');

  }
  var urlApi = '/coronaserver/coronaDataApi.php?country='+country;
  console.log('here = '+ urlApi);
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
        var dataElmTotal = [];//data Array for Geochart
        if(country=='USA'){
          var geochartData = ['provinces',   'confirmed'];
        }else{
          var geochartData = ['country',   'confirmed'];
        }
        var countryList = obj.data.countryList;
        var countryListLength = countryList != null ? countryList.length :'';

        //가져온 데이터 배열에 저장
        //for (var i = 0; i < countryListLength; i++) {
        for (var i = 0; i <countryListLength; i++) {
          if(countryList[i]['cnt']!=null&&parseInt(countryList[i]['cnt'])>0){
            if(country=='USA'){
              var tempDataElmTotal = [countryList[i]['ProvinceState'],parseInt(countryList[i]['cnt']!=0?countryList[i]['cnt']:0)];
            }else{
              var tempDataElmTotal = [countryList[i]['CountryRegion'],parseInt(countryList[i]['cnt']!=0?countryList[i]['cnt']:0)];
            }
            dataElmTotal.unshift(tempDataElmTotal);
          }
        }
          dataElmTotal.unshift(geochartData);
        //가져온 데이터 배열에 저장 끝
        var mapStr = '';


        mapStr +='Click on a State for a detailed analysis';
        mapStr +='<table class="table table-hover">';
        mapStr += '<col width="0.625rem">';
        mapStr += '<col width="0.625rem">';
        mapStr += '<col width="0.625rem">';
        mapStr += '<col width="0.625rem">';
        mapStr += '<thead class="table-success"><tr>';
        mapStr += '<th scope="col" class="smallfont">State</th>';
        mapStr += '<th scope="col" class="smallfont">Confirmed cases</th>';
        // mapStr += '<th scope="col">Deaths</th>';//추가하기
        // mapStr += '<th scope="col">Case fatality rate</th>';//추가하기
        // mapStr += '<th scope="col">Recovered</th>';//추가하기
        mapStr += '</tr></thead>';
        mapStr += '<tbody>';
        for(var k=dataElmTotal.length-1;k>0;k--){
          if(dataElmTotal[k][0]!='Unassigned Location'){
            mapStr += '<tr">';
            if(country=='USA'){
              mapStr += '<td class="mediumfont_data" onclick="coronaReport(\''+dataElmTotal[k][0]+'\', \''+country+'\',\'USA\',\'\');">'+dataElmTotal[k][0]+'</td>';
            }else{
              mapStr += '<td class="mediumfont_data" onclick="coronaReport(\'\',\''+dataElmTotal[k][0]+'\',\'global\',\'\');">'+dataElmTotal[k][0]+'</td>';
            }
            mapStr += '<td class="largefont_data">'+dataElmTotal[k][1]+'</td>';
            mapStr += '</tr>';
          }
        }
        mapStr += '<tbody>';
        mapStr += '</table>';
        $('#coronaUsMapReport').html(mapStr);

      }
    }
  });
}
