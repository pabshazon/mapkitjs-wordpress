jQuery().ready(function() {
    function visualize(filterVis) {

        // Fake JSON data

        var diameter = 600;

        var svg = d3.select('#graph').append('svg')
            .attr('width', diameter)
            .attr('height', diameter);

        var bubble = d3.layout.pack()
            .size([diameter, diameter])
            .value(function(d) {return d.size;})
            // .sort(function(a, b) {
            // 	return -(a.value - b.value)
            // })
            .padding(3);


        var tooltip = d3.select("body")
            .append("div")
            .style("position", "absolute")
            .style("z-index", "10")
            .style("visibility", "hidden")
            .style("color", "white")
            .style("padding", "8px")
            .style("background-color", "rgba(0, 0, 0, 0.75)")
            .style("border-radius", "6px")
            .style("font", "12px sans-serif")
            .text("tooltip");

        // generate data with calculated layout values
        var nodes = bubble.nodes(processData(mapData3d))
            .filter(function(d) { return !d.children; }); // filter out the outer bubble

        var vis = svg.selectAll('.node')
            .data(nodes)
            .enter().append('g')
            .attr("class", "node");

        vis.append('circle')
            .attr('class', function(d) { return d.className; })
            .attr('r', function(d) { return d.r; })
            .on("mouseover", function(d) {

               if(filterVis == 'hours'){
                    var text = d.code_name + ': ' + d.size;

                } else if(filterVis == 'money'){
                    var text = d.code_name + ': ' + formatCurrency(d.size);

                }else if(filterVis == 'total'){
                    var text = d.code_name + ': ' + d.size;

                }else{
                    var text = d.code_name + ': ' + d.size;
                }

                tooltip.text(text);
                tooltip.style("visibility", "visible");
            })
            .on("mousemove", function() {
                return tooltip.style("top", (d3.event.pageY-10)+"px").style("left",(d3.event.pageX+10)+"px");
            })
            .on("mouseout", function(){return tooltip.style("visibility", "hidden");});

        //.attr('transform', function(d) { return 'translate(' + d.x + ',' + d.y + ')'; });


        vis.append("text")
            .text(function(d) {
                return d.name; })
            .attr("text-anchor", "middle");

        vis.attr('transform', function(d) { return 'translate(' + d.x + ',' + d.y + ')'; })

        function processData(data) {
            var obj = data.countries;

            var newDataSet = [];

            for(var prop in obj) {

                if(filterVis == 'hours'){
                    sizeFiltered = obj[prop].hours;

                } else if(filterVis == 'money'){
                    sizeFiltered = obj[prop].money;

                }else if(filterVis == 'total'){
                    sizeFiltered = obj[prop].total_pledges;

                }else{
                    sizeFiltered = obj[prop].total_pledges;
                }

                newDataSet.push({name: obj[prop].country_name, code_name: prop, className: prop.toLowerCase(), size: sizeFiltered });
            }
            return {children: newDataSet};
        }

    }

    visualize();


    jQuery('#filter_vis').on('change', function() {
        var filter = this.value;
        jQuery("svg").remove();
        visualize(filter)
    });

    function formatCurrency(total) {
        var neg = false;
        if(total < 0) {
            neg = true;
            total = Math.abs(total);
        }
        return (neg ? "-$" : '$') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
    }

});
