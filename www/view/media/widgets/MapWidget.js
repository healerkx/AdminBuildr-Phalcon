/**
 * Created by Healer on 14-9-22.
 */
$class("BaiduMap", [kx.Widget, kx.ActionMixin, kx.EventMixin],
{

    onAttach: function(domNode) {

        this.showMap();
    },

    showMap: function() {

        // 大气站
        var y1 = 22.02178333;
        var x1 = 113.21576333;
        var gpsPoint1 = new BMap.Point(x1, y1);

        var gpsCenter = new BMap.Point(112.76, 22.5);

        //地图初始化
        var map = new BMap.Map("allmap");

        map.addControl(new BMap.NavigationControl());

        var this_ = this;
        this.addStation(map, gpsPoint1, "超大流量大气辐射环境自动监测站", function(){
            this_.showStationRow(128);
        });

        map.centerAndZoom(gpsCenter, 11);

    },

    showStationRow: function(stationId) {
        $('#network-row').hide();

    },

    addStation: function(map, gpsPoint, text, clickHandler) {

        translateCallback = function (point){
            var marker = new BMap.Marker(point);
            map.addOverlay(marker);
            var label = new BMap.Label(text, {offset:new BMap.Size(20, -0)});            
            label.addEventListener("click", clickHandler);
            marker.setLabel(label);
            /*var infowindow = new BMap.InfoWindow(text, {width:20,height:10, title:"自动站"})
            map.openInfoWindow(infowindow,point);
            infowindow.addEventListener("click", clickHandler);*/
            //marker.setAnimation(BMAP_ANIMATION_BOUNCE);
        };

        BMap.Convertor.translate(gpsPoint, 0, translateCallback);
    }
});