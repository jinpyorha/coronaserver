function coronaReport(ProvinceState, CountryRegion, country) {
  //console.log('onesunny3.cafe24.com/crawler/coronaDataApi.php?ProvinceState='+ProvinceState+'&CountryRegion='+CountryRegion);
  if (country == 'USA') {
    var urlApi = '/coronaserver/coronaDataApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion + '&country=' + country;
    console.log(urlApi);
  } else if (country == 'global') {
    var urlApi = '/coronaserver/coronaDataApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion;
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


        selectStr += '<h5 class="class-border smallfont">&nbsp;';
        if (country == 'USA') {
          selectStr += lang=='en'?'Search by States (cumulative cases)':'미국 주별 검색';
        } else {
          selectStr += lang=='en'?'Search by country':'국가별 검색';
        }
        selectStr += '</h5>'

        selectStr += '<select id="countryList" class="smallfont">';
        selectStr += '<option>';
        if (country == 'USA') {
          selectStr += lang=='en'?'Select States':'주를 선택하세요(확진자 수)';
        } else {
          selectStr += lang=='en'?'select country(confirmed count)':'나라를 선택하세요(확진자 수)';
        }

        if (country == 'USA') {
          $('#title').html('#COVID-19 hourly updates in US');
        } else {
          $('#title').html('#COVID-19 hourly updates Global');
        }

        selectStr += '</option>';
        for (var i = 0; i < countryListLength; i++) {
          if (countryList[i]['ProvinceState'] == ProvinceState && countryList[i]['CountryRegion'] == CountryRegion) {
            if(country=='USA'){selectStr += '<option  selected value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '(' + countryList[i]['cnt'] +
              ')</option>';
            }else{
              selectStr += '<option  selected value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '[' + countryList[i]['CountryRegion'] + '](' + countryList[i]['cnt'] +
                ')</option>';
              }
          } else {
            if(country=='USA'){
              selectStr += '<option value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '(' + countryList[i]['cnt'] +
              ')</option>';
            }else{
              selectStr += '<option value="' + countryList[i]['ProvinceState'] + '@' + countryList[i]['CountryRegion'] + '">' + countryList[i]['ProvinceState'] + '[' + countryList[i]['CountryRegion'] + '](' + countryList[i]['cnt'] +
              ')</option>';
            }
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

        // function divideTwoNum(number1, number2){
        //   var result = number1 / number2;
        //   return result;
        // }


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
          var tempCountryRecentDate =countryDataRecent.RecentDate;
          // reportDailyStr +='<h1>Total Active : '+subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)+'</h1>'

          //reportDailyStr +='<h5 class="display-6 class-border mediumfont">&nbsp;last updated :'+tempCountryRecentDate+'</h5>';
    		  reportDailyStr +='<h5 class="display-6 class-border mediumfont">&nbsp;Situation summary</h5>';

          reportDailyStr +='<table class="table table-hover">';
          reportDailyStr += '<thead class="table-success"><tr>';
          reportDailyStr += '<th scope="col" colspan="1" class="smallfont">Confirmed<br>';
          if(country=='USA'){
            if(ProvinceState=='Select States'||ProvinceState==''){
              reportDailyStr+='in US';
            }else{
              reportDailyStr+='<span>in </span>'+ ProvinceState;
            }
          }else{
            if(ProvinceState=='Select States'||CountryRegion==''){
              reportDailyStr+='Globally';
            }else{
              reportDailyStr+=ProvinceState+'['+CountryRegion+']';
            }
          }
          reportDailyStr += '</th>';
          reportDailyStr += '<th scope="col" colspan="1" class="smallfont">Total<br>active</th>';          
          reportDailyStr += '<th scope="col" colspan="1" class="smallfont">Total<br>death</th>';
          if(country!='USA'){
            reportDailyStr += '<th scope="col" colspan="1" class="smallfont">Total<br>recovered</th>';
          }
          reportDailyStr += '</tr></thead>';
          reportDailyStr += '<tbody>';
          reportDailyStr += '<tr>';

          // if(country=='USA'){
          reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Confirmed+'</span><br><span style="font-size:0.75rem">(<span style="color:red">+'+countryDataRecent.Increase+'</span>)</span></td>';
          // }else{
          // reportDailyStr += '<td><span class="mediumfont_data">'+countryDataRecent.Confirmed+'</span><br><span style="font-size:0.75rem">(<span style="color:red">+'+countryDataRecent.Increase+'</span>)</span></td>';  
          // }
          
          if(subtractThree(tempCountryRecentConfirmedIncrease, tempCountryRecentDeathsIncrease, tempCountryRecentRecoveredIncrease)>=0){
            reportDailyStr += '<td><span class="largefont_data">'+subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)+'</span><br><span style="font-size:0.75rem">(<span class="red">+'+subtractThree(tempCountryRecentConfirmedIncrease, tempCountryRecentDeathsIncrease, tempCountryRecentRecoveredIncrease)+'</span>)<br></span><span style="font-size:0.75rem; color:grey">'+toPercent(subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)/tempCountryRecentConfirmed,1)+'/total</span></td>';
          }else{
            reportDailyStr += '<td><span class="largefont_data">'+subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)+'</span><br><span style="font-size:0.75rem">(<span class="blue">'+subtractThree(tempCountryRecentConfirmedIncrease, tempCountryRecentDeathsIncrease, tempCountryRecentRecoveredIncrease)+'</span>)<br></span><span style="font-size:0.75rem; color:grey">'+toPercent(subtractThree(tempCountryRecentConfirmed, tempCountryRecentDeaths, tempCountryRecentRecovered)/tempCountryRecentConfirmed,1)+'/total</span></td>';
          }

          if(countryDataRecent.DeathsIncrease>=0){
            reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Deaths+'</span><br><span style="font-size:0.75rem">(<span class="red">+'+countryDataRecent.DeathsIncrease+'</span>)</span><span style="font-size:0.75rem; color:grey"><br>'+toPercent(tempCountryRecentDeaths/tempCountryRecentConfirmed,1)+'/total</span></td>';
          }else{
            reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Deaths+'</span><br><span style="font-size:0.75rem">(<span class="blue">'+countryDataRecent.DeathsIncrease+'</span>)</span><span style="font-size:0.75rem; color:grey"><br>'+toPercent(tempCountryRecentDeaths/tempCountryRecentConfirmed,1)+'/total</span></td>';
          }
          if(country!='USA'){
           reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Recovered+'</span><br><span style="font-size:0.75rem">(<span class="red">+'+countryDataRecent.RecoveredIncrease+'</span>)</span><span style="font-size:0.75rem; color:grey"><br>'+toPercent(tempCountryRecentRecovered/tempCountryRecentConfirmed,1)+'/total</span></td>';
          }
          reportDailyStr += '</tr>';
          reportDailyStr += '<tr>';
          reportDailyStr += '</tr>';
          reportDailyStr += '</tbody>';
          reportDailyStr += '</table>';
          reportDailyStr += '<p class="font-italic"><small>Last updated: '+tempCountryRecentDate+' in GMT; Data updates every hour and is sourced and cross-checked with official government websites</small></p>';
          reportDailyStr += '<p class="font-italic"><small> </small></p>';

        }



        if (countryData == null) {
          selectStr += '<div class="alert alert-success smallfont" role="alert"><i>';
          selectStr += lang=='en'?'Sorted by number of total cases':'지역은 확진자 많은 순으로 정렬되어있습니다!';
          selectStr += '</i></div>';
          $('#reportDaily').html('');
          $('#columnchart_confirmed').html('');
          $('#linechart_confirmed_increase').html('');
          $('#columnchart_death').html('');
          $('#columnchart_recovered').html('');
          $('#stackchart').html('');
          if(country!='USA'){
              $('#reportDaily').html('');
          }
          else{
            $('#reportDaily').html(reportDailyStr);
          }
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
          reportStr += '<p class="margin-top">' + ProvinceState + '[' + CountryRegion + ']</p>';
          reportStr += '<table class="table table-hover">';

          reportStr += '<thead class="table-success"><tr>';
          reportStr += '<th scope="col" colspan="1" class="smallfont_data">Date</th>';
          reportStr += '<th scope="col" colspan="1" class="smallfont_data">Total</div></th>';
          reportStr += '<th scope="col" colspan="1" class="smallfont_data">Active</th>';
          reportStr += '<th scope="col" colspan="1" class="smallfont_data">Death</th>';
          if(country!='USA'){reportStr += '<th scope="col" colspan="1" class="smallfont_data">Recovered</th>';}
          reportStr += '</tr></thead>';
          reportStr += '<tbody>';
          if(countryDataLength<30){
             $('#stack-line-box').addClass('row');
          }
          //countryDataLength=countryDataLength>30?30:countryDataLength;
          for (var i = 0; i < countryDataLength; i++) {
            var tempActiveIncrease = countryData[i]['Increase'] - countryData[i]['DeathsIncrease']-countryData[i]['RecoveredIncrease'];

            reportStr += '<tr>';
            reportStr += '<td><span class="smallfont_data">' + countryData[i]['DataDate'] + '</span></td>';
            if (countryData[i]['Increase'] > 0) {
              reportStr += '<td><span class="mediumfont_data">+' + countryData[i]['Confirmed'] + '</span><span class="red smallfont_data"> <br> (+'+ countryData[i]['Increase'] +')</span></td>';
            } else {
              reportStr += '<td><span class="mediumfont_data">+' + countryData[i]['Confirmed'] + '<br>(0)</span></td>';
               // '<span class="blue"> ('+ countryData[i]['Increase'] + ')</span></td>';
            }
            if (tempActiveIncrease>0){
            reportStr += '<td><span class="mediumfont_data">' + subtractThree(countryData[i]['Confirmed'], countryData[i]['Deaths'],countryData[i]['Recovered'])+ '</span><br><span class="red smallfont_data">(+' + tempActiveIncrease +  ')</span></td>';
            } else if (tempActiveIncrease<0){
            reportStr += '<td><span class="mediumfont_data">' + subtractThree(countryData[i]['Confirmed'], countryData[i]['Deaths'],countryData[i]['Recovered'])+ '</span><br><span class="blue smallfont_data">(' + tempActiveIncrease +  ')</span></td>';
            }else{
              reportStr += '<td><span class="mediumfont_data">' + subtractThree(countryData[i]['Confirmed'], countryData[i]['Deaths'],countryData[i]['Recovered'])+ '<br>(0)</span></td>';
            }

            if(countryData[i]['Deaths']>0){
            reportStr += '<td><span class="mediumfont_data">' + countryData[i]['Deaths'] + '</span><br><span class="red smallfont_data">('+toPercent(countryData[i]['Deaths']/countryData[i]['Confirmed'],2)+')<br></span><span class="smallfont_data">of total</span></td>' ;
          }else{
            reportStr += '<td><span class="mediumfont_data">' + countryData[i]['Deaths'] + '<br>(0)</span></td>';
          }
          if(country!='USA'){
            if(countryData[i]['Recovered']>0){
            reportStr += '<td><span class="mediumfont_data">' + countryData[i]['Recovered'] + '</span><br><span class="green smallfont_data">('+toPercent(countryData[i]['Recovered']/countryData[i]['Confirmed'],2)+')<br></span><span class="smallfont_data">of total</span></td>';
            }else{
              reportStr += '<td><span class="mediumfont_data">' + countryData[i]['Recovered'] + '<br>(0)</span></td>';
            }
          }
            reportStr += '</tr>';

            //tempData = [countryData [i]['DataDate'], countryData [i]['Confirmed']];
            var tempConfirmed = countryData[i]['Confirmed'] != 0 ? countryData[i]['Confirmed'] : 0;
            var tempDeaths = countryData[i]['Deaths'] != 0 ? countryData[i]['Deaths'] : 0;
            var tempRecovered = countryData[i]['Recovered'] != 0 ? countryData[i]['Recovered'] : 0;
            var tempActive = countryData[i]['Active'] != 0 ? countryData[i]['Active'] : 0;

            dateConvert(countryData[i]['DataDate']);
            tempDataConfirmed = [dateConvert(countryData[i]['DataDate']),tempConfirmed]; //array push for column chart
            tempDataDeath = [dateConvert(countryData[i]['DataDate']), tempDeaths]; //array push for column chart
            tempDataRecovered = [dateConvert(countryData[i]['DataDate']),tempRecovered]; //array push for column chart
            tempDataIncrease = [dateConvert(countryData[i]['DataDate']), countryData[i]['Increase']]; //array push for column chart
            function dateConvert(date){
              month = [,'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
              monthDate = month[parseInt(date.substr(5,2))];
              dayDate=date.substr(8,2);
              return (monthDate+' '+dayDate);
            }
            if(country=='USA'){
              tempDataStack = [dateConvert(countryData[i]['DataDate']),parseInt(tempActive),parseInt(tempConfirmed),parseInt(tempDeaths)]; //array push for stack chart
            }else{
              tempDataStack = [dateConvert(countryData[i]['DataDate']),parseInt(tempActive),parseInt(tempConfirmed),parseInt(tempRecovered),parseInt(tempDeaths)]; //array push for stack chart
            }

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
        //$('#colors').html(conditionStr);
        if(country!='USA'&&countryData == null){
            $('#reportDaily').html('');
        }
        else{
          $('#reportDaily').html(reportDailyStr);
        }


        //console.log(dataCorona);
        //console.log(dataElm);

        //var tempData =['DateDate', 'Confirmed'];
        var tempDataConfirmed = ['DataDate', 'Confirmed'];
        var tempDataDeath = ['DataDate', 'Deaths'];
        var tempDataRecovered = ['DataDate', 'Recovered'];
        var tempDataIncrease = ['DataDate', 'Increase'];

        if(country=='USA'){
          var tempDataStack = ['DataDate','Active','Confirmed','Deaths'];
        }else{
          var tempDataStack = ['DataDate','Active','Confirmed','Recovered','Deaths'];
        }

        dataElmConfirmed.unshift(tempDataConfirmed); //array push for column chart
        dataElmDeath.unshift(tempDataDeath); //array push for column chart
        dataElmRecovered.unshift(tempDataRecovered); //array push for column chart
        dataElmIncrease.unshift(tempDataIncrease); //array push for column chart
        dataElmStack.unshift(tempDataStack);


        if (countryData != null) {
          google.charts.load('current', {
            'packages': ['bar']
          });
          google.charts.load('current', {'packages':['corechart']});
          //if(tempCountryRecentConfirmed>0){google.charts.setOnLoadCallback(drawConfirmedChart);}
          //if(tempCountryRecentDeaths>0){google.charts.setOnLoadCallback(drawDeathChart);}
          //if(tempCountryRecentRecovered>0){google.charts.setOnLoadCallback(drawRecoveredChart);}

          google.charts.load('current', {
            packages: ['corechart', 'line']
          });
          google.charts.setOnLoadCallback(drawIncreaseChart);
          google.charts.setOnLoadCallback(drawStackChart);
        }

        if(country=='USA'){
          if(countryData==null){
            coronaUsMap('main');
          }else{
            coronaUsMap('');
          }
        }
        else{$('#coronaUsMap').html('');  $('#coronaUsMapReport').html('');}


        function drawStackChart(){
          var data = google.visualization.arrayToDataTable(dataElmStack);
          if(country=='USA'){
            var options = {
             title: 'Confirmed, Active, Deaths',
             pointSize:5,
             height: 600,
             legend: { position: 'top', maxLines: 3 },
             bar: { groupWidth: '75%' },
             seriesType: 'bars',
             series: {
               0:{color:'#FF932B',type:'bar',targetAxisIndex:0},//Active
               1:{color:'#8B5EFF',type:'bar',targetAxisIndex:0},//confirmed
              // 2:{color:'#FFDC73',type:'bar',targetAxisIndex:0},
              // 2:{color:'#8FDAFF',type: 'line',targetAxisIndex:1},//recovery
               //2:{color:'#FFDC73',type: 'line',targetAxisIndex:1},//death
               2:{color:'#44FF57',type: 'line',targetAxisIndex:1},//death
              },
              //isStacked: true,
              hAxis: {
              title: 'Date',
              slantedText: true,
              slantedTextAngle:50,
              },
              vAxis:{

                0:{minValue:0},
                1:{minValue:0}
              },
              vAxes: {
                0: {
                },
                1: {
                    format:"#"
                }
            },
            width: '100%',
           };
          }else{
            var options = {
             title: 'Confirmed, Active, Recovered, Deaths',
             pointSize:5,
             height: 600,
             legend: { position: 'top', maxLines: 3 },
             bar: { groupWidth: '75%' },
             seriesType: 'bars',
             series: {
               0:{color:'#FF932B',type:'bar',targetAxisIndex:0},// active
               1:{color:'#8B5EFF',type:'bar',targetAxisIndex:0},//confirmed
              // 2:{color:'#FFDC73',type:'bar',targetAxisIndex:0},
               2:{color:'#5EFFF2',type: 'line',targetAxisIndex:1},//recovery
               //2:{color:'#8FDAFF',type: 'line',targetAxisIndex:1},//recovery
               3:{color:'#F64FFF',type: 'line',targetAxisIndex:1},//death
              },
              //isStacked: true,
              hAxis: {
              title: 'Date',
              slantedText: true,
              slantedTextAngle:50,
              },
              vAxis:{

                0:{minValue:0},
                1:{minValue:0}
              },
              vAxes: {
                0: {
                },
                1: {
                    format:"#"
                }
            },
            width: '100%',
           };
          }

          var chart = new google.visualization.ComboChart(document.getElementById('stackchart'));
          chart.draw(data, options);
        }


        function drawConfirmedChart() {

          var data = google.visualization.arrayToDataTable(dataElmConfirmed);

          //https://developers.google.com/chart/interactive/docs/gallery/piechart?hl=ko#Configuration_Options
          //collumn chart customization - options
          //=>https://developers.google.com/chart/interactive/docs/gallery/columnchart

          var options = {

            title: 'Total confirmed cases',
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
            title: 'Total deaths',
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
            title: 'Total recovered',
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
            pointSize:5,
            height: 600,
            title: 'Daily confirmed case increase',
            colors: ['#EB7F75'],
            legend: {
              position: "top"
            },
            hAxis: {
              title: 'Time',
              slantedText: true,
              slantedTextAngle:50
            },
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
