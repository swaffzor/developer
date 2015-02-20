	google.load('visualization', '1', {packages:['corechart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          	['Job', 'Hours per Day'],
          	['Admin',	38.25],
          	['Yard',	0.0],
          	['Hampton',	0.0],
			['CC Pond',	40.0],
			['Twin Oaks',	0.0],
			['Coats',	28.0],
			['BW Capers',	0.0],
			['Osc. Channel',	0.0],
			['Lake Parks',	0.0],
			['Bids',	0.0],
		
        ]);

        var options = {
          title: 'Total Hours by Job 8-23-2013',
			backgroundColor: 'white',
			pieSliceText: 'label',
			is3D:'true',
			chartArea:{left:20,top:20,width:'50%',height:'60%'}
			
			//legend.textStyle: {color: <'white'>}

			
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }

	
