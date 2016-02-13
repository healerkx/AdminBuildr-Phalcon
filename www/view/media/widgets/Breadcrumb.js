/**
 * Created by Healer on 14-6-6.
 */
$class("Breadcrumb", [kx.Widget, kx.EventMixin],
{
    __constructor: function() {
    },

    onAttach: function() {
        console.log('onAttach');
        $("body").bind("transfer-selected-time", kx.bind(this, "onDateRangeChanged"));
        this.setLevels([{"url": "", "name": "", "type": ""}]);
    },

    onDateRangeChanged: function(e, date) {
        g.setBeginTime(date['start']);
        g.setEndTime(date['end']);
    },

    setLevels: function(levels) {
        console.log('setLevels', levels);
        this._domNode.find("ul li.home").hide();
        this._domNode.find("ul li.home i.icon-angle-right").hide();
        var l = 0;
        for (var i in levels)
        {
            l += 1;
            var level = this._domNode.find("ul li.level" + l);
            level.show();
            level.find("a")
                .text(levels[i]['name'])
                .attr("href", levels[i]['url'])
                .attr("type", levels[i]['type']);

            if (l > 1)
            {
                var prev = l - 1;
                this._domNode.find("ul li.level" + prev).find("i.icon-angle-right").show();
            }
        }

        this._domNode.find("ul li.home a").bind("click", kx.bind(this, "onLevelClick"));
    }

});