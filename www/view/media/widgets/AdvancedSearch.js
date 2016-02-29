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

    addOptions: function() {
        var o = "<li class='active-result'></li>";

    },

    onChange: function(value, results) {
        var tabNode = this._domNode.find('[table]');
        var tableName = tabNode.attr('table');
        var field = tabNode.attr('field');
        $.post('ajax/search', {
            'table': tableName, 'field': field, 'search': value
        }, function (data) {
            console.log(data);
        });
    }
});