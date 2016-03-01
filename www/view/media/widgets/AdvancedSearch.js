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

    addOptions: function(items, primaryKey) {
        var real = this._domNode.find('select');
        var field = real.attr('search-field');
        real.children().not('[value=0]').remove();

        for (var i in items) {
            var item = items[i];
            var n = $("<option></option>");
            n.val(item[primaryKey]).text(item[field]);
            real.append(n);
        }

        var v = this._domNode.find('.chzn-search input').val();
        real.trigger('liszt:updated');      // Why liszt ???
        if (!String.isEmpty(v)) {
            this._domNode.find('.chzn-search input').val(v.trim());
        }

    },

    onChange: function(value, select) {
        var tabNode = this._domNode.find('[search-table]');
        var tableName = tabNode.attr('search-table');
        var field = tabNode.attr('search-field');
        var this_ = this;

        $.post('ajax/search', {
            'table': tableName, 'field': field, 'search': value
        }, function (data) {
            console.log(data)
            var json = data.toJson();
            if (json.error == 0) {
                console.log(data);
                this_.addOptions(json.data.results, json.data.key);
            }
        });
    }
});