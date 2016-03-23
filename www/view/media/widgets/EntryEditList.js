$class('EntryEditList', [kx.Widget, kx.ActionMixin, kx.EventMixin], {

    __constructor: function() {
    },

    onAttach: function(domNode) {
        this.tBody = this._domNode.find('tbody');
        this._domNode.find('a.add').off().click(kx.bind(this, 'onAddEntry'));
        this._domNode.delegate('a.del', 'click', kx.bind(this, 'onDelEntry'));
    },

    init: function (closest) {
        this.closest = closest;
    },

    resetBody: function() {
        this.tBody.empty();
    },

    addEntry: function(entry) {
        var tr = this.onAddEntry();
        return tr;
    },

    onAddEntry: function () {
        var entry = this._domNode.find('thead tr.entry-template');
        var tr = entry.clone().removeClass('entry-template').css('display', '');
        tr.appendTo(this.tBody);

        return tr;
    },

    onDelEntry: function (a) {
        var target = a.target;
        var tr = target.closest('tr');  // this.closest
        tr.remove();
    }
});
