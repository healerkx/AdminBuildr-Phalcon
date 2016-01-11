/**
 * Created by kcj on 14-9-28.
 */
$class("DoubleCharts", null, {

    showCharts: function(domNode, p) {

        Highcharts.setOptions({ global: {useUTC: false}});
        this.params = p;

        console.log(new Date(p.start), new Date(p.end))
        if (p.filter)
        {
            var array = p.filter(this._items);
            this.chartsData = [];
            for (var i in array.data)
            {
                var v = array.data[i];
                if (v)
                    this.chartsData.push(parseFloat(v));
                else
                    this.chartsData.push(null);
            }
        }

        this.createMaster(domNode, p);
    },

    createDetail: function(domNode, p, masterChart) {

        if (p.filter)
        {
            //var items = p.filter(this._items);
        }

        var this_ = this;
        var selector = p.selector || 'div.charts';

        var detailData = [];
        jQuery.each(masterChart.series[0].data, function(i, point) {

            if (point.x >= p.start) {
                detailData.push(point.y);
            }
        });


        this.detailsChart = domNode.find(selector).css('width', '100%').highcharts({
            chart: {
                marginBottom: 80,
                reflow: true,
                marginLeft: 60,
                marginRight: 20,
                turboThreshold: 2000
            },
            credits: {
                enabled: false
            },
            title: {
                text: p.title
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: p.ytitle
                },
                maxZoom: 0.1,
                max: p.max,
                min: p.min
            },
            tooltip: {
                formatter: function() {

                    var point = this.points[0];
                    /*return '<b>'+ point.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%A %B %e %Y', this.x) + ':<br/>'+
                        '1 USD = '+ Highcharts.numberFormat(point.y, 2) +' EUR';*/
                    //return '<b>' + point.series.name;
                    return '<b>' + Highcharts.numberFormat(point.y, 2) + '</b><br/>' + 
                        Highcharts.dateFormat('[%Y-%m-%d %H:%M:%S]', this.x); 

                },
                shared: true
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    marker: {
                        enabled: false,
                        states: {
                            hover: {
                                enabled: true,
                                radius: 3
                            }
                        }
                    }
                },
                area:{ turboThreshold: 10000}
            },
            series: [{
                name: '',
                pointStart: p.start,
                pointInterval: p.interval,
                data: detailData
            }],

            exporting: {
                enabled: false
            }
        }).highcharts();

    },

    // create the master chart
    createMaster: function(domNode, p)
    {
        console.log(this.chartsData);
        var this_ = this;
        var selector = 'div.charts-bar';
        this.masterChart = domNode.find(selector).highcharts({
            chart: {
                reflow: false,
                borderWidth: 1,
                backgroundColor: null,
                marginLeft: 50,
                marginRight: 20,
                zoomType: 'x',
                events: {

                    // listen to the selection event on the master chart to update the
                    // extremes of the detail chart
                    selection: function(event) {
                        var extremesObject = event.xAxis[0],
                            min = extremesObject.min,
                            max = extremesObject.max,
                            detailData = [],
                            xAxis = this.xAxis[0];

                        // console.log(this.series[0].data.length)

                        jQuery.each(this.series[0].data, function(i, point) {

                            if (point.x > min && point.x < max)
                            {
                                if (!isNaN(point.y))
                                {
                                    detailData.push({ x: point.x, y: point.y });
                                }
                            }
                        });

                        // move the plot bands to reflect the new detail span
                        xAxis.removePlotBand('mask-before');
                        xAxis.addPlotBand({
                            id: 'mask-before',
                            from: p.start,
                            to: min,
                            color: 'rgba(0, 0, 0, 0.2)'
                        });

                        xAxis.removePlotBand('mask-after');
                        xAxis.addPlotBand({
                            id: 'mask-after',
                            from: max,
                            to: p.end,
                            color: 'rgba(0, 0, 0, 0.2)'
                        });

                        //this_.detailsChart.series[0].setData([]);

                        this_.detailsChart.series[0].setData(detailData);

                        return false;
                    }
                }
            },
            title: {
                text: null
            },
            xAxis: {
                type: 'datetime',
                showLastTickLabel: true,
                maxZoom: 1 * 24 * 3600000, // 7 days
                plotBands: [{
                    id: 'mask-before',
                    from: p.start,
                    to: p.end,
                    color: 'rgba(0, 0, 0, 0.2)'
                }],
                title: {
                    text: null
                }
            },
            yAxis: {
                gridLineWidth: 1,
                labels: {
                    enabled: false
                },
                title: {
                    text: null
                },
                max: p.max,
                min: p.min,
                showFirstLabel: true
            },
            tooltip: {
                formatter: function() {
                    return false;
                }
            },
            legend: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    fillColor: {
                        linearGradient: [0, 0, 0, 70],
                        stops: [
                            [0, '#4572A7'],
                            [1, 'rgba(0,0,0,0)']
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    enableMouseTracking: false
                },
                area:{ turboThreshold: 10000}
            },

            series: [{
                type: 'area',
                name: '',
                pointInterval: p.interval,
                pointStart: p.start,

                data: this_.chartsData
            }],

            exporting: {
                enabled: false
            }

        }, function(masterChart) {
            this_.createDetail(domNode, this_.params, masterChart)
        }).highcharts();
    }

});
