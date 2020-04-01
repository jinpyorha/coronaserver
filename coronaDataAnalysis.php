<?PHP
  include_once('dbconnect.php');

  $today = date('Y-m-d');
  $yesterday = date('Y-m-d',strtotime("-1 days"));
  $fiveDaysAgo = date('Y-m-d',strtotime("-5 days"));
/*한 나라의 최근 5일자 활성환자 증가율
$sql2="SELECT CountryRegion AS CR,ProvinceState AS PS , ActiveCases,DataDate AS DD ,
(ActiveCases-(SELECT ActiveCases FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState = PS AND DataDate<DD ORDER BY DataDate DESC LIMIT 1))/ActiveCases*100 AS ActiveCasesIncrease
FROM  CoronaData2
WHERE CountryRegion = 'USA' AND ProvinceState='' AND DataDate<'2020-04-01'
ORDER BY DataDate DESC
LIMIT 10";
*/
  $sql = "SELECT CR,PS,AVG(ActiveCasesIncrease) AS ACIavg FROM (
SELECT CountryRegion AS CR ,ProvinceState AS PS , ActiveCases,DataDate AS DD ,
(ActiveCases-(SELECT ActiveCases FROM CoronaData2 WHERE CountryRegion=CR AND ProvinceState=PS AND DataDate<DD ORDER BY DataDate DESC LIMIT 1))/ActiveCases*100 AS ActiveCasesIncrease
FROM  CoronaData2
WHERE DataDate>'".$fiveDaysAgo."' AND DataDate<'".$today."' AND ActiveCases>100 AND CountryRegion<>'Total:'
AND CountryRegion<>'Diamond Princess'
ORDER BY DataDate DESC
) AS b
GROUP BY CR";

  $sqlAsc= $sql." ORDER BY ACIavg ASC LIMIT 5";

  $sqlDesc= $sql." ORDER BY ACIavg DESC LIMIT 5";

  $resultAsc = $conn->query($sqlAsc);
  
  $resultDesc = $conn->query($sqlDesc);

  if ($resultAsc->num_rows > 0) {
    // output data of each row
    while($row = $resultAsc->fetch_assoc()) {
      echo $row['CR'].','.$row['ACIavg'].'<br>';
    }
  }
echo '#############';
  if ($resultDesc->num_rows > 0) {
    // output data of each row
    while($row = $resultDesc->fetch_assoc()) {
      echo $row['CR'].','.$row['ACIavg'].'<br>';
    }
  }

?>
