<?PHP

$sql2="SELECT CountryRegion,ActiveCases,DataDate AS DD ,
(ActiveCases-(SELECT ActiveCases FROM CoronaData2 WHERE CountryRegion='China' AND DataDate<DD ORDER BY DataDate DESC LIMIT 1))/ActiveCases*100 AS ActiveCasesIncrease
FROM  CoronaData2
WHERE CountryRegion = 'China'
ORDER BY DataDate DESC
LIMIT 5";
  $sql="
SELECT ROUND(AVG(ActiveCasesIncrease),2) AS avgActiveIncrease FROM(
SELECT CountryRegion,ActiveCases,DataDate AS DD ,
(ActiveCases-(SELECT ActiveCases FROM CoronaData2 WHERE CountryRegion='China' AND DataDate<DD ORDER BY DataDate DESC LIMIT 1))/ActiveCases*100 AS ActiveCasesIncrease
FROM  CoronaData2
WHERE CountryRegion = 'China'
ORDER BY DataDate DESC
LIMIT 5
)AS a";
?>
