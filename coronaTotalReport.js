function coronaTotalReport(ProvinceState,CountryRegion,country,type){
  var country= country!=null&&country!=''?country:'global';
  if(type==''){
    $('#coronaUsMap').html('');

  }
  var urlApi = '/coronaserver/coronaTotalReportApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion + '&country=' + country;
  console.log('coronaTotalReportApi = '+ urlApi);
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
        var countryListLength = countryList != null ? countryList.length : 0;

        //select 문 만들기 시작
        var selectStr = '';
        selectStr += '<h5 class="class-border smallfont">&nbsp;';
        if (country == 'USA') {
          selectStr += lang=='en'?'Search by States (cumulative cases)':'미국 주별 검색';
        } else {
          selectStr += lang=='en'?'Search by country':'국가별 검색';
        }
        selectStr += '</h5>'

        selectStr += '<select id="countryList" class="smallfont">';
        selectStr += '<option value="total">';
        if (country == 'USA') {
          selectStr += lang=='en'?'Select States':'주를 선택하세요(확진자 수)';
        } else {
          selectStr += lang=='en'?'Select country':'나라를 선택하세요(확진자 수)';
        }

        selectStr += '</option>';
        for (var i = 0; i < countryListLength; i++) {
          if (countryList[i]['selectStatus']) {
            if(country=='USA'){selectStr += '<option  selected value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '(' + countryList[i]['cnt'] +
              ')</option>';
            }else{
              selectStr += '<option  selected value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '"> [' + countryList[i]['CountryRegion'] + '](' + countryList[i]['cnt'] +
                ')</option>';
              }
          } else {
            if(country=='USA'){
              selectStr += '<option value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '(' + countryList[i]['cnt'] +
              ')</option>';
            }else{
              selectStr += '<option value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">[' + countryList[i]['CountryRegion'] + '](' + countryList[i]['cnt'] +
              ')</option>';
            }
          }

        }
        selectStr += '</select>';
        selectStr += '<div class="alert alert-success smallfont" role="alert"><i>';
        selectStr += lang=='en'?'Sorted by number of total cases':'지역은 확진자 많은 순으로 정렬되어있습니다!';
        selectStr += '</i></div>';

        //select 문 만들기 끝

        var mapStr = '';

        if(country=='USA'){
          mapStr +='Click on a State for a detailed analysis';
        }else{
          mapStr +='Click on a Country for a detailed analysis';
        }
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

        for (var i = 0; i < countryListLength; i++) {
          if (countryList[i]['selectStatus']) {
            mapStr += '<tr class="on">';
          }
          else{
            mapStr += '<tr>';
          }
          if(country=='USA'){
            mapStr += '<td class="mediumfont_data" onclick="coronaReport(\''+countryList[i]['ProvinceState']+'\', \''+country+'\',\'USA\',\'\');">'+countryList[i]['ProvinceState']+'</td>';
          }else{
            mapStr += '<td class="mediumfont_data" onclick="coronaReport(\''+countryList[i]['ProvinceState']+'\',\''+countryList[i]['CountryRegion']+'\',\'global\',\'\');">'+countryList[i]['CountryRegion']+'</td>';
          }
          mapStr += '<td class="largefont_data">'+countryList[i]['cnt']+'</td>';
          mapStr += '</tr>';

        }
        mapStr += '<tbody>';
        mapStr += '</table>';
        $('#coronaUsMapReport').html(mapStr);
        $('#select-box').html(selectStr);
        //내부함수
        $(function() {
          $('#countryList').change(function() {
            if($(this).val()=='total'){
              type='total';
            }else{
              type='';
            }
            var regionArray = this.value.split('@');
            regionArray[0]=regionArray[0]!=null?regionArray[0]:'';
            regionArray[1]=regionArray[1]!=null?regionArray[1]:'';

            coronaReport(regionArray[0], regionArray[1], country,type);
          });
        });
        //내부함수 끝

      }
    }
  });
}
