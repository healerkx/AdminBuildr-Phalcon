$class('AdvancedSearch', [kx.Widget, kx.ActionMixin, kx.EventMixin], {

    __constructor: function() {

    },

    onAttach: function (domNode) {
        var this_ = this;
        var $results = domNode.find('.chzn-results');
        domNode.find('.chzn-search input').bind('input', function(){
            var value = $(this).val();
            this_.onChange(value, $results);
        })

    },

    addOptions: function(items, select) {
        var real = this._domNode.find('select');
        var a = real.children().not('[value=0]');
        a.remove();
        // TODO:
        var o = "<option value='{0}'>{1}</option>".format(2, 3);
        for (var i in items) {
            var n = $(o);
            real.append(n);
        }

        var v = this._domNode.find('.chzn-search input').val();
        real.trigger('liszt:updated');      // Why liszt ???
        this._domNode.find('.chzn-search input').val(v);
    },

    onChange: function(value, select) {
        var tabNode = this._domNode.find('[search-table]');
        var tableName = tabNode.attr('search-table');
        var field = tabNode.attr('search-field');
        var this_ = this;
        $.post('ajax/search', {
            'table': tableName, 'field': field, 'search': value
        }, function (data) {
            var json = data.toJson();
            if (json.error == 0) {
                this_.addOptions(json.data.results, select);
            }
        });
    }
});