function coronaReport(ProvinceState, CountryRegion, country) {
  //console.log('onesunny3.cafe24.com/crawler/coronaDataApi.php?ProvinceState='+ProvinceState+'&CountryRegion='+CountryRegion);
  if (country == 'US') {
    var urlApi = '../coronaserver/coronaDataApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion + '&country=' + country;
    console.log(urlApi);
  } else if (country == 'global') {
    var urlApi = '../coronaserver/coronaDataApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion;
    console.log(urlApi);
  }

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
        var dataElmConfirmed = []; //data Array for column chart
        var dataElmDeath = []; //data Array for column chart
        var dataElmRecovered = []; //data Array for column chart
        var dataElmIncrease = []; //전체 데이터 위한 배열

        var dataElmStack = [];

        var selectStr = '';
        var countryList = obj.data.countryList;
        var countryData = obj.data.countryData;
        var countryDataRecent = obj.data.countryDataRecent;
        var countryListLength = countryList != null ? countryList.length : 0;


        selectStr += '<h5>';
        if (country == 'US') {
          selectStr += lang=='en'?'Search by states':'미국 주별 검색';
        } else {
          selectStr += lang=='en'?'Search by country':'국가별 검색';
        }
        selectStr += '</h5>'

        selectStr += '<select id="countryList">';
        selectStr += '<option>';
        if (country == 'US') {
          selectStr += lang=='en'?'select states(confirmed count)':'주를 선택하세요(확진자 수)';
        } else {
          selectStr += lang=='en'?'select country(confirmed count)':'나라를 선택하세요(확진자 수)';
        }
        selectStr += '</option>';
        for (var i = 0; i < countryListLength; i++) {
          if (countryList[i]['ProvinceState'] == ProvinceState && countryList[i]['CountryRegion'] == CountryRegion) {
            selectStr += '<option  selected value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '[' + countryList[i]['CountryRegion'] + '](' + countryList[i]['cnt'] +
              ')</option>';
          } else {
            selectStr += '<option value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '[' + countryList[i]['CountryRegion'] + '](' + countryList[i]['cnt'] +
              ')</option>';
          }

        }
        selectStr += '</select>';
        var conditionStr = '';
        var reportDailyStr ='';
        var reportStr = '';

        function toPercent(number, float) {
          var percent = parseFloat(number * 100).toFixed(float) + "%";
          return percent;
        } //percentage

        function subtractThree(number1,number2,number3){
          var result = number1 - number2 - number3;
          return result;
        }//subtract

        // console.log(countryData);
        if(countryDataRecent !=null){
          // selectStr +=+percentRecovered+;
          var tempCountryRecentConfirmed = +countryDataRecent.Confirmed;
          var tempCountryRecentDeaths = +countryDataRecent.Deaths;
          var tempCountryRecentRecovered =+countryDataRecent.Recovered;
          var tempCountryRecentConfirmedIncrease = +countryDataRecent.Increase;
          var tempCountryRecentDeathsIncrease = +countryDataRecent.DeathsIncrease;
          var tempCountryRecentRecoveredIncrease =+countryDataRecent.RecoveredIncrease;
          reportDailyStr +='<h1>Total Active : '+subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)+'</h1>'

          reportDailyStr +='<table class="table table-hover">';
          reportDailyStr += '<col width="10px">';
          reportDailyStr += '<col width="10px">';
          reportDailyStr += '<col width="10px">';
          reportDailyStr += '<col width="10px">';
          reportDailyStr += '<thead class="thead-dark"><tr>';
          reportDailyStr += '<th scope="col">Total cases</th>';
          reportDailyStr += '<th scope="col">Total active</th>';
          reportDailyStr += '<th scope="col">Total death</th>';
          reportDailyStr += '<th scope="col">Total recovered</th>';
          reportDailyStr += '</tr></thead>';
          reportDailyStr += '<tbody>';
          reportDailyStr += '<tr>';
          reportDailyStr += '<td><span style="font-size: 20px">'+countryDataRecent.Confirmed+'</span><br><span style="font-size:12px">(<span style="color:red">+'+countryDataRecent.Increase+'</span>) in '+ProvinceState+'</span></td>';
          if(subtractThree(tempCountryRecentConfirmedIncrease, tempCountryRecentDeathsIncrease, tempCountryRecentRecoveredIncrease)>0){
            reportDailyStr += '<td><span style="font-size: 20px">'+subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)+'</span><br><span style="font-size:12px">(<span class="red">+'+subtractThree(tempCountryRecentConfirmedIncrease, tempCountryRecentDeathsIncrease, tempCountryRecentRecoveredIncrease)+'</span> new active)</span></td>';
          }else{
            reportDailyStr += '<td><span style="font-size: 20px">'+subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)+'</span><br><span style="font-size:12px">(<span class="blue">+'+subtractThree(tempCountryRecentConfirmedIncrease, tempCountryRecentDeathsIncrease, tempCountryRecentRecoveredIncrease)+'</span> new active)</span></td>';
          }
          if(countryDataRecent.DeathsIncrease>0){
            reportDailyStr += '<td><span style="font-size: 20px">'+countryDataRecent.Deaths+'</span><br><span style="font-size:12px">(<span class="red">+'+countryDataRecent.DeathsIncrease+'</span>)</span><span style="font-size:12px; color:grey"> '+toPercent(tempCountryRecentDeaths/tempCountryRecentConfirmed,2)+' of total</span></td>';
          }else{
            reportDailyStr += '<td><span style="font-size: 20px">'+countryDataRecent.Deaths+'</span><br><span style="font-size:12px">(<span class="blue">+'+countryDataRecent.DeathsIncrease+'</span>)</span><span style="font-size:12px; color:grey"> '+toPercent(tempCountryRecentDeaths/tempCountryRecentConfirmed,2)+' of total</span></td>';
          }
          reportDailyStr += '<td><span style="font-size: 20px">'+countryDataRecent.Recovered+'</span><br><span style="font-size:12px">(<span class="red">+'+countryDataRecent.RecoveredIncrease+'</span>)</span><span style="font-size:12px; color:green"> '+toPercent(tempCountryRecentRecovered/tempCountryRecentConfirmed,2)+' of total</span></td>';
          reportDailyStr += '</tr>';
          reportDailyStr += '<tr>';
          reportDailyStr += '</tr>';
          reportDailyStr += '</tbody>';
          reportDailyStr += '</table>';
        }

        if (countryData == null) {
          selectStr += '<div class="alert alert-info" role="alert">';
          selectStr += lang=='en'?'The regions are sorted by many confirmers':'지역은 확진자 많은 순으로 정렬되어있습니다!';
          selectStr += '</div>';
          $('#reportDaily').html('');
          $('#columnchart_confirmed').html('');
          $('#linechart_confirmed_increase').html('');
          $('#columnchart_death').html('');
          $('#columnchart_recovered').html('');
          $('#stackchart').html('');

          if(country=='US'){coronaUsMap();}
          else{$('#coronaUsMap').html('');  $('#coronaUsMapReport').html('');}

          $('#reportDaily').html(reportDailyStr);


        }

        if (countryData != null) {
          $('#coronaUsMap').html('');
          $('#coronaUsMapReport').html('');

          $('#reportDaily').html('');
          $('#columnchart_confirmed').html('');
          $('#linechart_confirmed_increase').html('');
          $('#columnchart_death').html('');
          $('#columnchart_recovered').html('');
          $('#stackchart').html('');
          var countryDataLength = countryData.length;
          //console.log( countryDataLength );
          reportStr += '<h3>' + ProvinceState + '[' + CountryRegion + ']</h3>';
          reportStr += '<table class="table table-hover">';
          reportStr += '<col width="60px">';
          // reportStr += '<col width="10px">';
          reportStr += '<col width="10px">';
          reportStr += '<col width="10px">';
          reportStr += '<col width="10px">';
          reportStr += '<col width="10px">';
          reportStr += '<thead class="thead-dark"><tr>';
          reportStr += '<th scope="col">Date</th>';
          reportStr += '<th scope="col">Confirmed<div> (vs. yesterday)</div></th>';
          reportStr += '<th scope="col">Active</th>';
          reportStr += '<th scope="col">Death</th>';
          reportStr += '<th scope="col">Recovered</th>';
          reportStr += '</tr></thead>';
          reportStr += '<tbody>';
          for (var i = 0; i < countryDataLength; i++) {
            var tempActiveIncrease = countryData[i]['Increase'] - countryData[i]['DeathsIncrease']-countryData[i]['RecoveredIncrease'];
            reportStr += '<tr>';
            reportStr += '<td>' + countryData[i]['DataDate'] + '</td>';
            if (countryData[i]['Increase'] > 0) {
              reportStr += '<td>+' + countryData[i]['Confirmed'] + '<span class="red"> (+'+ countryData[i]['Increase'] +')</span></td>';
            } else {
              reportStr += '<td>+' + countryData[i]['Confirmed'] + '<span class="blue"> ('+ countryData[i]['Increase'] + ')</span></td>';
            }
            if (tempActiveIncrease>0){
            reportStr += '<td>' + subtractThree(countryData[i]['Confirmed'], countryData[i]['Deaths'],countryData[i]['Recovered'])+ ' (<span class="red">+' + tempActiveIncrease +  '</span>)</td>';
            } else{
            reportStr += '<td>' + subtractThree(countryData[i]['Confirmed'], countryData[i]['Deaths'],countryData[i]['Recovered'])+ ' (<span class="blue">' + tempActiveIncrease +  '</span>)</td>';
            }
            reportStr += '<td>' + countryData[i]['Deaths'] + '</td>';
            reportStr += '<td>' + countryData[i]['Recovered'] + '</td>';
            reportStr += '</tr>';

            //tempData = [countryData [i]['DataDate'], countryData [i]['Confirmed']];
            var tempConfirmed = countryData[i]['Confirmed'] != 0 ? countryData[i]['Confirmed'] : 0;
            var tempDeaths = countryData[i]['Deaths'] != 0 ? countryData[i]['Deaths'] : 0;
            var tempRecovered = countryData[i]['Recovered'] != 0 ? countryData[i]['Recovered'] : 0;
            var tempActive = countryData[i]['Active'] != 0 ? countryData[i]['Active'] : 0;


            tempDataConfirmed = [countryData[i]['DataDate'],tempConfirmed]; //array push for column chart
            tempDataDeath = [countryData[i]['DataDate'], tempDeaths]; //array push for column chart
            tempDataRecovered = [countryData[i]['DataDate'],tempRecovered]; //array push for column chart
            tempDataIncrease = [countryData[i]['DataDate'], countryData[i]['Increase']]; //array push for column chart

            tempDataStack = [countryData[i]['DataDate'],tempActive,tempRecovered,tempDeaths] //array push for stack chart

            dataElmConfirmed.unshift(tempDataConfirmed); //array push for column chart
            dataElmDeath.unshift(tempDataDeath); //array push for column chart
            dataElmRecovered.unshift(tempDataRecovered); //array push for column chart
            dataElmIncrease.unshift(tempDataIncrease); //array push for column chart
            dataElmStack.unshift(tempDataStack);//array push for stack chart
          }
          reportStr += '</tbody>';
          reportStr += '</table>';

          conditionStr += '<div class="colors">';
          conditionStr += '<div class="color red"></div><div class="label">Confirmed Count</div>';
          conditionStr += '<div class="color yellow"></div><div class="label">Death Count</div>';
          conditionStr += '<div class="color blue"></div><div class="label">Recovered Count</div>';
          conditionStr += '</div>'
        }
        $('#select-box').html(selectStr);
        $('#report').html(reportStr);
        $('#colors').html(conditionStr);
        $('#reportDaily').html(reportDailyStr);
        //console.log(dataCorona);
        //console.log(dataElm);

        //var tempData =['DateDate', 'Confirmed'];
        var tempDataConfirmed = ['DataDate', 'Confirmed'];
        var tempDataDeath = ['DataDate', 'Deaths'];
        var tempDataRecovered = ['DataDate', 'Recovered'];
        var tempDataIncrease = ['DataDate', 'Increase'];
        var tempDataStack = ['DataDate','Active','Recovered','Deaths'];
        dataElmConfirmed.unshift(tempDataConfirmed); //array push for column chart
        dataElmDeath.unshift(tempDataDeath); //array push for column chart
        dataElmRecovered.unshift(tempDataRecovered); //array push for column chart
        dataElmIncrease.unshift(tempDataIncrease); //array push for column chart
        dataElmStack.unshift(tempDataStack);

        if (countryData != null) {
          google.charts.load('current', {
            'packages': ['bar']
          });
          if(tempCountryRecentConfirmed>0){google.charts.setOnLoadCallback(drawConfirmedChart);}
          if(tempCountryRecentDeaths>0){google.charts.setOnLoadCallback(drawDeathChart);}
          if(tempCountryRecentRecovered>0){google.charts.setOnLoadCallback(drawRecoveredChart);}

          google.charts.load('current', {
            packages: ['corechart', 'line']
          });
          google.charts.setOnLoadCallback(drawIncreaseChart);
          google.charts.setOnLoadCallback(drawStackChart);
        }
        function drawStackChart(){
          var data = google.visualization.arrayToDataTable(dataElmStack);

          var options = {
            height: 600,
            legend: { position: 'none', maxLines: 3 },
            bar: { groupWidth: '75%' },
            series: {
              0:{color:'#EB7F75'},
              1:{color:'#8FDAFF'},
              2:{color:'#FFDC73'},
            },
            //colors: ['#EB7F75','#FFDC73','#8FDAFF'],
            vAxis: {
              viewWindowMode: 'explicit',
              viewWindow: {
                min: 0,
              },
              minValue: 0,
              gridlines: {
                color: '#f0f0f0',
                count: -1
              }
            },
            hAxis: {
              viewWindow: {
                min: 0,
              },
              minValue: 0
            },
            isStacked: true,
          };

          var chart = new google.charts.Bar(document.getElementById('stackchart'));

          chart.draw(data, google.charts.Bar.convertOptions(options));
        }


        function drawConfirmedChart() {

          var data = google.visualization.arrayToDataTable(dataElmConfirmed);

          //https://developers.google.com/chart/interactive/docs/gallery/piechart?hl=ko#Configuration_Options
          //collumn chart customization - options
          //=>https://developers.google.com/chart/interactive/docs/gallery/columnchart

          var options = {
            title: '확진자 누적수',
            legend: {
              position: "none"
            },
            chart: {

              //title: 'Company Performance',
              //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
            },
            colors: ['#EB7F75'],
            vAxis: {
              viewWindowMode: 'explicit',
              viewWindow: {
                min: 0,
              },
              minValue: 0,
              gridlines: {
                color: '#f0f0f0',
                count: -1
              }
            },
            hAxis: {
              viewWindow: {
                min: 0,
              },
              minValue: 0
            },
            height: 400,
          };

          var chart = new google.charts.Bar(document.getElementById('columnchart_confirmed'));

          chart.draw(data, google.charts.Bar.convertOptions(options));
        }



        function drawDeathChart() {

          var data = google.visualization.arrayToDataTable(dataElmDeath);

          //https://developers.google.com/chart/interactive/docs/gallery/piechart?hl=ko#Configuration_Options
          //collumn chart customization - options
          //=>https://developers.google.com/chart/interactive/docs/gallery/columnchart

          var options = {
            title: '사망자 누적수',
            legend: {
              position: "none"
            },
            chart: {

              //title: 'Company Performance',
              //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
            },
            colors: ['#FFDC73'],
            vAxis: {
              viewWindowMode: 'explicit',
              viewWindow: {
                min: 0,
              },
              minValue: 0,
              gridlines: {
                color: '#f0f0f0'
              }
            },
            hAxis: {
              viewWindow: {
                min: 0,
              },
              minValue: 0
            },
            height: 400,
          };

          var chart = new google.charts.Bar(document.getElementById('columnchart_death'));

          chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function drawRecoveredChart() {

          var data = google.visualization.arrayToDataTable(dataElmRecovered);

          //https://developers.google.com/chart/interactive/docs/gallery/piechart?hl=ko#Configuration_Options
          //collumn chart customization - options
          //=>https://developers.google.com/chart/interactive/docs/gallery/columnchart

          var options = {
            title: '회복자 누적수',
            legend: {
              position: "none"
            },
            chart: {

              //title: 'Company Performance',
              //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
            },
            colors: ['#8FDAFF'], //
            vAxis: {
              gridlines: {
                color: '#f0f0f0',
                minSpacing: 20,
                count: -1
              }
            },
            height: 400,
          };

          var chart = new google.charts.Bar(document.getElementById('columnchart_recovered'));

          chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function drawIncreaseChart() {

          //var data = new google.visualization.DataTable();
          var data = google.visualization.arrayToDataTable(dataElmIncrease);

          var options = {
            title: '일별 확진자 증가수',
            colors: ['#EB7F75'],
            legend: {
              position: "none"
            },
            hAxis: {
              title: 'Time'
            },
            vAxis: {
              title: 'daily increase(confirmed)'
            },
            height: 400,
          };

          var chart = new google.visualization.LineChart(document.getElementById('linechart_confirmed_increase'));

          chart.draw(data, options);
        }

        //내부함수
        $(function() {
          $('#countryList').change(function() {
            //console.log(this.value);
            var regionArray = this.value.split('@');
            coronaReport(regionArray[0], regionArray[1], country);
          });
        });

        //내부함수 끝
      }
    }


  });


}
