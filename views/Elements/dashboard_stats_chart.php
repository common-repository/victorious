<div id="chart_div" style="width: 100%; height: 500px;"></div>


<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
        // ['Month', 'Total Contests'],['1', 1000],['2', 1170],['3', 660],['4', 1030],['5', 1200],['6', 600],['7', 2000],['8', 2500],['9', 2100],['10', 2300],['11', 1900],['12', 2900]
        <?php echo $str_chart; ?>
    ]);

    var options = {
      title: 'Site Stats',
      hAxis: {title: 'Month',  titleTextStyle: {color: '#333'}},
      vAxis: {minValue: 0},
      isStacked: true,
      tooltip: {trigger: 'focus'}
    };

    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>