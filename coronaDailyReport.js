function coronaDailyReport(ProvinceState, CountryRegion, country,type) {
  if (country == 'USA') {
    var urlApi = '/coronaserver/coronaDailyDataApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion + '&country=' + country;
  } else if (country == 'global') {
    var urlApi = '/coronaserver/coronaDailyDataApi.php?ProvinceState=' + ProvinceState + '&CountryRegion=' + CountryRegion;
  }
  if(type=='total'){
    urlApi+='&type=total';
  }
  console.log(urlApi);

  $.ajax({
    type: 'GET',
    url: urlApi,
    contentType: false,
    data: false,
    processData: false,
    success: function(data) {
      isUploading = false;
      var obj = JSON.parse(data);

      if (obj.result == "success") {
        var countryDataRecent = obj.data.countryDataRecent;

        function toPercent(number, float) {
          var percent = parseFloat(number * 100).toFixed(float) + "%";
          return percent;
        } //percentage

        var reportDailyStr ='';

        if(countryDataRecent !=null){
          var RecentConfirmed = countryDataRecent.Confirmed;
          var RecentActive = countryDataRecent.Active;
          var RecentDeaths = countryDataRecent.Deaths;
          var RecentRecovered =countryDataRecent.Recovered;
          var RecentConfirmedIncrease = countryDataRecent.Increase;
          var RecentActiveIncrease = countryDataRecent.ActiveIncrease;
          var RecentDeathsIncrease = countryDataRecent.DeathsIncrease;
          var RecentRecoveredIncrease =countryDataRecent.RecoveredIncrease;
          var RecentDate =countryDataRecent.RecentDate;
          var RecentWrittenAtUtc = countryDataRecent.WrittenAtUtc;

    		  reportDailyStr +='<h5 class="display-6 class-border mediumfont">&nbsp;Situation summary</h5>';

          reportDailyStr +='<table class="table table-hover">';
          reportDailyStr += '<thead class="table-success"><tr>';
          reportDailyStr += '<th scope="col" colspan="1" class="smallfont">Confirmed<br>';

          if(type=='total'){
            if(country=='USA'){
              reportDailyStr+='in US';
            }else{//global
              reportDailyStr+='Globally';
              //reportDailyStr+='<span>in </span>'+ ProvinceState;
            }
          }else{//type!=total
            if(country=='USA'){ //country=USA, ProvinceState= specific state
              reportDailyStr+='<span>in </span>'+ ProvinceState+'[USA]';
            }else{
              reportDailyStr+='['+CountryRegion+']';
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

          reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Confirmed+'</span><br><span style="font-size:0.75rem">(<span style="color:red">+'+countryDataRecent.Increase+'</span>)</span>   <span style="font-size:0.75rem; color:grey"><br>'+toPercent(countryDataRecent.Confirmed/(countryDataRecent.Confirmed-countryDataRecent.Increase)-1,1)+' growth</span>   </td>';

          reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Active+'</span><br><span style="font-size:0.75rem">(<span class="';
          countryDataRecent.Active>=0? reportDailyStr += 'red':reportDailyStr += 'blue';
          if(RecentActiveIncrease>=0){
            reportDailyStr += '">+'+RecentActiveIncrease+'</span>)<br></span><span style="font-size:0.75rem; color:grey">'+toPercent(countryDataRecent.Active/(countryDataRecent.Active-countryDataRecent.ActiveIncrease)-1,1)+' growth</span></td>';
          }else {
            reportDailyStr += '">'+RecentActiveIncrease+'</span>)<br></span><span style="font-size:0.75rem; color:grey">'+toPercent(countryDataRecent.Active/(countryDataRecent.Active-countryDataRecent.ActiveIncrease)-1,1)+' growth</span></td>';
          }

            
          if(countryDataRecent.DeathsIncrease>=0){
            reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Deaths+'</span><br><span style="font-size:0.75rem">(<span class="red">+'+countryDataRecent.DeathsIncrease+'</span>)</span><span style="font-size:0.75rem; color:grey"><br>'+toPercent(RecentDeaths/(RecentDeaths-countryDataRecent.DeathsIncrease)-1,1)+' growth</span></td>';
          }else{
            reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Deaths+'</span><br><span style="font-size:0.75rem">(<span class="blue">'+countryDataRecent.DeathsIncrease+'</span>)</span><span style="font-size:0.75rem; color:grey"><br>'+toPercent(RecentDeaths/(RecentDeaths-countryDataRecent.DeathsIncrease)-1,1)+' growth</span></td>';
          }
          if(country!='USA'){
           reportDailyStr += '<td><span class="largefont_data">'+countryDataRecent.Recovered+'</span><br><span style="font-size:0.75rem">(<span class="red">+'+countryDataRecent.RecoveredIncrease+'</span>)</span><span style="font-size:0.75rem; color:grey"><br>'+toPercent(countryDataRecent.Recovered/(countryDataRecent.Recovered-countryDataRecent.RecoveredIncrease)-1,1)+' growth</span></td>';
          }
          reportDailyStr += '</tr>';
          reportDailyStr += '<tr>';
          reportDailyStr += '</tr>';
          reportDailyStr += '</tbody>';
          reportDailyStr += '</table>';
          reportDailyStr += '<p class="font-italic"><small>Last updated: '+RecentDate+' in GMT; Data updates every hour and is sourced and cross-checked with official government websites</small></p>';
          reportDailyStr += '<p class="font-italic"><small> </small></p>';

        }
        $('#reportDaily').html(reportDailyStr);
      }
    }


  });


}
