$class('RegionSelector', [kx.Widget, kx.ActionMixin, kx.EventMixin], {

    listenTo: null,

    mode: null,

    onAttach: function(domNode) {
        this.listenTo = domNode.attr('listen-to');
        this.mode = domNode.attr('mode');
        var widgetId = this.widgetId();
        domNode.find('select').change(function () {

            var select = $(this), value = select.val();
            console.log(widgetId, value);
            select.trigger('region-selector-changed', [select, widgetId, value]);
        });

        var selectors = $('div[widget-class=RegionSelector]');
        selectors.on('region-selector-changed', kx.bind(this, 'onEvent'));
    },

    loaded: function (data) {
        var d = data.toJson();
        var items = d.data;
        var select = this._domNode.find('select');
        select.empty();

        console.log(items);

        var template = '<option value="{region_index}">{region_name}</option>';

        for (var i in items) {
            var r = items[i];
            var regionIndex = r['sys_region_index'], regionName = r['sys_region_name'];

            var o = template.format({region_index: regionIndex, region_name: regionName});
            $(o).appendTo(select);
        }

        select.change();
    },

    reload: function(parentId) {
        if (this.mode == 'city') {
            var action = 'ajax/cities/' + parentId;
        } else if (this.mode == 'county') {
            var action = 'ajax/counties/' + parentId;
        }

        this.ajax(action, null, kx.bind(this, 'loaded'));
    },

    onEvent: function (e, select, widgetId, val) {
        if (widgetId == this.listenTo) {
            this.reload(val);
        }
    }
});