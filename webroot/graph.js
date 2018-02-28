var margin = {top: 25, right: 15, bottom: 30, left: 35},
	width = parseInt(d3.select("#graph-container").style("width")) - margin.left - margin.right,
	height = parseInt(d3.select("#graph-container").style("height")) - margin.top - margin.bottom;

var parseDate = d3.timeParse("%d-%m-%Y %H:%M:%S");

var xScale = d3.scaleTime().range([0, width]);
var yScale = d3.scaleLinear().range([height, 0]);
var color = d3.scaleOrdinal(d3.schemeCategory10);

var xAxis = d3.axisBottom().scale(xScale).tickFormat(d3.timeFormat("%H:%M"));
var yAxis = d3.axisLeft().scale(yScale);

var line = d3.line().curve(d3.curveBasis)
	.x(function(d) { return xScale(d["time"]); })
	.y(function(d) { return yScale(d["light"]); });

var svg = d3.select("#graph-container")
	.attr("width", width + margin.left + margin.right)
	.attr("height", height + margin.top + margin.bottom)
	.append("g")
	.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var sel_date = document.getElementById('date-graph').value;

d3.json("data.json.php?for=graph&date=" + sel_date, function(error, data){
	if (error) throw error;

	var sensorCat = d3.keys(data[0]).filter(function(key){return (key !== "insert_time")})
	color.domain(sensorCat);

	data.forEach(function(d){
		d["insert_time"] = parseDate(d["insert_time"])
	});

	var intensity = sensorCat.map(function(category){
		return {category: category, datapoints: data.map(function(d){
			return {time: d["insert_time"], light: +d[category]}
		})}
	});

	xScale.domain(d3.extent(data, function(d) {return d["insert_time"]; }));
	yScale.domain([1023, 0]);

	svg.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);
	svg.append("g")
		.attr("class", "y axis")
		.call(yAxis)
	.append("text")
		.attr("class", "label")
		.attr("y", 5)
		.attr("dy", "0")
		.attr("dx", "5px")
		.text("Nilai Sensor");
	
	var products = svg.selectAll(".category")
		.data(intensity)
		.enter().append("g")
		.attr("class", "category");
	products.append("path")
		.attr("class", "line")
		.attr("d", function(d) {return line(d.datapoints);})
		.attr("data-legend", function(d) {return d.category;})
		.style("stroke", function(d) {return color(d.category);});
	
	var legendRectSize = 10;
	var legendSpacing = 5;

	var legend = d3.select('svg')
		.append("g")
		.selectAll("g")
		.data(color.domain())
		.enter()
		.append('g')
		.attr('class', 'legend')
		.attr('transform', function(d, i) {
			var x = (i * 80) + 80;
			var y = 0;
			return 'translate(' + x + ',' + y + ')';
		});
	legend.append('rect')
		.attr('width', legendRectSize)
		.attr('height', legendRectSize)
		.style('fill', color)
		.style('stroke', color);
	legend.append('text')
		.attr('x', legendRectSize + legendSpacing)
		.attr('y', legendRectSize - 1)
		.text(function(d) {return d;});
	svg.append("text")
		.attr("x", -35)
		.attr("y", -16)
		.attr('style', 'font-weight: bold; font-size: 9pt;')
		.text("Keterangan:");

});

function resize() {
	var width = parseInt(d3.select("#graph-container").style("width")) - margin.left - margin.right,
		height = parseInt(d3.select("#graph-container").style("height")) - margin.top - margin.bottom;

	xScale.range([0, width]);
	yScale.range([height, 0]);

	svg.select('.x.axis')
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);
	svg.select('.y.axis')
		.call(yAxis);
	svg.selectAll('.line')
		.attr("d", function(d) { return line(d.datapoints); });

	xAxis.ticks(Math.max(width/75, 2));
	yAxis.ticks(Math.max(height/50, 2));
};

d3.select(window).on('resize', resize);
resize();
