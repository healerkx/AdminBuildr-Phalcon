/**
 * Created by Healer on 14-6-10.
 */

$class("AverageValue", null, {

    _values: null,

    __constructor: function()
    {
        this._max = 0.0;
        this._min = null;
    },

    addValue: function(value)
    {
        if (this._values == null)
        {
            this._values = [];
        }

        if (value)
        {
            var v = parseFloat(value)
            if (v > this._max)
                this._max = v;
            if (this._min)
            {
                if (v < this._min)
                    this._min = v;
            }
            else
            {
                this._min = v;
            }
            this._values.push(v);
        }
    },

    getValue: function() {
        var size = this._values.length;
        if (size > 1) {
            var s = 0.0;
            for (var i in this._values) {
                s += this._values[i];
            }
            return (s / size);
        } else if (size == 1) {
            var v = this._values[0];

            var f = parseFloat(v);
            if (!isNaN(f))
                return f;

            console.log(v)
            return null;
        }

        return null;
    },

    getMax: function() {
        return this._max;
    },

    getMin: function() {
        return this._min;
    },

    clearValues: function() {
        this._values = [];
    }
});

$class("GroupValue", null, {

    _values: null,

    __constructor: function(time) {
        this._time = time || {};
    },

    addValue: function(value) {
        if (this._values == null)
        {
            this._values = [];
        }

        if (value)
        {
            this._values.push(value);
        }
    },

    getValue: function() {
        var size = this._values.length;

        var r = {}
        for (var i in this._values) {
            var v = this._values[i];
            for (var k in v) {
                if (k == 'time') {
                    continue;
                }
                if (r[k]) {
                    r[k] += parseFloat(v[k])
                } else {
                    r[k] = parseFloat(v[k])
                }
            }
        }

        for (var k in r) {

            if (k != 'time' && k != 'starttime' && k != 'endtime' &&  k != 'BeginTime')
                r[k] = (r[k] / size).toFixed(1);
        }
        for (var i in this._time) {
            r[i] = this._time[i];
        }
        return r;
    },

    clearValues: function() {
        this._values = [];
    }
});


$class("Global", Base,
{
    _row: null,

    _curStationId: null,


    init: function() {

        var self = this;
        $("ul.sub-menu a").click(function(){

            var url = $(this).attr("href");
            var id = url;

            self.showRow(id);

            return false;
        });
    },

    addRow: function(row) {
        if (!this._row) {
            this._row = [];
        }

        this._row.push(row);
    },

    showRow: function(row) {
        // console.log(this);
        for (var i in this._row)
        {
            if (this._row[i] == row)
            {
                $(row).css('display', 'block');
            }
            else
            {
                $(this._row[i]).css('display', 'none');
            }
        }
    },

    getDeviceName: function(deviceType) {
        switch (deviceType)
        {
            case "hpic":
                return "高压电离室";
            case "labr":
                return "溴化镧谱仪(空气)";
            case "cinderella":
                return "特征核素甄别系统";
            case "cinderelladata":
                return "特征核素甄别系统";
            case "hpge":
                return "高纯锗能谱仪";
            case "weather":
                return "气象站";
            case "environment":
                return "环境与安防监控";
            case "bai9850":
                return "气溶胶总放与碘监测系统";
            case "inspector1000":
                return "便携式核素甄别仪";
            case "mds":
                return "大体积放射源搜索系统";
            case "radeye":
                return "便携式γ辐射测量仪";
            case "bai9125":
                return "水质放射性监测系统";
            case "labrfilter":
                return "溴化镧谱仪(滤纸)";
        }
    },

    getCurrentStationId: function() {
        return this._curStationId;
    },

    setCurrentStationId: function(stationId) {
        this._curStationId = stationId;
    },

    getCurrentStationName: function() {
        return this._stationName;
    },

    setCurrentStationName: function(stationName) {
        this._stationName = stationName;
    },

    setBeginTime: function(beginTime) {
        this._beginTime = beginTime;
    },

    setEndTime: function(endTime) {
        this._endTime = endTime;
    },

    getBeginTime: function(format) {
        var ret = this._beginTime || Date.today();
        if (format)
        {
            return ret.toString(format);
        }
        return ret;
    },

    getEndTime: function(format) {
        var ret = this._endTime || Date.today().addHours(24);
        if (format)
        {
            return ret.toString(format);
        }
        return ret;

    },

    showTip: function(text, title) {

        $.gritter.add({
            'title': title || "系统消息",
            'text': text
        });
    },

    // Get PHP Unix time equals time()
    getUnixTime: function() {
        return Math.round(new Date().getTime() / 1000);
    },

    setAlerts: function(alerts) {
        this._alerts = alerts;
    }

});
